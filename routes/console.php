<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


/*
|--------------------------------------------------------------------------
| Artisan Command mẫu của Laravel
|--------------------------------------------------------------------------
*/

Artisan::command(
    'inspire',
    function () {

        $this->comment(
            Inspiring::quote()
        );
    }
)->purpose(
    'Display an inspiring quote'
);


/*
|--------------------------------------------------------------------------
| VELORA - APPOINTMENT REMINDER
|--------------------------------------------------------------------------
|
| Mỗi giờ kiểm tra lịch đã Confirmed.
|
| Nếu lịch còn trong vòng 24 giờ
| và chưa gửi reminder thì gửi Email.
|
*/

Schedule::command(
    'appointments:send-reminders'
)
    ->hourly()
    ->withoutOverlapping();


/*
|--------------------------------------------------------------------------
| VELORA - AUTO COMPLETE DELIVERED ORDERS
|--------------------------------------------------------------------------
|
| Mỗi giờ kiểm tra các đơn đã giao.
| Sau 4 ngày nếu khách chưa xác nhận nhận hàng,
| hệ thống tự động chuyển đơn sang hoàn thành.
|
*/

Schedule::command(
    'orders:auto-complete-delivered'
)
    ->hourly()
    ->withoutOverlapping();
