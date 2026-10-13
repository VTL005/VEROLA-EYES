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

    public const MODE_AUTO = 'auto';

    public const MODE_ASSIST = 'assist';

    public const MODE_MANUAL = 'manual';

    /**
     * Các field được phép mass assignment.
     */
    protected $fillable = [
        'customer_id',
        'admin_id',
        'chat_mode',
        'status',
        'last_message_at',
        'ai_last_reply_at',
        'human_takeover_at',
        'closed_at',
    ];

    /**
     * Ép kiểu dữ liệu.
     */
    protected function casts(): array
    {
        return [
            'last_message_at' => 'datetime',
            'ai_last_reply_at' => 'datetime',
            'human_takeover_at' => 'datetime',
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
     * Admin đang phụ trách cuộc trò chuyện.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'admin_id'
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
     * Các phản hồi nháp AI chỉ hiển thị cho Admin trong chế độ hỗ trợ.
     */
    public function aiSuggestions(): HasMany
    {
        return $this->hasMany(
            ChatAiSuggestion::class,
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

    /**
     * Các chế độ được phép dùng để định tuyến hội thoại.
     *
     * @return array<int, string>
     */
    public static function chatModes(): array
    {
        return [
            self::MODE_AUTO,
            self::MODE_ASSIST,
            self::MODE_MANUAL,
        ];
    }

    public function isAutoMode(): bool
    {
        return $this->chat_mode === self::MODE_AUTO;
    }

    public function isAssistMode(): bool
    {
        return $this->chat_mode === self::MODE_ASSIST;
    }

    public function isManualMode(): bool
    {
        return $this->chat_mode === self::MODE_MANUAL;
    }

    public function chatModeLabel(): string
    {
        return match ($this->chat_mode) {
            self::MODE_AUTO => 'AI tự động',
            self::MODE_ASSIST => 'AI hỗ trợ Admin',
            default => 'Admin tiếp quản',
        };
    }
}
