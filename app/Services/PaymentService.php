<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private OrderCancellationService $orderCancellationService
    ) {
    }


    /**
     * Admin ghi nhận hoàn tiền cho
     * giao dịch online và đồng thời
     * hủy Order.
     */
    public function refundAndCancel(
        User $operator,
        Payment $payment
    ): Payment {
        /*
        |--------------------------------------------------------------------------
        | CHỈ ADMIN
        |--------------------------------------------------------------------------
        */

        if (!$operator->isAdmin()) {
            abort(403);
        }


        return DB::transaction(
            function () use (
                $operator,
                $payment
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK PAYMENT
                |--------------------------------------------------------------------------
                */

                $lockedPayment =
                    Payment::query()
                        ->where(
                            'id',
                            $payment->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $order =
                    Order::query()
                        ->where(
                            'id',
                            $lockedPayment->order_id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | CHỈ REFUND THANH TOÁN ONLINE
                |--------------------------------------------------------------------------
                */

                if (
                    !in_array(
                        $lockedPayment->payment_method,
                        [
                            Payment::METHOD_QR,
                            Payment::METHOD_VNPAY,
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Chỉ có thể hoàn tiền trực tuyến cho giao dịch QR hoặc VNPay.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | CHỐNG REFUND LẶP
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedPayment->status
                    === Payment::STATUS_REFUNDED
                ) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Giao dịch này đã được hoàn tiền trước đó.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | PAYMENT PHẢI PAID
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedPayment->status
                    !== Payment::STATUS_PAID
                ) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Chỉ giao dịch đã thanh toán thành công mới được hoàn tiền.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | ORDER CŨNG PHẢI PAID
                |--------------------------------------------------------------------------
                */

                if (
                    $order->payment_status
                    !== Order::PAYMENT_PAID
                ) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Trạng thái thanh toán của đơn hàng không hợp lệ để hoàn tiền.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | CHỈ REFUND ORDER PENDING
                |--------------------------------------------------------------------------
                |
                | Order đã Confirmed trở đi cần nghiệp vụ
                | đổi/trả riêng, không dùng Refund + Cancel.
                |
                */

                if (
                    $order->order_status
                    !== Order::STATUS_PENDING
                ) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Chỉ có thể hoàn tiền và hủy đơn khi đơn hàng đang ở trạng thái Chờ xác nhận.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | ĐÁNH DẤU PAYMENT REFUNDED
                |--------------------------------------------------------------------------
                */

                $lockedPayment->update([
                    'status' =>
                        Payment::STATUS_REFUNDED,

                    'refunded_at' =>
                        now(),
                ]);


                /*
                |--------------------------------------------------------------------------
                | ĐỒNG BỘ ORDER
                |--------------------------------------------------------------------------
                */

                $order->update([
                    'payment_status' =>
                        Order::PAYMENT_REFUNDED,
                ]);


                /*
                |--------------------------------------------------------------------------
                | HỦY ORDER + HOÀN KHO
                |--------------------------------------------------------------------------
                |
                | OrderCancellationService đã xử lý:
                |
                | - Lock Order
                | - Chỉ Pending
                | - Hoàn stock
                | - stock_restored_at
                | - Cancelled
                | - Timeline
                |
                | Lúc này payment_status của Order đã là
                | refunded nên không bị chặn bởi điều kiện Paid.
                |
                */

                $this->orderCancellationService
                    ->cancelByOperator(
                        $operator,
                        $order
                    );


                return $lockedPayment->fresh([
                    'order',
                    'order.details',
                    'order.statusHistories',
                ]);
            }
        );
    }
}