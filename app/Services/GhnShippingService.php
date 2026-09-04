<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class GhnShippingService
{
    /**
     * Endpoint tính phí vận chuyển GHN.
     */
    private const FEE_ENDPOINT =
        '/shiip/public-api/v2/shipping-order/fee';


    /**
     * Tính phí vận chuyển GHN.
     *
     * Hiện tại VELORA Eyes sử dụng:
     * - Hàng nhẹ: service_type_id = 2
     * - Kích thước kiện mặc định phù hợp sản phẩm mắt kính
     *
     * Sau này có thể thay weight / size
     * theo số lượng sản phẩm trong giỏ hàng.
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
        $token = config(
            'services.ghn.token'
        );

        $shopId = config(
            'services.ghn.shop_id'
        );

        $baseUrl = rtrim(
            (string) config(
                'services.ghn.base_url'
            ),
            '/'
        );


        /*
         * Kiểm tra cấu hình trước khi gọi GHN.
         */
        if (!$token) {
            throw new RuntimeException(
                'Chưa cấu hình GHN_TOKEN.'
            );
        }


        if (!$shopId) {
            throw new RuntimeException(
                'Chưa cấu hình GHN_SHOP_ID.'
            );
        }


        if (!$baseUrl) {
            throw new RuntimeException(
                'Chưa cấu hình GHN_BASE_URL.'
            );
        }


        /*
         * GHN giới hạn insurance_value
         * tối đa 5.000.000đ.
         */
        $insuranceValue = max(
            0,
            min(
                $insuranceValue,
                5_000_000
            )
        );


        try {

            $response = Http::withHeaders([
                'Token' => $token,
                'ShopId' => (string) $shopId,
                'Content-Type' => 'application/json',
            ])
                ->acceptJson()
                ->timeout(15)
                ->post(
                    $baseUrl . self::FEE_ENDPOINT,
                    [
                        /*
                         * 2 = Hàng nhẹ.
                         */
                        'service_type_id' => 2,

                        /*
                         * Địa chỉ nhận hàng GHN.
                         */
                        'to_district_id' =>
                            $toDistrictId,

                        'to_ward_code' =>
                            $toWardCode,

                        /*
                         * Thông tin kiện hàng.
                         */
                        'weight' => $weight,
                        'length' => $length,
                        'width' => $width,
                        'height' => $height,

                        /*
                         * Giá trị khai giá.
                         */
                        'insurance_value' =>
                            $insuranceValue,

                        'coupon' => null,
                    ]
                );


            /*
             * Nếu GHN trả HTTP lỗi
             * như 400 / 401 / 500...
             */
            $response->throw();


            $data = $response->json(
                'data'
            );


            if (
                !is_array($data)
                || !isset($data['total'])
            ) {
                throw new RuntimeException(
                    'GHN không trả về phí vận chuyển hợp lệ.'
                );
            }


            return [
                /*
                 * Tổng phí cuối cùng GHN trả về.
                 */
                'total' =>
                    (int) $data['total'],

                /*
                 * Phí dịch vụ cơ bản.
                 */
                'service_fee' =>
                    (int) (
                        $data['service_fee']
                        ?? 0
                    ),

                /*
                 * Phí bảo hiểm.
                 */
                'insurance_fee' =>
                    (int) (
                        $data['insurance_fee']
                        ?? 0
                    ),

                /*
                 * Giữ response để sau này
                 * nếu cần xem các loại phí khác.
                 */
                'details' => $data,
            ];

        } catch (RequestException $exception) {

            $message =
                $exception
                    ->response
                    ?->json('message')
                ?? 'Không thể kết nối đến GHN.';


            throw new RuntimeException(
                'GHN: ' . $message,
                previous: $exception
            );
        }
    }
}