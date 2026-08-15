<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Payment;
use App\Models\OrderDetail;

class Order extends Model
{
    use HasFactory;

    /**
     * Các trạng thái chuẩn của Order.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_PACKED = 'packed';
    public const STATUS_SHIPPING = 'shipping';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Payment status.
     */
    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'order_code',
        'user_id',
        'voucher_id',

        'customer_name',
        'phone',
        'email',
        'address',

        'subtotal',
        'discount_amount',
        'shipping_fee',
        'total',

        'payment_method',
        'payment_status',
        'order_status',

        'note',
        'stock_restored_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total' => 'decimal:2',

            'stock_restored_at' => 'datetime',
        ];
    }

    /**
     * Order thuộc Customer nào.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Voucher được dùng cho Order.
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Một Order có nhiều dòng sản phẩm.
     */
    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Timeline trạng thái đơn hàng.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)
            ->orderBy('created_at');
    }

    /**
     * Một Order có thể có nhiều lần thanh toán.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Lịch sử Voucher của Order.
     *
     * Migration đã unique order_id,
     * nên mỗi Order tối đa một VoucherUsage.
     */
    public function voucherUsage(): HasOne
    {
        return $this->hasOne(VoucherUsage::class);
    }

    /**
     * Customer chỉ được tự hủy khi Pending.
     */
    public function isCancellableByCustomer(): bool
    {
        return $this->order_status === self::STATUS_PENDING;
    }

    /**
     * Order đã hoàn thành?
     */
    public function isCompleted(): bool
    {
        return $this->order_status === self::STATUS_COMPLETED;
    }

    /**
     * Order đã bị hủy?
     */
    public function isCancelled(): bool
    {
        return $this->order_status === self::STATUS_CANCELLED;
    }

    /**
     * Order đã thanh toán?
     */
    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    /**
     * Stock đã được hoàn lại chưa?
     */
    public function hasStockBeenRestored(): bool
    {
        return $this->stock_restored_at !== null;
    }

    /**
     * Kiểm tra chuyển trạng thái theo luồng chuẩn.
     *
     * Không xử lý Cancel ở đây vì quyền Cancel của
     * Customer và Staff/Admin là khác nhau.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            self::STATUS_PENDING => [
                self::STATUS_CONFIRMED,
            ],

            self::STATUS_CONFIRMED => [
                self::STATUS_PREPARING,
            ],

            self::STATUS_PREPARING => [
                self::STATUS_PACKED,
            ],

            self::STATUS_PACKED => [
                self::STATUS_SHIPPING,
            ],

            self::STATUS_SHIPPING => [
                self::STATUS_COMPLETED,
            ],

            self::STATUS_COMPLETED => [],

            self::STATUS_CANCELLED => [],
        ];

        return in_array(
            $newStatus,
            $transitions[$this->order_status] ?? [],
            true
        );
    }

    /**
     * Scope lấy Order của Customer.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function payment()
        {
    return $this->hasOne(
        Payment::class,
        'order_id'
    );
        }

        

}