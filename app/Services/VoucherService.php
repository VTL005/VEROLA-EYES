<?php

namespace App\Services;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    /**
     * Tìm Voucher theo Code.
     */
    public function findByCode(
        string $code
    ): Voucher {
        $voucher = Voucher::query()
            ->where(
                'code',
                strtoupper(trim($code))
            )
            ->first();

        if (!$voucher) {
            throw ValidationException::withMessages([
                'voucher_code' =>
                    'Mã giảm giá không tồn tại.',
            ]);
        }

        return $voucher;
    }


    /**
     * Kiểm tra Voucher có hợp lệ
     * với giá trị đơn hàng hiện tại không.
     */
    public function validateVoucher(
        Voucher $voucher,
        float $orderAmount
    ): void {
        /*
         * Voucher phải Active.
         */
        if (!$voucher->is_active) {
            throw ValidationException::withMessages([
                'voucher_code' =>
                    'Mã giảm giá hiện không hoạt động.',
            ]);
        }


        /*
         * Kiểm tra thời gian bắt đầu.
         */
        if ($voucher->starts_at) {

            $startsAt = Carbon::parse(
                $voucher->starts_at
            );

            if (now()->lt($startsAt)) {
                throw ValidationException::withMessages([
                    'voucher_code' =>
                        'Mã giảm giá chưa đến thời gian sử dụng.',
                ]);
            }
        }


        /*
         * Kiểm tra thời gian kết thúc.
         */
        if ($voucher->ends_at) {

            $endsAt = Carbon::parse(
                $voucher->ends_at
            );

            if (now()->gt($endsAt)) {
                throw ValidationException::withMessages([
                    'voucher_code' =>
                        'Mã giảm giá đã hết hạn.',
                ]);
            }
        }


        /*
         * Kiểm tra giới hạn lượt sử dụng.
         */
        if (
            $voucher->usage_limit !== null
            && $voucher->usage_count
                >= $voucher->usage_limit
        ) {
            throw ValidationException::withMessages([
                'voucher_code' =>
                    'Mã giảm giá đã hết lượt sử dụng.',
            ]);
        }


        /*
         * Kiểm tra giá trị đơn tối thiểu.
         */
        if (
            $orderAmount
            < (float) $voucher
                ->minimum_order_amount
        ) {
            throw ValidationException::withMessages([
                'voucher_code' =>
                    'Đơn hàng chưa đạt giá trị tối thiểu '
                    . number_format(
                        (float) $voucher
                            ->minimum_order_amount,
                        0,
                        ',',
                        '.'
                    )
                    . 'đ để sử dụng mã này.',
            ]);
        }


        /*
         * Kiểm tra loại Voucher.
         */
        if (
            !in_array(
                $voucher->discount_type,
                [
                    'percentage',
                    'fixed',
                ],
                true
            )
        ) {
            throw ValidationException::withMessages([
                'voucher_code' =>
                    'Loại mã giảm giá không hợp lệ.',
            ]);
        }


        /*
         * Giá trị giảm phải > 0.
         */
        if (
            (float) $voucher->discount_value
            <= 0
        ) {
            throw ValidationException::withMessages([
                'voucher_code' =>
                    'Giá trị mã giảm giá không hợp lệ.',
            ]);
        }


        /*
         * Percentage hợp lệ trong khoảng
         * lớn hơn 0 và không vượt 100%.
         */
        if (
            $voucher->discount_type
                === 'percentage'
            && (float) $voucher->discount_value
                > 100
        ) {
            throw ValidationException::withMessages([
                'voucher_code' =>
                    'Phần trăm giảm giá không hợp lệ.',
            ]);
        }
    }


    /**
     * Tính số tiền được giảm.
     */
    public function calculateDiscount(
        Voucher $voucher,
        float $orderAmount
    ): float {
        $this->validateVoucher(
            $voucher,
            $orderAmount
        );


        /*
         * Giảm theo phần trăm.
         */
        if (
            $voucher->discount_type
            === 'percentage'
        ) {
            $discount =
                $orderAmount
                * (float) $voucher
                    ->discount_value
                / 100;
        } else {

            /*
             * Giảm số tiền cố định.
             */
            $discount =
                (float) $voucher
                    ->discount_value;
        }


        /*
         * Không cho số tiền giảm
         * lớn hơn giá trị đơn hàng.
         *
         * Total không được âm.
         */
        return min(
            $discount,
            $orderAmount
        );
    }


    /**
     * Áp mã Voucher bằng Code.
     */
    public function apply(
        string $code,
        float $orderAmount
    ): array {
        $voucher = $this->findByCode(
            $code
        );

        $discountAmount =
            $this->calculateDiscount(
                $voucher,
                $orderAmount
            );


        $finalAmount = max(
            0,
            $orderAmount
            - $discountAmount
        );


        return [
            'voucher' =>
                $voucher,

            'discount_amount' =>
                $discountAmount,

            'final_amount' =>
                $finalAmount,
        ];
    }
}