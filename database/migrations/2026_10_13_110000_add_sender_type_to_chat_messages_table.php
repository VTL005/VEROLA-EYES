<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add an explicit sender type so AI messages are distinguishable from users.
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->string('sender_type', 20)
                ->default('customer')
                ->after('sender_id');

            $table->index([
                'chat_conversation_id',
                'sender_type',
            ]);
        });

        DB::table('chat_messages')
            ->orderBy('id')
            ->chunkById(200, function ($messages): void {
                $customerIds = DB::table('chat_conversations')
                    ->whereIn(
                        'id',
                        collect($messages)
                            ->pluck('chat_conversation_id')
                            ->unique()
                            ->all()
                    )
                    ->pluck('customer_id', 'id');

                foreach ($messages as $message) {
                    $senderType = (int) $message->sender_id
                        === (int) ($customerIds[$message->chat_conversation_id] ?? 0)
                        ? 'customer'
                        : 'admin';

                    DB::table('chat_messages')
                        ->where('id', $message->id)
                        ->update([
                            'sender_type' => $senderType,
                        ]);
                }
            });
    }

    /**
     * Remove the explicit sender type.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex([
                'chat_conversation_id',
                'sender_type',
            ]);

            $table->dropColumn('sender_type');
        });
    }
};
