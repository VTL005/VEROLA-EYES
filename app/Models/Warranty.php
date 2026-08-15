<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warranty extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'warranty_code',
        'user_id',
        'order_detail_id',
        'product_id',

        'start_date',
        'end_date',

        'status',
        'warranty_content',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Warranty thuộc Customer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Warranty phát sinh từ OrderDetail.
     */
    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class);
    }

    /**
     * Product được bảo hành.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Bảo hành đang còn hiệu lực?
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && now()->startOfDay()
                ->between(
                    $this->start_date->startOfDay(),
                    $this->end_date->endOfDay()
                );
    }

    /**
     * Bảo hành đã hết hạn theo ngày?
     */
    public function isExpired(): bool
    {
        return now()->startOfDay()
            ->greaterThan(
                $this->end_date->endOfDay()
            );
    }

    /**
     * Scope lấy bảo hành của Customer.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}