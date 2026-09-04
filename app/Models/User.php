<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Các field được phép mass assignment.
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'google_id',
        'facebook_id',
        'phone',
        'avatar',
        'position',
        'is_active',
        'password',
    ];

    /**
     * Không đưa các field này ra JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Ép kiểu dữ liệu.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * User thuộc một Role.
     */
    public function role()
    {
        return $this->belongsTo(
            Role::class,
            'role_id'
        );
    }

    /**
     * User có nhiều địa chỉ.
     */
    public function addresses()
    {
        return $this->hasMany(
            Address::class,
            'user_id'
        );
    }

    /**
     * User có một Wishlist.
     */
    public function wishlist(): HasOne
    {
        return $this->hasOne(Wishlist::class);
    }

    /**
     * User có một Cart.
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Các cuộc trò chuyện mà User là Customer.
     */
    public function customerChatConversations(): HasMany
    {
        return $this->hasMany(
            ChatConversation::class,
            'customer_id'
        );
    }

    /**
     * Các cuộc trò chuyện mà User là Staff phụ trách.
     */
    public function staffChatConversations(): HasMany
    {
        return $this->hasMany(
            ChatConversation::class,
            'staff_id'
        );
    }

    /**
     * Các tin nhắn User đã gửi.
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(
            ChatMessage::class,
            'sender_id'
        );
    }

    /**
     * User có nhiều Order.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Lịch sử sử dụng Voucher.
     */
    public function voucherUsages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    /**
     * Review của User.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Lịch đo mắt của User.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Hồ sơ thông số kính.
     */
    public function eyePrescriptions(): HasMany
    {
        return $this->hasMany(EyePrescription::class);
    }

    /**
     * Bảo hành của User.
     */
    public function warranties(): HasMany
    {
        return $this->hasMany(Warranty::class);
    }

    /**
     * Kiểm tra User có Role cụ thể hay không.
     */
    public function hasRole(string $role): bool
    {
        return $this->role?->name === $role;
    }

    /**
     * Customer?
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    /**
     * Staff?
     */
    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }

    /**
     * Admin?
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }
}
