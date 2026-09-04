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
        Schema::create('chat_conversations', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | CUSTOMER
            |--------------------------------------------------------------------------
            |
            | Người bắt đầu cuộc trò chuyện.
            |
            */

            $table->foreignId('customer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | STAFF
            |--------------------------------------------------------------------------
            |
            | Staff đang tiếp nhận cuộc trò chuyện.
            |
            | Ban đầu có thể chưa có Staff nhận,
            | vì vậy cho phép NULL.
            |
            */

            $table->foreignId('staff_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | STATUS
            |--------------------------------------------------------------------------
            |
            | open   : đang chờ / đang tư vấn
            | closed : đã kết thúc
            |
            */

            $table->string(
                'status',
                20
            )->default('open');

            /*
            |--------------------------------------------------------------------------
            | LAST MESSAGE
            |--------------------------------------------------------------------------
            |
            | Dùng để sắp xếp danh sách chat:
            | hội thoại mới nhất nằm trên cùng.
            |
            */

            $table->timestamp('last_message_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | CLOSED AT
            |--------------------------------------------------------------------------
            */

            $table->timestamp('closed_at')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index([
                'customer_id',
                'status',
            ]);

            $table->index([
                'staff_id',
                'status',
            ]);

            $table->index(
                'last_message_at'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'chat_conversations'
        );
    }
};
