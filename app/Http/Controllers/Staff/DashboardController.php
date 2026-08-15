<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | THỐNG KÊ NHANH
        |--------------------------------------------------------------------------
        */

        $totalProducts =
            Product::query()->count();


        $pendingOrders =
            Order::query()
                ->where(
                    'order_status',
                    Order::STATUS_PENDING
                )
                ->count();


        $pendingAppointments =
            Appointment::query()
                ->where(
                    'status',
                    Appointment::STATUS_PENDING
                )
                ->count();


        $totalReviews =
            Review::query()->count();


        /*
        |--------------------------------------------------------------------------
        | DỮ LIỆU GẦN ĐÂY
        |--------------------------------------------------------------------------
        */

        $recentOrders =
            Order::query()
                ->latest()
                ->limit(5)
                ->get();


        $recentAppointments =
            Appointment::query()
                ->latest()
                ->limit(5)
                ->get();


        return view(
            'staff.dashboard',
            compact(
                'totalProducts',
                'pendingOrders',
                'pendingAppointments',
                'totalReviews',
                'recentOrders',
                'recentAppointments'
            )
        );
    }
}