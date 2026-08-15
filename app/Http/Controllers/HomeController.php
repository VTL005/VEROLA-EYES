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
        /*
         * Danh mục đang hoạt động.
         */
        $categories = Category::query()
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->limit(6)
            ->get();


        /*
         * Sản phẩm mới.
         *
         * Chỉ hiển thị Product thực sự
         * sẵn sàng kinh doanh.
         */
        $newProducts = Product::query()

            ->where(
                'is_active',
                true
            )

            /*
             * Phải có ảnh thật.
             */
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

            /*
             * Phải có Variant active.
             */
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
                'categories',
                'newProducts',
                'saleProducts'
            )
        );
    }
}