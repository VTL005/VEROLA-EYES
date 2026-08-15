<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'price',
        'sale_price',
        'material',
        'shape',
        'gender',
        'dimensions',
        'description',
        'highlights',
        'recommended_face_shapes',
        'style_tags',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',

            'recommended_face_shapes' => 'array',
            'style_tags' => 'array',

            'is_active' => 'boolean',
        ];
    }

    /**
     * Product thuộc một Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Product có nhiều hình ảnh.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }

    /**
     * Ảnh chính của Product.
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)
            ->where('is_primary', true);
    }

    /**
     * Product có nhiều Variant.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Product có thể xuất hiện
     * trong nhiều WishlistItem.
     */
    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Product xuất hiện trong các OrderDetail.
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Các Review của Product.
     */
    public function reviews()
    {
    return $this->hasMany(
        Review::class,
        'product_id'
        );
    }

    /**
     * Các Warranty liên quan Product.
     */
    public function warranties(): HasMany
    {
        return $this->hasMany(Warranty::class);
    }

    /**
     * Chỉ lấy Product đang kinh doanh.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Giá bán hiện tại của Product.
     *
     * Nếu có sale_price thì ưu tiên sale_price,
     * ngược lại dùng price.
     */
    public function getCurrentPriceAttribute()
    {
        return $this->sale_price !== null
            ? $this->sale_price
            : $this->price;
    }

    /**
     * Product có ảnh thật hay chưa.
     */
    public function hasRealImage(): bool
    {
        return $this->images()
            ->where(
                'image_path',
                '!=',
                'images/no-image.png'
            )
            ->exists();
    }

    /**
     * Product có Variant đang hoạt động hay chưa.
     */
    public function hasActiveVariant(): bool
    {
        return $this->variants()
            ->where(
                'is_active',
                true
            )
            ->exists();
    }

    /**
     * Product đã đủ điều kiện kinh doanh chưa.
     */
    public function isReadyForSale(): bool
    {
        return $this->hasRealImage()
            && $this->hasActiveVariant();
    }
}