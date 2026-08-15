<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $variants = [
            'VLR-OVAL-001' => [
                [
                    'color' => 'black',
                    'size' => 'M',
                    'sku' => 'VLR-OVAL-001-BLK-M',
                    'stock_quantity' => 12,
                    'price_adjustment' => 0,
                ],
                [
                    'color' => 'transparent',
                    'size' => 'M',
                    'sku' => 'VLR-OVAL-001-CLR-M',
                    'stock_quantity' => 8,
                    'price_adjustment' => 0,
                ],
            ],

            'VLR-SQUARE-002' => [
                [
                    'color' => 'silver',
                    'size' => 'M',
                    'sku' => 'VLR-SQUARE-002-SLV-M',
                    'stock_quantity' => 6,
                    'price_adjustment' => 0,
                ],
                [
                    'color' => 'black',
                    'size' => 'L',
                    'sku' => 'VLR-SQUARE-002-BLK-L',
                    'stock_quantity' => 4,
                    'price_adjustment' => 50000,
                ],
            ],

            'VLR-MYOPIA-003' => [
                [
                    'color' => 'black',
                    'size' => 'M',
                    'sku' => 'VLR-MYOPIA-003-BLK-M',
                    'stock_quantity' => 15,
                    'price_adjustment' => 0,
                ],
                [
                    'color' => 'brown',
                    'size' => 'M',
                    'sku' => 'VLR-MYOPIA-003-BRN-M',
                    'stock_quantity' => 7,
                    'price_adjustment' => 0,
                ],
            ],

            'VLR-SUN-AVI-004' => [
                [
                    'color' => 'gold',
                    'size' => 'L',
                    'sku' => 'VLR-SUN-AVI-004-GLD-L',
                    'stock_quantity' => 10,
                    'price_adjustment' => 0,
                ],
                [
                    'color' => 'silver',
                    'size' => 'L',
                    'sku' => 'VLR-SUN-AVI-004-SLV-L',
                    'stock_quantity' => 5,
                    'price_adjustment' => 0,
                ],
            ],

            'VLR-SUN-CAT-005' => [
                [
                    'color' => 'brown',
                    'size' => 'M',
                    'sku' => 'VLR-SUN-CAT-005-BRN-M',
                    'stock_quantity' => 9,
                    'price_adjustment' => 0,
                ],
                [
                    'color' => 'black',
                    'size' => 'M',
                    'sku' => 'VLR-SUN-CAT-005-BLK-M',
                    'stock_quantity' => 5,
                    'price_adjustment' => 0,
                ],
            ],

            'VLR-FASHION-BRW-006' => [
                [
                    'color' => 'black',
                    'size' => 'M',
                    'sku' => 'VLR-FASHION-BRW-006-BLK-M',
                    'stock_quantity' => 11,
                    'price_adjustment' => 0,
                ],
                [
                    'color' => 'brown',
                    'size' => 'M',
                    'sku' => 'VLR-FASHION-BRW-006-BRN-M',
                    'stock_quantity' => 6,
                    'price_adjustment' => 0,
                ],
            ],

            'VLR-BLUE-REC-007' => [
                [
                    'color' => 'transparent',
                    'size' => 'M',
                    'sku' => 'VLR-BLUE-REC-007-CLR-M',
                    'stock_quantity' => 18,
                    'price_adjustment' => 0,
                ],
                [
                    'color' => 'black',
                    'size' => 'M',
                    'sku' => 'VLR-BLUE-REC-007-BLK-M',
                    'stock_quantity' => 12,
                    'price_adjustment' => 0,
                ],
            ],

            'VLR-BLUE-RND-008' => [
                [
                    'color' => 'silver',
                    'size' => 'M',
                    'sku' => 'VLR-BLUE-RND-008-SLV-M',
                    'stock_quantity' => 7,
                    'price_adjustment' => 0,
                ],
                [
                    'color' => 'gold',
                    'size' => 'M',
                    'sku' => 'VLR-BLUE-RND-008-GLD-M',
                    'stock_quantity' => 4,
                    'price_adjustment' => 50000,
                ],
            ],

            'VLR-KIDS-RND-009' => [
                [
                    'color' => 'blue',
                    'size' => 'S',
                    'sku' => 'VLR-KIDS-RND-009-BLU-S',
                    'stock_quantity' => 14,
                    'price_adjustment' => 0,
                ],
                [
                    'color' => 'black',
                    'size' => 'S',
                    'sku' => 'VLR-KIDS-RND-009-BLK-S',
                    'stock_quantity' => 8,
                    'price_adjustment' => 0,
                ],
            ],

            'VLR-KIDS-PNK-010' => [
                [
                    'color' => 'pink',
                    'size' => 'S',
                    'sku' => 'VLR-KIDS-PNK-010-PNK-S',
                    'stock_quantity' => 13,
                    'price_adjustment' => 0,
                ],
                [
                    'color' => 'purple',
                    'size' => 'S',
                    'sku' => 'VLR-KIDS-PNK-010-PPL-S',
                    'stock_quantity' => 5,
                    'price_adjustment' => 0,
                ],
            ],
        ];

        foreach ($variants as $productSku => $productVariants) {

            $product = Product::where(
                'sku',
                $productSku
            )->firstOrFail();

            foreach ($productVariants as $variant) {

                ProductVariant::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'color' => $variant['color'],
                        'size' => $variant['size'],
                    ],
                    [
                        'sku' => $variant['sku'],
                        'stock_quantity' => $variant['stock_quantity'],
                        'price_adjustment' => $variant['price_adjustment'],
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}