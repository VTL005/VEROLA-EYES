<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    /**
     * Số lịch tối đa trong một khung giờ.
     */
    private const MAX_APPOINTMENTS_PER_SLOT = 5;


    /**
     * Customer tạo lịch hẹn.
     */
    public function create(
        User $user,
        array $data
    ): Appointment {
        return DB::transaction(
            function () use (
                $user,
                $data
            ) {
                $appointmentDate = Carbon::parse(
                    $data['appointment_date']
                )->startOfDay();


                /*
                |--------------------------------------------------------------------------
                | KHÔNG CHO CHỌN NGÀY QUÁ KHỨ
                |--------------------------------------------------------------------------
                */

                if (
                    $appointmentDate->isBefore(
                        today()
                    )
                ) {
                    throw ValidationException::withMessages([
                        'appointment_date' =>
                            'Không thể đặt lịch vào ngày trong quá khứ.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | KHÔNG CHO CHỌN GIỜ ĐÃ QUA
                |--------------------------------------------------------------------------
                */

                $slotStart =
                    $this->getSlotStartDateTime(
                        $appointmentDate,
                        $data['time_slot']
                    );


                if (
                    $appointmentDate->isToday()
                    && $slotStart->lessThanOrEqualTo(
                        now()
                    )
                ) {
                    throw ValidationException::withMessages([
                        'time_slot' =>
                            'Khung giờ này đã qua. Vui lòng chọn khung giờ khác.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | CUSTOMER KHÔNG ĐẶT TRÙNG THỜI ĐIỂM
                |--------------------------------------------------------------------------
                |
                | Chỉ Pending và Confirmed được xem
                | là lịch đang giữ chỗ.
                |
                */

                $duplicateAppointment =
                    Appointment::query()

                        ->where(
                            'user_id',
                            $user->id
                        )

                        ->whereDate(
                            'appointment_date',
                            $appointmentDate
                        )

                        ->where(
                            'time_slot',
                            $data['time_slot']
                        )

                        ->whereIn(
                            'status',
                            [
                                Appointment::STATUS_PENDING,
                                Appointment::STATUS_CONFIRMED,
                            ]
                        )

                        ->lockForUpdate()

                        ->exists();


                if ($duplicateAppointment) {
                    throw ValidationException::withMessages([
                        'time_slot' =>
                            'Bạn đã có lịch hẹn trong khung giờ này.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | KIỂM TRA SỨC CHỨA KHUNG GIỜ
                |--------------------------------------------------------------------------
                */

                $appointmentsInSlot =
                    Appointment::query()

                        ->whereDate(
                            'appointment_date',
                            $appointmentDate
                        )

                        ->where(
                            'time_slot',
                            $data['time_slot']
                        )

                        ->whereIn(
                            'status',
                            [
                                Appointment::STATUS_PENDING,
                                Appointment::STATUS_CONFIRMED,
                            ]
                        )

                        ->lockForUpdate()

                        ->get();


                if (
                    $appointmentsInSlot->count()
                    >= self::MAX_APPOINTMENTS_PER_SLOT
                ) {
                    throw ValidationException::withMessages([
                        'time_slot' =>
                            'Khung giờ này đã đủ lịch. Vui lòng chọn khung giờ khác.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | TẠO APPOINTMENT
                |--------------------------------------------------------------------------
                |
                | appointment_code là unique nên tạo mã tạm
                | trước khi biết ID.
                |
                */

                $appointment =
                    Appointment::create([
                        'appointment_code' =>
                            $this->temporaryCode(),

                        'user_id' =>
                            $user->id,

                        'customer_name' =>
                            trim(
                                $data['customer_name']
                            ),

                        'email' =>
                            trim(
                                $data['email']
                            ),

                        'phone' =>
                            trim(
                                $data['phone']
                            ),

                        'appointment_date' =>
                            $appointmentDate,

                        'time_slot' =>
                            $data['time_slot'],

                        'service_type' =>
                            $data['service_type'],

                        'note' =>
                            $data['note'] ?? null,

                        'status' =>
                            Appointment::STATUS_PENDING,

                        'confirmed_by' =>
                            null,

                        'confirmed_at' =>
                            null,

                        'reminder_sent_at' =>
                            null,
                    ]);


                /*
                |--------------------------------------------------------------------------
                | SINH MÃ LỊCH CHÍNH THỨC
                |--------------------------------------------------------------------------
                |
                | Ví dụ:
                | APT-VLR-000001
                |
                */

                $appointment->update([
                    'appointment_code' =>
                        'APT-VLR-'
                        . str_pad(
                            (string) $appointment->id,
                            6,
                            '0',
                            STR_PAD_LEFT
                        ),
                ]);


                return $appointment->fresh([
                    'user',
                    'confirmer',
                ]);
            }
        );
    }


    /**
     * Customer hủy lịch của chính mình.
     */
    public function cancelByCustomer(
        User $user,
        Appointment $appointment
    ): Appointment {
        return DB::transaction(
            function () use (
                $user,
                $appointment
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
                | CHỈ CHỦ LỊCH
                |--------------------------------------------------------------------------
                */

                if (
                    $lockedAppointment->user_id
                    !== $user->id
                ) {
                    abort(403);
                }


                /*
                |--------------------------------------------------------------------------
                | CHỈ PENDING / CONFIRMED
                |--------------------------------------------------------------------------
                */

                if (
                    !$lockedAppointment
                        ->isCancellableByCustomer()
                ) {
                    throw ValidationException::withMessages([
                        'appointment' =>
                            'Lịch hẹn này không thể hủy.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | KHÔNG HỦY LỊCH ĐÃ BẮT ĐẦU
                |--------------------------------------------------------------------------
                */

                $appointmentDate =
                    Carbon::parse(
                        $lockedAppointment
                            ->appointment_date
                    );


                $slotStart =
                    $this->getSlotStartDateTime(
                        $appointmentDate,
                        $lockedAppointment
                            ->time_slot
                    );


                if (
                    $slotStart->lessThanOrEqualTo(
                        now()
                    )
                ) {
                    throw ValidationException::withMessages([
                        'appointment' =>
                            'Không thể hủy vì thời gian lịch hẹn đã bắt đầu hoặc đã qua.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | CANCEL
                |--------------------------------------------------------------------------
                */

                $lockedAppointment->update([
                    'status' =>
                        Appointment::STATUS_CANCELLED,
                ]);


                return $lockedAppointment->fresh([
                    'user',
                    'confirmer',
                ]);
            }
        );
    }


    /**
     * Staff/Admin cập nhật trạng thái lịch.
     */
    public function updateStatusByOperator(
        User $user,
        Appointment $appointment,
        string $newStatus
    ): Appointment {
        /*
         * Chỉ Staff/Admin.
         */
        if (
            !$user->isStaff()
            && !$user->isAdmin()
        ) {
            abort(403);
        }


        return DB::transaction(
            function () use (
                $user,
                $appointment,
                $newStatus
            ) {
                /*
                |--------------------------------------------------------------------------
                | LOCK
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
                | KIỂM TRA LUỒNG
                |--------------------------------------------------------------------------
                */

                $allowedStatuses =
                    $this->nextStatuses(
                        $lockedAppointment
                    );


                if (
                    !in_array(
                        $newStatus,
                        $allowedStatuses,
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'status' =>
                            'Không thể chuyển lịch hẹn từ "'
                            . $this->statusLabel(
                                $lockedAppointment->status
                            )
                            . '" sang "'
                            . $this->statusLabel(
                                $newStatus
                            )
                            . '".',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | STAFF XÁC NHẬN LỊCH
                |--------------------------------------------------------------------------
                */

                if (
                    $newStatus
                    === Appointment::STATUS_CONFIRMED
                ) {
                    $lockedAppointment->update([
                        'status' =>
                            Appointment::STATUS_CONFIRMED,

                        'confirmed_by' =>
                            $user->id,

                        'confirmed_at' =>
                            now(),
                    ]);


                    return $lockedAppointment->fresh([
                        'user',
                        'confirmer',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | COMPLETED / CANCELLED / NO SHOW
                |--------------------------------------------------------------------------
                */

                $lockedAppointment->update([
                    'status' =>
                        $newStatus,
                ]);


                return $lockedAppointment->fresh([
                    'user',
                    'confirmer',
                ]);
            }
        );
    }


    /**
     * Trạng thái tiếp theo Staff được phép chọn.
     */
    public function nextStatuses(
        Appointment $appointment
    ): array {
        return match (
            $appointment->status
        ) {
            Appointment::STATUS_PENDING => [
                Appointment::STATUS_CONFIRMED,
                Appointment::STATUS_CANCELLED,
            ],

            Appointment::STATUS_CONFIRMED => [
                Appointment::STATUS_COMPLETED,
                Appointment::STATUS_NO_SHOW,
                Appointment::STATUS_CANCELLED,
            ],

            default => [],
        };
    }


    /**
     * Label loại dịch vụ.
     */
    public function serviceLabel(
        string $serviceType
    ): string {
        return match (
            $serviceType
        ) {
            Appointment::SERVICE_EYE_EXAM =>
                'Đo mắt cận',

            Appointment::SERVICE_RECHECK =>
                'Kiểm tra lại độ kính',

            Appointment::SERVICE_LENS_CONSULTATION =>
                'Tư vấn tròng kính',

            Appointment::SERVICE_FRAME_CONSULTATION =>
                'Tư vấn chọn gọng',

            default =>
                $serviceType,
        };
    }


    /**
     * Label trạng thái.
     */
    public function statusLabel(
        string $status
    ): string {
        return match (
            $status
        ) {
            Appointment::STATUS_PENDING =>
                'Chờ xác nhận',

            Appointment::STATUS_CONFIRMED =>
                'Đã xác nhận',

            Appointment::STATUS_COMPLETED =>
                'Hoàn thành',

            Appointment::STATUS_CANCELLED =>
                'Đã hủy',

            Appointment::STATUS_NO_SHOW =>
                'Không đến',

            default =>
                $status,
        };
    }


    /**
     * Lấy thời điểm bắt đầu của Time Slot.
     *
     * Ví dụ:
     *
     * 2026-08-15
     * 09:00-10:00
     *
     * =>
     *
     * 2026-08-15 09:00:00
     */
    private function getSlotStartDateTime(
        Carbon $appointmentDate,
        string $timeSlot
    ): Carbon {
        $parts =
            explode(
                '-',
                $timeSlot,
                2
            );


        $startTime =
            $parts[0] ?? '00:00';


        return Carbon::parse(
            $appointmentDate
                ->format('Y-m-d')
            . ' '
            . $startTime
        );
    }


    /**
     * Mã tạm để thỏa unique appointment_code.
     */
    private function temporaryCode(): string
    {
        return 'TMP-APPOINTMENT-'
            . now()->format(
                'YmdHisv'
            )
            . '-'
            . random_int(
                1000,
                9999
            );
    }
}