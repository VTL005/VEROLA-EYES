<?php

namespace App\Http\Controllers;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\ApplyVoucherRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
            ->getOrCreateCart(auth()->user());

        $cart->load([
            'items.variant.product.primaryImage',
        ]);

        /*
         * Chỉ tính các CartItem đang được chọn.
         * Nếu chưa có Session, mặc định chọn tất cả
         * sản phẩm có thể thanh toán.
         */
        $selectedItems = $this->selectedItems($cart);

        $selectedCartItemIds = $selectedItems
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        $subtotal = (float) $selectedItems
            ->sum(fn ($item) => (float) $item->subtotal);

        $discountAmount = 0;
        $finalAmount = $subtotal;
        $appliedVoucher = null;
        $voucherError = null;

        $voucherCode = session('cart_voucher_code');

        if ($voucherCode && $selectedItems->isNotEmpty()) {
            try {
                $result = $voucherService->apply(
                    $voucherCode,
                    $subtotal
                );

                $appliedVoucher = $result['voucher'];
                $discountAmount = $result['discount_amount'];
                $finalAmount = $result['final_amount'];
            } catch (ValidationException $exception) {
                session()->forget('cart_voucher_code');

                $voucherError = collect($exception->errors())
                    ->flatten()
                    ->first();
            }
        } elseif ($voucherCode) {
            session()->forget('cart_voucher_code');

            $voucherError =
                'Voucher đã được bỏ vì bạn chưa chọn sản phẩm.';
        }

        $availableVouchers = [];
        $lockedVouchers = [];

        if ($selectedItems->isNotEmpty()) {
            $voucherOptions = $voucherService
                ->getPublicVoucherOptions($subtotal);

            $availableVouchers = $voucherOptions['available'];
            $lockedVouchers = $voucherOptions['locked'];
        }

        return view(
            'cart.index',
            compact(
                'cart',
                'selectedCartItemIds',
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
     * Thêm Variant vào giỏ hàng
     * hoặc chuyển thẳng tới Checkout.
     */
    public function store(
        AddToCartRequest $request,
        CartService $cartService
    ) {
        $variant = ProductVariant::findOrFail(
            $request->integer('variant_id')
        );

        /*
         * Khách bấm "Mua ngay".
         */
        if (
            $request->input('checkout_action')
            === 'buy_now'
        ) {
            /*
             * Không thêm vào Cart.
             * Dữ liệu Mua ngay được lưu riêng trong Session.
             */
            $variant->loadMissing('product');

            $quantity = $request->integer('quantity');

            if (
                ! $variant->product
                || ! $variant->product->is_active
                || ! $variant->is_active
            ) {
                throw ValidationException::withMessages([
                    'variant_id' => 'Sản phẩm hoặc phiên bản hiện không khả dụng.',
                ]);
            }

            if (
                $quantity < 1
                || $quantity > $variant->stock_quantity
            ) {
                throw ValidationException::withMessages([
                    'quantity' => 'Số lượng yêu cầu không hợp lệ. Chỉ còn '
                        .$variant->stock_quantity
                        .' sản phẩm.',
                ]);
            }

            session()->put(
                'checkout_buy_now',
                [
                    'variant_id' => $variant->id,
                    'quantity' => $quantity,
                ]
            );

            session()->forget([
                'checkout_cart_item_ids',
                'checkout_amount_after_discount',
            ]);

            return redirect()
                ->route('checkout.index');
        }

        $cartService->add(
            auth()->user(),
            $variant,
            $request->integer('quantity')
        );

        /*
         * Khi thêm sản phẩm mới, cho phép trang giỏ hàng
         * khởi tạo lại danh sách sản phẩm có thể chọn.
         */
        session()->forget([
            'cart_selected_item_ids',
            'checkout_buy_now',
        ]);

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
        $cartService->clear(auth()->user());

        session()->forget([
            'cart_voucher_code',
            'cart_selected_item_ids',
            'checkout_cart_item_ids',
            'checkout_buy_now',
            'checkout_amount_after_discount',
        ]);

        return redirect()
            ->route('cart.index')
            ->with(
                'success',
                'Đã xóa toàn bộ giỏ hàng.'
            );
    }

    /**
     * Lưu các sản phẩm Customer đang chọn.
     */
    public function updateSelection(
        Request $request,
        CartService $cartService,
        VoucherService $voucherService
    ): JsonResponse {
        $validated = $request->validate([
            'selected_items' => [
                'present',
                'array',
            ],
            'selected_items.*' => [
                'integer',
                'distinct',
                'min:1',
            ],
        ]);

        $cart = $cartService
            ->getOrCreateCart(auth()->user());

        $cart->load([
            'items.variant.product',
        ]);

        $eligibleItems = $this->eligibleItems($cart);

        $eligibleIds = $eligibleItems
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        $selectedIds = collect($validated['selected_items'])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        /*
         * Không cho browser gửi CartItem không thuộc Cart
         * hoặc không đủ điều kiện thanh toán.
         */
        if ($selectedIds->diff($eligibleIds)->isNotEmpty()) {
            throw ValidationException::withMessages([
                'selected_items' => 'Danh sách sản phẩm được chọn không hợp lệ.',
            ]);
        }

        session()->put(
            'cart_selected_item_ids',
            $selectedIds->all()
        );

        $selectedItems = $eligibleItems
            ->whereIn('id', $selectedIds->all())
            ->values();

        $subtotal = (float) $selectedItems
            ->sum(fn ($item) => (float) $item->subtotal);

        $discountAmount = 0;
        $finalAmount = $subtotal;
        $voucherRemoved = false;
        $message = null;

        $voucherCode = session('cart_voucher_code');

        if ($voucherCode && $selectedItems->isNotEmpty()) {
            try {
                $result = $voucherService->apply(
                    $voucherCode,
                    $subtotal
                );

                $discountAmount =
                    (float) $result['discount_amount'];

                $finalAmount =
                    (float) $result['final_amount'];
            } catch (ValidationException $exception) {
                session()->forget('cart_voucher_code');

                $voucherRemoved = true;
                $message = collect($exception->errors())
                    ->flatten()
                    ->first();
            }
        } elseif ($voucherCode) {
            session()->forget('cart_voucher_code');

            $voucherRemoved = true;
            $message =
                'Voucher đã được bỏ vì bạn chưa chọn sản phẩm.';
        }

        return response()->json([
            'data' => [
                'selected_ids' => $selectedIds->all(),
                'selected_count' => $selectedIds->count(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'final_amount' => $finalAmount,
                'voucher_removed' => $voucherRemoved,
                'message' => $message,
            ],
        ]);
    }

    /**
     * Áp mã giảm giá cho các sản phẩm đang chọn.
     */
    public function applyVoucher(
        ApplyVoucherRequest $request,
        CartService $cartService,
        VoucherService $voucherService
    ) {
        $cart = $cartService
            ->getOrCreateCart(auth()->user());

        $cart->load([
            'items.variant.product',
        ]);

        if ($cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Giỏ hàng đang trống, không thể áp mã giảm giá.'
                );
        }

        $selectedItems = $this->selectedItems($cart);

        if ($selectedItems->isEmpty()) {
            return redirect()
                ->to(route('cart.index').'#cart-voucher')
                ->with(
                    'error',
                    'Vui lòng chọn ít nhất một sản phẩm trước khi áp voucher.'
                );
        }

        $subtotal = (float) $selectedItems
            ->sum(fn ($item) => (float) $item->subtotal);

        $previousVoucherCode =
            session('cart_voucher_code');

        $result = $voucherService->apply(
            $request->voucher_code,
            $subtotal
        );

        $newVoucherCode = $result['voucher']->code;

        session([
            'cart_voucher_code' => $newVoucherCode,
        ]);

        if (
            $previousVoucherCode
            && $previousVoucherCode !== $newVoucherCode
        ) {
            $message =
                'Đã đổi voucher sang mã '
                .$newVoucherCode
                .'.';
        } elseif ($previousVoucherCode === $newVoucherCode) {
            $message =
                'Voucher '
                .$newVoucherCode
                .' đang được áp dụng.';
        } else {
            $message =
                'Áp dụng voucher '
                .$newVoucherCode
                .' thành công.';
        }

        return redirect()
            ->to(route('cart.index').'#cart-voucher')
            ->with('voucher_success', $message);
    }

    /**
     * Hủy Voucher khỏi Cart.
     */
    public function removeVoucher()
    {
        $voucherCode = session('cart_voucher_code');

        session()->forget('cart_voucher_code');

        $message = $voucherCode
            ? 'Đã bỏ voucher '.$voucherCode.'.'
            : 'Đã bỏ voucher.';

        return redirect()
            ->to(route('cart.index').'#cart-voucher')
            ->with('voucher_success', $message);
    }

    /**
     * Các CartItem có thể thanh toán.
     */
    private function eligibleItems(Cart $cart): Collection
    {
        return $cart->items
            ->filter(function ($item) {
                $variant = $item->variant;
                $product = $variant?->product;

                return $variant
                    && $product
                    && $product->is_active
                    && $variant->is_active
                    && (int) $variant->stock_quantity
                        >= (int) $item->quantity;
            })
            ->values();
    }

    /**
     * Lấy lựa chọn hiện tại từ Session.
     */
    private function selectedItems(Cart $cart): Collection
    {
        $eligibleItems = $this->eligibleItems($cart);

        $eligibleIds = $eligibleItems
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values();

        if (session()->has('cart_selected_item_ids')) {
            $selectedIds = collect(
                session('cart_selected_item_ids', [])
            )
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique()
                ->intersect($eligibleIds)
                ->values();
        } else {
            $selectedIds = $eligibleIds;
        }

        session()->put(
            'cart_selected_item_ids',
            $selectedIds->all()
        );

        return $eligibleItems
            ->whereIn('id', $selectedIds->all())
            ->values();
    }
}
