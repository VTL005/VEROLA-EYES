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
            'cart_items',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('cart_id')
                    ->constrained('carts')
                    ->cascadeOnDelete();

                /*
                 * Cart mua theo Variant,
                 * không mua Product chung chung.
                 */
                $table->foreignId('variant_id')
                    ->constrained('product_variants')
                    ->cascadeOnDelete();

                $table->unsignedInteger('quantity')
                    ->default(1);

                $table->timestamps();


                /*
                 * Một Variant chỉ xuất hiện
                 * một lần trong cùng Cart.
                 */
                $table->unique(
                    [
                        'cart_id',
                        'variant_id',
                    ],
                    'cart_variant_unique'
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
            'cart_items'
        );
    }
};