<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'VELORA10',
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'minimum_order_amount' => 500000,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(6),
                'usage_limit' => 200,
                'usage_count' => 0,
                'is_active' => true,
            ],

            [
                'code' => 'VLR100K',
                'discount_type' => 'fixed',
                'discount_value' => 100000,
                'minimum_order_amount' => 1000000,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(6),
                'usage_limit' => 100,
                'usage_count' => 0,
                'is_active' => true,
            ],

            [
                'code' => 'WELCOME50',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'minimum_order_amount' => 300000,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
                'usage_limit' => null,
                'usage_count' => 0,
                'is_active' => true,
            ],

            /*
             * Voucher hết hạn để test Validation.
             */
            [
                'code' => 'EXPIRED20',
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'minimum_order_amount' => 500000,
                'starts_at' => now()->subMonths(2),
                'ends_at' => now()->subMonth(),
                'usage_limit' => 50,
                'usage_count' => 10,
                'is_active' => true,
            ],

            /*
             * Voucher bị Admin khóa để test.
             */
            [
                'code' => 'LOCKED15',
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'minimum_order_amount' => 500000,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
                'usage_limit' => 50,
                'usage_count' => 0,
                'is_active' => false,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::updateOrCreate(
                [
                    'code' => $voucher['code'],
                ],
                $voucher
            );
        }
    }
}