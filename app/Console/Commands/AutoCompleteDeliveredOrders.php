<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AutoCompleteDeliveredOrders extends Command
{
    /**
     * Tên lệnh Artisan.
     */
    protected $signature = 'orders:auto-complete-delivered';

    /**
     * Mô tả lệnh.
     */
    protected $description =
        'Tự động hoàn thành đơn hàng sau 4 ngày kể từ khi giao thành công';

    /**
     * Thực thi lệnh.
     */
    public function handle(): int
    {
        $deadline = now()->subDays(4);

        $completedCount = 0;
        $skippedCount = 0;

        Order::query()
            ->where(
                'order_status',
                Order::STATUS_DELIVERED
            )
            ->whereHas(
                'statusHistories',
                function ($query) use ($deadline) {
                    $query
                        ->where(
                            'status',
                            Order::STATUS_DELIVERED
                        )
                        ->where(
                            'created_at',
                            '<=',
                            $deadline
                        );
                }
            )
            ->select('id')
            ->orderBy('id')
            ->chunkById(
                100,
                function ($orders) use (
                    $deadline,
                    &$completedCount,
                    &$skippedCount
                ) {
                    foreach ($orders as $candidate) {
                        $wasCompleted = DB::transaction(
                            function () use (
                                $candidate,
                                $deadline
                            ): bool {
                                $order = Order::query()
                                    ->whereKey($candidate->id)
                                    ->lockForUpdate()
                                    ->first();

                                /*
                                 * Có thể trạng thái vừa được thay đổi
                                 * bởi một tiến trình khác.
                                 */
                                if (
                                    ! $order
                                    || $order->order_status
                                        !== Order::STATUS_DELIVERED
                                ) {
                                    return false;
                                }

                                /*
                                 * Lấy lần gần nhất đơn được chuyển sang
                                 * trạng thái đã giao.
                                 */
                                $deliveredAt = $order
                                    ->statusHistories()
                                    ->where(
                                        'status',
                                        Order::STATUS_DELIVERED
                                    )
                                    ->latest('created_at')
                                    ->value('created_at');

                                if (
                                    ! $deliveredAt
                                    || Carbon::parse($deliveredAt)
                                        ->greaterThan($deadline)
                                ) {
                                    return false;
                                }

                                /*
                                 * Đơn thanh toán online chỉ được hoàn thành
                                 * khi đã thanh toán thành công.
                                 */
                                if (
                                    $order->payment_method !== 'cod'
                                    && $order->payment_status
                                        !== Order::PAYMENT_PAID
                                ) {
                                    return false;
                                }

                                /*
                                 * Với COD, sau khi hết thời gian chờ xác nhận
                                 * thì hệ thống ghi nhận đơn đã thanh toán.
                                 */
                                if ($order->payment_method === 'cod') {
                                    $order->payment_status =
                                        Order::PAYMENT_PAID;

                                    $payment = $order
                                        ->payment()
                                        ->lockForUpdate()
                                        ->first();

                                    if ($payment) {
                                        $payment->update([
                                            'status' =>
                                                Order::PAYMENT_PAID,

                                            'paid_at' =>
                                                $payment->paid_at
                                                ?? now(),
                                        ]);
                                    }
                                }

                                $order->order_status =
                                    Order::STATUS_COMPLETED;

                                $order->save();

                                $order
                                    ->statusHistories()
                                    ->create([
                                        'status' =>
                                            Order::STATUS_COMPLETED,

                                        'description' =>
                                            'Hệ thống tự động hoàn thành đơn hàng sau 4 ngày kể từ khi giao thành công.',

                                        /*
                                         * NULL vì đây là thay đổi tự động,
                                         * không do Customer/Admin/Admin.
                                         */
                                        'updated_by' => null,
                                    ]);

                                return true;
                            }
                        );

                        if ($wasCompleted) {
                            $completedCount++;
                        } else {
                            $skippedCount++;
                        }
                    }
                }
            );

        $this->info(
            "Đã tự động hoàn thành {$completedCount} đơn hàng."
        );

        if ($skippedCount > 0) {
            $this->warn(
                "Đã bỏ qua {$skippedCount} đơn không còn đủ điều kiện."
            );
        }

        return self::SUCCESS;
    }
}