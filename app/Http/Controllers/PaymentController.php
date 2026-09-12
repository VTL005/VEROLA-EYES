<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\OnePayService;
use App\Services\PayOSService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        abort_if($order->user_id !== auth()->id(), 403);

        if ($order->payment_method !== Payment::METHOD_QR) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Đơn hàng này không sử dụng phương thức thanh toán QR.');
        }

        if ($order->order_status === Order::STATUS_CANCELLED) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Đơn hàng đã bị hủy và không thể thanh toán.');
        }

        $order->load(['details', 'payment']);

        if (! $order->payment) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Không tìm thấy thông tin thanh toán.');
        }

        if (
            $order->payment_status === Order::PAYMENT_PAID
            || $order->payment->isPaid()
        ) {
            return redirect()
                ->route('checkout.success', $order)
                ->with('success', 'Đơn hàng đã được thanh toán.');
        }

        try {
            $checkoutUrl = $payOSService->createPaymentLink($order);
        } catch (Throwable $exception) {
            Log::error('Không thể tạo link thanh toán payOS.', [
                'order_id' => $order->id,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Không thể kết nối payOS. Vui lòng thử lại sau.');
        }

        return redirect()->away($checkoutUrl);
    }

    /**
     * Trình duyệt quay lại sau khi khách thanh toán.
     * Hệ thống gọi API payOS để kiểm tra lại.
     */
    public function payOSReturn(
        Order $order,
        PayOSService $payOSService
    ): RedirectResponse {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load('payment');

        if (
            ! $order->payment
            || $order->payment_method !== Payment::METHOD_QR
        ) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Thông tin thanh toán không hợp lệ.');
        }

        if (
            $order->payment_status === Order::PAYMENT_PAID
            || $order->payment->isPaid()
        ) {
            return redirect()
                ->route('checkout.success', $order)
                ->with('success', 'Thanh toán payOS thành công.');
        }

        try {
            $paid = $payOSService->synchronizePayment($order->payment);
        } catch (Throwable $exception) {
            Log::error('Không thể đồng bộ kết quả payOS.', [
                'order_id' => $order->id,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Chưa thể xác minh giao dịch. Hệ thống sẽ tiếp tục kiểm tra qua payOS.');
        }

        if (! $paid) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Giao dịch chưa được payOS xác nhận thanh toán.');
        }

        return redirect()
            ->route('checkout.success', $order)
            ->with('success', 'Thanh toán payOS thành công.');
    }

    /**
     * Khách hủy tại trang thanh toán payOS.
     */
    public function payOSCancel(Order $order): RedirectResponse
    {
        abort_if($order->user_id !== auth()->id(), 403);

        return redirect()
            ->route('orders.show', $order)
            ->with('error', 'Bạn đã hủy thanh toán payOS. Đơn hàng vẫn đang chờ thanh toán.');
    }

    /**
     * Webhook công khai do máy chủ payOS gọi.
     */
    public function payOSWebhook(
        Request $request,
        PayOSService $payOSService
    ): JsonResponse {
        try {
            $payOSService->handleWebhook($request->all());

            return response()->json([
                'success' => true,
            ]);
        } catch (WebhookException $exception) {
            Log::warning('Webhook payOS có chữ ký không hợp lệ.', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid webhook signature.',
            ], 400);
        } catch (Throwable $exception) {
            Log::error('Xử lý webhook payOS thất bại.', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Webhook processing failed.',
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ONEPAY SANDBOX
    |--------------------------------------------------------------------------
    */

    /**
     * Tạo URL có chữ ký rồi chuyển khách sang OnePAY Sandbox.
     */
    public function showOnePay(
        Request $request,
        Order $order,
        OnePayService $onePayService
    ): RedirectResponse {
        abort_if($order->user_id !== auth()->id(), 403);

        if ($order->payment_method !== Payment::METHOD_ONEPAY) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Đơn hàng này không sử dụng phương thức OnePAY.');
        }

        if ($order->order_status === Order::STATUS_CANCELLED) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Đơn hàng đã bị hủy và không thể thanh toán.');
        }

        $order->load('payment');

        if (! $order->payment) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Không tìm thấy thông tin thanh toán.');
        }

        if (
            $order->payment_status === Order::PAYMENT_PAID
            || $order->payment->isPaid()
        ) {
            return redirect()
                ->route('checkout.success', $order)
                ->with('success', 'Đơn hàng đã được thanh toán.');
        }

        try {
            $paymentUrl = $onePayService->createPaymentUrl($order, $request);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', $this->firstValidationMessage($exception));
        } catch (Throwable $exception) {
            Log::error('Không thể tạo giao dịch OnePAY.', [
                'order_id' => $order->id,
                'exception' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('orders.show', $order)
                ->with('error', 'Không thể kết nối OnePAY. Vui lòng thử lại sau.');
        }

        return redirect()->away($paymentUrl);
    }

    /**
     * Trình duyệt quay về từ OnePAY.
     * Chỉ cập nhật thanh toán sau khi chữ ký, mã đơn và số tiền đều hợp lệ.
     */
    public function onePayReturn(
        Request $request,
        OnePayService $onePayService
    ): RedirectResponse {
        try {
            $result = $onePayService->handleResponse($request->query());
        } catch (ValidationException $exception) {
            return redirect()
                ->route('orders.index')
                ->with('error', $this->firstValidationMessage($exception));
        } catch (Throwable $exception) {
            Log::error('Xử lý OnePAY Return thất bại.', [
                'exception' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('orders.index')
                ->with('error', 'Không thể xác minh kết quả OnePAY.');
        }

        /** @var Order $order */
        $order = $result['order'];

        if (! $result['paid']) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', $result['message']);
        }

        return redirect()
            ->route('checkout.success', $order)
            ->with('success', 'Thanh toán OnePAY thành công.');
    }

    /**
     * IPN công khai do máy chủ OnePAY gọi.
     */
    public function onePayIpn(
        Request $request,
        OnePayService $onePayService
    ): JsonResponse {
        try {
            $result = $onePayService->handleResponse($request->all());

            return response()->json([
                'responsecode' => 0,
                'desc' => $result['paid'] ? 'confirm-success' : 'payment-failed',
            ]);
        } catch (ValidationException $exception) {
            Log::warning('OnePAY IPN không hợp lệ.', [
                'message' => $this->firstValidationMessage($exception),
            ]);

            return response()->json([
                'responsecode' => 1,
                'desc' => 'invalid-request',
            ], 400);
        } catch (Throwable $exception) {
            Log::error('Xử lý OnePAY IPN thất bại.', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'responsecode' => 1,
                'desc' => 'processing-failed',
            ], 500);
        }
    }

    private function firstValidationMessage(
        ValidationException $exception
    ): string {
        $message = collect($exception->errors())
            ->flatten()
            ->first();

        return is_string($message) && $message !== ''
            ? $message
            : 'Dữ liệu thanh toán không hợp lệ.';
    }
}
