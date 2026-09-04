<?php

namespace App\Services;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class VoucherService
{
    /**
     * Tìm Voucher theo Code.
     *
     * Voucher private vẫn có thể được tìm thấy
     * nếu khách hàng biết chính xác mã.
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
        return round(
            min(
                $discount,
                $orderAmount
            ),
            2
        );
    }


    /**
     * Áp mã Voucher bằng Code.
     *
     * Áp được cả:
     * - Voucher public
     * - Voucher private nếu khách biết mã
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


    /**
     * Lấy danh sách Voucher công khai
     * dành cho Customer.
     *
     * Chỉ lấy Voucher:
     * - public
     * - active
     * - đã bắt đầu
     * - chưa hết hạn
     * - còn lượt sử dụng
     * - có cấu hình giảm giá hợp lệ
     *
     * Voucher chưa đạt minimum_order_amount
     * vẫn được hiển thị để Customer biết
     * cần mua thêm bao nhiêu tiền.
     */
    public function getPublicVoucherOptions(
        float $orderAmount
    ): array {
        $now = now();


        $vouchers = Voucher::query()
            ->active()
            ->public()

            /*
             * Đã đến thời gian sử dụng.
             */
            ->where(function ($query) use ($now) {
                $query
                    ->whereNull('starts_at')
                    ->orWhere(
                        'starts_at',
                        '<=',
                        $now
                    );
            })

            /*
             * Chưa hết hạn.
             */
            ->where(function ($query) use ($now) {
                $query
                    ->whereNull('ends_at')
                    ->orWhere(
                        'ends_at',
                        '>=',
                        $now
                    );
            })

            /*
             * Còn lượt sử dụng.
             */
            ->where(function ($query) {
                $query
                    ->whereNull('usage_limit')
                    ->orWhereColumn(
                        'usage_count',
                        '<',
                        'usage_limit'
                    );
            })

            ->get();


        $availableVouchers = [];
        $lockedVouchers = [];


        foreach ($vouchers as $voucher) {

            /*
             * Voucher có cấu hình bất thường
             * thì không hiển thị ra Customer.
             */
            if (
                !$this->hasValidDiscountConfiguration(
                    $voucher
                )
            ) {
                continue;
            }


            $minimumOrderAmount =
                (float) $voucher
                    ->minimum_order_amount;


            /*
             * Customer đủ điều kiện.
             */
            if (
                $orderAmount
                >= $minimumOrderAmount
            ) {
                $discountAmount =
                    $this->calculateDiscount(
                        $voucher,
                        $orderAmount
                    );


                $availableVouchers[] = [
                    'voucher' =>
                        $voucher,

                    'discount_amount' =>
                        $discountAmount,

                    'amount_missing' =>
                        0,
                ];

                continue;
            }


            /*
             * Voucher hợp lệ nhưng Cart
             * chưa đạt giá trị tối thiểu.
             */
            $lockedVouchers[] = [
                'voucher' =>
                    $voucher,

                'discount_amount' =>
                    0,

                'amount_missing' =>
                    round(
                        max(
                            0,
                            $minimumOrderAmount
                            - $orderAmount
                        ),
                        2
                    ),
            ];
        }


        /*
         * Voucher dùng được:
         * ưu tiên mã tiết kiệm nhiều tiền nhất.
         */
        usort(
            $availableVouchers,
            function (
                array $first,
                array $second
            ): int {
                return $second['discount_amount']
                    <=> $first['discount_amount'];
            }
        );


        /*
         * Voucher chưa đủ điều kiện:
         * ưu tiên mã gần đạt nhất.
         */
        usort(
            $lockedVouchers,
            function (
                array $first,
                array $second
            ): int {
                return $first['amount_missing']
                    <=> $second['amount_missing'];
            }
        );


        return [
            'available' =>
                $availableVouchers,

            'locked' =>
                $lockedVouchers,
        ];
    }


    /**
     * Kiểm tra cấu hình giảm giá
     * trước khi hiển thị Voucher
     * cho Customer.
     */
    private function hasValidDiscountConfiguration(
        Voucher $voucher
    ): bool {
        /*
         * Chỉ hỗ trợ 2 loại.
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
            return false;
        }


        /*
         * Giá trị giảm phải lớn hơn 0.
         */
        if (
            (float) $voucher->discount_value
            <= 0
        ) {
            return false;
        }


        /*
         * Percentage không được quá 100%.
         */
        if (
            $voucher->discount_type
                === 'percentage'
            && (float) $voucher->discount_value
                > 100
        ) {
            return false;
        }


        return true;
    }
}