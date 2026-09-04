<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',

        'recipient_name',
        'phone',

        /*
        |--------------------------------------------------------------------------
        | Địa chỉ hành chính
        |--------------------------------------------------------------------------
        |
        | province / ward:
        |     lưu tên để hiển thị trực tiếp.
        |
        | province_code / ward_code:
        |     lưu mã hành chính để dữ liệu được chuẩn hóa.
        |
        | district:
        |     giữ lại để tương thích với các địa chỉ cũ.
        |     Địa chỉ mới có thể để NULL.
        |
        */

        'province',
        'province_code',

        'district',

        'ward',
        'ward_code',

        'detail_address',

        'label',

        'is_default',
    ];


    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }


    /**
     * Address thuộc một User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}