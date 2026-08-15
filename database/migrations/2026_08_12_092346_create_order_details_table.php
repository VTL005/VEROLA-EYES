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
            'order_details',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('order_id')
                    ->constrained('orders')
                    ->cascadeOnDelete();

                /*
                 * Giữ liên kết tới Product.
                 *
                 * Product đã có Order thì không
                 * được hard delete.
                 */
                $table->foreignId('product_id')
                    ->constrained('products')
                    ->restrictOnDelete();

                /*
                 * Tài liệu cho phép:
                 * Variant ID nếu có.
                 */
                $table->foreignId('variant_id')
                    ->nullable()
                    ->constrained('product_variants')
                    ->restrictOnDelete();


                /*
                 * ==========================
                 * SNAPSHOT SẢN PHẨM
                 * ==========================
                 */

                $table->string(
                    'product_name',
                    150
                );

                /*
                 * SKU thực tế khách mua.
                 *
                 * Nếu có Variant:
                 * lưu Variant SKU.
                 *
                 * Nếu không:
                 * lưu Product SKU.
                 */
                $table->string(
                    'sku',
                    120
                );

                $table->string(
                    'color',
                    50
                )
                ->nullable();

                $table->string(
                    'size',
                    30
                )
                ->nullable();

                /*
                 * Giá thực tế tại thời điểm mua.
                 */
                $table->decimal(
                    'unit_price',
                    15,
                    2
                );

                $table->unsignedInteger(
                    'quantity'
                );

                $table->decimal(
                    'subtotal',
                    15,
                    2
                );

                $table->timestamps();


                $table->index('order_id');

                $table->index('product_id');

                $table->index('variant_id');

            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'order_details'
        );
    }
};