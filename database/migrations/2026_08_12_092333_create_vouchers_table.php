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
        Schema::create('vouchers', function (Blueprint $table) {

            $table->id();

            /*
             * Mã Voucher.
             *
             * Ví dụ:
             * VELORA10
             * SALE100K
             */
            $table->string('code', 50)
                ->unique();

            /*
             * Loại giảm:
             *
             * percentage = giảm %
             * fixed      = giảm số tiền cố định
             */
            $table->string('discount_type', 20);

            /*
             * Giá trị giảm.
             *
             * percentage:
             * 10 = giảm 10%
             *
             * fixed:
             * 100000 = giảm 100.000đ
             */
            $table->decimal(
                'discount_value',
                15,
                2
            );

            /*
             * Giá trị đơn tối thiểu.
             */
            $table->decimal(
                'minimum_order_amount',
                15,
                2
            )
            ->default(0);

            /*
             * Thời gian hiệu lực.
             */
            $table->dateTime('starts_at');

            $table->dateTime('ends_at');

            /*
             * Tổng số lượt được phép sử dụng.
             *
             * NULL = không giới hạn.
             */
            $table->unsignedInteger(
                'usage_limit'
            )
            ->nullable();

            /*
             * Số lượt đã sử dụng.
             */
            $table->unsignedInteger(
                'usage_count'
            )
            ->default(0);

            /*
             * Admin có thể khóa Voucher.
             */
            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            /*
             * Hỗ trợ tìm Voucher hợp lệ nhanh hơn.
             */
            $table->index('is_active');

            $table->index([
                'starts_at',
                'ends_at',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};