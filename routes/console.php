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