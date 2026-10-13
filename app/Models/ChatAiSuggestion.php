<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatAiSuggestion extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_USED = 'used';

    protected $fillable = [
        'chat_conversation_id',
        'source_chat_message_id',
        'content',
        'status',
        'used_at',
    ];

    protected function casts(): array
    {
        return [
            'used_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(
            ChatConversation::class,
            'chat_conversation_id'
        );
    }

    public function sourceMessage(): BelongsTo
    {
        return $this->belongsTo(
            ChatMessage::class,
            'source_chat_message_id'
        );
    }
}
