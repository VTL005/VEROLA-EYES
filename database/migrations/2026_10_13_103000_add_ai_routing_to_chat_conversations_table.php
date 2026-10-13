<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the state needed to route a conversation between AI and Admin.
     */
    public function up(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->string('chat_mode', 20)
                ->default('manual')
                ->after('admin_id');

            $table->timestamp('ai_last_reply_at')
                ->nullable()
                ->after('last_message_at');

            $table->timestamp('human_takeover_at')
                ->nullable()
                ->after('ai_last_reply_at');

            $table->index([
                'chat_mode',
                'status',
            ]);
        });
    }

    /**
     * Remove the AI routing state.
     */
    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropIndex([
                'chat_mode',
                'status',
            ]);

            $table->dropColumn([
                'chat_mode',
                'ai_last_reply_at',
                'human_takeover_at',
            ]);
        });
    }
};
