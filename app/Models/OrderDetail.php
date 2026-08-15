<?php

namespace App\Models;
use App\Models\Warranty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',

        'product_name',
        'sku',
        'color',
        'size',

        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'integer',
            'subtotal' => 'decimal:2',
        ];
    }

    /**
     * OrderDetail thuộc Order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Product gốc.
     *
     * Chỉ dùng để tham chiếu.
     * Không dùng giá hiện tại của Product
     * để hiển thị giá lịch sử Order.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Variant khách đã mua.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(
            ProductVariant::class,
            'variant_id'
        );
    }

    /**
     * Bảo hành phát sinh từ dòng Order này.
     */
    public function warranties(): HasMany
    {
        return $this->hasMany(Warranty::class);
    }
    /**
 * Bảo hành điện tử của sản phẩm trong đơn.
 */
public function warranty()
{
    return $this->hasOne(
        Warranty::class,
        'order_detail_id'
    );
}
}