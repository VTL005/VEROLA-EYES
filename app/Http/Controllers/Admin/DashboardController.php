<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ChatConversation;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Dashboard Admin.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | TỔNG QUAN
        |--------------------------------------------------------------------------
        */

        $productCount =
            Product::query()
                ->count();


        $orderCount =
            Order::query()
                ->count();


        /*
         * Doanh thu hiện tại:
         * chỉ tính Order Completed.
         */
        $revenue =
            Order::query()
                ->where(
                    'order_status',
                    Order::STATUS_COMPLETED
                )
                ->sum('total');


        /*
         * Customer.
         */
        $customerCount =
            User::query()
                ->whereHas(
                    'role',
                    function ($query) {
                        $query->where(
                            'name',
                            'customer'
                        );
                    }
                )
                ->count();


        /*
         * Hội thoại đang chờ Admin tiếp nhận.
         */
        $waitingChatCount =
            ChatConversation::query()
                ->where('status', 'open')
                ->whereNull('admin_id')
                ->count();


        /*
        |--------------------------------------------------------------------------
        | CÔNG VIỆC CẦN XỬ LÝ
        |--------------------------------------------------------------------------
        */

        $pendingOrderCount =
            Order::query()
                ->where(
                    'order_status',
                    Order::STATUS_PENDING
                )
                ->count();


        $pendingAppointmentCount =
            Appointment::query()
                ->where(
                    'status',
                    Appointment::STATUS_PENDING
                )
                ->count();


        $todayAppointmentCount =
            Appointment::query()
                ->whereDate(
                    'appointment_date',
                    today()
                )
                ->whereNotIn(
                    'status',
                    [
                        Appointment::STATUS_CANCELLED,
                    ]
                )
                ->count();


        $reviewCount =
            Review::query()
                ->count();


        /*
        |--------------------------------------------------------------------------
        | ĐƠN HÀNG GẦN ĐÂY
        |--------------------------------------------------------------------------
        */

        $latestOrders =
            Order::query()
                ->with([
                    'payment',
                ])
                ->latest()
                ->limit(6)
                ->get();


        /*
        |--------------------------------------------------------------------------
        | LỊCH HẸN GẦN ĐÂY
        |--------------------------------------------------------------------------
        */

        $latestAppointments =
            Appointment::query()
                ->with([
                    'user',
                    'confirmer',
                ])
                ->latest()
                ->limit(6)
                ->get();


        return view(
            'admin.dashboard',
            compact(
                'productCount',
                'orderCount',
                'revenue',
                'customerCount',
                'waitingChatCount',
                'pendingOrderCount',
                'pendingAppointmentCount',
                'todayAppointmentCount',
                'reviewCount',
                'latestOrders',
                'latestAppointments'
            )
        );
    }
}
