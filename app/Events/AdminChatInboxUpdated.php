<?php

namespace App\Events;

use App\Models\ChatConversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminChatInboxUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public ChatConversation $conversation;

    public function __construct(
        ChatConversation $conversation
    ) {
        $this->conversation =
            $conversation->loadMissing([
                'customer:id,name,email,phone',
                'admin:id,name',
                'latestMessage.sender:id,name',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CHANNEL
    |--------------------------------------------------------------------------
    */

    public function broadcastOn(): array
    {
        /*
         * Chat chưa có Admin nhận:
         * → toàn bộ Admin được biết có khách đang chờ.
         */
        if ($this->conversation->admin_id === null) {

            return [
                new PrivateChannel(
                    'admin.chat.inbox'
                ),
            ];
        }

        /*
         * Chat đã có Admin phụ trách:
         * → chỉ Admin đó nhận cập nhật.
         */
        return [
            new PrivateChannel(
                'admin.chat.inbox.'
                .$this->conversation->admin_id
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
        return 'admin.chat.inbox.updated';
    }

    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    public function broadcastWith(): array
    {
        $latestMessage =
            $this->conversation->latestMessage;

        return [
            'conversation' => [

                'id' => $this->conversation->id,

                'customer_id' => $this->conversation->customer_id,

                'admin_id' => $this->conversation->admin_id,

                'status' => $this->conversation->status,

                'customer_name' => $this->conversation->customer?->name
                    ?? 'Khách hàng',

                'customer_email' => $this->conversation->customer?->email,

                'customer_phone' => $this->conversation->customer?->phone,

                'latest_message' => $latestMessage?->message,

                'latest_sender_id' => $latestMessage?->sender_id,

                'latest_sender_name' => $latestMessage?->sender?->name,

                'last_message_at' => $this->conversation
                    ->last_message_at
                    ?->toISOString(),
            ],
        ];
    }
}
