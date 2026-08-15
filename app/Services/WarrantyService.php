<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use App\Models\Warranty;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WarrantyService
{
    /**
     * Staff/Admin tạo bảo hành điện tử
     * cho sản phẩm thuộc đơn hàng đã hoàn thành.
     */
    public function create(
        User $operator,
        OrderDetail $orderDetail,
        ?string $warrantyContent = null
    ): Warranty {
        /*
        |--------------------------------------------------------------------------
        | CHỈ STAFF / ADMIN
        |--------------------------------------------------------------------------
        */

        if (
            !$operator->isStaff()
            && !$operator->isAdmin()
        ) {
            abort(403);
        }


        return DB::transaction(
            function () use (
                $orderDetail,
                $warrantyContent
            ) {

                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER DETAIL
                |--------------------------------------------------------------------------
                */

                $lockedDetail =
                    OrderDetail::query()
                        ->where(
                            'id',
                            $orderDetail->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | LOCK ORDER
                |--------------------------------------------------------------------------
                */

                $order =
                    Order::query()
                        ->where(
                            'id',
                            $lockedDetail->order_id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                |--------------------------------------------------------------------------
                | ORDER PHẢI COMPLETED
                |--------------------------------------------------------------------------
                */

                if (
                    $order->order_status
                    !== Order::STATUS_COMPLETED
                ) {
                    throw ValidationException::withMessages([
                        'warranty' =>
                            'Chỉ có thể cấp bảo hành cho sản phẩm thuộc đơn hàng đã hoàn thành.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | MỖI ORDER DETAIL CHỈ CÓ 1 WARRANTY
                |--------------------------------------------------------------------------
                */

                $existingWarranty =
                    Warranty::query()
                        ->where(
                            'order_detail_id',
                            $lockedDetail->id
                        )
                        ->lockForUpdate()
                        ->first();


                if ($existingWarranty) {
                    throw ValidationException::withMessages([
                        'warranty' =>
                            'Sản phẩm này đã có bảo hành điện tử.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | PHẢI XÁC ĐỊNH ĐƯỢC PRODUCT
                |--------------------------------------------------------------------------
                */

                if (!$lockedDetail->product_id) {
                    throw ValidationException::withMessages([
                        'warranty' =>
                            'Không xác định được sản phẩm cần bảo hành.',
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | THỜI HẠN 12 THÁNG
                |--------------------------------------------------------------------------
                */

                $startDate =
                    today();


                $endDate =
                    $startDate
                        ->copy()
                        ->addYear();


                /*
                |--------------------------------------------------------------------------
                | CREATE
                |--------------------------------------------------------------------------
                */

                $warranty =
                    Warranty::create([
                        'warranty_code' =>
                            $this->temporaryCode(),

                        'user_id' =>
                            $order->user_id,

                        'order_detail_id' =>
                            $lockedDetail->id,

                        'product_id' =>
                            $lockedDetail->product_id,

                        'start_date' =>
                            $startDate,

                        'end_date' =>
                            $endDate,

                        'status' =>
                            Warranty::STATUS_ACTIVE,

                        'warranty_content' =>
                            $warrantyContent
                                ? trim($warrantyContent)
                                : $this->defaultWarrantyContent(),
                    ]);


                /*
                |--------------------------------------------------------------------------
                | MÃ BẢO HÀNH
                |--------------------------------------------------------------------------
                |
                | Ví dụ:
                | BH-VLR-000001
                |
                */

                $warranty->update([
                    'warranty_code' =>
                        'BH-VLR-'
                        . str_pad(
                            (string) $warranty->id,
                            6,
                            '0',
                            STR_PAD_LEFT
                        ),
                ]);


                return $warranty->fresh([
                    'user',
                    'orderDetail.order',
                    'product',
                ]);
            }
        );
    }


    /**
     * Mã tạm để thỏa UNIQUE trước
     * khi Warranty có ID.
     */
    private function temporaryCode(): string
    {
        return 'TMP-WARRANTY-'
            . now()->format('YmdHisv')
            . '-'
            . random_int(
                1000,
                9999
            );
    }


    /**
     * Chính sách bảo hành mặc định.
     */
    private function defaultWarrantyContent(): string
    {
        return 'Bảo hành điện tử VELORA Eyes trong 12 tháng kể từ ngày kích hoạt. '
            . 'Bảo hành áp dụng theo chính sách của cửa hàng và tình trạng thực tế của sản phẩm.';
    }


    /**
     * Label trạng thái lưu trong DB.
     */
    public function statusLabel(
        string $status
    ): string {
        return match ($status) {

            Warranty::STATUS_ACTIVE =>
                'Còn hiệu lực',

            Warranty::STATUS_EXPIRED =>
                'Hết hạn',

            Warranty::STATUS_CANCELLED =>
                'Đã hủy',

            default =>
                $status,
        };
    }


    /**
     * Trạng thái thực tế theo ngày.
     */
    public function effectiveStatus(
        Warranty $warranty
    ): string {
        if (
            $warranty->status
            === Warranty::STATUS_CANCELLED
        ) {
            return Warranty::STATUS_CANCELLED;
        }


        if ($warranty->isExpired()) {
            return Warranty::STATUS_EXPIRED;
        }


        return Warranty::STATUS_ACTIVE;
    }


    /**
     * Label trạng thái thực tế.
     */
    public function effectiveStatusLabel(
        Warranty $warranty
    ): string {
        return $this->statusLabel(
            $this->effectiveStatus(
                $warranty
            )
        );
    }
}