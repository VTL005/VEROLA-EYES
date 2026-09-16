<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Trang chủ Customer.
     */
    public function index()
    {
        $primaryCategory = Category::query()
            ->where('is_active', true)
            ->where('is_home_featured', true)
            ->first();

        if (! $primaryCategory) {
            $primaryCategory = Category::query()
                ->where('is_active', true)
                ->first();
        }

        $secondaryCategories = Category::query()
            ->where('is_active', true)
            ->when($primaryCategory, function ($query) use ($primaryCategory) {
                $query->where('id', '!=', $primaryCategory->id);
            })
            ->limit(4)
            ->get();

        $newProductsRaw = Product::query()
            ->where('is_active', true)
            ->whereHas('images', function ($query) {
                $query->where('image_path', '!=', 'images/no-image.png');
            })
            ->whereHas('variants', function ($query) {
                $query->where('is_active', true);
            })
            ->with(['category', 'primaryImage'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $primaryProduct = $newProductsRaw->first();
        $secondaryProducts = $newProductsRaw->skip(1)->take(4);

        /*
         * Sản phẩm đang giảm giá.
         */
        $saleProducts = Product::query()

            ->where(
                'is_active',
                true
            )

            ->whereNotNull(
                'sale_price'
            )

            ->whereColumn(
                'sale_price',
                '<',
                'price'
            )

            ->whereHas(
                'images',
                function ($query) {
                    $query->where(
                        'image_path',
                        '!=',
                        'images/no-image.png'
                    );
                }
            )

            ->whereHas(
                'variants',
                function ($query) {
                    $query->where(
                        'is_active',
                        true
                    );
                }
            )

            ->with([
                'category',
                'primaryImage',
            ])

            ->latest()

            ->limit(8)

            ->get();

        return view(
            'home',
            compact(
                'primaryCategory',
                'secondaryCategories',
                'primaryProduct',
                'secondaryProducts',
                'saleProducts'
            )
        );
    }
}
