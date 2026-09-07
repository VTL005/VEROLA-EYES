<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Validation\ValidationException;

class ShippingFeeService
{
    /**
     * Đơn hàng có giá trị sau khi trừ voucher
     * từ 2.000.000đ sẽ được miễn phí vận chuyển.
     */
    public const FREE_SHIPPING_THRESHOLD = 2_000_000;


    public function __construct(
        private GhnShippingService $ghnShippingService
    ) {
    }


    /**
     * Tính phí vận chuyển cho một địa chỉ.
     *
     * Quy tắc:
     * - Từ 2.000.000đ: miễn phí vận chuyển.
     * - Dưới 2.000.000đ: lấy phí từ GHN Test.
     */
    public function calculateForAddress(
        Address $address,
        float $amountAfterDiscount
    ): array {
        /*
         * Địa chỉ phải có đầy đủ mã GHN.
         */
        if (
            !$address->ghn_province_id
            || !$address->ghn_district_id
            || !$address->ghn_ward_code
        ) {
            throw ValidationException::withMessages([
                'address_id' =>
                    'Địa chỉ chưa có đầy đủ mã GHN. '
                    . 'Vui lòng chỉnh sửa và chọn lại địa chỉ.',
            ]);
        }


        /*
         * Không cho giá trị đơn hàng nhỏ hơn 0.
         */
        $amountAfterDiscount = max(
            0,
            $amountAfterDiscount
        );


        /*
         * Tự động miễn phí vận chuyển
         * khi đơn hàng đạt ngưỡng.
         */
        if (
            $amountAfterDiscount
            >= self::FREE_SHIPPING_THRESHOLD
        ) {
            return [
                'fee' => 0,
                'ghn_fee' => null,
                'is_free' => true,
                'free_shipping_threshold' =>
                    self::FREE_SHIPPING_THRESHOLD,
                'details' => [],
            ];
        }


        /*
         * Đơn chưa đạt ngưỡng miễn phí:
         * gọi GHN Test để lấy phí vận chuyển.
         */
        $ghnResult =
            $this->ghnShippingService->calculateFee(
                toDistrictId:
                    (int) $address->ghn_district_id,

                toWardCode:
                    (string) $address->ghn_ward_code,

                /*
                 * Kiện hàng mặc định phù hợp mắt kính.
                 */
                weight: 500,
                length: 25,
                width: 15,
                height: 10,

                /*
                 * Giá trị khai giá.
                 * GhnShippingService sẽ tự giới hạn
                 * trong mức GHN cho phép.
                 */
                insuranceValue:
                    (int) round($amountAfterDiscount)
            );


        return [
            'fee' =>
                (float) $ghnResult['total'],

            'ghn_fee' =>
                (float) $ghnResult['total'],

            'is_free' => false,

            'free_shipping_threshold' =>
                self::FREE_SHIPPING_THRESHOLD,

            'details' =>
                $ghnResult['details'] ?? [],
        ];
    }
}