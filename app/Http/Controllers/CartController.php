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
         * =====================================================
         * 1. TẠM TÍNH GIỎ HÀNG
         * =====================================================
         */
        $subtotal = (float) $cart->total_amount;

        $discountAmount = 0;

        $finalAmount = $subtotal;

        $appliedVoucher = null;

        $voucherError = null;


        /*
         * =====================================================
         * 2. KIỂM TRA VOUCHER ĐANG ĐƯỢC ÁP
         * =====================================================
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
                 * Voucher không còn hợp lệ
                 * với Cart hiện tại
                 * thì tự động loại khỏi Session.
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


        /*
         * =====================================================
         * 3. DANH SÁCH VOUCHER CÔNG KHAI
         * =====================================================
         */
        $availableVouchers = [];

        $lockedVouchers = [];


        if (!$cart->items->isEmpty()) {

            $voucherOptions =
                $voucherService
                    ->getPublicVoucherOptions(
                        $subtotal
                    );


            $availableVouchers =
                $voucherOptions['available'];


            $lockedVouchers =
                $voucherOptions['locked'];
        }


        /*
         * =====================================================
         * 4. HIỂN THỊ CART
         * =====================================================
         */
        return view(
            'cart.index',
            compact(
                'cart',
                'subtotal',
                'discountAmount',
                'finalAmount',
                'appliedVoucher',
                'voucherError',
                'availableVouchers',
                'lockedVouchers'
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
         * Lưu lại Voucher cũ
         * để biết đây là áp mới
         * hay đổi Voucher.
         */
        $previousVoucherCode =
            session('cart_voucher_code');


        /*
         * Kiểm tra và áp Voucher.
         */
        $result = $voucherService->apply(
            $request->voucher_code,
            $subtotal
        );


        $newVoucherCode =
            $result['voucher']->code;


        /*
         * Chỉ lưu Code vào Session.
         */
        session([
            'cart_voucher_code' =>
                $newVoucherCode,
        ]);


        /*
         * Nội dung thông báo riêng
         * dành cho thao tác Voucher.
         */
        if (
            $previousVoucherCode
            && $previousVoucherCode
                !== $newVoucherCode
        ) {

            $message =
                'Đã đổi voucher sang mã '
                . $newVoucherCode
                . '.';

        } elseif (
            $previousVoucherCode
            === $newVoucherCode
        ) {

            $message =
                'Voucher '
                . $newVoucherCode
                . ' đang được áp dụng.';

        } else {

            $message =
                'Áp dụng voucher '
                . $newVoucherCode
                . ' thành công.';
        }


        /*
         * Không dùng session "success"
         * để tránh thông báo xuất hiện đầu trang.
         *
         * Đồng thời thêm #cart-voucher
         * để trình duyệt tự quay lại
         * khu vực Voucher sau khi reload.
         */
        return redirect()
            ->to(
                route('cart.index')
                . '#cart-voucher'
            )
            ->with(
                'voucher_success',
                $message
            );
    }


    /**
     * Hủy Voucher khỏi Cart.
     */
    public function removeVoucher()
    {
        $voucherCode =
            session('cart_voucher_code');


        session()->forget(
            'cart_voucher_code'
        );


        $message = $voucherCode
            ? 'Đã bỏ voucher '
                . $voucherCode
                . '.'
            : 'Đã bỏ voucher.';


        /*
         * Sau khi bỏ Voucher,
         * quay lại đúng khu vực Voucher.
         */
        return redirect()
            ->to(
                route('cart.index')
                . '#cart-voucher'
            )
            ->with(
                'voucher_success',
                $message
            );
    }
}