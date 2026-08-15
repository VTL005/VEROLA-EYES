<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Hiển thị sản phẩm theo danh mục.
     */
    public function show(Category $category)
    {
        /*
        |--------------------------------------------------------------------------
        | CATEGORY PHẢI ĐANG HOẠT ĐỘNG
        |--------------------------------------------------------------------------
        */

        abort_if(
            !$category->is_active,
            404
        );


        /*
        |--------------------------------------------------------------------------
        | PRODUCTS
        |--------------------------------------------------------------------------
        |
        | Customer chỉ được xem Product:
        |
        | - Đang hoạt động
        | - Có ít nhất một ảnh thật
        | - Có ít nhất một Variant active
        |
        */

        $products =
            $category
                ->products()

                ->where(
                    'is_active',
                    true
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
                    'primaryImage',
                ])

                ->latest()

                ->paginate(12);


        return view(
            'categories.show',
            compact(
                'category',
                'products'
            )
        );
    }
}