<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
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


        $appointments =
            Appointment::query()

                ->with([
                    'user',
                    'confirmer',
                ])

                /*
                 * Search:
                 *
                 * - Mã lịch
                 * - Tên khách
                 * - SĐT
                 * - Email
                 */
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

                /*
                 * Filter trạng thái.
                 */
                ->when(
                    $status,
                    function ($query) use ($status) {

                        $query->where(
                            'status',
                            $status
                        );
                    }
                )

                /*
                 * Filter ngày.
                 */
                ->when(
                    $appointmentDate,
                    function ($query) use ($appointmentDate) {

                        $query->whereDate(
                            'appointment_date',
                            $appointmentDate
                        );
                    }
                )

                /*
                 * Lịch gần nhất trước.
                 */
                ->orderBy(
                    'appointment_date'
                )

                ->orderBy(
                    'time_slot'
                )

                ->paginate(15)

                ->withQueryString();


        /*
         * Thống kê nhanh.
         */
        $pendingCount =
            Appointment::query()
                ->where(
                    'status',
                    Appointment::STATUS_PENDING
                )
                ->count();


        $todayCount =
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


        $confirmedCount =
            Appointment::query()
                ->where(
                    'status',
                    Appointment::STATUS_CONFIRMED
                )
                ->count();


        return view(
            'staff.appointments.index',
            compact(
                'appointments',
                'keyword',
                'status',
                'appointmentDate',
                'pendingCount',
                'todayCount',
                'confirmedCount'
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

            'eyePrescriptions' => function ($query) {
                $query->latest();
            },
        ]);


        $nextStatuses =
            $appointmentService
                ->nextStatuses(
                    $appointment
                );


        return view(
            'staff.appointments.show',
            compact(
                'appointment',
                'appointmentService',
                'nextStatuses'
            )
        );
    }


    /**
     * Staff cập nhật trạng thái.
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


        $appointmentService
            ->updateStatusByOperator(
                auth()->user(),
                $appointment,
                $validated['status']
            );


        return redirect()
            ->route(
                'staff.appointments.show',
                $appointment
            )
            ->with(
                'success',
                'Cập nhật trạng thái lịch hẹn thành công.'
            );
    }
}