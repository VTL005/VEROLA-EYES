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
        | Tên và mã địa chỉ hành chính hiện có
        |--------------------------------------------------------------------------
        */

        'province',
        'province_code',

        'district',

        'ward',
        'ward_code',

        /*
        |--------------------------------------------------------------------------
        | Mã địa chỉ GHN
        |--------------------------------------------------------------------------
        */

        'ghn_province_id',
        'ghn_district_id',
        'ghn_ward_code',

        'detail_address',

        'label',

        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'ghn_province_id' => 'integer',
            'ghn_district_id' => 'integer',
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