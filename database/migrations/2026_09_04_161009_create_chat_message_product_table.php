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
            'chat_message_product',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | CHAT MESSAGE
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId(
                        'chat_message_id'
                    )
                    ->constrained(
                        'chat_messages'
                    )
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | PRODUCT
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId(
                        'product_id'
                    )
                    ->constrained(
                        'products'
                    )
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | THỨ TỰ HIỂN THỊ
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedTinyInteger(
                        'sort_order'
                    )
                    ->default(0);

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | KHÔNG CHO TRÙNG SẢN PHẨM
                | TRONG CÙNG MỘT MESSAGE
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'chat_message_id',
                    'product_id',
                ]);

                /*
                |--------------------------------------------------------------------------
                | HỖ TRỢ SẮP XẾP
                |--------------------------------------------------------------------------
                */

                $table->index([
                    'chat_message_id',
                    'sort_order',
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
            'chat_message_product'
        );
    }
};
