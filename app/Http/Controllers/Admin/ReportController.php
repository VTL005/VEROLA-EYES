<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function index(
        Request $request
    ) {
        /*
        |--------------------------------------------------------------------------
        | DATE RANGE
        |--------------------------------------------------------------------------
        |
        | Mặc định: 30 ngày gần nhất.
        |
        */

        $fromDate =
            $request->query(
                'from_date',
                now()
                    ->subDays(29)
                    ->format('Y-m-d')
            );


        $toDate =
            $request->query(
                'to_date',
                now()->format('Y-m-d')
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDATE DATE
        |--------------------------------------------------------------------------
        */

        $validator =
            Validator::make(
                [
                    'from_date' =>
                        $fromDate,

                    'to_date' =>
                        $toDate,
                ],
                [
                    'from_date' => [
                        'required',
                        'date_format:Y-m-d',
                    ],

                    'to_date' => [
                        'required',
                        'date_format:Y-m-d',
                        'after_or_equal:from_date',
                    ],
                ],
                [
                    'from_date.required' =>
                        'Vui lòng chọn ngày bắt đầu.',

                    'from_date.date_format' =>
                        'Ngày bắt đầu không hợp lệ.',

                    'to_date.required' =>
                        'Vui lòng chọn ngày kết thúc.',

                    'to_date.date_format' =>
                        'Ngày kết thúc không hợp lệ.',

                    'to_date.after_or_equal' =>
                        'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.',
                ]
            );


        $validator->validate();


        $from =
            Carbon::createFromFormat(
                'Y-m-d',
                $fromDate
            )->startOfDay();


        $to =
            Carbon::createFromFormat(
                'Y-m-d',
                $toDate
            )->endOfDay();


        /*
         * Không cho một báo cáo quá dài.
         * Giúp trang không phải sinh quá nhiều
         * điểm dữ liệu cùng lúc.
         */
        if (
            $from->diffInDays($to)
            > 365
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'to_date' =>
                        'Khoảng thời gian báo cáo tối đa là 366 ngày.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | BASE ORDER QUERY
        |--------------------------------------------------------------------------
        */

        $ordersInPeriod =
            Order::query()
                ->whereBetween(
                    'created_at',
                    [
                        $from,
                        $to,
                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | TOTAL ORDERS
        |--------------------------------------------------------------------------
        */

        $totalOrders =
            (clone $ordersInPeriod)
                ->count();


        /*
        |--------------------------------------------------------------------------
        | COMPLETED
        |--------------------------------------------------------------------------
        */

        $completedOrders =
            (clone $ordersInPeriod)
                ->where(
                    'order_status',
                    Order::STATUS_COMPLETED
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | CANCELLED
        |--------------------------------------------------------------------------
        */

        $cancelledOrders =
            (clone $ordersInPeriod)
                ->where(
                    'order_status',
                    Order::STATUS_CANCELLED
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | REVENUE
        |--------------------------------------------------------------------------
        |
        | Chỉ Order Completed được tính doanh thu.
        |
        */

        $revenue =
            (float) (clone $ordersInPeriod)
                ->where(
                    'order_status',
                    Order::STATUS_COMPLETED
                )
                ->sum('total');


        /*
        |--------------------------------------------------------------------------
        | AVERAGE ORDER VALUE
        |--------------------------------------------------------------------------
        */

        $averageOrderValue =
            $completedOrders > 0
                ? $revenue
                    / $completedOrders
                : 0;


        /*
        |--------------------------------------------------------------------------
        | COMPLETION / CANCELLATION RATE
        |--------------------------------------------------------------------------
        */

        $completionRate =
            $totalOrders > 0
                ? round(
                    (
                        $completedOrders
                        / $totalOrders
                    ) * 100,
                    1
                )
                : 0;


        $cancellationRate =
            $totalOrders > 0
                ? round(
                    (
                        $cancelledOrders
                        / $totalOrders
                    ) * 100,
                    1
                )
                : 0;


        /*
        |--------------------------------------------------------------------------
        | TOTAL ITEMS SOLD
        |--------------------------------------------------------------------------
        |
        | Chỉ tính sản phẩm của Order Completed.
        |
        */

        $totalItemsSold =
            (int) OrderDetail::query()

                ->join(
                    'orders',
                    'orders.id',
                    '=',
                    'order_details.order_id'
                )

                ->where(
                    'orders.order_status',
                    Order::STATUS_COMPLETED
                )

                ->whereBetween(
                    'orders.created_at',
                    [
                        $from,
                        $to,
                    ]
                )

                ->sum(
                    'order_details.quantity'
                );


        /*
        |--------------------------------------------------------------------------
        | ORDER STATUS COUNTS
        |--------------------------------------------------------------------------
        */

        $rawStatusCounts =
            (clone $ordersInPeriod)

                ->select(
                    'order_status',
                    DB::raw(
                        'COUNT(*) as total'
                    )
                )

                ->groupBy(
                    'order_status'
                )

                ->pluck(
                    'total',
                    'order_status'
                );


        /*
         * Luôn trả đủ trạng thái,
         * kể cả khi số lượng bằng 0.
         */
        $orderStatusCounts = [

            Order::STATUS_PENDING =>
                (int) (
                    $rawStatusCounts[
                        Order::STATUS_PENDING
                    ] ?? 0
                ),

            Order::STATUS_CONFIRMED =>
                (int) (
                    $rawStatusCounts[
                        Order::STATUS_CONFIRMED
                    ] ?? 0
                ),

            Order::STATUS_PREPARING =>
                (int) (
                    $rawStatusCounts[
                        Order::STATUS_PREPARING
                    ] ?? 0
                ),

            Order::STATUS_PACKED =>
                (int) (
                    $rawStatusCounts[
                        Order::STATUS_PACKED
                    ] ?? 0
                ),

            Order::STATUS_SHIPPING =>
                (int) (
                    $rawStatusCounts[
                        Order::STATUS_SHIPPING
                    ] ?? 0
                ),

            Order::STATUS_COMPLETED =>
                (int) (
                    $rawStatusCounts[
                        Order::STATUS_COMPLETED
                    ] ?? 0
                ),

            Order::STATUS_CANCELLED =>
                (int) (
                    $rawStatusCounts[
                        Order::STATUS_CANCELLED
                    ] ?? 0
                ),
        ];


        /*
        |--------------------------------------------------------------------------
        | PAYMENT METHOD
        |--------------------------------------------------------------------------
        |
        | Phân bổ phương thức của toàn bộ Order
        | được tạo trong khoảng thời gian.
        |
        */

        $rawPaymentMethodCounts =
            (clone $ordersInPeriod)

                ->select(
                    'payment_method',
                    DB::raw(
                        'COUNT(*) as total'
                    )
                )

                ->groupBy(
                    'payment_method'
                )

                ->pluck(
                    'total',
                    'payment_method'
                );


        $paymentMethodCounts = [
            'cod' =>
                (int) (
                    $rawPaymentMethodCounts[
                        'cod'
                    ] ?? 0
                ),

            'qr' =>
                (int) (
                    $rawPaymentMethodCounts[
                        'qr'
                    ] ?? 0
                ),

            'vnpay' =>
                (int) (
                    $rawPaymentMethodCounts[
                        'vnpay'
                    ] ?? 0
                ),
        ];


        /*
        |--------------------------------------------------------------------------
        | TOP PRODUCTS
        |--------------------------------------------------------------------------
        |
        | Dùng dữ liệu snapshot OrderDetail.
        | Không lấy giá Product hiện tại.
        |
        */

        $topProducts =
            OrderDetail::query()

                ->join(
                    'orders',
                    'orders.id',
                    '=',
                    'order_details.order_id'
                )

                ->where(
                    'orders.order_status',
                    Order::STATUS_COMPLETED
                )

                ->whereBetween(
                    'orders.created_at',
                    [
                        $from,
                        $to,
                    ]
                )

                ->select([
                    'order_details.product_id',
                    'order_details.product_name',

                    DB::raw(
                        'SUM(order_details.quantity) as quantity_sold'
                    ),

                    DB::raw(
                        'SUM(order_details.subtotal) as revenue'
                    ),

                    DB::raw(
                        'COUNT(DISTINCT order_details.order_id) as order_count'
                    ),
                ])

                ->groupBy(
                    'order_details.product_id',
                    'order_details.product_name'
                )

                ->orderByDesc(
                    'quantity_sold'
                )

                ->limit(10)

                ->get();


        /*
        |--------------------------------------------------------------------------
        | DAILY REVENUE
        |--------------------------------------------------------------------------
        |
        | Chỉ Completed.
        |
        */

        $rawRevenueByDate =
            Order::query()

                ->where(
                    'order_status',
                    Order::STATUS_COMPLETED
                )

                ->whereBetween(
                    'created_at',
                    [
                        $from,
                        $to,
                    ]
                )

                ->selectRaw(
                    'DATE(created_at) as report_date'
                )

                ->selectRaw(
                    'COUNT(*) as order_count'
                )

                ->selectRaw(
                    'SUM(total) as revenue'
                )

                ->groupBy(
                    DB::raw(
                        'DATE(created_at)'
                    )
                )

                ->orderBy(
                    'report_date'
                )

                ->get()

                ->keyBy(
                    'report_date'
                );


        /*
        |--------------------------------------------------------------------------
        | FILL MISSING DATES
        |--------------------------------------------------------------------------
        |
        | Ngày không có doanh thu vẫn hiển thị 0
        | để biểu đồ không bị đứt quãng.
        |
        */

        $revenueByDate = collect();


        $cursor =
            $from->copy()
                ->startOfDay();


        $lastDate =
            $to->copy()
                ->startOfDay();


        while (
            $cursor->lte(
                $lastDate
            )
        ) {
            $dateKey =
                $cursor->format(
                    'Y-m-d'
                );


            $row =
                $rawRevenueByDate->get(
                    $dateKey
                );


            $revenueByDate->push(
                (object) [
                    'report_date' =>
                        $dateKey,

                    'order_count' =>
                        $row
                            ? (int) $row->order_count
                            : 0,

                    'revenue' =>
                        $row
                            ? (float) $row->revenue
                            : 0,
                ]
            );


            $cursor->addDay();
        }


        /*
        |--------------------------------------------------------------------------
        | MAX DAILY REVENUE
        |--------------------------------------------------------------------------
        |
        | Dùng để tính chiều rộng thanh biểu đồ CSS.
        |
        */

        $maxDailyRevenue =
            (float) (
                $revenueByDate
                    ->max('revenue')
                ?? 0
            );


        /*
        |--------------------------------------------------------------------------
        | RECENT COMPLETED ORDERS IN PERIOD
        |--------------------------------------------------------------------------
        */

        $recentCompletedOrders =
            Order::query()

                ->where(
                    'order_status',
                    Order::STATUS_COMPLETED
                )

                ->whereBetween(
                    'created_at',
                    [
                        $from,
                        $to,
                    ]
                )

                ->latest()

                ->limit(8)

                ->get();


        return view(
            'admin.reports.index',
            compact(
                'fromDate',
                'toDate',

                'totalOrders',
                'completedOrders',
                'cancelledOrders',

                'revenue',
                'averageOrderValue',
                'completionRate',
                'cancellationRate',
                'totalItemsSold',

                'orderStatusCounts',
                'paymentMethodCounts',

                'topProducts',
                'revenueByDate',
                'maxDailyRevenue',

                'recentCompletedOrders'
            )
        );
    }
}