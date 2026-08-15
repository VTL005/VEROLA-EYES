<?php

namespace App\Http\Controllers;

use App\Models\Warranty;
use App\Services\WarrantyService;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    /**
     * Danh sách bảo hành điện tử của Customer.
     */
    public function index(
        Request $request,
        WarrantyService $warrantyService
    ) {
        $user = auth()->user();


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );


        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */

        $status = $request->query('status');


        $allowedStatuses = [
            Warranty::STATUS_ACTIVE,
            Warranty::STATUS_EXPIRED,
            Warranty::STATUS_CANCELLED,
        ];


        if (
            $status
            && !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            $status = null;
        }


        /*
        |--------------------------------------------------------------------------
        | QUERY
        |--------------------------------------------------------------------------
        */

        $warranties = Warranty::query()

            /*
             * Customer chỉ xem
             * Warranty của chính mình.
             */
            ->where(
                'user_id',
                $user->id
            )


            /*
             * Load dữ liệu phục vụ giao diện.
             */
            ->with([
                'product',
                'orderDetail.order',
            ])


            /*
             * Search theo mã Warranty.
             */
            ->when(
                $keyword !== '',
                function ($query) use ($keyword) {

                    $query->where(
                        'warranty_code',
                        'like',
                        "%{$keyword}%"
                    );
                }
            )


            /*
             * Còn hiệu lực.
             */
            ->when(
                $status === Warranty::STATUS_ACTIVE,
                function ($query) {

                    $query
                        ->where(
                            'status',
                            Warranty::STATUS_ACTIVE
                        )
                        ->whereDate(
                            'end_date',
                            '>=',
                            today()
                        );
                }
            )


            /*
             * Hết hạn.
             *
             * Kiểm tra cả status DB
             * và ngày hết hạn thực tế.
             */
            ->when(
                $status === Warranty::STATUS_EXPIRED,
                function ($query) {

                    $query
                        ->where(
                            'status',
                            '!=',
                            Warranty::STATUS_CANCELLED
                        )
                        ->where(
                            function ($subQuery) {

                                $subQuery
                                    ->where(
                                        'status',
                                        Warranty::STATUS_EXPIRED
                                    )
                                    ->orWhereDate(
                                        'end_date',
                                        '<',
                                        today()
                                    );
                            }
                        );
                }
            )


            /*
             * Đã hủy.
             */
            ->when(
                $status === Warranty::STATUS_CANCELLED,
                function ($query) {

                    $query->where(
                        'status',
                        Warranty::STATUS_CANCELLED
                    );
                }
            )


            ->latest()

            ->paginate(10)

            ->withQueryString();


        return view(
            'warranties.index',
            compact(
                'warranties',
                'warrantyService',
                'keyword',
                'status'
            )
        );
    }


    /**
     * Customer xem chi tiết bảo hành.
     */
    public function show(
        Warranty $warranty,
        WarrantyService $warrantyService
    ) {
        /*
         * Chỉ chủ sở hữu Warranty
         * mới được xem.
         */
        abort_if(
            $warranty->user_id
                !== auth()->id(),
            403
        );


        $warranty->load([
            'product',
            'orderDetail.order',
        ]);


        return view(
            'warranties.show',
            compact(
                'warranty',
                'warrantyService'
            )
        );
    }


    /**
     * Form tra cứu bảo hành công khai.
     */
    public function lookupForm()
    {
        return view(
            'warranties.lookup'
        );
    }


    /**
     * Xử lý tra cứu bảo hành.
     */
    public function lookup(
        Request $request,
        WarrantyService $warrantyService
    ) {
        $validated = $request->validate(
            [
                'warranty_code' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ],
            [
                'warranty_code.required' =>
                    'Vui lòng nhập mã bảo hành.',

                'warranty_code.max' =>
                    'Mã bảo hành không được vượt quá 50 ký tự.',
            ]
        );


        /*
         * Chuẩn hóa mã:
         *
         * bh-vlr-000001
         * =>
         * BH-VLR-000001
         */
        $code = strtoupper(
            trim(
                $validated['warranty_code']
            )
        );


        /*
         * Public lookup chỉ load Product.
         *
         * Không load User để tránh
         * làm lộ dữ liệu Customer.
         */
        $warranty = Warranty::query()
            ->with([
                'product',
            ])
            ->where(
                'warranty_code',
                $code
            )
            ->first();


        if (!$warranty) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Không tìm thấy mã bảo hành.'
                );
        }


        return view(
            'warranties.lookup-result',
            compact(
                'warranty',
                'warrantyService'
            )
        );
    }
}