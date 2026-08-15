<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | QR
    |--------------------------------------------------------------------------
    */

    public function showQr(Order $order)
    {
        abort_if(
            $order->user_id !== auth()->id(),
            403
        );


        if ($order->payment_method !== 'qr') {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Đơn hàng này không sử dụng phương thức thanh toán QR.'
                );
        }


        if ($order->order_status === 'cancelled') {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Đơn hàng đã bị hủy và không thể thanh toán.'
                );
        }


        $order->load([
            'details',
            'payment',
        ]);


        if (!$order->payment) {
            return redirect()
                ->route('orders.show', $order)
                ->with(
                    'error',
                    'Không tìm thấy thông tin thanh toán.'
                );
        }


        if (
            $order->payment_status === 'paid'
            || $order->payment->status === 'paid'
        ) {
            return redirect()
                ->route(
                    'checkout.success',
                    $order
                )
                ->with(
                    'success',
                    'Đơn hàng đã được thanh toán.'
                );
        }


        return view(
            'payment.qr',
            compact('order')
        );
    }


    public function confirmQr(Order $order)
    {
        $user = auth()->user();


        DB::transaction(
            function () use (
                $order,
                $user
            ) {

                $lockedOrder = Order::query()
                    ->where('id', $order->id)
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                if (
                    $lockedOrder->payment_method
                    !== 'qr'
                ) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Đơn hàng không sử dụng thanh toán QR.',
                    ]);
                }


                if (
                    $lockedOrder->order_status
                    === 'cancelled'
                ) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Đơn hàng đã bị hủy và không thể thanh toán.',
                    ]);
                }


                $payment = Payment::query()
                    ->where(
                        'order_id',
                        $lockedOrder->id
                    )
                    ->lockForUpdate()
                    ->first();


                if (!$payment) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Không tìm thấy thông tin thanh toán.',
                    ]);
                }


                if (
                    $lockedOrder->payment_status
                    === 'paid'
                    || $payment->status
                    === 'paid'
                ) {
                    return;
                }


                $payment->update([
                    'status' =>
                        'paid',

                    'transaction_code' =>
                        'QR-'
                        . $lockedOrder->id
                        . '-'
                        . Str::upper(
                            Str::random(8)
                        ),

                    'response_code' =>
                        '00',

                    'paid_at' =>
                        now(),
                ]);


                $lockedOrder->update([
                    'payment_status' =>
                        'paid',
                ]);
            }
        );


        return redirect()
            ->route(
                'checkout.success',
                $order
            )
            ->with(
                'success',
                'Thanh toán QR thành công.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | VNPAY MÔ PHỎNG
    |--------------------------------------------------------------------------
    */

    /**
     * Trang cổng thanh toán VNPay mô phỏng.
     */
    public function showVnpay(Order $order)
    {
        abort_if(
            $order->user_id !== auth()->id(),
            403
        );


        if (
            $order->payment_method
            !== 'vnpay'
        ) {
            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Đơn hàng này không sử dụng phương thức VNPay.'
                );
        }


        if (
            $order->order_status
            === 'cancelled'
        ) {
            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Đơn hàng đã bị hủy và không thể thanh toán.'
                );
        }


        $order->load([
            'details',
            'payment',
        ]);


        if (!$order->payment) {
            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Không tìm thấy thông tin thanh toán.'
                );
        }


        /*
         * Đã thanh toán thì không cho
         * thanh toán lại.
         */
        if (
            $order->payment_status
            === 'paid'
            || $order->payment->status
            === 'paid'
        ) {
            return redirect()
                ->route(
                    'checkout.success',
                    $order
                )
                ->with(
                    'success',
                    'Đơn hàng đã được thanh toán.'
                );
        }


        return view(
            'payment.vnpay',
            compact('order')
        );
    }


    /**
     * Callback VNPay mô phỏng thành công.
     */
    public function confirmVnpay(
        Order $order
    ) {
        $user = auth()->user();


        DB::transaction(
            function () use (
                $order,
                $user
            ) {

                /*
                 * Lock Order.
                 */
                $lockedOrder = Order::query()
                    ->where(
                        'id',
                        $order->id
                    )
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();


                if (
                    $lockedOrder->payment_method
                    !== 'vnpay'
                ) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Đơn hàng không sử dụng VNPay.',
                    ]);
                }


                if (
                    $lockedOrder->order_status
                    === 'cancelled'
                ) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Đơn hàng đã bị hủy và không thể thanh toán.',
                    ]);
                }


                /*
                 * Lock Payment.
                 */
                $payment = Payment::query()
                    ->where(
                        'order_id',
                        $lockedOrder->id
                    )
                    ->lockForUpdate()
                    ->first();


                if (!$payment) {
                    throw ValidationException::withMessages([
                        'payment' =>
                            'Không tìm thấy thông tin thanh toán.',
                    ]);
                }


                /*
                 * Chống callback lặp.
                 */
                if (
                    $lockedOrder->payment_status
                    === 'paid'
                    || $payment->status
                    === 'paid'
                ) {
                    return;
                }


                /*
                 * Mô phỏng VNPay trả:
                 * ResponseCode = 00
                 */
                $payment->update([
                    'status' =>
                        'paid',

                    'transaction_code' =>
                        'VNPAY-'
                        . $lockedOrder->id
                        . '-'
                        . Str::upper(
                            Str::random(10)
                        ),

                    'response_code' =>
                        '00',

                    'paid_at' =>
                        now(),
                ]);


                /*
                 * Đồng bộ Order.
                 */
                $lockedOrder->update([
                    'payment_status' =>
                        'paid',
                ]);
            }
        );


        return redirect()
            ->route(
                'checkout.success',
                $order
            )
            ->with(
                'success',
                'Thanh toán VNPay thành công.'
            );
    }
}