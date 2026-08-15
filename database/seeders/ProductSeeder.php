<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id', 'slug');

        $products = [
            [
                'category_slug' => 'gong-kinh',
                'name' => 'Gọng kính Oval Classic Black',
                'slug' => 'gong-kinh-oval-classic-black',
                'sku' => 'VLR-OVAL-001',
                'price' => 790000,
                'sale_price' => 690000,
                'material' => 'acetate',
                'shape' => 'oval',
                'gender' => 'unisex',
                'dimensions' => '50-18-145 mm',
                'description' => 'Gọng kính Oval Classic mang phong cách tối giản, thanh lịch và dễ phối đồ.',
                'highlights' => 'Acetate nhẹ, thiết kế oval cổ điển, phù hợp sử dụng hằng ngày.',
                'recommended_face_shapes' => ['square', 'heart'],
                'style_tags' => ['minimal', 'elegant'],
            ],

            [
                'category_slug' => 'gong-kinh',
                'name' => 'Gọng kính Square Titanium Silver',
                'slug' => 'gong-kinh-square-titanium-silver',
                'sku' => 'VLR-SQUARE-002',
                'price' => 1450000,
                'sale_price' => null,
                'material' => 'titanium',
                'shape' => 'square',
                'gender' => 'male',
                'dimensions' => '53-18-145 mm',
                'description' => 'Gọng vuông Titanium thiết kế hiện đại với trọng lượng nhẹ.',
                'highlights' => 'Titanium cao cấp, nhẹ, bền và phù hợp phong cách công sở.',
                'recommended_face_shapes' => ['round', 'oval'],
                'style_tags' => ['minimal', 'elegant'],
            ],

            [
                'category_slug' => 'kinh-can',
                'name' => 'Kính cận AirFlex TR90',
                'slug' => 'kinh-can-airflex-tr90',
                'sku' => 'VLR-MYOPIA-003',
                'price' => 980000,
                'sale_price' => null,
                'material' => 'tr90',
                'shape' => 'rectangle',
                'gender' => 'unisex',
                'dimensions' => '52-17-140 mm',
                'description' => 'Kính cận AirFlex sử dụng chất liệu TR90 nhẹ và linh hoạt.',
                'highlights' => 'Dẻo nhẹ, đeo thoải mái, phù hợp sử dụng trong thời gian dài.',
                'recommended_face_shapes' => ['round', 'oval'],
                'style_tags' => ['minimal'],
            ],

            [
                'category_slug' => 'kinh-ram',
                'name' => 'Kính râm Aviator Gold UV400',
                'slug' => 'kinh-ram-aviator-gold-uv400',
                'sku' => 'VLR-SUN-AVI-004',
                'price' => 1250000,
                'sale_price' => null,
                'material' => 'metal',
                'shape' => 'aviator',
                'gender' => 'unisex',
                'dimensions' => '58-14-140 mm',
                'description' => 'Mẫu Aviator cổ điển kết hợp tròng chống tia UV400.',
                'highlights' => 'Phong cách phi công, khung kim loại thanh mảnh và sang trọng.',
                'recommended_face_shapes' => ['square', 'oval', 'heart'],
                'style_tags' => ['elegant', 'bold'],
            ],

            [
                'category_slug' => 'kinh-ram',
                'name' => 'Kính râm Cat Eye Luna Brown',
                'slug' => 'kinh-ram-cat-eye-luna-brown',
                'sku' => 'VLR-SUN-CAT-005',
                'price' => 1100000,
                'sale_price' => 990000,
                'material' => 'acetate',
                'shape' => 'cat_eye',
                'gender' => 'female',
                'dimensions' => '54-17-140 mm',
                'description' => 'Thiết kế mắt mèo thời trang với đường nét mềm mại.',
                'highlights' => 'Tôn đường nét khuôn mặt và phù hợp phong cách cá tính.',
                'recommended_face_shapes' => ['round', 'oval'],
                'style_tags' => ['elegant', 'bold'],
            ],

            [
                'category_slug' => 'kinh-thoi-trang',
                'name' => 'Kính thời trang Browline Retro',
                'slug' => 'kinh-thoi-trang-browline-retro',
                'sku' => 'VLR-FASHION-BRW-006',
                'price' => 870000,
                'sale_price' => null,
                'material' => 'metal',
                'shape' => 'browline',
                'gender' => 'unisex',
                'dimensions' => '51-19-145 mm',
                'description' => 'Kính Browline lấy cảm hứng từ phong cách cổ điển.',
                'highlights' => 'Thiết kế retro nổi bật, dễ tạo điểm nhấn cho trang phục.',
                'recommended_face_shapes' => ['round', 'oval'],
                'style_tags' => ['vintage', 'bold'],
            ],

            [
                'category_slug' => 'kinh-chong-anh-sang-xanh',
                'name' => 'Kính BlueShield Rectangle Clear',
                'slug' => 'kinh-blueshield-rectangle-clear',
                'sku' => 'VLR-BLUE-REC-007',
                'price' => 920000,
                'sale_price' => null,
                'material' => 'tr90',
                'shape' => 'rectangle',
                'gender' => 'unisex',
                'dimensions' => '51-18-140 mm',
                'description' => 'Kính BlueShield phù hợp cho người làm việc thường xuyên trước màn hình.',
                'highlights' => 'Thiết kế trong suốt hiện đại, nhẹ và dễ sử dụng hằng ngày.',
                'recommended_face_shapes' => ['round', 'oval'],
                'style_tags' => ['minimal', 'elegant'],
            ],

            [
                'category_slug' => 'kinh-chong-anh-sang-xanh',
                'name' => 'Kính BlueShield Round Metal',
                'slug' => 'kinh-blueshield-round-metal',
                'sku' => 'VLR-BLUE-RND-008',
                'price' => 1050000,
                'sale_price' => null,
                'material' => 'metal',
                'shape' => 'round',
                'gender' => 'unisex',
                'dimensions' => '50-20-140 mm',
                'description' => 'Mẫu kính tròn kim loại mang phong cách trẻ trung và thanh lịch.',
                'highlights' => 'Khung kim loại mảnh, kiểu dáng tròn và phù hợp môi trường học tập.',
                'recommended_face_shapes' => ['square', 'heart'],
                'style_tags' => ['vintage', 'minimal'],
            ],

            [
                'category_slug' => 'kinh-tre-em',
                'name' => 'Kính trẻ em Flexy Round Blue',
                'slug' => 'kinh-tre-em-flexy-round-blue',
                'sku' => 'VLR-KIDS-RND-009',
                'price' => 550000,
                'sale_price' => null,
                'material' => 'tr90',
                'shape' => 'round',
                'gender' => 'kids',
                'dimensions' => '45-16-125 mm',
                'description' => 'Kính trẻ em thiết kế nhẹ và linh hoạt cho hoạt động hằng ngày.',
                'highlights' => 'TR90 dẻo, trọng lượng nhẹ và phù hợp với trẻ.',
                'recommended_face_shapes' => ['square', 'oval'],
                'style_tags' => ['minimal'],
            ],

            [
                'category_slug' => 'kinh-tre-em',
                'name' => 'Kính trẻ em Bunny Pink',
                'slug' => 'kinh-tre-em-bunny-pink',
                'sku' => 'VLR-KIDS-PNK-010',
                'price' => 590000,
                'sale_price' => null,
                'material' => 'tr90',
                'shape' => 'oval',
                'gender' => 'kids',
                'dimensions' => '46-16-125 mm',
                'description' => 'Mẫu kính trẻ em màu sắc tươi sáng với thiết kế đáng yêu.',
                'highlights' => 'Gọng mềm, nhẹ và màu sắc nổi bật.',
                'recommended_face_shapes' => ['round', 'square'],
                'style_tags' => ['bold'],
            ],
        ];

        foreach ($products as $data) {
            $categoryId = $categories[$data['category_slug']];

            unset($data['category_slug']);

            Product::updateOrCreate(
                [
                    'sku' => $data['sku'],
                ],
                array_merge(
                    $data,
                    [
                        'category_id' => $categoryId,
                        'is_active' => true,
                    ]
                )
            );
        }
    }
}