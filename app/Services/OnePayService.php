<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OnePayService
{
    /**
     * Tạo URL chuyển khách sang cổng OnePAY Sandbox.
     */
    public function createPaymentUrl(
        Order $order,
        Request $request
    ): string {
        $this->ensureConfigured();

        return DB::transaction(function () use ($order, $request) {
            $lockedOrder = Order::query()
                ->where('id', $order->id)
                ->lockForUpdate()
                ->firstOrFail();

            $payment = Payment::query()
                ->where('order_id', $lockedOrder->id)
                ->lockForUpdate()
                ->first();

            if (! $payment) {
                throw ValidationException::withMessages([
                    'payment' => 'Không tìm thấy thông tin thanh toán.',
                ]);
            }

            if (
                $lockedOrder->payment_method !== Payment::METHOD_ONEPAY
                || $payment->payment_method !== Payment::METHOD_ONEPAY
            ) {
                throw ValidationException::withMessages([
                    'payment' => 'Đơn hàng không sử dụng OnePAY.',
                ]);
            }

            if ($lockedOrder->order_status === Order::STATUS_CANCELLED) {
                throw ValidationException::withMessages([
                    'payment' => 'Đơn hàng đã bị hủy và không thể thanh toán.',
                ]);
            }

            if (
                $lockedOrder->payment_status === Order::PAYMENT_PAID
                || $payment->isPaid()
            ) {
                throw ValidationException::withMessages([
                    'payment' => 'Đơn hàng đã được thanh toán.',
                ]);
            }

            $merchantTransactionReference = implode('-', [
                'VELORA',
                $lockedOrder->id,
                now()->format('YmdHis'),
                Str::upper(Str::random(6)),
            ]);

            $payment->update([
                'status' => Payment::STATUS_PENDING,
                'transaction_code' => $merchantTransactionReference,
                'response_code' => null,
                'paid_at' => null,
            ]);

            $lockedOrder->update([
                'payment_status' => Order::PAYMENT_PENDING,
            ]);

            $parameters = [
                'vpc_Version' => '2',
                'vpc_Command' => 'pay',
                'vpc_SecureHashType' => 'SHA256',
                'vpc_AccessCode' => (string) config('services.onepay.access_code'),
                'vpc_Merchant' => (string) config('services.onepay.merchant_id'),
                'vpc_Locale' => 'vn',
                'vpc_MerchTxnRef' => $merchantTransactionReference,
                'vpc_OrderInfo' => $lockedOrder->order_code,
                'vpc_Amount' => (string) ((int) round((float) $lockedOrder->total * 100)),
                'vpc_Currency' => 'VND',
                'vpc_CardList' => 'INTERNATIONAL',
                'vpc_ReturnURL' => route('payments.onepay.return'),
                'vpc_CallbackURL' => route('payments.onepay.ipn'),
                'vpc_TicketNo' => $request->ip() ?: '127.0.0.1',
                'AgainLink' => route('orders.show', $lockedOrder),
                'Title' => 'VELORA Eyes',
            ];

            $parameters['vpc_SecureHash'] = $this->makeSecureHash($parameters);

            return rtrim((string) config('services.onepay.payment_url'), '?')
                .'?'
                .http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
        });
    }

    /**
     * Xác minh và đồng bộ kết quả do OnePAY gửi về.
     * Có thể gọi nhiều lần an toàn từ Return URL hoặc IPN.
     *
     * @return array{order: Order, paid: bool, message: string}
     */
    public function handleResponse(array $payload): array
    {
        $this->ensureConfigured();

        if (! $this->hasValidSecureHash($payload)) {
            throw ValidationException::withMessages([
                'payment' => 'Chữ ký phản hồi OnePAY không hợp lệ.',
            ]);
        }

        $orderCode = trim((string) ($payload['vpc_OrderInfo'] ?? ''));
        $merchantReference = trim((string) ($payload['vpc_MerchTxnRef'] ?? ''));
        $responseCode = trim((string) ($payload['vpc_TxnResponseCode'] ?? ''));

        if ($orderCode === '' || $merchantReference === '' || $responseCode === '') {
            throw ValidationException::withMessages([
                'payment' => 'Phản hồi OnePAY thiếu thông tin bắt buộc.',
            ]);
        }

        return DB::transaction(function () use (
            $payload,
            $orderCode,
            $merchantReference,
            $responseCode
        ) {
            $order = Order::query()
                ->where('order_code', $orderCode)
                ->lockForUpdate()
                ->first();

            if (! $order) {
                throw ValidationException::withMessages([
                    'payment' => 'Không tìm thấy đơn hàng của giao dịch OnePAY.',
                ]);
            }

            $payment = Payment::query()
                ->where('order_id', $order->id)
                ->lockForUpdate()
                ->first();

            if (! $payment || $payment->payment_method !== Payment::METHOD_ONEPAY) {
                throw ValidationException::withMessages([
                    'payment' => 'Thông tin thanh toán OnePAY không hợp lệ.',
                ]);
            }

            $expectedAmount = (int) round((float) $payment->amount * 100);
            $returnedAmount = (int) ($payload['vpc_Amount'] ?? -1);

            if ($returnedAmount !== $expectedAmount) {
                throw ValidationException::withMessages([
                    'payment' => 'Số tiền OnePAY trả về không khớp với đơn hàng.',
                ]);
            }

            if (
                $order->payment_status === Order::PAYMENT_PAID
                || $payment->isPaid()
            ) {
                return [
                    'order' => $order->fresh(['details', 'payment']),
                    'paid' => true,
                    'message' => 'Giao dịch đã được ghi nhận trước đó.',
                ];
            }

            if (! hash_equals((string) $payment->transaction_code, $merchantReference)) {
                throw ValidationException::withMessages([
                    'payment' => 'Mã tham chiếu giao dịch OnePAY không hợp lệ.',
                ]);
            }

            if ($responseCode !== '0') {
                $payment->update([
                    'status' => Payment::STATUS_FAILED,
                    'response_code' => $responseCode,
                ]);

                $order->update([
                    'payment_status' => Order::PAYMENT_FAILED,
                ]);

                return [
                    'order' => $order->fresh(['details', 'payment']),
                    'paid' => false,
                    'message' => $this->responseMessage($responseCode),
                ];
            }

            $gatewayTransactionCode = trim(
                (string) ($payload['vpc_TransactionNo'] ?? '')
            );

            $payment->update([
                'status' => Payment::STATUS_PAID,
                'transaction_code' => $gatewayTransactionCode !== ''
                    ? $gatewayTransactionCode
                    : $merchantReference,
                'response_code' => $responseCode,
                'paid_at' => now(),
            ]);

            $order->update([
                'payment_status' => Order::PAYMENT_PAID,
            ]);

            return [
                'order' => $order->fresh(['details', 'payment']),
                'paid' => true,
                'message' => 'Thanh toán OnePAY thành công.',
            ];
        });
    }

    /**
     * Kiểm tra chữ ký HMAC SHA-256 trong phản hồi OnePAY.
     */
    private function hasValidSecureHash(array $payload): bool
    {
        $receivedHash = strtoupper(
            trim((string) ($payload['vpc_SecureHash'] ?? ''))
        );

        if ($receivedHash === '') {
            return false;
        }

        return hash_equals(
            $this->makeSecureHash($payload),
            $receivedHash
        );
    }

    /**
     * Tạo chữ ký từ các trường vpc_ và user_ theo thứ tự tên trường.
     */
    private function makeSecureHash(array $parameters): string
    {
        unset(
            $parameters['vpc_SecureHash'],
            $parameters['vpc_SecureHashType']
        );

        $parameters = array_filter(
            $parameters,
            fn ($value, $key) => $value !== ''
                && $value !== null
                && (
                    str_starts_with((string) $key, 'vpc_')
                    || str_starts_with((string) $key, 'user_')
                ),
            ARRAY_FILTER_USE_BOTH
        );

        ksort($parameters);

        $hashData = collect($parameters)
            ->map(
                fn ($value, $key) => $key.'='.$value
            )
            ->implode('&');

        return strtoupper(hash_hmac(
            'sha256',
            $hashData,
            $this->secureSecretBytes()
        ));
    }

    private function secureSecretBytes(): string
    {
        $secret = trim((string) config('services.onepay.secure_secret'));

        if (
            $secret === ''
            || strlen($secret) % 2 !== 0
            || ! ctype_xdigit($secret)
        ) {
            throw ValidationException::withMessages([
                'payment' => 'ONEPAY_SECURE_SECRET chưa được cấu hình đúng.',
            ]);
        }

        $bytes = hex2bin($secret);

        if ($bytes === false) {
            throw ValidationException::withMessages([
                'payment' => 'ONEPAY_SECURE_SECRET không hợp lệ.',
            ]);
        }

        return $bytes;
    }

    private function ensureConfigured(): void
    {
        foreach ([
            'payment_url',
            'merchant_id',
            'access_code',
            'secure_secret',
        ] as $key) {
            if (blank(config('services.onepay.'.$key))) {
                throw ValidationException::withMessages([
                    'payment' => 'OnePAY Sandbox chưa được cấu hình đầy đủ.',
                ]);
            }
        }
    }

    private function responseMessage(string $responseCode): string
    {
        return match ($responseCode) {
            '1' => 'Ngân hàng phát hành từ chối giao dịch.',
            '3' => 'Đơn vị thanh toán không tồn tại.',
            '4' => 'Thẻ đã hết hạn.',
            '5' => 'Số dư hoặc hạn mức thẻ không đủ.',
            '6' => 'Lỗi giao tiếp với ngân hàng.',
            '7' => 'Ngân hàng nghi ngờ giao dịch gian lận.',
            '8' => 'Hệ thống không hỗ trợ loại giao dịch này.',
            '9' => 'Ngân hàng từ chối giao dịch.',
            '99' => 'Khách hàng đã hủy giao dịch.',
            default => 'Giao dịch OnePAY chưa thành công (mã '.$responseCode.').',
        };
    }
}
