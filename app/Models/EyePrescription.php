<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EyePrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'appointment_id',
        'performed_by',

        'right_sph',
        'right_cyl',
        'right_axis',

        'left_sph',
        'left_cyl',
        'left_axis',

        'pd',
        'exam_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'right_sph' => 'decimal:2',
            'right_cyl' => 'decimal:2',
            'right_axis' => 'integer',

            'left_sph' => 'decimal:2',
            'left_cyl' => 'decimal:2',
            'left_axis' => 'integer',

            'pd' => 'decimal:2',

            'exam_date' => 'date',
        ];
    }

    /**
     * Hồ sơ thuộc Customer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Hồ sơ được tạo từ Appointment nào.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Staff thực hiện đo mắt.
     */
    public function performer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'performed_by'
        );
    }

    /**
     * Scope lấy hồ sơ của một Customer.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}