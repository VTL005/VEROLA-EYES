<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'is_home_featured',
        'homepage_image_path',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_home_featured' => 'boolean',
        ];
    }

    /**
     * Một Category có nhiều Product.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
