<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\ApplyVoucherRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\VoucherService;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    /**
     * Hiển thị giỏ hàng.
     */
    public function index(
        CartService $cartService,
        VoucherService $voucherService
    ) {
        $cart = $cartService
            ->getOrCreateCart(
                auth()->user()
            );

        $cart->load([
            'items.variant.product.primaryImage',
        ]);

        /*
         * Tạm tính trước Voucher.
         */
        $subtotal = (float) $cart->total_amount;

        $discountAmount = 0;

        $finalAmount = $subtotal;

        $appliedVoucher = null;

        $voucherError = null;


        /*
         * Nếu Customer đã áp Voucher,
         * tính lại Voucher dựa trên Cart hiện tại.
         */
        $voucherCode = session(
            'cart_voucher_code'
        );

        if (
            $voucherCode
            && !$cart->items->isEmpty()
        ) {
            try {

                $result = $voucherService->apply(
                    $voucherCode,
                    $subtotal
                );

                $appliedVoucher =
                    $result['voucher'];

                $discountAmount =
                    $result['discount_amount'];

                $finalAmount =
                    $result['final_amount'];

            } catch (
                ValidationException $exception
            ) {

                /*
                 * Ví dụ:
                 *
                 * Customer áp Voucher khi Cart 600k,
                 * sau đó xóa Product khiến Cart còn 300k.
                 *
                 * Voucher yêu cầu tối thiểu 500k
                 * → Voucher phải tự bị loại.
                 */
                session()->forget(
                    'cart_voucher_code'
                );

                $errors = $exception
                    ->errors();

                $voucherError = collect(
                    $errors
                )
                    ->flatten()
                    ->first();
            }
        }


        return view(
            'cart.index',
            compact(
                'cart',
                'subtotal',
                'discountAmount',
                'finalAmount',
                'appliedVoucher',
                'voucherError'
            )
        );
    }


    /**
     * Thêm Variant vào giỏ hàng.
     */
    public function store(
        AddToCartRequest $request,
        CartService $cartService
    ) {
        $variant = ProductVariant::findOrFail(
            $request->variant_id
        );

        $cartService->add(
            auth()->user(),
            $variant,
            (int) $request->quantity
        );

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Đã thêm sản phẩm vào giỏ hàng.'
            );
    }


    /**
     * Cập nhật số lượng.
     */
    public function update(
        UpdateCartItemRequest $request,
        ProductVariant $variant,
        CartService $cartService
    ) {
        $cartService->updateQuantity(
            auth()->user(),
            $variant,
            (int) $request->quantity
        );

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Đã cập nhật số lượng sản phẩm.'
            );
    }


    /**
     * Xóa một Variant khỏi Cart.
     */
    public function destroy(
        ProductVariant $variant,
        CartService $cartService
    ) {
        $cartService->remove(
            auth()->user(),
            $variant
        );

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Đã xóa sản phẩm khỏi giỏ hàng.'
            );
    }


    /**
     * Xóa toàn bộ Cart.
     */
    public function clear(
        CartService $cartService
    ) {
        $cartService->clear(
            auth()->user()
        );

        /*
         * Cart trống thì Voucher
         * cũng phải được xóa.
         */
        session()->forget(
            'cart_voucher_code'
        );

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Đã xóa toàn bộ giỏ hàng.'
            );
    }


    /**
     * Áp mã giảm giá.
     */
    public function applyVoucher(
        ApplyVoucherRequest $request,
        CartService $cartService,
        VoucherService $voucherService
    ) {
        $cart = $cartService
            ->getOrCreateCart(
                auth()->user()
            );

        $cart->load([
            'items.variant.product',
        ]);


        /*
         * Không được áp Voucher
         * nếu Cart đang trống.
         */
        if ($cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Giỏ hàng đang trống, không thể áp mã giảm giá.'
                );
        }


        $subtotal =
            (float) $cart->total_amount;


        /*
         * VoucherService sẽ kiểm tra:
         *
         * - tồn tại
         * - active
         * - thời gian
         * - usage_limit
         * - minimum_order_amount
         * - discount_type
         */
        $result = $voucherService->apply(
            $request->voucher_code,
            $subtotal
        );


        /*
         * Chỉ lưu Code vào Session.
         *
         * Không lưu discount_amount cố định,
         * vì Cart có thể thay đổi sau đó.
         */
        session([
            'cart_voucher_code' =>
                $result['voucher']->code,
        ]);


        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Áp dụng mã giảm giá thành công.'
            );
    }


    /**
     * Hủy Voucher khỏi Cart.
     */
    public function removeVoucher()
    {
        session()->forget(
            'cart_voucher_code'
        );

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Đã bỏ mã giảm giá.'
            );
    }
}