<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tạo bảng lưu Voucher
     * mà Customer đã thu thập.
     */
    public function up(): void
    {
        Schema::create(
            'user_vouchers',
            function (Blueprint $table) {
                $table->id();

                /*
                 * Customer đã lưu Voucher.
                 */
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();

                /*
                 * Voucher được lưu.
                 */
                $table->foreignId('voucher_id')
                    ->constrained('vouchers')
                    ->cascadeOnDelete();

                $table->timestamps();

                /*
                 * Mỗi Customer chỉ được lưu
                 * một Voucher một lần.
                 */
                $table->unique([
                    'user_id',
                    'voucher_id',
                ]);
            }
        );
    }

    /**
     * Xóa bảng khi rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'user_vouchers'
        );
    }
};