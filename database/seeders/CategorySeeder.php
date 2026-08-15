<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Gọng kính',
                'slug' => 'gong-kinh',
                'description' => 'Các mẫu gọng kính đa dạng về kiểu dáng, chất liệu và màu sắc.',
            ],
            [
                'name' => 'Kính cận',
                'slug' => 'kinh-can',
                'description' => 'Các mẫu kính phù hợp cho nhu cầu sử dụng kính cận hằng ngày.',
            ],
            [
                'name' => 'Kính râm',
                'slug' => 'kinh-ram',
                'description' => 'Kính râm thời trang giúp bảo vệ mắt khi hoạt động ngoài trời.',
            ],
            [
                'name' => 'Kính thời trang',
                'slug' => 'kinh-thoi-trang',
                'description' => 'Những mẫu kính tạo điểm nhấn cho phong cách cá nhân.',
            ],
            [
                'name' => 'Kính chống ánh sáng xanh',
                'slug' => 'kinh-chong-anh-sang-xanh',
                'description' => 'Kính phù hợp với người thường xuyên sử dụng máy tính và thiết bị điện tử.',
            ],
            [
                'name' => 'Kính trẻ em',
                'slug' => 'kinh-tre-em',
                'description' => 'Các mẫu kính nhẹ, bền và phù hợp với trẻ em.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                [
                    'slug' => $category['slug'],
                ],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'image' => null,
                    'is_active' => true,
                ]
            );
        }
    }
}