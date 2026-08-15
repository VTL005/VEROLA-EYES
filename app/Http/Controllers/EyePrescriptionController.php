<?php

namespace App\Http\Controllers;

use App\Models\EyePrescription;
use Illuminate\Http\Request;

class EyePrescriptionController extends Controller
{
    /**
     * Danh sách hồ sơ đo mắt của Customer.
     */
    public function index(Request $request)
    {
        $user = auth()->user();


        $prescriptions = EyePrescription::query()

            /*
             * Customer chỉ được xem
             * hồ sơ đo mắt của chính mình.
             */
            ->where(
                'user_id',
                $user->id
            )


            /*
             * Load thông tin Appointment
             * và nhân viên thực hiện.
             */
            ->with([
                'appointment',
                'performer',
            ])


            /*
             * Có thể lọc theo ngày đo.
             */
            ->when(
                $request->filled('exam_date'),
                function ($query) use ($request) {

                    $query->whereDate(
                        'exam_date',
                        $request->exam_date
                    );
                }
            )


            /*
             * Kết quả mới nhất lên trước.
             */
            ->orderByDesc('exam_date')
            ->latest('id')


            /*
             * Phân trang.
             */
            ->paginate(10)
            ->withQueryString();


        return view(
            'eye-prescriptions.index',
            compact('prescriptions')
        );
    }


    /**
     * Customer xem chi tiết hồ sơ đo mắt.
     */
    public function show(
        EyePrescription $eyePrescription
    ) {
        /*
         * Không cho Customer xem
         * hồ sơ của người khác.
         */
        abort_if(
            $eyePrescription->user_id
                !== auth()->id(),
            403
        );


        /*
         * Load dữ liệu liên quan.
         */
        $eyePrescription->load([
            'appointment',
            'performer',
        ]);


        return view(
            'eye-prescriptions.show',
            compact('eyePrescription')
        );
    }
}