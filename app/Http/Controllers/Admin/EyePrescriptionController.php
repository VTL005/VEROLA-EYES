<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEyePrescriptionRequest;
use App\Models\Appointment;
use App\Models\EyePrescription;
use App\Services\EyePrescriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EyePrescriptionController extends Controller
{
    /**
     * Danh sách hồ sơ đo mắt.
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


        $examDate =
            $request->query(
                'exam_date'
            );


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE DATE
        |--------------------------------------------------------------------------
        */

        if (
            $examDate
            && !Validator::make(
                [
                    'exam_date' => $examDate,
                ],
                [
                    'exam_date' =>
                        'date_format:Y-m-d',
                ]
            )->passes()
        ) {
            $examDate = null;
        }


        /*
        |--------------------------------------------------------------------------
        | LIST
        |--------------------------------------------------------------------------
        */

        $prescriptions =
            EyePrescription::query()

                ->with([
                    'user',
                    'appointment',
                    'performer',
                ])

                ->when(
                    $keyword !== '',
                    function ($query) use ($keyword) {

                        $query->where(
                            function ($subQuery) use ($keyword) {

                                $subQuery
                                    ->whereHas(
                                        'user',
                                        function ($userQuery) use ($keyword) {

                                            $userQuery
                                                ->where(
                                                    'name',
                                                    'like',
                                                    "%{$keyword}%"
                                                )

                                                ->orWhere(
                                                    'email',
                                                    'like',
                                                    "%{$keyword}%"
                                                )

                                                ->orWhere(
                                                    'phone',
                                                    'like',
                                                    "%{$keyword}%"
                                                );
                                        }
                                    )

                                    ->orWhereHas(
                                        'appointment',
                                        function ($appointmentQuery) use ($keyword) {

                                            $appointmentQuery
                                                ->where(
                                                    'appointment_code',
                                                    'like',
                                                    "%{$keyword}%"
                                                )

                                                ->orWhere(
                                                    'customer_name',
                                                    'like',
                                                    "%{$keyword}%"
                                                );
                                        }
                                    );
                            }
                        );
                    }
                )

                ->when(
                    $examDate,
                    fn ($query) =>
                        $query->whereDate(
                            'exam_date',
                            $examDate
                        )
                )

                ->orderByDesc(
                    'exam_date'
                )

                ->latest('id')

                ->paginate(15)

                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $totalPrescriptions =
            EyePrescription::query()
                ->count();


        $todayPrescriptions =
            EyePrescription::query()
                ->whereDate(
                    'exam_date',
                    today()
                )
                ->count();


        $monthPrescriptions =
            EyePrescription::query()
                ->whereYear(
                    'exam_date',
                    now()->year
                )
                ->whereMonth(
                    'exam_date',
                    now()->month
                )
                ->count();


        return view(
            'admin.eye-prescriptions.index',
            compact(
                'prescriptions',
                'keyword',
                'examDate',
                'totalPrescriptions',
                'todayPrescriptions',
                'monthPrescriptions'
            )
        );
    }


    /**
     * Form nhập kết quả đo mắt
     * từ Appointment.
     */
    public function create(
        Appointment $appointment
    ) {
        if (
            !in_array(
                $appointment->status,
                [
                    Appointment::STATUS_CONFIRMED,
                    Appointment::STATUS_COMPLETED,
                ],
                true
            )
        ) {
            return redirect()
                ->route(
                    'admin.appointments.show',
                    $appointment
                )
                ->with(
                    'error',
                    'Chỉ có thể nhập kết quả đo mắt cho lịch hẹn đã xác nhận hoặc đã hoàn thành.'
                );
        }


        $appointment->load([
            'user',
        ]);


        return view(
            'admin.eye-prescriptions.create',
            compact('appointment')
        );
    }


    /**
     * Lưu kết quả đo mắt.
     */
    public function store(
        StoreEyePrescriptionRequest $request,
        Appointment $appointment,
        EyePrescriptionService $eyePrescriptionService
    ) {
        $eyePrescription =
            $eyePrescriptionService->create(
                auth()->user(),
                $appointment,
                $request->validated()
            );


        return redirect()
            ->route(
                'admin.eye-prescriptions.show',
                $eyePrescription
            )
            ->with(
                'success',
                'Lưu kết quả đo mắt thành công.'
            );
    }


    /**
     * Chi tiết hồ sơ.
     */
    public function show(
        EyePrescription $eyePrescription
    ) {
        $eyePrescription->load([
            'user',
            'appointment',
            'performer',
        ]);


        return view(
            'admin.eye-prescriptions.show',
            compact('eyePrescription')
        );
    }
}