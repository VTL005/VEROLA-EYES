<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_ai_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_conversation_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('source_chat_message_id')
                ->nullable()
                ->constrained('chat_messages')
                ->nullOnDelete();
            $table->text('content');
            $table->string('status', 20)->default('pending');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->index([
                'chat_conversation_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_ai_suggestions');
    }
};
