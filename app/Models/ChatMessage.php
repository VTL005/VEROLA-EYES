<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    /*
    |--------------------------------------------------------------------------
    | ATTACHMENTS
    |--------------------------------------------------------------------------
    */

    public function attachments(): HasMany
    {
        return $this->hasMany(
            ChatMessageAttachment::class,
            'chat_message_id'
        )
            ->orderBy('sort_order')
            ->orderBy('id');
    }
    /*
    |--------------------------------------------------------------------------
    | MESSAGE TYPES
    |--------------------------------------------------------------------------
    */

    public const TYPE_TEXT = 'text';

    public const TYPE_IMAGE = 'image';

    public const TYPE_PRODUCT_LIST = 'product_list';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'chat_conversation_id',
        'sender_id',
        'message_type',
        'message',
        'read_at',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CONVERSATION
    |--------------------------------------------------------------------------
    */

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(
            ChatConversation::class,
            'chat_conversation_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SENDER
    |--------------------------------------------------------------------------
    */

    public function sender(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'sender_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    |
    | Dùng khi message_type = product_list.
    |
    */

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'chat_message_product',
            'chat_message_id',
            'product_id'
        )
            ->withPivot(
                'sort_order'
            )
            ->withTimestamps()
            ->orderByPivot(
                'sort_order'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function isText(): bool
    {
        return $this->message_type
            === self::TYPE_TEXT;
    }

    public function isProductList(): bool
    {
        return $this->message_type
            === self::TYPE_PRODUCT_LIST;
    }
}
