<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\EyePrescription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EyePrescriptionService
{
    /**
     * Staff/Admin tạo hồ sơ thị lực
     * từ một Appointment.
     */
    public function create(
        User $operator,
        Appointment $appointment,
        array $data
    ): EyePrescription {
        /*
         * Chỉ Staff/Admin.
         */
        if (
            !$operator->isStaff()
            && !$operator->isAdmin()
        ) {
            abort(403);
        }


        return DB::transaction(
            function () use (
                $operator,
                $appointment,
                $data
            ) {
                /*
                |--------------------------------------------------------------------------
                | LOCK APPOINTMENT
                |--------------------------------------------------------------------------
                */

                $lockedAppointment =
                    Appointment::query()
                        ->where(
                            'id',
                            $appointment->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | KIỂM TRA TRẠNG THÁI
                |--------------------------------------------------------------------------
                |
                | Chỉ nhập kết quả khi lịch đã Confirmed
                | hoặc Completed.
                |
                */

                if (
                    !in_array(
                        $lockedAppointment->status,
                        [
                            Appointment::STATUS_CONFIRMED,
                            Appointment::STATUS_COMPLETED,
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'appointment' =>
                            'Chỉ có thể tạo hồ sơ thị lực cho lịch đã được xác nhận hoặc đã hoàn thành.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | TẠO EYE PRESCRIPTION
                |--------------------------------------------------------------------------
                |
                | user_id lấy từ Appointment.
                | performed_by lấy từ người đang thao tác.
                |
                | Không nhận hai giá trị này từ form.
                |
                */

                $prescription =
                    EyePrescription::create([
                        'user_id' =>
                            $lockedAppointment->user_id,

                        'appointment_id' =>
                            $lockedAppointment->id,

                        'performed_by' =>
                            $operator->id,


                        'right_sph' =>
                            $data['right_sph']
                            ?? null,

                        'right_cyl' =>
                            $data['right_cyl']
                            ?? null,

                        'right_axis' =>
                            $data['right_axis']
                            ?? null,


                        'left_sph' =>
                            $data['left_sph']
                            ?? null,

                        'left_cyl' =>
                            $data['left_cyl']
                            ?? null,

                        'left_axis' =>
                            $data['left_axis']
                            ?? null,


                        'pd' =>
                            $data['pd']
                            ?? null,

                        'exam_date' =>
                            $data['exam_date'],

                        'note' =>
                            $data['note']
                            ?? null,
                    ]);


                return $prescription->fresh([
                    'user',
                    'appointment',
                    'performer',
                ]);
            }
        );
    }
}