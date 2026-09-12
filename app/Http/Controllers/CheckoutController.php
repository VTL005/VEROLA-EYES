<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\PrepareCheckoutRequest;
use App\Mail\OrderConfirmationMail;
use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\ShippingFeeService;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Throwable;

class CheckoutController extends Controller
{
    /**
     * Nhận danh sách CartItem Customer muốn thanh toán.
     */
    public function prepare(
        PrepareCheckoutRequest $request,
        CartService $cartService
    ) {
        $user = auth()->user();

        $cart = $cartService
            ->getOrCreateCart($user);

        $validated = $request->validated();

        /*
         * Chuẩn hóa ID.
         */
        $selectedIds = collect(
            $validated['selected_items']
        )
            ->map(
                fn ($id) => (int) $id
            )
            ->filter(
                fn ($id) => $id > 0
            )
            ->unique()
            ->values();

        /*
         * Chỉ lấy CartItem thuộc đúng Cart
         * của Customer hiện tại.
         */
        $ownedItems = $cart
            ->items()
            ->whereIn(
                'id',
                $selectedIds->all()
            )
            ->get();

        /*
         * Nếu Customer sửa HTML và gửi
         * CartItem của người khác -> chặn.
         */
        if (
            $ownedItems->count()
            !== $selectedIds->count()
        ) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Danh sách sản phẩm thanh toán không hợp lệ.'
                );
        }

        /*
         * Lưu lựa chọn vào Session.
         *
         * Không tin selected_items từ browser
         * ở bước tạo Order; CheckoutService
         * vẫn sẽ kiểm tra lại.
         */
        session()->put(
            'checkout_cart_item_ids',
            $selectedIds->all()
        );

        /*
         * Customer đã chọn thanh toán từ giỏ hàng,
         * vì vậy hủy dữ liệu Mua ngay cũ nếu có.
         */
        session()->forget('checkout_buy_now');

        return redirect()
            ->route('checkout.index');
    }

    /**
     * Hiển thị trang Checkout.
     */
    public function index(
        CartService $cartService,
        VoucherService $voucherService,
        ShippingFeeService $shippingFeeService
    ) {
        $user = auth()->user();

        /*
         * Lấy Cart hiện tại.
         */
        $cart = $cartService
            ->getOrCreateCart($user);

        $cart->load([
            'items.variant.product.primaryImage',
        ]);

        /*
|--------------------------------------------------------------------------
| SELECTED CART ITEMS
|--------------------------------------------------------------------------
*/

        $buyNow = session('checkout_buy_now');

        $isBuyNow = is_array($buyNow)
            && (int) ($buyNow['variant_id'] ?? 0) > 0
            && (int) ($buyNow['quantity'] ?? 0) > 0;

        if ($isBuyNow) {
            /*
             * Mua ngay không sử dụng CartItem trong database.
             * Tạo CartItem trong bộ nhớ để tái sử dụng checkout view.
             */
            $variant = ProductVariant::query()
                ->with('product.primaryImage')
                ->find((int) $buyNow['variant_id']);

            $quantity = (int) $buyNow['quantity'];

            if (
                ! $variant
                || ! $variant->product
                || ! $variant->product->is_active
                || ! $variant->is_active
                || $quantity > $variant->stock_quantity
            ) {
                session()->forget('checkout_buy_now');

                return redirect()
                    ->route('products.index')
                    ->with(
                        'error',
                        'Sản phẩm Mua ngay không còn khả dụng hoặc không đủ tồn kho.'
                    );
            }

            $buyNowItem = new CartItem([
                'variant_id' => $variant->id,
                'quantity' => $quantity,
            ]);

            $buyNowItem->setRelation(
                'variant',
                $variant
            );

            $selectedItems = collect([
                $buyNowItem,
            ]);
        } else {
            $selectedIds = collect(
                session(
                    'checkout_cart_item_ids',
                    []
                )
            )
                ->map(
                    fn ($id) => (int) $id
                )
                ->filter(
                    fn ($id) => $id > 0
                )
                ->unique()
                ->values();

            if ($selectedIds->isEmpty()) {
                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Vui lòng chọn sản phẩm cần thanh toán.'
                    );
            }

            $selectedItems = $cart->items
                ->whereIn(
                    'id',
                    $selectedIds->all()
                )
                ->values();

            /*
             * Selection không còn hợp lệ.
             * Ví dụ CartItem đã bị xóa ở tab khác.
             */
            if (
                $selectedItems->count()
                !== $selectedIds->count()
            ) {
                session()->forget(
                    'checkout_cart_item_ids'
                );

                return redirect()
                    ->route('cart.index')
                    ->with(
                        'error',
                        'Giỏ hàng đã thay đổi. Vui lòng chọn lại sản phẩm.'
                    );
            }
        }

        /*
         * Từ đây checkout view chỉ nhìn thấy
         * các sản phẩm được chọn.
         */
        $cart->setRelation(
            'items',
            $selectedItems
        );

        /*
         * Không cho Checkout nếu Cart trống.
         */
        if ($cart->items->isEmpty()) {

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Giỏ hàng đang trống, không thể thanh toán.'
                );
        }

        /*
         * Lấy địa chỉ của Customer hiện tại.
         */
        $addresses = Address::query()
            ->where(
                'user_id',
                $user->id
            )
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        /*
         * Chưa có địa chỉ thì bắt tạo trước.
         */
        if ($addresses->isEmpty()) {

            return redirect()
                ->route('addresses.create')
                ->with(
                    'error',
                    'Vui lòng thêm địa chỉ nhận hàng trước khi thanh toán.'
                );
        }

        /*
 * Chỉ tính tiền các sản phẩm được chọn.
 */
        $subtotal = (float) $selectedItems
            ->sum(
                fn ($item) => (float) $item->subtotal
            );

        $discountAmount = 0;

        $finalAmount = $subtotal;

        $appliedVoucher = null;

        /*
         * Lấy Voucher đang áp trong Session.
         */
        $voucherCode = session(
            'cart_voucher_code'
        );

        if ($voucherCode) {

            try {

                $result =
                    $voucherService->apply(
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
                 * thì xóa khỏi Session.
                 */
                session()->forget(
                    'cart_voucher_code'
                );

                if ($isBuyNow) {
                    $appliedVoucher = null;
                    $discountAmount = 0;
                    $finalAmount = $subtotal;
                } else {
                    return redirect()
                        ->route('cart.index')
                        ->withErrors(
                            $exception->errors()
                        );
                }
            }
        }

        /*
         * Xác định địa chỉ đang được chọn.
         */
        $selectedAddressId = (int) old(
            'address_id',
            $addresses
                ->firstWhere('is_default', true)
                ?->id
                ?? $addresses->first()?->id
        );

        $selectedAddress =
            $addresses->firstWhere(
                'id',
                $selectedAddressId
            )
            ?? $addresses->first();

        /*
         * Lưu số tiền sau voucher vào Session.
         *
         * Endpoint cập nhật phí vận chuyển
         * sẽ sử dụng giá trị này.
         */
        session([
            'checkout_amount_after_discount' => $finalAmount,
        ]);

        $shippingFee = 0;

        $shippingError = null;

        $isFreeShipping = false;

        /*
         * Tính phí cho địa chỉ đang chọn.
         */
        try {
            $shippingResult =
                $shippingFeeService
                    ->calculateForAddress(
                        $selectedAddress,
                        $finalAmount
                    );

            $shippingFee =
                (float) $shippingResult['fee'];

            $isFreeShipping =
                (bool) $shippingResult['is_free'];
        } catch (ValidationException $exception) {
            $shippingError = collect(
                $exception->errors()
            )
                ->flatten()
                ->first();
        } catch (\RuntimeException $exception) {
            $shippingError =
                'Không thể lấy phí vận chuyển từ GHN. '
                .$exception->getMessage();
        }

        $total =
            $finalAmount
            + $shippingFee;

        return view(
            'checkout.index',
            compact(
                'cart',
                'addresses',
                'subtotal',
                'discountAmount',
                'shippingFee',
                'shippingError',
                'isFreeShipping',
                'selectedAddressId',
                'total',
                'appliedVoucher',
                'isBuyNow'
            )
        );
    }

    /**
     * Tính lại phí vận chuyển khi Customer đổi địa chỉ.
     */
    public function shippingFee(
        Request $request,
        ShippingFeeService $shippingFeeService
    ): JsonResponse {
        $validated = $request->validate([
            'address_id' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        /*
         * Chỉ cho phép dùng địa chỉ
         * thuộc Customer đang đăng nhập.
         */
        $address = Address::query()
            ->where(
                'id',
                $validated['address_id']
            )
            ->where(
                'user_id',
                auth()->id()
            )
            ->first();

        if (! $address) {
            throw ValidationException::withMessages([
                'address_id' => 'Địa chỉ nhận hàng không hợp lệ.',
            ]);
        }

        /*
         * Giá trị này đã được index()
         * tính từ sản phẩm được chọn và voucher.
         */
        if (
            ! session()->has(
                'checkout_amount_after_discount'
            )
        ) {
            return response()->json([
                'message' => 'Phiên thanh toán đã hết hạn. '
                    .'Vui lòng quay lại giỏ hàng.',
            ], 409);
        }

        $amountAfterDiscount = (float) session(
            'checkout_amount_after_discount'
        );

        try {
            $shippingResult = $shippingFeeService
                ->calculateForAddress(
                    $address,
                    $amountAfterDiscount
                );
        } catch (\RuntimeException $exception) {
            return response()->json([
                'message' => 'Không thể lấy phí vận chuyển từ GHN. '
                    .$exception->getMessage(),
            ], 502);
        }

        $shippingFee =
            (float) $shippingResult['fee'];

        return response()->json([
            'data' => [
                'address_id' => (int) $address->id,

                'shipping_fee' => $shippingFee,

                'is_free' => (bool) $shippingResult['is_free'],

                'amount_after_discount' => $amountAfterDiscount,

                'total' => $amountAfterDiscount
                    + $shippingFee,
            ],
        ]);
    }

    /**
     * Customer đặt hàng.
     */
    public function store(
        CheckoutRequest $request,
        CheckoutService $checkoutService
    ) {
        $user = auth()->user();

        $buyNow = session('checkout_buy_now');

        $isBuyNow = is_array($buyNow)
            && (int) ($buyNow['variant_id'] ?? 0) > 0
            && (int) ($buyNow['quantity'] ?? 0) > 0;

        $selectedCartItemIds = collect(
            session(
                'checkout_cart_item_ids',
                []
            )
        )
            ->map(
                fn ($id) => (int) $id
            )
            ->filter(
                fn ($id) => $id > 0
            )
            ->unique()
            ->values()
            ->all();

        if (
            ! $isBuyNow
            && empty($selectedCartItemIds)
        ) {

            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Vui lòng chọn sản phẩm cần thanh toán.'
                );
        }

        /*
         * Dữ liệu đã được validate
         * trong CheckoutRequest.
         */
        $validated =
            $request->validated();

        /*
         * Kiểm tra Address phải thuộc
         * Customer đang đăng nhập.
         */
        $address = Address::query()
            ->where(
                'id',
                $validated['address_id']
            )
            ->where(
                'user_id',
                $user->id
            )
            ->first();

        if (! $address) {

            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Địa chỉ nhận hàng không hợp lệ.'
                );
        }

        /*
         * Voucher hiện tại đang lưu
         * trong Session.
         */
        $voucherCode = session(
            'cart_voucher_code'
        );

        /*
         * CheckoutService chịu trách nhiệm:
         *
         * - Lock Cart
         * - Lock Variant
         * - Kiểm tra Stock
         * - Kiểm tra Voucher
         * - Tạo Order
         * - Tạo OrderDetail
         * - Trừ Stock
         * - Tạo Payment
         * - Ghi OrderStatusHistory
         * - Xóa CartItem
         */
        $order = $checkoutService
            ->placeOrder(
                $user,
                $address,
                $validated['payment_method'],
                $validated['note'] ?? null,
                $voucherCode,
                $selectedCartItemIds,
                $isBuyNow ? $buyNow : null
            );

        /*
        |--------------------------------------------------------------------------
        | GỬI EMAIL XÁC NHẬN ĐƠN HÀNG
        |--------------------------------------------------------------------------
        |
        | Đoạn này PHẢI nằm trong method store().
        |
        | CheckoutService đã tạo Order thành công
        | và transaction đã hoàn tất.
        |
        */

        try {

            /*
             * Đảm bảo dữ liệu cần
             * cho Email đã được load.
             */
            $order->loadMissing([
                'details',
                'payment',
            ]);

            /*
             * Gửi email xác nhận.
             */
            Mail::to(
                $order->email
            )->send(
                new OrderConfirmationMail(
                    $order
                )
            );

        } catch (Throwable $exception) {

            /*
             * Nếu Email lỗi:
             *
             * Order vẫn giữ nguyên,
             * không làm Customer mất đơn hàng.
             */
            Log::error(
                'Không thể gửi email xác nhận đơn hàng.',
                [
                    'order_id' => $order->id,

                    'order_code' => $order->order_code,

                    'email' => $order->email,

                    'error' => $exception->getMessage(),
                ]
            );
        }

        /*
         * Sau khi tạo Order thành công
         * thì bỏ Voucher khỏi Session.
         */
        session()->forget([
            'cart_voucher_code',
            'cart_selected_item_ids',
            'checkout_cart_item_ids',
            'checkout_buy_now',
            'checkout_amount_after_discount',
        ]);

        /*
         * QR:
         * chuyển sang trang thanh toán QR.
         */
        if (
            $order->payment_method
            === Payment::METHOD_QR
        ) {

            return redirect()
                ->route(
                    'payments.qr.show',
                    $order
                );
        }

        /*
         * OnePAY:
         * chuyển sang cổng thanh toán OnePAY Sandbox.
         */
        if (
            $order->payment_method
            === Payment::METHOD_ONEPAY
        ) {

            return redirect()
                ->route(
                    'payments.onepay.show',
                    $order
                );
        }

        /*
         * COD:
         * tạo Order xong chuyển thẳng
         * sang trang đặt hàng thành công.
         */
        return redirect()
            ->route(
                'checkout.success',
                $order
            )
            ->with(
                'success',
                'Đặt hàng thành công.'
            );
    }

    /**
     * Trang đặt hàng thành công.
     */
    public function success(
        Order $order
    ) {
        /*
         * Customer chỉ được xem
         * Order của chính mình.
         */
        abort_if(
            $order->user_id
            !== auth()->id(),
            403
        );

        /*
         * Load thông tin phục vụ giao diện.
         */
        $order->load([
            'details',
            'payment',
        ]);

        return view(
            'checkout.success',
            compact('order')
        );
    }
}
