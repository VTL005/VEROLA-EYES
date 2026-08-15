<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'color',
        'size',
        'sku',
        'stock_quantity',
        'price_adjustment',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'stock_quantity' => 'integer',
            'price_adjustment' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Variant thuộc Product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Variant có thể nằm trong nhiều CartItem.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(
            CartItem::class,
            'variant_id'
        );
    }

    /**
     * Variant có thể xuất hiện
     * trong nhiều OrderDetail.
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(
            OrderDetail::class,
            'variant_id'
        );
    }

    /**
     * Chỉ lấy Variant đang hoạt động.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Variant còn hàng hay không.
     */
    public function getInStockAttribute(): bool
    {
        return $this->is_active
            && $this->stock_quantity > 0;
    }

    /**
     * Giá cuối cùng của Variant.
     */
    public function getFinalPriceAttribute()
    {
        return $this->product->current_price
            + $this->price_adjustment;
    }
}