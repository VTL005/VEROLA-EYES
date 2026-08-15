<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    /**
     * Danh sách lịch hẹn.
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


        $status =
            $request->query('status');


        $appointmentDate =
            $request->query(
                'appointment_date'
            );


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE STATUS
        |--------------------------------------------------------------------------
        */

        $validStatuses = [
            Appointment::STATUS_PENDING,
            Appointment::STATUS_CONFIRMED,
            Appointment::STATUS_COMPLETED,
            Appointment::STATUS_CANCELLED,
            Appointment::STATUS_NO_SHOW,
        ];


        if (
            $status
            && !in_array(
                $status,
                $validStatuses,
                true
            )
        ) {
            $status = null;
        }


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE DATE
        |--------------------------------------------------------------------------
        */

        if (
            $appointmentDate
            && !Validator::make(
                [
                    'appointment_date' =>
                        $appointmentDate,
                ],
                [
                    'appointment_date' =>
                        'date_format:Y-m-d',
                ]
            )->passes()
        ) {
            $appointmentDate = null;
        }


        /*
        |--------------------------------------------------------------------------
        | LIST
        |--------------------------------------------------------------------------
        */

        $appointments =
            Appointment::query()

                ->with([
                    'user',
                    'confirmer',
                ])

                ->when(
                    $keyword !== '',
                    function ($query) use ($keyword) {

                        $query->where(
                            function ($subQuery) use ($keyword) {

                                $subQuery
                                    ->where(
                                        'appointment_code',
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
                    $status,
                    fn ($query) =>
                        $query->where(
                            'status',
                            $status
                        )
                )

                ->when(
                    $appointmentDate,
                    fn ($query) =>
                        $query->whereDate(
                            'appointment_date',
                            $appointmentDate
                        )
                )

                ->orderBy(
                    'appointment_date'
                )

                ->orderBy(
                    'time_slot'
                )

                ->paginate(15)

                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $totalAppointments =
            Appointment::query()
                ->count();


        $pendingAppointments =
            Appointment::query()
                ->where(
                    'status',
                    Appointment::STATUS_PENDING
                )
                ->count();


        $confirmedAppointments =
            Appointment::query()
                ->where(
                    'status',
                    Appointment::STATUS_CONFIRMED
                )
                ->count();


        $todayAppointments =
            Appointment::query()
                ->whereDate(
                    'appointment_date',
                    today()
                )
                ->count();


        return view(
            'admin.appointments.index',
            compact(
                'appointments',
                'keyword',
                'status',
                'appointmentDate',
                'totalAppointments',
                'pendingAppointments',
                'confirmedAppointments',
                'todayAppointments'
            )
        );
    }


    /**
     * Chi tiết lịch hẹn.
     */
    public function show(
        Appointment $appointment,
        AppointmentService $appointmentService
    ) {
        $appointment->load([
            'user',
            'confirmer',

            'eyePrescriptions' =>
                function ($query) {

                    $query
                        ->with('performer')
                        ->orderByDesc('exam_date')
                        ->latest('id');
                },
        ]);


        $nextStatuses =
            $appointmentService
                ->nextStatuses(
                    $appointment
                );


        return view(
            'admin.appointments.show',
            compact(
                'appointment',
                'appointmentService',
                'nextStatuses'
            )
        );
    }


    /**
     * Admin cập nhật trạng thái.
     */
    public function updateStatus(
        Request $request,
        Appointment $appointment,
        AppointmentService $appointmentService
    ) {
        $validated =
            $request->validate(
                [
                    'status' => [
                        'required',
                        'string',

                        Rule::in([
                            Appointment::STATUS_CONFIRMED,
                            Appointment::STATUS_COMPLETED,
                            Appointment::STATUS_CANCELLED,
                            Appointment::STATUS_NO_SHOW,
                        ]),
                    ],
                ],
                [
                    'status.required' =>
                        'Vui lòng chọn trạng thái.',

                    'status.in' =>
                        'Trạng thái lịch hẹn không hợp lệ.',
                ]
            );


        /*
         * Service quyết định Transition thực sự
         * có hợp lệ hay không.
         */
        $appointmentService
            ->updateStatusByOperator(
                auth()->user(),
                $appointment,
                $validated['status']
            );


        return redirect()
            ->route(
                'admin.appointments.show',
                $appointment
            )
            ->with(
                'success',
                'Cập nhật trạng thái lịch hẹn thành công.'
            );
    }
}