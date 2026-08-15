<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Chuyển dữ liệu trạng thái cũ
     * processing -> preparing.
     */
    public function up(): void
    {
        /*
         * Order hiện tại.
         */
        DB::table('orders')
            ->where(
                'order_status',
                'processing'
            )
            ->update([
                'order_status' =>
                    'preparing',
            ]);


        /*
         * Timeline lịch sử Order.
         */
        DB::table('order_status_histories')
            ->where(
                'status',
                'processing'
            )
            ->update([
                'status' =>
                    'preparing',
            ]);
    }


    /**
     * Không tự động đổi preparing
     * ngược lại processing.
     *
     * Vì sau migration có thể xuất hiện
     * các Order preparing hợp lệ mới.
     */
    public function down(): void
    {
        //
    }
};