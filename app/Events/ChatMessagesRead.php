<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessagesRead implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public int $conversationId;

    public int $readerId;

    public function __construct(
        int $conversationId,
        int $readerId
    ) {
        $this->conversationId =
            $conversationId;

        $this->readerId =
            $readerId;
    }

    /*
    |--------------------------------------------------------------------------
    | CHANNEL
    |--------------------------------------------------------------------------
    */

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(
                'chat.conversation.'
                .$this->conversationId
            ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | EVENT NAME
    |--------------------------------------------------------------------------
    */

    public function broadcastAs(): string
    {
        return 'chat.messages.read';
    }

    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,

            'reader_id' => $this->readerId,
        ];
    }
}
