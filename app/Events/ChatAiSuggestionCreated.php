<?php

namespace App\Events;

use App\Models\ChatAiSuggestion;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatAiSuggestionCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public ChatAiSuggestion $suggestion,
        public int $adminId
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'admin.chat.suggestion.'
                . $this->suggestion->chat_conversation_id
                . '.'
                . $this->adminId
            ),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.ai.suggestion.created';
    }

    /**
     * @return array{id: int, content: string, created_at: string}
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->suggestion->id,
            'content' => $this->suggestion->content,
            'created_at' => $this->suggestion->created_at?->toIso8601String()
                ?? now()->toIso8601String(),
        ];
    }
}
