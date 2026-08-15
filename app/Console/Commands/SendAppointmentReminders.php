<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminderMail;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendAppointmentReminders extends Command
{
    /**
     * Tên command.
     */
    protected $signature =
        'appointments:send-reminders';


    /**
     * Mô tả command.
     */
    protected $description =
        'Gửi email nhắc lịch hẹn VELORA Eyes trong vòng 24 giờ tới';


    public function handle(): int
    {
        /*
         * Thời điểm hiện tại.
         */
        $now = now();


        /*
         * Chỉ nhắc các lịch
         * trong vòng 24 giờ tới.
         */
        $until =
            $now
                ->copy()
                ->addHours(24);


        /*
         * Lấy Appointment:
         *
         * - confirmed
         * - chưa gửi reminder
         * - ngày hẹn nằm trong vùng cần kiểm tra
         */
        $appointments =
            Appointment::query()

                ->where(
                    'status',
                    Appointment::STATUS_CONFIRMED
                )

                ->whereNull(
                    'reminder_sent_at'
                )

                ->whereBetween(
                    'appointment_date',
                    [
                        $now
                            ->copy()
                            ->startOfDay()
                            ->toDateString(),

                        $until
                            ->copy()
                            ->endOfDay()
                            ->toDateString(),
                    ]
                )

                ->orderBy(
                    'appointment_date'
                )

                ->get();


        $sent = 0;

        $failed = 0;


        foreach (
            $appointments
            as $appointment
        ) {

            /*
             * time_slot dự kiến dạng:
             *
             * 08:00 - 09:00
             *
             * hoặc:
             *
             * 08:00-09:00
             */
            $timeSlot =
                trim(
                    $appointment->time_slot
                );


            /*
             * Lấy giờ bắt đầu.
             */
            if (
                !preg_match(
                    '/^(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/',
                    $timeSlot,
                    $matches
                )
            ) {

                $this->warn(
                    'Bỏ qua lịch '
                    . $appointment->appointment_code
                    . ' vì time_slot không hợp lệ: '
                    . $timeSlot
                );

                continue;
            }


            $startTime =
                $matches[1];


            /*
             * Ghép ngày hẹn + giờ bắt đầu.
             */
            $appointmentStart =
                Carbon::createFromFormat(
                    'Y-m-d H:i',
                    $appointment
                        ->appointment_date
                        ->format('Y-m-d')
                    . ' '
                    . $startTime,
                    config('app.timezone')
                );


            /*
             * Không gửi nếu lịch đã qua.
             */
            if (
                $appointmentStart
                    ->lessThanOrEqualTo(
                        $now
                    )
            ) {
                continue;
            }


            /*
             * Không gửi nếu còn quá 24 giờ.
             */
            if (
                $appointmentStart
                    ->greaterThan(
                        $until
                    )
            ) {
                continue;
            }


            try {

                Mail::to(
                    $appointment->email
                )->send(
                    new AppointmentReminderMail(
                        $appointment
                    )
                );


                /*
                 * Chỉ đánh dấu sau khi
                 * Email gửi thành công.
                 */
                $appointment->update([
                    'reminder_sent_at' =>
                        now(),
                ]);


                $sent++;


                $this->info(
                    'Đã gửi nhắc lịch: '
                    . $appointment->appointment_code
                );

            } catch (
                Throwable $exception
            ) {

                $failed++;


                Log::error(
                    'Không thể gửi email nhắc lịch hẹn.',
                    [
                        'appointment_id' =>
                            $appointment->id,

                        'appointment_code' =>
                            $appointment
                                ->appointment_code,

                        'email' =>
                            $appointment->email,

                        'error' =>
                            $exception
                                ->getMessage(),
                    ]
                );


                $this->error(
                    'Gửi thất bại: '
                    . $appointment
                        ->appointment_code
                );
            }
        }


        $this->newLine();


        $this->info(
            'Hoàn tất.'
        );


        $this->line(
            'Đã gửi: '
            . $sent
        );


        $this->line(
            'Thất bại: '
            . $failed
        );


        return self::SUCCESS;
    }
}