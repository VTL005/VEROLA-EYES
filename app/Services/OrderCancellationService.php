<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderCancellationService
{
    /**
     * Customer hủy đơn của chính mình.
     */
    public function cancel(
        User $user,
        Order $order
    ): Order {
        if (
            $order->user_id
            !== $user->id
        ) {
            abort(403);
        }


        return $this->performCancellation(
            $user,
            $order,
            'Khách hàng đã hủy đơn hàng.'
        );
    }


    /**
     * Staff/Admin hủy đơn.
     */
    public function cancelByOperator(
        User $user,
        Order $order
    ): Order {
        if (
            !$user->isStaff()
            && !$user->isAdmin()
        ) {
            abort(403);
        }


        $description =
            $user->isAdmin()
                ? 'Admin đã hủy đơn hàng.'
                : 'Nhân viên đã hủy đơn hàng.';


        return $this->performCancellation(
            $user,
            $order,
            $description
        );
    }


    /**
     * Logic hủy đơn dùng chung.
     */
    private function performCancellation(
        User $user,
        Order $order,
        string $description
    ): Order {
        return DB::transaction(
            function () use (
                $user,
                $order,
                $description
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder =
                    Order::query()
                        ->where(
                            'id',
                            $order->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | CHỈ HỦY KHI PENDING
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedOrder->order_status
                    !== Order::STATUS_PENDING
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'Chỉ có thể hủy đơn hàng khi đơn đang ở trạng thái Chờ xác nhận.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | ĐƠN ĐÃ PAID KHÔNG ĐƯỢC HỦY TRỰC TIẾP
                |--------------------------------------------------------------------------
                |
                | Đơn online đã thanh toán phải đi qua Refund.
                |
                */

                if (
                    $lockedOrder->payment_status
                    === Order::PAYMENT_PAID
                ) {
                    throw ValidationException::withMessages([
                        'order' =>
                            'Đơn hàng đã thanh toán. Cần xử lý hoàn tiền trước khi hủy đơn.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER DETAILS
                |--------------------------------------------------------------------------
                */

                $details =
                    $lockedOrder
                        ->details()
                        ->lockForUpdate()
                        ->get();


                /*
                |--------------------------------------------------------------------------
                | HOÀN TỒN KHO
                |--------------------------------------------------------------------------
                |
                | stock_restored_at giúp chống hoàn kho lặp.
                |
                */

                if (
                    $lockedOrder->stock_restored_at
                    === null
                ) {

                    foreach (
                        $details
                        as $detail
                    ) {

                        $variant =
                            ProductVariant::query()
                                ->where(
                                    'id',
                                    $detail->variant_id
                                )
                                ->lockForUpdate()
                                ->first();


                        if (!$variant) {

                            throw ValidationException::withMessages([
                                'order' =>
                                    'Không thể hoàn tồn kho vì một biến thể của đơn hàng không còn tồn tại.',
                            ]);
                        }


                        $variant->increment(
                            'stock_quantity',
                            (int) $detail->quantity
                        );
                    }


                    $lockedOrder->stock_restored_at =
                        now();
                }


                /*
                |--------------------------------------------------------------------------
                | CANCEL ORDER
                |--------------------------------------------------------------------------
                */

                $lockedOrder->order_status =
                    Order::STATUS_CANCELLED;


                $lockedOrder->save();


                /*
                |--------------------------------------------------------------------------
                | TIMELINE
                |--------------------------------------------------------------------------
                */

                $lockedOrder
                    ->statusHistories()
                    ->create([
                        'status' =>
                            Order::STATUS_CANCELLED,

                        'description' =>
                            $description,

                        'updated_by' =>
                            $user->id,
                    ]);


                return $lockedOrder->fresh([
                    'details',
                    'payment',
                    'statusHistories',
                ]);
            }
        );
    }
}