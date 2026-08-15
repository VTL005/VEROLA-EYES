<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderCancellationService;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng.
     */
    public function index(
        Request $request
    ) {
        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );


        $orderStatus =
            $request->query(
                'order_status'
            );


        $paymentStatus =
            $request->query(
                'payment_status'
            );


        $paymentMethod =
            $request->query(
                'payment_method'
            );


        $validOrderStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PREPARING,
            Order::STATUS_PACKED,
            Order::STATUS_SHIPPING,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ];


        $validPaymentStatuses = [
            Order::PAYMENT_UNPAID,
            Order::PAYMENT_PENDING,
            Order::PAYMENT_PAID,
            Order::PAYMENT_FAILED,
            Order::PAYMENT_REFUNDED,
        ];


        if (
            $orderStatus
            && !in_array(
                $orderStatus,
                $validOrderStatuses,
                true
            )
        ) {
            $orderStatus = null;
        }


        if (
            $paymentStatus
            && !in_array(
                $paymentStatus,
                $validPaymentStatuses,
                true
            )
        ) {
            $paymentStatus = null;
        }


        if (
            $paymentMethod
            && !in_array(
                $paymentMethod,
                [
                    'cod',
                    'qr',
                    'vnpay',
                ],
                true
            )
        ) {
            $paymentMethod = null;
        }


        /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */

        $orders =
            Order::query()

                ->with([
                    'payment',
                ])

                ->when(
                    $keyword !== '',
                    function ($query) use ($keyword) {

                        $query->where(
                            function ($subQuery) use ($keyword) {

                                $subQuery
                                    ->where(
                                        'order_code',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhere(
                                        'customer_name',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhere(
                                        'phone',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$keyword}%"
                                    );
                            }
                        );
                    }
                )

                ->when(
                    $orderStatus,
                    fn ($query) =>
                        $query->where(
                            'order_status',
                            $orderStatus
                        )
                )

                ->when(
                    $paymentStatus,
                    fn ($query) =>
                        $query->where(
                            'payment_status',
                            $paymentStatus
                        )
                )

                ->when(
                    $paymentMethod,
                    fn ($query) =>
                        $query->where(
                            'payment_method',
                            $paymentMethod
                        )
                )

                ->latest()

                ->paginate(15)

                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $totalOrders =
            Order::query()
                ->count();


        $pendingOrders =
            Order::query()
                ->where(
                    'order_status',
                    Order::STATUS_PENDING
                )
                ->count();


        $shippingOrders =
            Order::query()
                ->where(
                    'order_status',
                    Order::STATUS_SHIPPING
                )
                ->count();


        $completedOrders =
            Order::query()
                ->where(
                    'order_status',
                    Order::STATUS_COMPLETED
                )
                ->count();


        return view(
            'admin.orders.index',
            compact(
                'orders',
                'keyword',
                'orderStatus',
                'paymentStatus',
                'paymentMethod',
                'totalOrders',
                'pendingOrders',
                'shippingOrders',
                'completedOrders'
            )
        );
    }


    /**
     * Chi tiết đơn hàng.
     */
    public function show(
        Order $order,
        OrderStatusService $orderStatusService
    ) {
        $order->load([
            'details',
            'payment',

            'statusHistories' =>
                function ($query) {

                    $query->oldest();
                },

            'statusHistories.updater',

            'details.warranty',
        ]);


        $nextStatuses =
            $orderStatusService
                ->nextStatuses(
                    $order
                );


        return view(
            'admin.orders.show',
            compact(
                'order',
                'nextStatuses',
                'orderStatusService'
            )
        );
    }


    /**
     * Cập nhật trạng thái.
     */
    public function updateStatus(
        Request $request,
        Order $order,
        OrderStatusService $orderStatusService
    ) {
        $validated =
            $request->validate(
                [
                    'order_status' => [
                        'required',
                        'string',

                        Rule::in([
                            Order::STATUS_CONFIRMED,
                            Order::STATUS_PREPARING,
                            Order::STATUS_PACKED,
                            Order::STATUS_SHIPPING,
                            Order::STATUS_COMPLETED,
                        ]),
                    ],
                ],
                [
                    'order_status.required' =>
                        'Vui lòng chọn trạng thái đơn hàng.',

                    'order_status.in' =>
                        'Trạng thái đơn hàng không hợp lệ.',
                ]
            );


        $orderStatusService
            ->updateStatus(
                $order,
                $validated['order_status'],
                auth()->user()
            );


        return redirect()
            ->route(
                'admin.orders.show',
                $order
            )
            ->with(
                'success',
                'Cập nhật trạng thái đơn hàng thành công.'
            );
    }


    /**
     * Admin hủy đơn Pending.
     */
    public function cancel(
        Order $order,
        OrderCancellationService $cancellationService
    ) {
        $cancellationService
            ->cancelByOperator(
                auth()->user(),
                $order
            );


        return redirect()
            ->route(
                'admin.orders.show',
                $order
            )
            ->with(
                'success',
                'Hủy đơn hàng thành công. Tồn kho đã được hoàn lại.'
            );
    }
}