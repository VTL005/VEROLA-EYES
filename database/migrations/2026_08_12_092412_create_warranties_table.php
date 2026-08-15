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
        Schema::create('warranties', function (Blueprint $table) {

            $table->id();

            /*
             * Ví dụ:
             * BH-VLR-000128
             */
            $table->string(
                'warranty_code',
                50
            )
            ->unique();

            /*
             * Customer sở hữu bảo hành.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Sản phẩm cụ thể trong đơn hàng.
             */
            $table->foreignId('order_detail_id')
                ->constrained('order_details')
                ->restrictOnDelete();

            /*
             * Giữ liên kết trực tiếp tới Product
             * để tra cứu/quản trị thuận tiện.
             */
            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->date('start_date');

            $table->date('end_date');

            /*
             * active
             * expired
             * cancelled
             *
             * Validation sẽ giới hạn giá trị.
             */
            $table->string(
                'status',
                30
            )
            ->default('active');

            /*
             * Nội dung / chính sách bảo hành
             * áp dụng tại thời điểm phát hành.
             */
            $table->text('warranty_content')
                ->nullable();

            $table->timestamps();


            $table->index([
                'user_id',
                'status',
            ]);

            $table->index('order_detail_id');

            $table->index([
                'start_date',
                'end_date',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranties');
    }
};