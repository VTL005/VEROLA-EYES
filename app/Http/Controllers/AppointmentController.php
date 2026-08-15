<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Danh sách lịch hẹn của Customer.
     */
    public function index(
        Request $request,
        AppointmentService $appointmentService
    ) {
        $user = auth()->user();


        $appointments = Appointment::query()

            /*
             * Customer chỉ được xem
             * lịch hẹn của chính mình.
             */
            ->where(
                'user_id',
                $user->id
            )

            /*
             * Lọc theo trạng thái nếu có.
             */
            ->when(
                $request->filled('status'),
                function ($query) use ($request) {

                    $query->where(
                        'status',
                        $request->status
                    );
                }
            )

            /*
             * Lịch mới / ngày gần nhất lên trước.
             */
            ->orderByDesc(
                'appointment_date'
            )
            ->orderByDesc(
                'time_slot'
            )

            ->paginate(10)

            ->withQueryString();


        return view(
            'appointments.index',
            compact(
                'appointments',
                'appointmentService'
            )
        );
    }


    /**
     * Form Customer đặt lịch.
     */
    public function create()
    {
        $user = auth()->user();


        return view(
            'appointments.create',
            compact('user')
        );
    }


    /**
     * Lưu lịch hẹn.
     */
    public function store(
        StoreAppointmentRequest $request,
        AppointmentService $appointmentService
    ) {
        $appointment = $appointmentService
            ->create(
                auth()->user(),
                $request->validated()
            );


        return redirect()
            ->route(
                'appointments.show',
                $appointment
            )
            ->with(
                'success',
                'Đặt lịch đo mắt thành công. Lịch đang chờ xác nhận.'
            );
    }


    /**
     * Chi tiết lịch hẹn.
     */
    public function show(
        Appointment $appointment,
        AppointmentService $appointmentService
    ) {
        /*
         * Customer chỉ được xem
         * Appointment của chính mình.
         */
        abort_if(
            $appointment->user_id
                !== auth()->id(),
            403
        );


        $appointment->load([
            'confirmer',
        ]);


        return view(
            'appointments.show',
            compact(
                'appointment',
                'appointmentService'
            )
        );
    }


    /**
     * Customer hủy lịch.
     */
    public function cancel(
        Appointment $appointment,
        AppointmentService $appointmentService
    ) {
        $appointmentService
            ->cancelByCustomer(
                auth()->user(),
                $appointment
            );


        return redirect()
            ->route(
                'appointments.show',
                $appointment
            )
            ->with(
                'success',
                'Hủy lịch hẹn thành công.'
            );
    }
}