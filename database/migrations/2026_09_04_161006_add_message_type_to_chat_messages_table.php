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
        Schema::table(
            'chat_messages',
            function (Blueprint $table) {

                /*
                |--------------------------------------------------------------------------
                | MESSAGE TYPE
                |--------------------------------------------------------------------------
                |
                | text
                | product_list
                |
                */

                $table
                    ->string(
                        'message_type',
                        20
                    )
                    ->default('text')
                    ->after('sender_id')
                    ->index();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(
            'chat_messages',
            function (Blueprint $table) {

                $table->dropIndex([
                    'message_type',
                ]);

                $table->dropColumn(
                    'message_type'
                );
            }
        );
    }
};
