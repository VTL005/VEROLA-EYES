<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Appointment extends Model
{
    use HasFactory;

    /**
     * Appointment Status.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_NO_SHOW = 'no_show';

    /**
     * Service Type.
     */
    public const SERVICE_EYE_EXAM = 'eye_exam';
    public const SERVICE_RECHECK = 'recheck';
    public const SERVICE_LENS_CONSULTATION = 'lens_consultation';
    public const SERVICE_FRAME_CONSULTATION = 'frame_consultation';

    protected $fillable = [
        'appointment_code',
        'user_id',

        'customer_name',
        'email',
        'phone',

        'appointment_date',
        'time_slot',
        'service_type',

        'note',
        'status',

        'confirmed_by',
        'confirmed_at',
        'reminder_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'confirmed_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
        ];
    }

    /**
     * Appointment thuộc Customer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Staff/Admin xác nhận lịch.
     */
    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'confirmed_by'
        );
    }

    /**
     * Một Appointment có thể liên quan
     * tới các hồ sơ đo mắt.
     */
    public function eyePrescriptions(): HasMany
    {
        return $this->hasMany(EyePrescription::class);
    }

    /**
     * Customer có thể hủy khi lịch
     * chưa hoàn thành.
     *
     * Rule chi tiết sẽ kiểm tra thêm ở Service.
     */
    public function isCancellableByCustomer(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PENDING,
                self::STATUS_CONFIRMED,
            ],
            true
        );
    }

    /**
     * Lịch đã được xác nhận?
     */
    public function isConfirmed(): bool
    {
        return $this->status
            === self::STATUS_CONFIRMED;
    }

    /**
     * Lịch đã hoàn thành?
     */
    public function isCompleted(): bool
    {
        return $this->status
            === self::STATUS_COMPLETED;
    }

    /**
     * Reminder đã được gửi?
     */
    public function hasReminderBeenSent(): bool
    {
        return $this->reminder_sent_at !== null;
    }

    /**
     * Scope lấy Appointment của Customer.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}