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
            'product_variants',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('product_id')
                    ->constrained('products')
                    ->cascadeOnDelete();

                /*
                 * Biến thể
                 */
                $table->string('color', 50);

                $table->string('size', 30);

                /*
                 * SKU riêng cho Variant
                 */
                $table->string(
                    'sku',
                    120
                )
                ->unique();

                /*
                 * Tồn kho quản lý tại Variant
                 */
                $table->unsignedInteger(
                    'stock_quantity'
                )
                ->default(0);

                /*
                 * Điều chỉnh giá nếu Variant đặc biệt.
                 *
                 * Giá thực tế =
                 * giá Product hiện tại
                 * + price_adjustment.
                 */
                $table->decimal(
                    'price_adjustment',
                    15,
                    2
                )
                ->default(0);

                /*
                 * Có thể vô hiệu hóa riêng Variant.
                 */
                $table->boolean(
                    'is_active'
                )
                ->default(true);

                $table->timestamps();


                /*
                 * Không cho trùng:
                 *
                 * Product + Color + Size
                 */
                $table->unique(
                    [
                        'product_id',
                        'color',
                        'size',
                    ],
                    'product_color_size_unique'
                );


                /*
                 * Index cho Filter và Stock.
                 */
                $table->index('color');

                $table->index('size');

                $table->index(
                    'stock_quantity'
                );

                $table->index(
                    'is_active'
                );
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'product_variants'
        );
    }
};