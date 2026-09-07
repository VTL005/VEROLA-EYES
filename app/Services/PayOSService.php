<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PayOS\Models\V2\PaymentRequests\CreatePaymentLinkRequest;
use PayOS\Models\V2\PaymentRequests\PaymentLink;
use PayOS\Models\V2\PaymentRequests\PaymentLinkStatus;
use PayOS\Models\Webhooks\WebhookData;
use PayOS\PayOS;
use RuntimeException;

class PayOSService
{
    private PayOS $payOS;

    public function __construct()
    {
        $clientId = (string) config(
            'services.payos.client_id'
        );

        $apiKey = (string) config(
            'services.payos.api_key'
        );

        $checksumKey = (string) config(
            'services.payos.checksum_key'
        );

        if (
            $clientId === ''
            || $apiKey === ''
            || $checksumKey === ''
        ) {
            throw new RuntimeException(
                'Thông tin cấu hình payOS chưa đầy đủ.'
            );
        }

        $this->payOS = new PayOS(
            clientId: $clientId,
            apiKey: $apiKey,
            checksumKey: $checksumKey
        );
    }

    /**
     * Tạo link thanh toán payOS.
     *
     * Nếu Payment đã có link thì sử dụng lại,
     * tránh tạo nhiều link cho cùng giao dịch.
     */
    public function createPaymentLink(
        Order $order
    ): string {
        return DB::transaction(
            function () use ($order) {
                $payment = Payment::query()
                    ->where(
                        'order_id',
                        $order->id
                    )
                    ->lockForUpdate()
                    ->first();

                if (! $payment) {
                    throw new RuntimeException(
                        'Không tìm thấy thông tin thanh toán.'
                    );
                }

                if (
                    $payment->payment_method
                    !== Payment::METHOD_QR
                ) {
                    throw new RuntimeException(
                        'Đơn hàng không sử dụng thanh toán QR.'
                    );
                }

                if ($payment->isPaid()) {
                    throw new RuntimeException(
                        'Đơn hàng đã được thanh toán.'
                    );
                }

                /*
                 * Nếu Payment đã có link payOS,
                 * kiểm tra trạng thái thật trước khi sử dụng lại.
                 */
                if (
                    filled($payment->payos_checkout_url)
                    && $payment->payos_order_code
                ) {
                    $existingPaymentLink =
                        $this->getPaymentLink(
                            $payment->payos_order_code
                        );

                    /*
                     * Link vẫn còn khả năng nhận tiền
                     * thì tiếp tục sử dụng link hiện tại.
                     */
                    if (
                        in_array(
                            $existingPaymentLink->status,
                            [
                                PaymentLinkStatus::PENDING,
                                PaymentLinkStatus::PROCESSING,
                                PaymentLinkStatus::UNDERPAID,
                            ],
                            true
                        )
                    ) {
                        return $payment->payos_checkout_url;
                    }

                    /*
                     * payOS đã ghi nhận thanh toán nhưng
                     * dữ liệu local chưa kịp đồng bộ.
                     */
                    if (
                        $existingPaymentLink->status
                        === PaymentLinkStatus::PAID
                    ) {
                        return route(
                            'payments.payos.return',
                            $order
                        );
                    }

                    /*
                     * Link cũ đã CANCELLED, EXPIRED hoặc FAILED.
                     * Xóa thông tin link cũ để tạo link mới.
                     */
                    $payment->update([
                        'payos_order_code' => null,
                        'payos_payment_link_id' => null,
                        'payos_checkout_url' => null,

                        'status' => Payment::STATUS_PENDING,

                        'transaction_code' => null,
                        'response_code' => $existingPaymentLink->status->value,

                        'paid_at' => null,
                    ]);
                }

                $amount = (int) round(
                    (float) $payment->amount
                );

                if ($amount <= 0) {
                    throw new RuntimeException(
                        'Số tiền thanh toán không hợp lệ.'
                    );
                }

                $payOSOrderCode =
                    $payment->payos_order_code
                    ?? $this->generateOrderCode();

                if (
                    ! $payment->payos_order_code
                ) {
                    $payment->update([
                        'payos_order_code' => $payOSOrderCode,
                    ]);
                }

                /*
                 * Nội dung chuyển khoản cần ngắn gọn.
                 */
                $description =
                    'VELORA '.$order->id;

                $paymentRequest =
                    new CreatePaymentLinkRequest(
                        orderCode: $payOSOrderCode,

                        amount: $amount,

                        description: $description,

                        cancelUrl: route(
                            'payments.payos.cancel',
                            $order
                        ),

                        returnUrl: route(
                            'payments.payos.return',
                            $order
                        ),

                        buyerName: $order->customer_name,

                        buyerEmail: $order->email,

                        buyerPhone: $order->phone,

                        buyerAddress: $order->address
                    );

                /*
                 * Gọi API thật của payOS.
                 */
                $result =
                    $this->payOS
                        ->paymentRequests
                        ->create(
                            $paymentRequest
                        );

                $payment->update([
                    'payos_payment_link_id' => $result->paymentLinkId,

                    'payos_checkout_url' => $result->checkoutUrl,

                    'response_code' => PaymentLinkStatus::PENDING
                        ->value,
                ]);

                return $result->checkoutUrl;
            }
        );
    }

    /**
     * Lấy trạng thái link trực tiếp từ payOS.
     */
    public function getPaymentLink(
        int|string $id
    ): PaymentLink {
        return $this->payOS
            ->paymentRequests
            ->get($id);
    }

    /**
     * Xác minh chữ ký webhook payOS.
     */
    public function verifyWebhook(
        array $payload
    ): WebhookData {
        return $this->payOS
            ->webhooks
            ->verify($payload);
    }

    /**
     * Xử lý webhook đã được payOS ký.
     *
     * Webhook hợp lệ nhưng không thuộc Payment
     * trong hệ thống vẫn trả về false để payOS
     * không phải gửi lại liên tục.
     */
    public function handleWebhook(
        array $payload
    ): bool {
        $data = $this->verifyWebhook(
            $payload
        );

        /*
         * code 00 là giao dịch thành công.
         */
        if ($data->code !== '00') {
            return false;
        }

        $payment = Payment::query()
            ->where(
                'payos_order_code',
                $data->orderCode
            )
            ->first();

        if (! $payment) {
            Log::warning(
                'Không tìm thấy Payment cho webhook payOS.',
                [
                    'payos_order_code' => $data->orderCode,
                ]
            );

            return false;
        }

        if (
            filled(
                $payment->payos_payment_link_id
            )
            && $payment->payos_payment_link_id
                !== $data->paymentLinkId
        ) {
            throw new RuntimeException(
                'Payment Link ID của payOS không khớp.'
            );
        }

        return $this->markAsPaid(
            $payment,
            $data->amount,
            $data->reference,
            $data->paymentLinkId
        );
    }

    /**
     * Khi trình duyệt quay về từ payOS,
     * gọi API payOS để kiểm tra lại trạng thái.
     *
     * Không tin trực tiếp query string trên URL.
     */
    public function synchronizePayment(
        Payment $payment
    ): bool {
        if (! $payment->payos_order_code) {
            return false;
        }

        $paymentLink =
            $this->getPaymentLink(
                $payment->payos_order_code
            );

        if (
            $paymentLink->status
            !== PaymentLinkStatus::PAID
        ) {
            return false;
        }

        /*
         * Lấy mã tham chiếu của giao dịch
         * gần nhất nếu payOS trả về.
         */
        $transactions =
            $paymentLink->transactions;

        $lastTransaction =
            ! empty($transactions)
                ? end($transactions)
                : null;

        $reference =
            $lastTransaction?->reference
            ?? (
                'PAYOS-'
                .$paymentLink->orderCode
            );

        return $this->markAsPaid(
            $payment,
            $paymentLink->amountPaid,
            $reference,
            $paymentLink->id
        );
    }

    /**
     * Đồng bộ Payment và Order thành đã thanh toán.
     */
    private function markAsPaid(
        Payment $payment,
        int $paidAmount,
        string $reference,
        string $paymentLinkId
    ): bool {
        return DB::transaction(
            function () use (
                $payment,
                $paidAmount,
                $reference,
                $paymentLinkId
            ) {
                $lockedPayment =
                    Payment::query()
                        ->whereKey(
                            $payment->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                /*
                 * Webhook có thể được gửi nhiều lần.
                 */
                if ($lockedPayment->isPaid()) {
                    return true;
                }

                if (
                    $lockedPayment->isRefunded()
                ) {
                    throw new RuntimeException(
                        'Giao dịch đã hoàn tiền, không thể cập nhật lại thành paid.'
                    );
                }

                if (
                    $lockedPayment->payment_method
                    !== Payment::METHOD_QR
                ) {
                    throw new RuntimeException(
                        'Phương thức thanh toán không hợp lệ.'
                    );
                }

                $expectedAmount =
                    (int) round(
                        (float) $lockedPayment
                            ->amount
                    );

                if (
                    $paidAmount
                    !== $expectedAmount
                ) {
                    throw new RuntimeException(
                        'Số tiền thanh toán payOS không khớp với đơn hàng.'
                    );
                }

                if (
                    filled(
                        $lockedPayment
                            ->payos_payment_link_id
                    )
                    && $lockedPayment
                        ->payos_payment_link_id
                        !== $paymentLinkId
                ) {
                    throw new RuntimeException(
                        'Payment Link ID không khớp.'
                    );
                }

                $lockedPayment->update([
                    'status' => Payment::STATUS_PAID,

                    'transaction_code' => $reference,

                    'payos_payment_link_id' => $paymentLinkId,

                    'response_code' => '00',

                    'paid_at' => now(),
                ]);

                $order = Order::query()
                    ->whereKey(
                        $lockedPayment->order_id
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                $order->update([
                    'payment_status' => Order::PAYMENT_PAID,
                ]);

                return true;
            }
        );
    }

    /**
     * Tạo orderCode duy nhất dạng số.
     */
    private function generateOrderCode(): int
    {
        do {
            $orderCode = (int) (
                now()->format('ymdHis')
                .random_int(100, 999)
            );
        } while (
            Payment::query()
                ->where(
                    'payos_order_code',
                    $orderCode
                )
                ->exists()
        );

        return $orderCode;
    }
}
