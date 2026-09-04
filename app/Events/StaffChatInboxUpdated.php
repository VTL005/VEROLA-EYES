<?php

namespace App\Events;

use App\Models\ChatConversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StaffChatInboxUpdated implements ShouldBroadcastNow
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
                'staff:id,name',
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
         * Chat chưa có Staff nhận:
         * → toàn bộ Staff được biết có khách đang chờ.
         */
        if ($this->conversation->staff_id === null) {

            return [
                new PrivateChannel(
                    'staff.chat.inbox'
                ),
            ];
        }

        /*
         * Chat đã có Staff phụ trách:
         * → chỉ Staff đó nhận cập nhật.
         */
        return [
            new PrivateChannel(
                'staff.chat.inbox.'
                .$this->conversation->staff_id
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
        return 'staff.chat.inbox.updated';
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

                'staff_id' => $this->conversation->staff_id,

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
