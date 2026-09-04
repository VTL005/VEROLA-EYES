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
        Schema::create('chat_messages', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | CONVERSATION
            |--------------------------------------------------------------------------
            */

            $table->foreignId('chat_conversation_id')
                ->constrained('chat_conversations')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | SENDER
            |--------------------------------------------------------------------------
            |
            | Có thể là Customer hoặc Staff.
            |
            | Nếu tài khoản Staff sau này bị xóa,
            | lịch sử tin nhắn vẫn được giữ lại.
            |
            */

            $table->foreignId('sender_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | MESSAGE
            |--------------------------------------------------------------------------
            |
            | Phiên bản đầu chỉ hỗ trợ text.
            |
            */

            $table->text('message');

            /*
            |--------------------------------------------------------------------------
            | READ STATUS
            |--------------------------------------------------------------------------
            |
            | NULL  : chưa đọc
            | có giá trị: thời điểm đã đọc
            |
            */

            $table->timestamp('read_at')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            $table->index([
                'chat_conversation_id',
                'created_at',
            ]);

            $table->index([
                'sender_id',
                'read_at',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'chat_messages'
        );
    }
};
