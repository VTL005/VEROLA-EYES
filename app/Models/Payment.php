<?php

namespace App\Models;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const METHOD_COD = 'cod';
    public const METHOD_QR = 'qr';
    public const METHOD_VNPAY = 'vnpay';

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'status',
        'transaction_code',
        'response_code',
        'paid_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    /**
     * Payment thuộc Order.
     */
    public function order()
        {
    return $this->belongsTo(
        Order::class,
        'order_id'
    );
        }

    /**
     * Giao dịch đã thanh toán thành công?
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Giao dịch đang chờ?
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Giao dịch thất bại?
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Đã hoàn tiền?
     */
    public function isRefunded(): bool
    {
        return $this->status === self::STATUS_REFUNDED;
    }
}