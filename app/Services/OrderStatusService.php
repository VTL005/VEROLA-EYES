<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderStatusService
{
    /**
     * Luồng trạng thái dành cho Admin.
     *
     * Admin chỉ xác nhận đã giao hàng.
     * Customer mới là người xác nhận hoàn thành.
     */
    private array $allowedTransitions = [
        Order::STATUS_PENDING => [
            Order::STATUS_CONFIRMED,
        ],

        Order::STATUS_CONFIRMED => [
            Order::STATUS_PREPARING,
        ],

        Order::STATUS_PREPARING => [
            Order::STATUS_PACKED,
        ],

        Order::STATUS_PACKED => [
            Order::STATUS_SHIPPING,
        ],

        Order::STATUS_SHIPPING => [
            Order::STATUS_DELIVERED,
        ],

        Order::STATUS_DELIVERED => [],

        Order::STATUS_COMPLETED => [],

        Order::STATUS_CANCELLED => [],
    ];

    /**
     * Admin cập nhật trạng thái.
     */
    public function updateStatus(
        Order $order,
        string $newStatus,
        User $user
    ): Order {
        if (
            ! $user->isAdmin()
        ) {
            abort(403);
        }

        return DB::transaction(
            function () use (
                $order,
                $newStatus,
                $user
            ) {
                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder = Order::query()
                    ->where('id', $order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                |--------------------------------------------------------------------------
                | CHECK TRANSITION
                |--------------------------------------------------------------------------
                */

                $allowedStatuses = $this->allowedTransitions[
                    $lockedOrder->order_status
                ] ?? [];

                if (
                    ! in_array(
                        $newStatus,
                        $allowedStatuses,
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'order_status' => 'Không thể chuyển đơn hàng từ "'
                            .$this->statusLabel(
                                $lockedOrder->order_status
                            )
                            .'" sang "'
                            .$this->statusLabel(
                                $newStatus
                            )
                            .'".',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | ONLINE PAYMENT
                |--------------------------------------------------------------------------
                |
                | Đơn thanh toán online phải Paid trước khi
                | Admin xác nhận đã giao hàng.
                |
                */

                if (
                    $newStatus === Order::STATUS_DELIVERED
                    && $lockedOrder->payment_method !== 'cod'
                    && $lockedOrder->payment_status !== Order::PAYMENT_PAID
                ) {
                    throw ValidationException::withMessages([
                        'order_status' => 'Đơn hàng thanh toán online phải được thanh toán thành công trước khi xác nhận đã giao.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | UPDATE ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder->order_status = $newStatus;
                $lockedOrder->save();

                /*
                |--------------------------------------------------------------------------
                | TIMELINE
                |--------------------------------------------------------------------------
                */

                $lockedOrder
                    ->statusHistories()
                    ->create([
                        'status' => $newStatus,

                        'description' => $this->statusDescription(
                            $newStatus
                        ),

                        'updated_by' => $user->id,
                    ]);

                return $lockedOrder->fresh([
                    'details',
                    'payment',
                    'statusHistories',
                ]);
            }
        );
    }

    /**
     * Customer xác nhận đã nhận được hàng.
     */
    public function confirmReceived(
        Order $order,
        User $customer
    ): Order {
        if (! $customer->isCustomer()) {
            abort(403);
        }

        if ((int) $order->user_id !== (int) $customer->id) {
            abort(403);
        }

        return DB::transaction(
            function () use (
                $order,
                $customer
            ) {
                $lockedOrder = Order::query()
                    ->where('id', $order->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    (int) $lockedOrder->user_id
                    !== (int) $customer->id
                ) {
                    abort(403);
                }

                if (
                    $lockedOrder->order_status
                    !== Order::STATUS_DELIVERED
                ) {
                    throw ValidationException::withMessages([
                        'order_status' => 'Chỉ có thể xác nhận khi đơn hàng đã được giao và đang chờ bạn xác nhận.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | ONLINE PAYMENT
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedOrder->payment_method !== 'cod'
                    && $lockedOrder->payment_status !== Order::PAYMENT_PAID
                ) {
                    throw ValidationException::withMessages([
                        'order_status' => 'Đơn hàng thanh toán online chưa được ghi nhận thanh toán thành công.',
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | COD: CUSTOMER NHẬN HÀNG => PAID
                |--------------------------------------------------------------------------
                */

                if ($lockedOrder->payment_method === 'cod') {
                    $lockedOrder->payment_status =
                        Order::PAYMENT_PAID;

                    $payment = $lockedOrder
                        ->payment()
                        ->lockForUpdate()
                        ->first();

                    if ($payment) {
                        $payment->update([
                            'status' => Order::PAYMENT_PAID,
                            'paid_at' => $payment->paid_at ?? now(),
                        ]);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | CUSTOMER HOÀN THÀNH ĐƠN
                |--------------------------------------------------------------------------
                */

                $lockedOrder->order_status =
                    Order::STATUS_COMPLETED;

                $lockedOrder->save();

                $lockedOrder
                    ->statusHistories()
                    ->create([
                        'status' => Order::STATUS_COMPLETED,

                        'description' => $this->statusDescription(
                            Order::STATUS_COMPLETED
                        ),

                        'updated_by' => $customer->id,
                    ]);

                return $lockedOrder->fresh([
                    'details',
                    'payment',
                    'statusHistories',
                ]);
            }
        );
    }

    /**
     * Trạng thái tiếp theo hợp lệ dành cho Admin.
     */
    public function nextStatuses(
        Order $order
    ): array {
        return $this->allowedTransitions[
            $order->order_status
        ] ?? [];
    }

    /**
     * Tên tiếng Việt của trạng thái.
     */
    public function statusLabel(
        string $status
    ): string {
        return match ($status) {
            Order::STATUS_PENDING => 'Chờ xác nhận',

            Order::STATUS_CONFIRMED => 'Đã xác nhận',

            Order::STATUS_PREPARING => 'Đang chuẩn bị',

            Order::STATUS_PACKED => 'Đã đóng gói',

            Order::STATUS_SHIPPING => 'Đang giao',

            Order::STATUS_DELIVERED => 'Đã giao - chờ khách xác nhận',

            Order::STATUS_COMPLETED => 'Hoàn thành',

            Order::STATUS_CANCELLED => 'Đã hủy',

            default => $status,
        };
    }

    /**
     * Nội dung Timeline.
     */
    private function statusDescription(
        string $status
    ): string {
        return match ($status) {
            Order::STATUS_CONFIRMED => 'Đơn hàng đã được xác nhận.',

            Order::STATUS_PREPARING => 'Đơn hàng đang được chuẩn bị.',

            Order::STATUS_PACKED => 'Đơn hàng đã được đóng gói và sẵn sàng giao.',

            Order::STATUS_SHIPPING => 'Đơn hàng đã được bàn giao cho đơn vị giao hàng.',

            Order::STATUS_DELIVERED => 'Đơn hàng đã được giao và đang chờ khách hàng xác nhận.',

            Order::STATUS_COMPLETED => 'Khách hàng đã xác nhận nhận được hàng. Đơn hàng đã hoàn thành.',

            Order::STATUS_CANCELLED => 'Đơn hàng đã bị hủy.',

            default => 'Trạng thái đơn hàng đã được cập nhật.',
        };
    }
}
