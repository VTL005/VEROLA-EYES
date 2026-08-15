<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        private VoucherService $voucherService
    ) {
    }


    /**
     * Tạo đơn hàng.
     */
    public function placeOrder(
    User $user,
    Address $address,
    string $paymentMethod,
    ?string $note = null,
    ?string $voucherCode = null,
    array $selectedCartItemIds = []
): Order {
        /*
         * Không cho Customer dùng
         * Address của người khác.
         */
        if ($address->user_id !== $user->id) {
            throw ValidationException::withMessages([
                'address_id' =>
                    'Địa chỉ nhận hàng không hợp lệ.',
            ]);
        }


        /*
         * Chỉ chấp nhận 3 phương thức
         * hiện tại của VELORA.
         */
        if (
            !in_array(
                $paymentMethod,
                [
                    'cod',
                    'qr',
                    'vnpay',
                ],
                true
            )
        ) {
            throw ValidationException::withMessages([
                'payment_method' =>
                    'Phương thức thanh toán không hợp lệ.',
            ]);
        }
/*
 * Chuẩn hóa CartItem ID.
 */
$selectedCartItemIds = collect(
    $selectedCartItemIds
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


if (empty($selectedCartItemIds)) {

    throw ValidationException::withMessages([
        'cart' =>
            'Vui lòng chọn ít nhất một sản phẩm để thanh toán.',
    ]);
}

  return DB::transaction(
    function () use (
        $user,
        $address,
        $paymentMethod,
        $note,
        $voucherCode,
        $selectedCartItemIds
    ) {

                /*
                 * =====================================================
                 * 1. KHÓA CART
                 * =====================================================
                 */

                $cart = Cart::query()
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->lockForUpdate()
                    ->first();


                if (!$cart) {
                    throw ValidationException::withMessages([
                        'cart' =>
                            'Giỏ hàng không tồn tại.',
                    ]);
                }


                /*
                 * Lấy CartItem hiện tại.
                 */
                /*
 * Chỉ lock những CartItem
 * Customer đã chọn.
 */
$cartItems = $cart
    ->items()
    ->whereIn(
        'id',
        $selectedCartItemIds
    )
    ->lockForUpdate()
    ->get();


if ($cartItems->isEmpty()) {

    throw ValidationException::withMessages([
        'cart' =>
            'Không có sản phẩm nào được chọn để thanh toán.',
    ]);
}


/*
 * Chống giả mạo CartItem.
 *
 * Tất cả ID phải thuộc Cart
 * của Customer hiện tại.
 */
if (
    $cartItems->count()
    !== count($selectedCartItemIds)
) {

    throw ValidationException::withMessages([
        'cart' =>
            'Danh sách sản phẩm thanh toán không hợp lệ.',
    ]);
}


                if ($cartItems->isEmpty()) {
                    throw ValidationException::withMessages([
                        'cart' =>
                            'Giỏ hàng đang trống.',
                    ]);
                }


                /*
                 * =====================================================
                 * 2. KIỂM TRA VARIANT + STOCK LẦN CUỐI
                 * =====================================================
                 */

                $orderLines = [];

                $subtotal = 0;


                foreach ($cartItems as $cartItem) {

                    /*
                     * Khóa Variant để tránh 2 Customer
                     * cùng mua số lượng cuối cùng.
                     */
                    $variant = ProductVariant::query()
                        ->with('product')
                        ->where(
                            'id',
                            $cartItem->variant_id
                        )
                        ->lockForUpdate()
                        ->first();


                    if (!$variant) {
                        throw ValidationException::withMessages([
                            'cart' =>
                                'Một biến thể trong giỏ hàng không còn tồn tại.',
                        ]);
                    }


                    $product =
                        $variant->product;


                    if (
                        !$product
                        || !$product->is_active
                    ) {
                        throw ValidationException::withMessages([
                            'cart' =>
                                'Sản phẩm '
                                . ($product?->name ?? '')
                                . ' hiện không còn được kinh doanh.',
                        ]);
                    }


                    if (!$variant->is_active) {
                        throw ValidationException::withMessages([
                            'cart' =>
                                'Biến thể '
                                . $variant->sku
                                . ' hiện đã ngừng bán.',
                        ]);
                    }


                    $quantity =
                        (int) $cartItem->quantity;


                    if ($quantity < 1) {
                        throw ValidationException::withMessages([
                            'cart' =>
                                'Số lượng sản phẩm không hợp lệ.',
                        ]);
                    }


                    /*
                     * Đây là bước kiểm tra Stock
                     * quan trọng nhất trước khi Order.
                     */
                    if (
                        $quantity
                        > $variant->stock_quantity
                    ) {
                        throw ValidationException::withMessages([
                            'cart' =>
                                'Sản phẩm '
                                . $product->name
                                . ' - '
                                . $variant->color
                                . ' / '
                                . $variant->size
                                . ' chỉ còn '
                                . $variant->stock_quantity
                                . ' sản phẩm.',
                        ]);
                    }


                    /*
                     * Giá được đọc lại từ Database,
                     * không lấy giá gửi từ Browser.
                     */
                    $unitPrice =
                        (float) $variant->final_price;


                    $lineSubtotal =
                        $unitPrice
                        * $quantity;


                    $subtotal +=
                        $lineSubtotal;


                    /*
                     * Lưu dữ liệu chuẩn bị tạo
                     * OrderDetail snapshot.
                     */
                    $orderLines[] = [
                        'variant' =>
                            $variant,

                        'product' =>
                            $product,

                        'quantity' =>
                            $quantity,

                        'unit_price' =>
                            $unitPrice,

                        'subtotal' =>
                            $lineSubtotal,
                    ];
                }


                /*
                 * =====================================================
                 * 3. VOUCHER
                 * =====================================================
                 */

                $voucher = null;

                $discountAmount = 0;


                if ($voucherCode) {

                    /*
                     * Lock Voucher để usage_limit
                     * không bị vượt khi nhiều người
                     * Checkout cùng lúc.
                     */
                    $voucher = Voucher::query()
                        ->where(
                            'code',
                            strtoupper(
                                trim($voucherCode)
                            )
                        )
                        ->lockForUpdate()
                        ->first();


                    if (!$voucher) {
                        throw ValidationException::withMessages([
                            'voucher_code' =>
                                'Mã giảm giá không tồn tại.',
                        ]);
                    }


                    /*
                     * Kiểm tra lại toàn bộ Voucher:
                     *
                     * active
                     * starts_at
                     * ends_at
                     * usage_limit
                     * minimum_order_amount
                     */
                    $this->voucherService
                        ->validateVoucher(
                            $voucher,
                            $subtotal
                        );


                    $discountAmount =
                        $this->voucherService
                            ->calculateDiscount(
                                $voucher,
                                $subtotal
                            );
                }


                /*
                 * =====================================================
                 * 4. TÍNH TIỀN
                 * =====================================================
                 */

                /*
                 * Chưa tích hợp API vận chuyển.
                 */
                $shippingFee = 0;


                $total = max(
                    0,
                    $subtotal
                    - $discountAmount
                    + $shippingFee
                );


                /*
                 * =====================================================
                 * 5. CREATE ORDER
                 * =====================================================
                 */

                $order = Order::create([
                    'order_code' =>
                        $this->generateOrderCode(),

                    'user_id' =>
                        $user->id,

                    'voucher_id' =>
                        $voucher?->id,

                    /*
                     * Snapshot thông tin Customer.
                     *
                     * Sau này Customer đổi Address
                     * thì Order cũ vẫn giữ nguyên.
                     */
                    'customer_name' =>
                        $address->recipient_name,

                    'phone' =>
                        $address->phone,

                    'email' =>
                        $user->email,

                    'address' =>
                        $this->buildAddress(
                            $address
                        ),

                    'subtotal' =>
                        $subtotal,

                    'discount_amount' =>
                        $discountAmount,

                    'shipping_fee' =>
                        $shippingFee,

                    'total' =>
                        $total,

                    'payment_method' =>
                        $paymentMethod,

                    /*
                     * COD / QR / VNPay ban đầu
                     * đều chưa thanh toán thành công.
                     */
                    'payment_status' =>
                     $paymentMethod === 'cod'
                         ? Order::PAYMENT_UNPAID
                      : Order::PAYMENT_PENDING,

                    /*
                     * Đơn mới tạo.
                     */
                    'order_status' =>
                        'pending',

                    'note' =>
                        $note,

                    'stock_restored_at' =>
                        null,
                ]);


                /*
 * =====================================================
 * ORDER STATUS HISTORY
 * =====================================================
 */

                $order->statusHistories()->create([
                    'status' =>
                    'pending',

                    'description' =>
                    'Đơn hàng đã được tạo và đang chờ xác nhận.',

                    'updated_by' =>
                    $user->id,
                ]);

                /*
                 * =====================================================
                 * 6. ORDER DETAILS + TRỪ STOCK
                 * =====================================================
                 */

                foreach ($orderLines as $line) {

                    $variant =
                        $line['variant'];

                    $product =
                        $line['product'];

                    $quantity =
                        $line['quantity'];


                    $order->details()->create([
                        'product_id' =>
                            $product->id,

                        /*
                         * Schema thật của bạn:
                         * variant_id
                         */
                        'variant_id' =>
                            $variant->id,

                        /*
                         * Snapshot Product.
                         */
                        'product_name' =>
                            $product->name,

                        'sku' =>
                            $variant->sku,

                        'color' =>
                            $variant->color,

                        'size' =>
                            $variant->size,

                        'unit_price' =>
                            $line['unit_price'],

                        'quantity' =>
                            $quantity,

                        'subtotal' =>
                            $line['subtotal'],
                    ]);


                    /*
                     * Trừ tồn kho.
                     *
                     * Variant đang được lockForUpdate()
                     * nên an toàn hơn khi Checkout đồng thời.
                     */
                    $variant->decrement(
                        'stock_quantity',
                        $quantity
                    );
                }


                /*
                 * =====================================================
                 * 7. VOUCHER USAGE
                 * =====================================================
                 */

                if ($voucher) {

                    VoucherUsage::create([
                        'voucher_id' =>
                            $voucher->id,

                        'user_id' =>
                            $user->id,

                        'order_id' =>
                            $order->id,

                        'discount_amount' =>
                            $discountAmount,
                    ]);


                    /*
                     * Chỉ tăng usage_count
                     * sau khi Order được tạo.
                     */
                    $voucher->increment(
                        'usage_count'
                    );
                }


                /*
                 * =====================================================
                 * 8. PAYMENT
                 * =====================================================
                 */

                Payment::create([
                    'order_id' =>
                        $order->id,

                    'payment_method' =>
                        $paymentMethod,

                    'amount' =>
                        $total,

                    /*
                     * COD:
                     * chưa thu tiền.
                     *
                     * QR/VNPay:
                     * chờ thanh toán.
                     */
                    'status' =>
                        $paymentMethod === 'cod'
                            ? 'unpaid'
                            : 'pending',

                    'transaction_code' =>
                        null,

                    'response_code' =>
                        null,

                    'paid_at' =>
                        null,

                    'refunded_at' =>
                        null,
                ]);


                /*
                 * =====================================================
                 * 9. CLEAR CART
                 * =====================================================
                 */

                /*
 * Chỉ xóa CartItem vừa mua.
 *
 * Các sản phẩm Customer không chọn
 * vẫn giữ nguyên trong Cart.
 */
$cart
    ->items()
    ->whereIn(
        'id',
        $cartItems
            ->pluck('id')
            ->all()
    )
    ->delete();


                /*
                 * =====================================================
                 * 10. RETURN ORDER
                 * =====================================================
                 */

                return $order->load([
                    'details',
                    'payment',
                ]);
            }
        );
    }


    /**
     * Tạo Order Code.
     */
    private function generateOrderCode(): string
    {
        do {

            $code =
                'VEL-'
                . now()->format('Ymd')
                . '-'
                . Str::upper(
                    Str::random(6)
                );

        } while (
            Order::query()
                ->where(
                    'order_code',
                    $code
                )
                ->exists()
        );


        return $code;
    }


    /**
     * Ghép Address thành Snapshot String.
     */
    private function buildAddress(
        Address $address
    ): string {
        return implode(
            ', ',
            array_filter([
                $address->detail_address,
                $address->ward,
                $address->district,
                $address->province,
            ])
        );
    }
}