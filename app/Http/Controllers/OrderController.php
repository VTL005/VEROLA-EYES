<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderCancellationService;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng của Customer.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | LẤY TRẠNG THÁI TỪ URL
        |--------------------------------------------------------------------------
        */

        $status = $request->query('status');

        /*
        |--------------------------------------------------------------------------
        | DANH SÁCH TRẠNG THÁI HỢP LỆ
        |--------------------------------------------------------------------------
        */

        $allowedStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PREPARING,
            Order::STATUS_PACKED,
            Order::STATUS_SHIPPING,
            Order::STATUS_DELIVERED,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ];

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA STATUS
        |--------------------------------------------------------------------------
        */

        if (
            $status
            && ! in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            $status = null;
        }

        /*
        |--------------------------------------------------------------------------
        | LẤY ĐƠN HÀNG
        |--------------------------------------------------------------------------
        */

        $orders = Order::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->when(
                $status,
                function ($query) use ($status) {
                    $query->where(
                        'order_status',
                        $status
                    );
                }
            )
            ->with([
                'payment',

                'details' => function ($query) {
                    $query->orderBy('id');
                },
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'orders.index',
            compact(
                'orders',
                'status'
            )
        );
    }

    /**
     * Chi tiết đơn hàng.
     */
    public function show(
        Order $order
    ) {
        $this->ensureOwnership(
            $order
        );

        $order->load([
            'details',

            'payment',

            'statusHistories' => function ($query) {
                $query->oldest();
            },

            'statusHistories.updater',
        ]);

        return view(
            'orders.show',
            compact('order')
        );
    }

    /**
     * Customer xác nhận đã nhận được hàng.
     */
    public function confirmReceived(
        Order $order,
        OrderStatusService $orderStatusService
    ) {
        $this->ensureOwnership(
            $order
        );

        $orderStatusService->confirmReceived(
            $order,
            auth()->user()
        );

        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'success',
                'Cảm ơn bạn đã xác nhận nhận hàng. Đơn hàng đã hoàn thành.'
            );
    }

    /**
     * Customer hủy Order.
     */
    public function cancel(
        Order $order,
        OrderCancellationService $cancellationService
    ) {
        $this->ensureOwnership(
            $order
        );

        $cancellationService->cancel(
            auth()->user(),
            $order
        );

        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'success',
                'Hủy đơn hàng thành công. Tồn kho đã được hoàn lại.'
            );
    }

    /**
     * Kiểm tra quyền sở hữu Order.
     */
    private function ensureOwnership(
        Order $order
    ): void {
        abort_if(
            (int) $order->user_id
                !== (int) auth()->id(),
            403
        );
    }
}
