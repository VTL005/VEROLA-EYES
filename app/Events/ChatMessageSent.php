<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public ChatMessage $message;

    public function __construct(
        ChatMessage $message
    ) {
        /*
        |--------------------------------------------------------------------------
        | LOAD DỮ LIỆU CẦN BROADCAST
        |--------------------------------------------------------------------------
        */

        $this->message =
            $message->loadMissing([
                'sender:id,name,role_id',

                /*
                 * Product suggestion.
                 */
                'products.primaryImage',

                /*
                 * Image attachments.
                 */
                'attachments',
            ]);
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
                .$this->message
                    ->chat_conversation_id
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
        return 'chat.message.sent';
    }

    /*
    |--------------------------------------------------------------------------
    | PAYLOAD
    |--------------------------------------------------------------------------
    */

    public function broadcastWith(): array
    {
        /*
        |--------------------------------------------------------------------------
        | PRODUCT LIST
        |--------------------------------------------------------------------------
        */

        $products = [];

        if (
            $this->message->isProductList()
        ) {

            $products =
                $this->message
                    ->products
                    ->map(
                        function ($product) {

                            $imagePath =
                                $product
                                    ->primaryImage
                                    ?->image_path
                                ?? 'images/no-image.png';

                            $price =
                                (float) $product->price;

                            $salePrice =
                                $product->sale_price
                                    !== null
                                    ? (float) $product
                                        ->sale_price
                                    : null;

                            $currentPrice =
                                (float) $product
                                    ->current_price;

                            return [
                                'id' => $product->id,

                                'name' => $product->name,

                                'slug' => $product->slug,

                                'sku' => $product->sku,

                                'price' => $price,

                                'sale_price' => $salePrice,

                                'current_price' => $currentPrice,

                                'image_url' => asset(
                                    $imagePath
                                ),

                                'product_url' => route(
                                    'products.show',
                                    $product
                                ),
                            ];
                        }
                    )
                    ->values()
                    ->all();
        }

        /*
        |--------------------------------------------------------------------------
        | IMAGE ATTACHMENTS
        |--------------------------------------------------------------------------
        */

        $attachments =
            $this->message
                ->attachments
                ->filter(
                    function ($attachment) {

                        return $attachment
                            ->isImage();
                    }
                )
                ->map(
                    function ($attachment) {

                        return [
                            'id' => $attachment->id,

                            'type' => $attachment
                                ->attachment_type,

                            'url' => $attachment->url,

                            'original_name' => $attachment
                                ->original_name,

                            'mime_type' => $attachment
                                ->mime_type,

                            'file_size' => $attachment
                                ->file_size,

                            'sort_order' => $attachment
                                ->sort_order,
                        ];
                    }
                )
                ->values()
                ->all();

        /*
        |--------------------------------------------------------------------------
        | MESSAGE
        |--------------------------------------------------------------------------
        */

        return [
            'message' => [

                'id' => $this->message->id,

                'conversation_id' => $this->message
                    ->chat_conversation_id,

                'sender_id' => $this->message->sender_id,

                'sender_name' => $this->message
                    ->sender
                    ?->name
                    ?? 'VELORA Eyes',

                'message_type' => $this->message
                    ->message_type
                    ?? ChatMessage::TYPE_TEXT,

                'content' => $this->message->message,

                /*
                 * Product cards.
                 */
                'products' => $products,

                /*
                 * Image attachments.
                 */
                'attachments' => $attachments,

                'read_at' => $this->message
                    ->read_at
                    ?->toISOString(),

                'created_at' => $this->message
                    ->created_at
                    ?->toISOString(),

                'time' => $this->message
                    ->created_at
                    ?->format('H:i'),
            ],
        ];
    }
}
