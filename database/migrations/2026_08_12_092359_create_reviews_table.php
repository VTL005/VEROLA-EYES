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
        Schema::create('reviews', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            /*
             * Rating: 1 → 5 sao.
             */
            $table->unsignedTinyInteger('rating');

            /*
             * Theo yêu cầu VELORA:
             * tối đa 500 ký tự.
             *
             * Validation max:500 sẽ xử lý
             * ở Form Request.
             */
            $table->text('comment')
                ->nullable();

            /*
             * Staff/Admin có thể ẩn Review
             * thay vì xóa cứng.
             */
            $table->boolean('is_visible')
                ->default(true);

            $table->timestamps();

            $table->index([
                'product_id',
                'is_visible',
            ]);

            $table->index([
                'user_id',
                'created_at',
            ]);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};