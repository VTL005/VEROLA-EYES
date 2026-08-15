<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'minimum_order_amount',
        'starts_at',
        'ends_at',
        'usage_limit',
        'usage_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'minimum_order_amount' => 'decimal:2',

            'starts_at' => 'datetime',
            'ends_at' => 'datetime',

            'usage_limit' => 'integer',
            'usage_count' => 'integer',

            'is_active' => 'boolean',
        ];
    }

    /**
     * Một Voucher có nhiều lịch sử sử dụng.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /**
     * Một Voucher có thể được dùng cho nhiều Order.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope chỉ lấy Voucher đang được bật.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Kiểm tra Voucher còn trong thời gian hiệu lực.
     */
    public function isWithinValidPeriod(): bool
    {
        $now = now();

        return $now->greaterThanOrEqualTo($this->starts_at)
            && $now->lessThanOrEqualTo($this->ends_at);
    }

    /**
     * Kiểm tra Voucher còn lượt sử dụng hay không.
     */
    public function hasRemainingUsage(): bool
    {
        if ($this->usage_limit === null) {
            return true;
        }

        return $this->usage_count < $this->usage_limit;
    }

    /**
     * Kiểm tra subtotal có đạt giá trị tối thiểu hay không.
     */
    public function meetsMinimumOrder(float $subtotal): bool
    {
        return $subtotal >= (float) $this->minimum_order_amount;
    }

    /**
     * Kiểm tra Voucher có hợp lệ tại thời điểm hiện tại.
     */
    public function isCurrentlyValid(?float $subtotal = null): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->isWithinValidPeriod()) {
            return false;
        }

        if (!$this->hasRemainingUsage()) {
            return false;
        }

        if (
            $subtotal !== null
            && !$this->meetsMinimumOrder($subtotal)
        ) {
            return false;
        }

        return true;
    }

    /**
     * Tính số tiền được giảm.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0;
        }

        if (!$this->isCurrentlyValid($subtotal)) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {

            $discount = $subtotal
                * ((float) $this->discount_value / 100);

        } elseif ($this->discount_type === 'fixed') {

            $discount = (float) $this->discount_value;

        } else {

            return 0;
        }

        /*
         * Không cho số tiền giảm
         * lớn hơn subtotal.
         */
        return round(
            min($discount, $subtotal),
            2
        );
    }
}