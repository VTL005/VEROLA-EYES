<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            /*
             * Mã đơn hàng dạng số gửi sang payOS.
             *
             * Đây là mã do hệ thống VELORA tạo,
             * không phải mã giao dịch ngân hàng.
             */
            $table->unsignedBigInteger(
                'payos_order_code'
            )
                ->nullable()
                ->unique()
                ->after('transaction_code');

            /*
             * ID của link thanh toán do payOS trả về.
             */
            $table->string(
                'payos_payment_link_id',
                100
            )
                ->nullable()
                ->unique()
                ->after('payos_order_code');

            /*
             * URL trang thanh toán payOS.
             *
             * Lưu lại để khi khách tải lại trang,
             * hệ thống mở đúng link cũ thay vì
             * tạo thêm một giao dịch mới.
             */
            $table->text(
                'payos_checkout_url'
            )
                ->nullable()
                ->after('payos_payment_link_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique([
                'payos_order_code',
            ]);

            $table->dropUnique([
                'payos_payment_link_id',
            ]);

            $table->dropColumn([
                'payos_order_code',
                'payos_payment_link_id',
                'payos_checkout_url',
            ]);
        });
    }
};
