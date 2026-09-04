<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ChatMessageAttachment extends Model
{
    /*
    |--------------------------------------------------------------------------
    | ATTACHMENT TYPES
    |--------------------------------------------------------------------------
    */

    public const TYPE_IMAGE = 'image';

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'chat_message_id',
        'attachment_type',
        'disk',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'sort_order',
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MESSAGE
    |--------------------------------------------------------------------------
    */

    public function message(): BelongsTo
    {
        return $this->belongsTo(
            ChatMessage::class,
            'chat_message_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function isImage(): bool
    {
        return $this->attachment_type
            === self::TYPE_IMAGE;
    }

    /**
     * URL public của attachment.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk(
            $this->disk
        )->url(
            $this->file_path
        );
    }
}
