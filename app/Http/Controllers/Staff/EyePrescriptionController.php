<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEyePrescriptionRequest;
use App\Models\Appointment;
use App\Models\EyePrescription;
use App\Services\EyePrescriptionService;

class EyePrescriptionController extends Controller
{
    /**
     * Form nhập kết quả đo mắt.
     */
    public function create(
        Appointment $appointment
    ) {
        $appointment->load([
            'user',
            'confirmer',
            'eyePrescriptions',
        ]);


        /*
         * Chỉ mở form khi lịch
         * Confirmed hoặc Completed.
         */
        abort_unless(
            in_array(
                $appointment->status,
                [
                    Appointment::STATUS_CONFIRMED,
                    Appointment::STATUS_COMPLETED,
                ],
                true
            ),
            403
        );


        return view(
            'staff.eye-prescriptions.create',
            compact('appointment')
        );
    }


    /**
     * Lưu hồ sơ thị lực.
     */
    public function store(
        StoreEyePrescriptionRequest $request,
        Appointment $appointment,
        EyePrescriptionService $eyePrescriptionService
    ) {
        $prescription =
            $eyePrescriptionService
                ->create(
                    auth()->user(),
                    $appointment,
                    $request->validated()
                );


        return redirect()
            ->route(
                'staff.eye-prescriptions.show',
                $prescription
            )
            ->with(
                'success',
                'Lưu hồ sơ thị lực thành công.'
            );
    }


    /**
     * Staff xem hồ sơ thị lực.
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
            'staff.eye-prescriptions.show',
            compact('eyePrescription')
        );
    }
}