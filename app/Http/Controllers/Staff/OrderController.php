<?php

namespace App\Http\Controllers\Staff;

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
    public function index(Request $request)
    {
        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );


        $orderStatus =
            $request->query('order_status');


        $paymentStatus =
            $request->query('payment_status');


        $paymentMethod =
            $request->query('payment_method');


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


        $validPaymentMethods = [
            'cod',
            'qr',
            'vnpay',
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
                $validPaymentMethods,
                true
            )
        ) {
            $paymentMethod = null;
        }


        $orders = Order::query()

            ->with([
                'payment',
            ])

            /*
             * Tìm theo:
             *
             * - Mã đơn
             * - Tên khách
             * - Số điện thoại
             * - Email
             */
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

            /*
             * Trạng thái đơn.
             */
            ->when(
                $orderStatus,
                function ($query) use ($orderStatus) {

                    $query->where(
                        'order_status',
                        $orderStatus
                    );
                }
            )

            /*
             * Trạng thái thanh toán.
             */
            ->when(
                $paymentStatus,
                function ($query) use ($paymentStatus) {

                    $query->where(
                        'payment_status',
                        $paymentStatus
                    );
                }
            )

            /*
             * Phương thức thanh toán.
             */
            ->when(
                $paymentMethod,
                function ($query) use ($paymentMethod) {

                    $query->where(
                        'payment_method',
                        $paymentMethod
                    );
                }
            )

            ->latest()

            ->paginate(15)

            ->withQueryString();


        /*
         * Thống kê nhanh.
         */
        $pendingCount = Order::query()
            ->where(
                'order_status',
                Order::STATUS_PENDING
            )
            ->count();


        $shippingCount = Order::query()
            ->where(
                'order_status',
                Order::STATUS_SHIPPING
            )
            ->count();


        $completedCount = Order::query()
            ->where(
                'order_status',
                Order::STATUS_COMPLETED
            )
            ->count();


        return view(
            'staff.orders.index',
            compact(
                'orders',
                'keyword',
                'orderStatus',
                'paymentStatus',
                'paymentMethod',
                'pendingCount',
                'shippingCount',
                'completedCount'
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
    'details.warranty',

    'payment',

    'statusHistories' =>
        function ($query) {

            $query->oldest();
        },

    'statusHistories.updater',
]);


        /*
         * Trạng thái tiếp theo được phép.
         */
        $nextStatuses =
            $orderStatusService
                ->nextStatuses($order);


        return view(
            'staff.orders.show',
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


        $orderStatusService->updateStatus(
            $order,
            $validated['order_status'],
            auth()->user()
        );


        return redirect()
            ->route(
                'staff.orders.show',
                $order
            )
            ->with(
                'success',
                'Cập nhật trạng thái đơn hàng thành công.'
            );
    }


    /**
     * Staff hủy đơn Pending.
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
                'staff.orders.show',
                $order
            )
            ->with(
                'success',
                'Hủy đơn hàng thành công. Tồn kho đã được hoàn lại.'
            );
    }
}