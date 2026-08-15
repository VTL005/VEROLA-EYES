<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarrantyRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Warranty;
use App\Services\WarrantyService;

class WarrantyController extends Controller
{
    /**
     * Form tạo bảo hành.
     */
    public function create(
        OrderDetail $orderDetail
    ) {
        $orderDetail->load([
            'order.user',
            'product',
            'warranty',
        ]);


        /*
         * Chỉ đơn Completed mới được cấp bảo hành.
         */
        if (
            !$orderDetail->order
            || $orderDetail
                ->order
                ->order_status
                !== Order::STATUS_COMPLETED
        ) {
            abort(403);
        }


        /*
         * Nếu đã có Warranty,
         * đưa Staff thẳng tới hồ sơ hiện tại.
         */
        $existingWarranty =
            Warranty::query()
                ->where(
                    'order_detail_id',
                    $orderDetail->id
                )
                ->first();


        if ($existingWarranty) {
            return redirect()
                ->route(
                    'staff.warranties.show',
                    $existingWarranty
                );
        }


        return view(
            'staff.warranties.create',
            compact(
                'orderDetail'
            )
        );
    }


    /**
     * Lưu bảo hành.
     */
    public function store(
        StoreWarrantyRequest $request,
        OrderDetail $orderDetail,
        WarrantyService $warrantyService
    ) {
        $warranty =
            $warrantyService->create(
                auth()->user(),
                $orderDetail,
                $request->validated()[
                    'warranty_content'
                ] ?? null
            );


        return redirect()
            ->route(
                'staff.warranties.show',
                $warranty
            )
            ->with(
                'success',
                'Tạo bảo hành điện tử thành công.'
            );
    }


    /**
     * Staff xem bảo hành.
     */
    public function show(
        Warranty $warranty,
        WarrantyService $warrantyService
    ) {
        $warranty->load([
            'user',
            'product',
            'orderDetail.order',
        ]);


        return view(
            'staff.warranties.show',
            compact(
                'warranty',
                'warrantyService'
            )
        );
    }
}