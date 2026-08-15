<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;

class InventoryService
{
    /**
     * Stock <= 5 được xem là sắp hết hàng.
     */
    public const LOW_STOCK_THRESHOLD = 5;

    public const STATUS_IN_STOCK = 'in_stock';
    public const STATUS_LOW_STOCK = 'low_stock';
    public const STATUS_OUT_OF_STOCK = 'out_of_stock';


    /**
     * Variant đã hết hàng?
     */
    public function isOutOfStock(
        ProductVariant $variant
    ): bool {
        return $variant->stock_quantity <= 0;
    }


    /**
     * Variant sắp hết hàng?
     */
    public function isLowStock(
        ProductVariant $variant
    ): bool {
        return $variant->stock_quantity > 0
            && $variant->stock_quantity
                <= self::LOW_STOCK_THRESHOLD;
    }


    /**
     * Trạng thái tồn kho.
     */
    public function stockStatus(
        ProductVariant $variant
    ): string {
        if (
            $this->isOutOfStock(
                $variant
            )
        ) {
            return self::STATUS_OUT_OF_STOCK;
        }


        if (
            $this->isLowStock(
                $variant
            )
        ) {
            return self::STATUS_LOW_STOCK;
        }


        return self::STATUS_IN_STOCK;
    }


    /**
     * Nhãn tiếng Việt.
     */
    public function stockLabel(
        ProductVariant $variant
    ): string {
        return match (
            $this->stockStatus(
                $variant
            )
        ) {
            self::STATUS_OUT_OF_STOCK =>
                'Hết hàng',

            self::STATUS_LOW_STOCK =>
                'Sắp hết hàng',

            default =>
                'Còn hàng',
        };
    }


    /**
     * Tổng tồn kho của Product.
     *
     * Chỉ dùng để thống kê.
     * Khi bán vẫn trừ theo từng Variant.
     */
    public function totalStock(
        Product $product
    ): int {
        return (int) $product
            ->variants()
            ->sum(
                'stock_quantity'
            );
    }
}