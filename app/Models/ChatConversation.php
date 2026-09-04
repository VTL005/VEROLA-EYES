<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatConversation extends Model
{
    use HasFactory;

    /**
     * Các field được phép mass assignment.
     */
    protected $fillable = [
        'customer_id',
        'staff_id',
        'status',
        'last_message_at',
        'closed_at',
    ];

    /**
     * Ép kiểu dữ liệu.
     */
    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * Customer sở hữu cuộc trò chuyện.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'customer_id'
        );
    }

    /**
     * Staff đang phụ trách cuộc trò chuyện.
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'staff_id'
        );
    }

    /**
     * Toàn bộ tin nhắn trong cuộc trò chuyện.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(
            ChatMessage::class,
            'chat_conversation_id'
        );
    }

    /**
     * Tin nhắn mới nhất.
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(
            ChatMessage::class,
            'chat_conversation_id'
        )->latestOfMany();
    }

    /**
     * Chỉ lấy hội thoại đang mở.
     */
    public function scopeOpen(
        Builder $query
    ): Builder {
        return $query->where(
            'status',
            'open'
        );
    }

    /**
     * Cuộc trò chuyện đang mở?
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Cuộc trò chuyện đã đóng?
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
