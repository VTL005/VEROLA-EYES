<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use App\Services\ProductRecommendationService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm phía Customer.
     */
    public function index(Request $request)
    {
        $query = Product::query()

            /*
             * Chỉ Product đang kinh doanh.
             */
            ->where(
                'is_active',
                true
            )

            /*
             * Phải có ít nhất 1 ảnh thật.
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
             * Phải có ít nhất 1 Variant active.
             */
            ->whereHas(
                'variants',
                function ($query) {

                    $query->where(
                        'is_active',
                        true
                    );
                }
            );


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        |
        | Tìm theo:
        | - Tên Product
        | - SKU Product
        | - Shape
        | - Material
        | - Color của Variant
        |
        */

        if ($request->filled('keyword')) {

            $keyword = trim(
                $request->keyword
            );


            $query->where(
                function ($q) use ($keyword) {

                    $q->where(
                        'name',
                        'like',
                        "%{$keyword}%"
                    )

                    ->orWhere(
                        'sku',
                        'like',
                        "%{$keyword}%"
                    )

                    ->orWhere(
                        'shape',
                        'like',
                        "%{$keyword}%"
                    )

                    ->orWhere(
                        'material',
                        'like',
                        "%{$keyword}%"
                    )

                    ->orWhereHas(
                        'variants',
                        function ($variantQuery) use ($keyword) {

                            $variantQuery
                                ->where(
                                    'is_active',
                                    true
                                )
                                ->where(
                                    'color',
                                    'like',
                                    "%{$keyword}%"
                                );
                        }
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER CATEGORY
        |--------------------------------------------------------------------------
        */

        if ($request->filled('category_id')) {

            $query->where(
                'category_id',
                $request->category_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER PRICE
        |--------------------------------------------------------------------------
        |
        | Nếu có sale_price:
        | dùng sale_price.
        |
        | Nếu không:
        | dùng price.
        |
        */

        if ($request->filled('min_price')) {

            $query->whereRaw(
                'COALESCE(sale_price, price) >= ?',
                [
                    (float) $request->min_price,
                ]
            );
        }


        if ($request->filled('max_price')) {

            $query->whereRaw(
                'COALESCE(sale_price, price) <= ?',
                [
                    (float) $request->max_price,
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER SHAPE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('shape')) {

            $query->where(
                'shape',
                $request->shape
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER MATERIAL
        |--------------------------------------------------------------------------
        */

        if ($request->filled('material')) {

            $query->where(
                'material',
                $request->material
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER GENDER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('gender')) {

            $query->where(
                'gender',
                $request->gender
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FILTER COLOR
        |--------------------------------------------------------------------------
        */

        if ($request->filled('color')) {

            $color = trim(
                $request->color
            );


            $query->whereHas(
                'variants',
                function ($variantQuery) use ($color) {

                    $variantQuery
                        ->where(
                            'is_active',
                            true
                        )
                        ->where(
                            'color',
                            'like',
                            "%{$color}%"
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */

        switch ($request->sort) {

            case 'price_asc':

                $query->orderByRaw(
                    'COALESCE(sale_price, price) ASC'
                );

                break;


            case 'price_desc':

                $query->orderByRaw(
                    'COALESCE(sale_price, price) DESC'
                );

                break;


            case 'newest':

            default:

                $query->latest();

                break;
        }


        /*
        |--------------------------------------------------------------------------
        | LOAD DATA
        |--------------------------------------------------------------------------
        */

        $products = $query
            ->with([
                'category',
                'primaryImage',
            ])
            ->paginate(12)
            ->withQueryString();


        $categories = Category::query()
    ->where('is_active', true)
    ->orderBy('name')
    ->get();


/*
|--------------------------------------------------------------------------
| FILTER OPTIONS
|--------------------------------------------------------------------------
|
| Lấy trực tiếp giá trị đang tồn tại trong database.
| Không hard-code shape/material/gender.
|
*/

$shapes = Product::query()
    ->where('is_active', true)
    ->whereNotNull('shape')
    ->where('shape', '!=', '')
    ->distinct()
    ->orderBy('shape')
    ->pluck('shape');


$materials = Product::query()
    ->where('is_active', true)
    ->whereNotNull('material')
    ->where('material', '!=', '')
    ->distinct()
    ->orderBy('material')
    ->pluck('material');


$genders = Product::query()
    ->where('is_active', true)
    ->whereNotNull('gender')
    ->where('gender', '!=', '')
    ->distinct()
    ->orderBy('gender')
    ->pluck('gender');


return view(
    'products.index',
    compact(
        'products',
        'categories',
        'shapes',
        'materials',
        'genders'
    )
);
    }


    /**
     * Chi tiết sản phẩm phía Customer.
     */
    public function show(
        Product $product,
        InventoryService $inventoryService,
        ProductRecommendationService $recommendationService
    ) {
        /*
         * Customer chỉ được xem Product
         * đang thực sự sẵn sàng kinh doanh.
         */
        abort_if(
            !$product->is_active
            || !$product->hasRealImage()
            || !$product->hasActiveVariant(),
            404
        );


        /*
         * Load dữ liệu dùng cho trang chi tiết.
         */
        $product->load([

            /*
             * Danh mục.
             */
            'category',


            /*
             * Chỉ lấy ảnh thật.
             */
            'images' => function ($query) {

                $query
                    ->where(
                        'image_path',
                        '!=',
                        'images/no-image.png'
                    )
                    ->orderByDesc(
                        'is_primary'
                    )
                    ->orderBy(
                        'sort_order'
                    );
            },


            /*
             * Chỉ lấy Variant đang hoạt động.
             */
            'variants' => function ($query) {

                $query
                    ->where(
                        'is_active',
                        true
                    )
                    ->orderBy(
                        'color'
                    )
                    ->orderBy(
                        'size'
                    );
            },


            /*
             * Chỉ lấy Review đang hiển thị.
             */
            'reviews' => function ($query) {

                $query
                    ->where(
                        'is_visible',
                        true
                    )
                    ->with(
                        'user'
                    )
                    ->latest();
            },

        ]);


        /*
        |--------------------------------------------------------------------------
        | REVIEW STATISTICS
        |--------------------------------------------------------------------------
        */

        /*
         * Điểm đánh giá trung bình.
         */
        $averageRating =
            $product->reviews->isNotEmpty()
                ? round(
                    $product->reviews
                        ->avg('rating'),
                    1
                )
                : null;


        /*
         * Tổng số Review.
         */
        $reviewCount =
            $product->reviews->count();


        /*
        |--------------------------------------------------------------------------
        | PRODUCT RECOMMENDATION
        |--------------------------------------------------------------------------
        |
        | Lấy tối đa 4 sản phẩm phù hợp.
        |
        */

        $recommendedProducts =
            $recommendationService
                ->recommend(
                    $product,
                    4
                );

/*
|--------------------------------------------------------------------------
| WISHLIST STATUS
|------------------------------------------------------------------------

|
*/

$isWishlisted = false;


if (
    auth()->check()
    && auth()->user()->isCustomer()
) {

    $isWishlisted = auth()
        ->user()
        ->wishlist()
        ->whereHas(
            'items',
            function ($query) use ($product) {

                $query->where(
                    'product_id',
                    $product->id
                );

            }
        )
        ->exists();
}
        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

      return view(
    'products.show',
    compact(
        'product',
        'inventoryService',
        'averageRating',
        'reviewCount',
        'recommendedProducts',
        'isWishlisted'
    )
);
    }
}