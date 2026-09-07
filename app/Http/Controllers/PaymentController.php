<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\PayOSService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use PayOS\Exceptions\WebhookException;
use Throwable;

class PaymentController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PAYOS QR
    |--------------------------------------------------------------------------
    */

    /**
     * Tạo hoặc mở lại link thanh toán payOS.
     */
    public function showQr(
        Order $order,
        PayOSService $payOSService
    ): RedirectResponse {
        abort_if(
            $order->user_id !== auth()->id(),
            403
        );

        if (
            $order->payment_method
            !== Payment::METHOD_QR
        ) {
            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Đơn hàng này không sử dụng phương thức thanh toán QR.'
                );
        }

        if (
            $order->order_status
            === Order::STATUS_CANCELLED
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

        if (! $order->payment) {
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

        if (
            $order->payment_status
            === Order::PAYMENT_PAID
            || $order->payment->isPaid()
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

        try {
            $checkoutUrl =
                $payOSService
                    ->createPaymentLink(
                        $order
                    );
        } catch (Throwable $exception) {
            Log::error(
                'Không thể tạo link thanh toán payOS.',
                [
                    'order_id' => $order->id,

                    'exception' => $exception->getMessage(),
                ]
            );

            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Không thể kết nối payOS. Vui lòng thử lại sau.'
                );
        }

        return redirect()->away(
            $checkoutUrl
        );
    }

    /**
     * Trình duyệt quay lại sau khi khách thanh toán.
     *
     * Không tin trực tiếp query string từ trình duyệt.
     * Hệ thống gọi API payOS để kiểm tra lại.
     */
    public function payOSReturn(
        Order $order,
        PayOSService $payOSService
    ): RedirectResponse {
        abort_if(
            $order->user_id !== auth()->id(),
            403
        );

        $order->load('payment');

        if (
            ! $order->payment
            || $order->payment_method
                !== Payment::METHOD_QR
        ) {
            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Thông tin thanh toán không hợp lệ.'
                );
        }

        if (
            $order->payment_status
            === Order::PAYMENT_PAID
            || $order->payment->isPaid()
        ) {
            return redirect()
                ->route(
                    'checkout.success',
                    $order
                )
                ->with(
                    'success',
                    'Thanh toán payOS thành công.'
                );
        }

        try {
            $paid =
                $payOSService
                    ->synchronizePayment(
                        $order->payment
                    );
        } catch (Throwable $exception) {
            Log::error(
                'Không thể đồng bộ kết quả payOS.',
                [
                    'order_id' => $order->id,

                    'exception' => $exception->getMessage(),
                ]
            );

            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Chưa thể xác minh giao dịch. Hệ thống sẽ tiếp tục kiểm tra qua payOS.'
                );
        }

        if (! $paid) {
            return redirect()
                ->route(
                    'orders.show',
                    $order
                )
                ->with(
                    'error',
                    'Giao dịch chưa được payOS xác nhận thanh toán.'
                );
        }

        return redirect()
            ->route(
                'checkout.success',
                $order
            )
            ->with(
                'success',
                'Thanh toán payOS thành công.'
            );
    }

    /**
     * Khách hủy tại trang thanh toán payOS.
     *
     * Không cập nhật paid/failed chỉ dựa vào
     * URL quay lại từ trình duyệt.
     */
    public function payOSCancel(
        Order $order
    ): RedirectResponse {
        abort_if(
            $order->user_id !== auth()->id(),
            403
        );

        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'error',
                'Bạn đã hủy thanh toán payOS. Đơn hàng vẫn đang chờ thanh toán.'
            );
    }

    /**
     * Webhook công khai do máy chủ payOS gọi.
     *
     * Không yêu cầu đăng nhập nhưng bắt buộc
     * xác minh chữ ký bằng Checksum Key.
     */
    public function payOSWebhook(
        Request $request,
        PayOSService $payOSService
    ): JsonResponse {
        try {
            $payOSService->handleWebhook(
                $request->all()
            );

            /*
             * Kể cả webhook kiểm tra kết nối chưa
             * thuộc Payment nào, vẫn trả về 200
             * nếu chữ ký hợp lệ.
             */
            return response()->json([
                'success' => true,
            ]);
        } catch (
            WebhookException $exception
        ) {
            Log::warning(
                'Webhook payOS có chữ ký không hợp lệ.',
                [
                    'exception' => $exception->getMessage(),
                ]
            );

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Invalid webhook signature.',
                ],
                400
            );
        } catch (Throwable $exception) {
            Log::error(
                'Xử lý webhook payOS thất bại.',
                [
                    'exception' => $exception->getMessage(),
                ]
            );

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Webhook processing failed.',
                ],
                500
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | VNPAY MÔ PHỎNG
    |--------------------------------------------------------------------------
    */

    /**
     * Trang cổng thanh toán VNPay mô phỏng.
     */
    public function showVnpay(
        Order $order
    ) {
        abort_if(
            $order->user_id !== auth()->id(),
            403
        );

        if (
            $order->payment_method
            !== Payment::METHOD_VNPAY
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
            === Order::STATUS_CANCELLED
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

        if (! $order->payment) {
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

        if (
            $order->payment_status
            === Order::PAYMENT_PAID
            || $order->payment->isPaid()
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
                    !== Payment::METHOD_VNPAY
                ) {
                    throw ValidationException::withMessages([
                        'payment' => 'Đơn hàng không sử dụng VNPay.',
                    ]);
                }

                if (
                    $lockedOrder->order_status
                    === Order::STATUS_CANCELLED
                ) {
                    throw ValidationException::withMessages([
                        'payment' => 'Đơn hàng đã bị hủy và không thể thanh toán.',
                    ]);
                }

                $payment = Payment::query()
                    ->where(
                        'order_id',
                        $lockedOrder->id
                    )
                    ->lockForUpdate()
                    ->first();

                if (! $payment) {
                    throw ValidationException::withMessages([
                        'payment' => 'Không tìm thấy thông tin thanh toán.',
                    ]);
                }

                if (
                    $lockedOrder->payment_status
                    === Order::PAYMENT_PAID
                    || $payment->isPaid()
                ) {
                    return;
                }

                /*
                 * VNPay hiện vẫn là mô phỏng.
                 */
                $payment->update([
                    'status' => Payment::STATUS_PAID,

                    'transaction_code' => 'VNPAY-'
                        .$lockedOrder->id
                        .'-'
                        .Str::upper(
                            Str::random(10)
                        ),

                    'response_code' => '00',

                    'paid_at' => now(),
                ]);

                $lockedOrder->update([
                    'payment_status' => Order::PAYMENT_PAID,
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
