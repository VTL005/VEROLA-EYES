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
        Schema::create(
            'voucher_usages',
            function (Blueprint $table) {

                $table->id();

                /*
                 * Voucher đã sử dụng.
                 */
                $table->foreignId('voucher_id')
                    ->constrained('vouchers')
                    ->restrictOnDelete();

                /*
                 * Customer sử dụng Voucher.
                 */
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->restrictOnDelete();

                /*
                 * Order sử dụng Voucher.
                 *
                 * Tạm thời chưa tạo Foreign Key
                 * vì migration orders nằm phía sau.
                 */
                $table->unsignedBigInteger(
                    'order_id'
                )
                ->nullable();

                /*
                 * Snapshot số tiền được giảm
                 * tại thời điểm sử dụng.
                 */
                $table->decimal(
                    'discount_amount',
                    15,
                    2
                )
                ->default(0);

                $table->timestamps();


                /*
                 * Một Order chỉ ghi nhận
                 * một lần sử dụng Voucher.
                 */
                $table->unique(
                    'order_id'
                );

                $table->index([
                    'voucher_id',
                    'user_id',
                ]);

            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'voucher_usages'
        );
    }
};