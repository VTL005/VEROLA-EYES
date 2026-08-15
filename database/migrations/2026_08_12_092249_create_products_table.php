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
        Schema::create('products', function (Blueprint $table) {

            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            /*
             * Thông tin cơ bản
             */
            $table->string('name', 150);

            $table->string('slug', 180)
                ->unique();

            $table->string('sku', 100)
                ->unique();

            /*
             * Giá
             */
            $table->decimal('price', 15, 2);

            $table->decimal('sale_price', 15, 2)
                ->nullable();

            /*
             * Thông tin kính
             */
            $table->string('material', 100)
                ->nullable();

            $table->string('shape', 100)
                ->nullable();

            $table->string('gender', 50)
                ->nullable();

            $table->string('dimensions', 100)
                ->nullable();

            /*
             * Nội dung
             */
            $table->text('description')
                ->nullable();

            $table->text('highlights')
                ->nullable();

            /*
             * Hỗ trợ chức năng gợi ý kính.
             *
             * Đây là phần mở rộng kỹ thuật để triển khai
             * yêu cầu gợi ý theo khuôn mặt và phong cách.
             */
            $table->json('recommended_face_shapes')
                ->nullable();

            $table->json('style_tags')
                ->nullable();

            /*
             * Trạng thái kinh doanh
             */
            $table->boolean('is_active')
                ->default(true);

            /*
             * Không xóa cứng Product đã có lịch sử Order.
             */
            $table->softDeletes();

            $table->timestamps();

            /*
             * Index cho Search / Filter
             */
            $table->index('category_id');
            $table->index('material');
            $table->index('shape');
            $table->index('gender');
            $table->index('is_active');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};