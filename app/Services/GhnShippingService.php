<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GhnShippingService
{
    private const PROVINCE_ENDPOINT =
        '/shiip/public-api/master-data/province';

    private const DISTRICT_ENDPOINT =
        '/shiip/public-api/master-data/district';

    private const WARD_ENDPOINT =
        '/shiip/public-api/master-data/ward';

    private const FEE_ENDPOINT =
        '/shiip/public-api/v2/shipping-order/fee';

    /**
     * Lấy danh sách Tỉnh/Thành phố GHN.
     */
    public function getProvinces(): array
    {
        $data = $this->request(
            'get',
            self::PROVINCE_ENDPOINT
        );

        return collect($data)
            ->map(fn (array $province) => [
                'id' => (int) (
                    $province['ProvinceID'] ?? 0
                ),
                'name' => trim(
                    (string) (
                        $province['ProvinceName'] ?? ''
                    )
                ),
            ])
            ->filter(
                fn (array $province) =>
                    $province['id'] > 0
                    && $province['name'] !== ''
            )
            ->sortBy('name')
            ->values()
            ->all();
    }

    /**
     * Lấy Quận/Huyện theo Tỉnh/Thành phố GHN.
     */
    public function getDistricts(
        int $provinceId
    ): array {
        $data = $this->request(
            'get',
            self::DISTRICT_ENDPOINT,
            [
                'province_id' => $provinceId,
            ]
        );

        return collect($data)
            ->map(fn (array $district) => [
                'id' => (int) (
                    $district['DistrictID'] ?? 0
                ),
                'name' => trim(
                    (string) (
                        $district['DistrictName'] ?? ''
                    )
                ),
            ])
            ->filter(
                fn (array $district) =>
                    $district['id'] > 0
                    && $district['name'] !== ''
            )
            ->sortBy('name')
            ->values()
            ->all();
    }

    /**
     * Lấy Phường/Xã theo Quận/Huyện GHN.
     */
    public function getWards(
        int $districtId
    ): array {
        $data = $this->request(
            'get',
            self::WARD_ENDPOINT,
            [
                'district_id' => $districtId,
            ]
        );

        return collect($data)
            ->map(fn (array $ward) => [
                'code' => trim(
                    (string) (
                        $ward['WardCode'] ?? ''
                    )
                ),
                'name' => trim(
                    (string) (
                        $ward['WardName'] ?? ''
                    )
                ),
            ])
            ->filter(
                fn (array $ward) =>
                    $ward['code'] !== ''
                    && $ward['name'] !== ''
            )
            ->sortBy('name')
            ->values()
            ->all();
    }

    /**
     * Tính phí vận chuyển GHN.
     */
    public function calculateFee(
        int $toDistrictId,
        string $toWardCode,
        int $weight = 500,
        int $length = 25,
        int $width = 15,
        int $height = 10,
        int $insuranceValue = 0
    ): array {
        $insuranceValue = max(
            0,
            min($insuranceValue, 5_000_000)
        );

        $data = $this->request(
            'post',
            self::FEE_ENDPOINT,
            [
                'service_type_id' => 2,
                'to_district_id' => $toDistrictId,
                'to_ward_code' => $toWardCode,
                'weight' => $weight,
                'length' => $length,
                'width' => $width,
                'height' => $height,
                'insurance_value' => $insuranceValue,
                'coupon' => null,
            ]
        );

        if (! isset($data['total'])) {
            throw new RuntimeException(
                'GHN không trả về phí vận chuyển hợp lệ.'
            );
        }

        return [
            'total' => (int) $data['total'],

            'service_fee' => (int) (
                $data['service_fee'] ?? 0
            ),

            'insurance_fee' => (int) (
                $data['insurance_fee'] ?? 0
            ),

            'details' => $data,
        ];
    }

    /**
     * Gửi request đến GHN.
     */
    private function request(
        string $method,
        string $endpoint,
        array $data = []
    ): array {
        /*
         * GHN yêu cầu Token + ShopId.
         */
        $headers = [
            'Token' => $this->token(),
            'ShopId' => (string) $this->shopId(),
            'Content-Type' => 'application/json',
        ];

        try {
            /*
             * Ép cURL sử dụng IPv4.
             */
            $client = Http::withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ])
                ->withHeaders($headers)
                ->acceptJson()
                ->connectTimeout(30)
                ->timeout(45);

            $url = $this->baseUrl() . $endpoint;

            if (strtolower($method) === 'get') {
                $response = $client->get(
                    $url,
                    $data
                );
            } else {
                $response = $client->post(
                    $url,
                    $data
                );
            }

            $response->throw();

            $responseData = $response->json('data');

            if (! is_array($responseData)) {
                throw new RuntimeException(
                    'GHN không trả về dữ liệu hợp lệ.'
                );
            }

            return $responseData;

        } catch (ConnectionException $exception) {

            throw new RuntimeException(
                'Không thể kết nối đến GHN.',
                previous: $exception
            );

        } catch (RequestException $exception) {

            $response = $exception->response;

            $message = $response?->json('message');

            if (! is_string($message) || trim($message) === '') {
                $message = 'GHN trả về lỗi không xác định.';

                if ($response) {
                    $message .= ' HTTP '
                        . $response->status();
                }
            }

            throw new RuntimeException(
                'GHN: ' . $message,
                previous: $exception
            );
        }
    }

    /**
     * Lấy Token GHN từ cấu hình.
     */
    private function token(): string
    {
        $token = trim(
            (string) config(
                'services.ghn.token'
            )
        );

        if ($token === '') {
            throw new RuntimeException(
                'Chưa cấu hình GHN_TOKEN.'
            );
        }

        return $token;
    }

    /**
     * Lấy Shop ID GHN từ cấu hình.
     */
    private function shopId(): int
    {
        $shopId = (int) config(
            'services.ghn.shop_id'
        );

        if ($shopId <= 0) {
            throw new RuntimeException(
                'Chưa cấu hình GHN_SHOP_ID.'
            );
        }

        return $shopId;
    }

    /**
     * Lấy URL môi trường GHN.
     */
    private function baseUrl(): string
    {
        $baseUrl = rtrim(
            trim(
                (string) config(
                    'services.ghn.base_url'
                )
            ),
            '/'
        );

        if ($baseUrl === '') {
            throw new RuntimeException(
                'Chưa cấu hình GHN_BASE_URL.'
            );
        }

        return $baseUrl;
    }
}