<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    /**
     * Hiển thị Kho voucher của Customer.
     */
    public function index(
        Request $request
    ): View {
        $user = $request->user();

        /*
         * Voucher Customer đã sử dụng.
         * Không hiển thị lại trong Kho.
         */
        $usedVoucherIds = $user
            ->voucherUsages()
            ->pluck('voucher_id');

        /*
         * Voucher Customer đã lưu.
         */
        $savedVoucherIds = $user
            ->savedVouchers()
            ->pluck('vouchers.id');

        $now = now();

        /*
         * Kho voucher hiển thị:
         *
         * - Voucher đang dùng được
         * - Voucher chưa đến ngày sử dụng
         *
         * Không hiển thị:
         *
         * - Voucher private
         * - Voucher bị Admin tắt
         * - Voucher đã hết hạn
         * - Voucher đã hết lượt
         * - Voucher Customer đã sử dụng
         */
        $vouchers = Voucher::query()
            ->active()
            ->public()

            /*
             * Chưa hết hạn.
             *
             * Không lọc starts_at để Voucher
             * dùng sau vẫn được hiển thị.
             */
            ->where(
                'ends_at',
                '>=',
                $now
            )

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

            /*
             * Chưa được Customer sử dụng.
             */
            ->whereNotIn(
                'id',
                $usedVoucherIds
            )

            /*
             * Voucher dùng được xếp trước,
             * Voucher dùng sau xếp phía sau.
             */
            ->orderByRaw(
                'CASE
                    WHEN starts_at <= ?
                    THEN 0
                    ELSE 1
                END',
                [$now]
            )
            ->orderBy('starts_at')
            ->orderBy('ends_at')
            ->get()

            /*
             * Không hiển thị Voucher có
             * cấu hình giảm giá bất thường.
             */
            ->filter(
                function (Voucher $voucher): bool {
                    $discountValue =
                        (float) $voucher
                            ->discount_value;

                    if ($discountValue <= 0) {
                        return false;
                    }

                    if (
                        $voucher->discount_type
                        === 'fixed'
                    ) {
                        return true;
                    }

                    return $voucher->discount_type
                        === 'percentage'
                        && $discountValue <= 100;
                }
            )
            ->values();

        return view(
            'vouchers.index',
            compact(
                'vouchers',
                'savedVoucherIds'
            )
        );
    }

    /**
     * Customer lưu Voucher
     * vào Kho voucher.
     */
    public function claim(
        Request $request,
        Voucher $voucher
    ): RedirectResponse {
        $user = $request->user();

        /*
         * Voucher private hoặc bị Admin
         * tắt thì không được thu thập.
         */
        if (
            ! $voucher->is_public
            || ! $voucher->is_active
        ) {
            return back()->with(
                'error',
                'Voucher không còn khả dụng.'
            );
        }

        /*
         * Voucher hết hạn không được lưu.
         *
         * Voucher chưa đến starts_at vẫn
         * được phép lưu để dùng sau.
         */
        if (
            now()->greaterThan(
                $voucher->ends_at
            )
        ) {
            return back()->with(
                'error',
                'Voucher đã hết hạn.'
            );
        }

        /*
         * Voucher hết lượt không được lưu.
         */
        if (! $voucher->hasRemainingUsage()) {
            return back()->with(
                'error',
                'Voucher đã hết lượt sử dụng.'
            );
        }

        /*
         * Chặn Voucher có cấu hình giảm giá
         * không hợp lệ.
         */
        $discountValue =
            (float) $voucher->discount_value;

        $hasValidDiscount = in_array(
            $voucher->discount_type,
            [
                'percentage',
                'fixed',
            ],
            true
        )
            && $discountValue > 0
            && (
                $voucher->discount_type
                    !== 'percentage'
                || $discountValue <= 100
            );

        if (! $hasValidDiscount) {
            return back()->with(
                'error',
                'Voucher có cấu hình không hợp lệ.'
            );
        }

        /*
         * Mỗi Customer chỉ được sử dụng
         * Voucher một lần.
         */
        $hasUsed = $user
            ->voucherUsages()
            ->where(
                'voucher_id',
                $voucher->id
            )
            ->exists();

        if ($hasUsed) {
            return back()->with(
                'error',
                'Bạn đã sử dụng Voucher này.'
            );
        }

        /*
         * Không lưu trùng Voucher.
         */
        $alreadySaved = $user
            ->savedVouchers()
            ->where(
                'vouchers.id',
                $voucher->id
            )
            ->exists();

        if ($alreadySaved) {
            return back()->with(
                'success',
                'Voucher đã có trong Kho của bạn.'
            );
        }

        $user
            ->savedVouchers()
            ->attach($voucher->id);

        /*
         * Thông báo riêng cho Voucher
         * chưa đến thời gian sử dụng.
         */
        if (
            now()->lessThan(
                $voucher->starts_at
            )
        ) {
            return back()->with(
                'success',
                'Đã lưu Voucher '
                    .$voucher->code
                    .'. Có thể dùng từ '
                    .$voucher->starts_at
                        ->format('d/m/Y H:i')
                    .'.'
            );
        }

        return back()->with(
            'success',
            'Đã lưu Voucher '
                .$voucher->code
                .' vào Kho.'
        );
    }
        /**
     * Chọn Voucher đã lưu và
     * chuyển Customer tới giỏ hàng.
     */
    public function useNow(
        Request $request,
        Voucher $voucher
    ): RedirectResponse {
        $user = $request->user();

        /*
         * Chỉ được sử dụng Voucher
         * đã lưu trong Kho của Customer.
         */
        $isSaved = $user
            ->savedVouchers()
            ->where(
                'vouchers.id',
                $voucher->id
            )
            ->exists();

        if (! $isSaved) {
            return back()->with(
                'error',
                'Vui lòng lưu Voucher trước khi sử dụng.'
            );
        }

        /*
         * Voucher phải là Voucher công khai
         * và đang trong thời gian sử dụng.
         *
         * Voucher dùng sau chưa được chọn.
         */
        if (
            ! $voucher->is_public
            || ! $voucher->isCurrentlyValid()
        ) {
            return back()->with(
                'error',
                'Voucher chưa đến thời gian sử dụng hoặc không còn khả dụng.'
            );
        }

        /*
         * Mỗi Customer chỉ được sử dụng
         * Voucher một lần.
         */
        $hasUsed = $user
            ->voucherUsages()
            ->where(
                'voucher_id',
                $voucher->id
            )
            ->exists();

        if ($hasUsed) {
            return back()->with(
                'error',
                'Bạn đã sử dụng Voucher này.'
            );
        }

        /*
         * Giỏ hàng hiện tại đã sử dụng
         * session key này để tính giảm giá.
         */
        session()->put(
            'cart_voucher_code',
            $voucher->code
        );

        return redirect()
            ->to(
                route('cart.index')
                .'#cart-voucher'
            )
            ->with(
                'voucher_success',
                'Đã chọn Voucher '
                    .$voucher->code
                    .'. Vui lòng kiểm tra điều kiện áp dụng.'
            );
    }
}