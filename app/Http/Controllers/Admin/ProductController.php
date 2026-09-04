<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm.
     */
    public function index(Request $request)
    {
        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );

        $categoryId =
            $request->query(
                'category_id'
            );

        $status =
            $request->query(
                'status'
            );


        if (
            $status
            && ! in_array(
                $status,
                [
                    'active',
                    'inactive',
                ],
                true
            )
        ) {
            $status = null;
        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCT QUERY
        |--------------------------------------------------------------------------
        */

        $products = Product::query()

            ->with([
                'category',
                'primaryImage',
            ])

            ->withCount([

                'variants',

                'images as real_images_count' =>
                    function ($query) {

                        $query->where(
                            'image_path',
                            '!=',
                            'images/no-image.png'
                        );
                    },

                'variants as active_variants_count' =>
                    function ($query) {

                        $query->where(
                            'is_active',
                            true
                        );
                    },
            ])

            ->withSum(
                [
                    'variants as active_stock' =>
                        function ($query) {

                            $query->where(
                                'is_active',
                                true
                            );
                        },
                ],
                'stock_quantity'
            )

            ->when(
                $keyword !== '',
                function ($query) use ($keyword) {

                    $query->where(
                        function ($subQuery) use ($keyword) {

                            $subQuery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$keyword}%"
                                )

                                ->orWhere(
                                    'sku',
                                    'like',
                                    "%{$keyword}%"
                                );
                        }
                    );
                }
            )

            ->when(
                $categoryId,
                function ($query) use ($categoryId) {

                    $query->where(
                        'category_id',
                        $categoryId
                    );
                }
            )

            ->when(
                $status === 'active',
                function ($query) {

                    $query->where(
                        'is_active',
                        true
                    );
                }
            )

            ->when(
                $status === 'inactive',
                function ($query) {

                    $query->where(
                        'is_active',
                        false
                    );
                }
            )

            ->latest()

            ->paginate(10)

            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | CATEGORY FILTER
        |--------------------------------------------------------------------------
        */

        $categories = Category::query()
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $totalProducts = Product::query()
            ->count();


        $activeProducts = Product::query()
            ->where(
                'is_active',
                true
            )
            ->count();


        $inactiveProducts = Product::query()
            ->where(
                'is_active',
                false
            )
            ->count();


        $readyProducts = Product::query()

            ->where(
                'is_active',
                false
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

            ->count();


        return view(
            'admin.products.index',
            compact(
                'products',
                'categories',
                'keyword',
                'categoryId',
                'status',
                'totalProducts',
                'activeProducts',
                'inactiveProducts',
                'readyProducts'
            )
        );
    }


    /**
     * Form tạo sản phẩm.
     */
    public function create()
    {
        $categories = Category::query()
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get();


        return view(
            'admin.products.create',
            compact(
                'categories'
            )
        );
    }


    /**
     * Lưu sản phẩm mới.
     */
    public function store(
        StoreProductRequest $request
    ) {
        /*
        |--------------------------------------------------------------------------
        | CREATE PRODUCT
        |--------------------------------------------------------------------------
        */

        $product = Product::create([

            'category_id' =>
                $request->category_id,


            'name' =>
                trim(
                    $request->name
                ),


            'slug' =>
                $this->generateUniqueSlug(
                    $request->name
                ),


            'sku' =>
                strtoupper(
                    trim(
                        $request->sku
                    )
                ),


            'price' =>
                $request->price,


            /*
             * Giá khuyến mãi:
             *
             * - trống -> NULL
             * - 0     -> NULL
             * - > 0   -> lưu
             */
            'sale_price' =>
                $request->filled('sale_price')
                && (float) $request->sale_price > 0
                    ? $request->sale_price
                    : null,


            'material' =>
                $request->material,


            'shape' =>
                $request->shape,


            'gender' =>
                $request->gender,


            'dimensions' =>
                $request->dimensions,


            'description' =>
                $request->description,


            'highlights' =>
                $request->highlights,


            'recommended_face_shapes' =>
                $request->input(
                    'recommended_face_shapes',
                    []
                ),


            'style_tags' =>
                $request->input(
                    'style_tags',
                    []
                ),


            /*
             * Product mới luôn chưa kinh doanh.
             */
            'is_active' => false,
        ]);


        /*
        |--------------------------------------------------------------------------
        | SINGLE PAGE / AJAX RESPONSE
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            return response()->json(
                [
                    'success' => true,

                    'message' =>
                        'Đã lưu thông tin sản phẩm.',


                    'product' => [

                        'id' =>
                            $product->id,

                        'name' =>
                            $product->name,

                        'sku' =>
                            $product->sku,

                        'is_active' =>
                            (bool) $product->is_active,
                    ],


                    'urls' => [

                        'show' =>
                            route(
                                'admin.products.show',
                                $product
                            ),

                        'upload_images' =>
                            route(
                                'admin.products.images.store',
                                $product
                            ),

                        'store_variant' =>
                            route(
                                'admin.products.variants.store',
                                $product
                            ),

                        'activate' =>
                            route(
                                'admin.products.activate',
                                $product
                            ),
                    ],
                ],
                201
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'admin.products.show',
                $product
            )
            ->with(
                'success',
                'Đã lưu thông tin sản phẩm. Bạn có thể tiếp tục hoàn thiện hình ảnh, biến thể và tồn kho.'
            );
    }


    /**
     * Chi tiết / Hoàn thiện sản phẩm.
     */
    public function show(
        Product $product,
        InventoryService $inventoryService
    ) {
        /*
        |--------------------------------------------------------------------------
        | LOAD DATA
        |--------------------------------------------------------------------------
        */

        $product->load([
            'category',
            'images',
            'variants',
        ]);


        /*
        |--------------------------------------------------------------------------
        | HÌNH ẢNH THẬT
        |--------------------------------------------------------------------------
        */

        $realImages = $product->images
            ->filter(
                function ($image) {

                    return $image->image_path
                        !== 'images/no-image.png';
                }
            );


        $realImageCount =
            $realImages->count();


        $hasRealImage =
            $realImageCount > 0;


        /*
        |--------------------------------------------------------------------------
        | VARIANT ACTIVE
        |--------------------------------------------------------------------------
        */

        $activeVariants = $product->variants
            ->where(
                'is_active',
                true
            );


        $activeVariantCount =
            $activeVariants->count();


        $hasActiveVariant =
            $activeVariantCount > 0;


        /*
        |--------------------------------------------------------------------------
        | TỒN KHO
        |--------------------------------------------------------------------------
        */

        $totalStock = $activeVariants
            ->sum(
                'stock_quantity'
            );


        /*
        |--------------------------------------------------------------------------
        | ĐỦ ĐIỀU KIỆN KINH DOANH
        |--------------------------------------------------------------------------
        */

        $isReadyForSale =
            $hasRealImage
            &&
            $hasActiveVariant;


        /*
        |--------------------------------------------------------------------------
        | TIẾN ĐỘ
        |--------------------------------------------------------------------------
        */

        $completedSteps = 1;


        if ($hasRealImage) {

            $completedSteps++;
        }


        if ($hasActiveVariant) {

            $completedSteps++;
        }


        if ($product->is_active) {

            $completedSteps++;
        }


        $completionPercent =
            (int) round(
                ($completedSteps / 4)
                * 100
            );


        return view(
            'admin.products.show',
            compact(
                'product',
                'inventoryService',
                'realImages',
                'realImageCount',
                'hasRealImage',
                'activeVariants',
                'activeVariantCount',
                'hasActiveVariant',
                'totalStock',
                'isReadyForSale',
                'completedSteps',
                'completionPercent'
            )
        );
    }


    /**
     * Form sửa / quản lý toàn bộ sản phẩm.
     */
    public function edit(
        Product $product
    ) {
        /*
        |--------------------------------------------------------------------------
        | LOAD PRODUCT DATA
        |--------------------------------------------------------------------------
        */

        $product->load([

            'category',

            'images' =>
                function ($query) {

                    $query
                        ->orderByDesc(
                            'is_primary'
                        )
                        ->orderBy(
                            'sort_order'
                        );
                },

            'variants' =>
                function ($query) {

                    $query->orderBy(
                        'sku'
                    );
                },
        ]);


        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        $categories = Category::query()
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | HÌNH ẢNH THẬT
        |--------------------------------------------------------------------------
        */

        $realImages =
            $product->images
                ->filter(
                    function ($image) {

                        return $image->image_path
                            !== 'images/no-image.png';
                    }
                )
                ->values();


        $realImageCount =
            $realImages->count();


        $hasRealImage =
            $realImageCount > 0;


        /*
        |--------------------------------------------------------------------------
        | VARIANT ACTIVE
        |--------------------------------------------------------------------------
        */

        $activeVariants =
            $product->variants
                ->where(
                    'is_active',
                    true
                );


        $activeVariantCount =
            $activeVariants->count();


        $hasActiveVariant =
            $activeVariantCount > 0;


        /*
        |--------------------------------------------------------------------------
        | TỒN KHO
        |--------------------------------------------------------------------------
        */

        $totalStock =
            (int) $activeVariants
                ->sum(
                    'stock_quantity'
                );


        /*
        |--------------------------------------------------------------------------
        | READY FOR SALE
        |--------------------------------------------------------------------------
        */

        $readyForSale =
            $hasRealImage
            &&
            $hasActiveVariant;


        return view(
            'admin.products.edit',
            compact(
                'product',
                'categories',

                'realImages',
                'realImageCount',
                'hasRealImage',

                'activeVariants',
                'activeVariantCount',
                'hasActiveVariant',

                'totalStock',
                'readyForSale'
            )
        );
    }


    /**
     * Cập nhật thông tin Product.
     */
    public function update(
        UpdateProductRequest $request,
        Product $product
    ) {
        /*
        |--------------------------------------------------------------------------
        | SLUG
        |--------------------------------------------------------------------------
        */

        $slug =
            $product->slug;


        /*
         * Chỉ tạo lại Slug khi tên thay đổi.
         */
        if (
            trim(
                $request->name
            )
            !== $product->name
        ) {
            $slug =
                $this->generateUniqueSlug(
                    $request->name,
                    $product->id
                );
        }


        /*
        |--------------------------------------------------------------------------
        | TRẠNG THÁI KINH DOANH
        |--------------------------------------------------------------------------
        */

        $wantsToActivate =
            $request->boolean(
                'is_active'
            );


        /*
         * Không cho bật kinh doanh nếu:
         *
         * - chưa có ảnh thật
         * - hoặc chưa có Variant active
         */
        if (
            $wantsToActivate
            && ! $product->isReadyForSale()
        ) {
            /*
             * AJAX.
             */
            if ($request->expectsJson()) {

                return response()->json(
                    [
                        'success' =>
                            false,

                        'message' =>
                            'Sản phẩm phải có ít nhất 1 ảnh thật và 1 biến thể đang hoạt động trước khi được kinh doanh.',

                        'errors' => [

                            'is_active' => [
                                'Sản phẩm phải có ít nhất 1 ảnh thật và 1 biến thể đang hoạt động trước khi được kinh doanh.',
                            ],
                        ],
                    ],
                    422
                );
            }


            /*
             * Fallback cũ.
             */
            return back()
                ->withInput()
                ->withErrors([
                    'is_active' =>
                        'Sản phẩm phải có ít nhất 1 ảnh thật và 1 biến thể đang hoạt động trước khi được kinh doanh.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE PRODUCT
        |--------------------------------------------------------------------------
        */

        $product->update([

            'category_id' =>
                $request->category_id,


            'name' =>
                trim(
                    $request->name
                ),


            'slug' =>
                $slug,


            'sku' =>
                strtoupper(
                    trim(
                        $request->sku
                    )
                ),


            'price' =>
                $request->price,


            /*
             * Giá khuyến mãi:
             *
             * - trống -> NULL
             * - 0     -> NULL
             * - > 0   -> lưu
             */
            'sale_price' =>
                $request->filled('sale_price')
                && (float) $request->sale_price > 0
                    ? $request->sale_price
                    : null,


            'material' =>
                $request->material,


            'shape' =>
                $request->shape,


            'gender' =>
                $request->gender,


            'dimensions' =>
                $request->dimensions,


            'description' =>
                $request->description,


            'highlights' =>
                $request->highlights,


            'recommended_face_shapes' =>
                $request->input(
                    'recommended_face_shapes',
                    []
                ),


            'style_tags' =>
                $request->input(
                    'style_tags',
                    []
                ),


            'is_active' =>
                $wantsToActivate,
        ]);


        /*
        |--------------------------------------------------------------------------
        | LẤY LẠI TRẠNG THÁI SAU KHI UPDATE
        |--------------------------------------------------------------------------
        */

        $hasRealImage =
            $product->images()
                ->where(
                    'image_path',
                    '!=',
                    'images/no-image.png'
                )
                ->exists();


        $activeVariantCount =
            $product->variants()
                ->where(
                    'is_active',
                    true
                )
                ->count();


        $hasActiveVariant =
            $activeVariantCount > 0;


        $totalStock =
            (int) $product->variants()
                ->where(
                    'is_active',
                    true
                )
                ->sum(
                    'stock_quantity'
                );


        $readyForSale =
            $hasRealImage
            &&
            $hasActiveVariant;


        /*
        |--------------------------------------------------------------------------
        | AJAX RESPONSE
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            return response()->json([

                'success' =>
                    true,


                'message' =>
                    'Cập nhật thông tin sản phẩm thành công.',


                'product' => [

                    'id' =>
                        $product->id,

                    'name' =>
                        $product->name,

                    'sku' =>
                        $product->sku,

                    'slug' =>
                        $product->slug,

                    'price' =>
                        (float) $product->price,

                    'sale_price' =>
                        $product->sale_price !== null
                            ? (float) $product->sale_price
                            : null,

                    'current_price' =>
                        (float) $product->current_price,

                    'is_active' =>
                        (bool) $product->is_active,
                ],


                'status' => [

                    'has_real_image' =>
                        $hasRealImage,

                    'has_active_variant' =>
                        $hasActiveVariant,

                    'active_variant_count' =>
                        $activeVariantCount,

                    'total_stock' =>
                        $totalStock,

                    'ready_for_sale' =>
                        $readyForSale,
                ],


                'urls' => [

                    'show' =>
                        route(
                            'admin.products.show',
                            $product
                        ),

                    'edit' =>
                        route(
                            'admin.products.edit',
                            $product
                        ),

                    'customer_show' =>
                        route(
                            'products.show',
                            $product
                        ),
                ],
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK CŨ
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'admin.products.show',
                $product
            )
            ->with(
                'success',
                'Cập nhật sản phẩm thành công.'
            );
    }


    /**
     * Kích hoạt sản phẩm
     * và đưa lên website.
     */
    public function activate(
        Product $product,
        Request $request
    ) {
        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA ẢNH THẬT
        |--------------------------------------------------------------------------
        */

        $hasRealImage =
            $product->images()
                ->where(
                    'image_path',
                    '!=',
                    'images/no-image.png'
                )
                ->exists();


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA VARIANT ACTIVE
        |--------------------------------------------------------------------------
        */

        $hasActiveVariant =
            $product->variants()
                ->where(
                    'is_active',
                    true
                )
                ->exists();


        /*
        |--------------------------------------------------------------------------
        | CHƯA ĐỦ ĐIỀU KIỆN
        |--------------------------------------------------------------------------
        */

        if (
            ! $hasRealImage
            ||
            ! $hasActiveVariant
        ) {
            $missing = [];


            if (! $hasRealImage) {

                $missing[] =
                    'hình ảnh';
            }


            if (! $hasActiveVariant) {

                $missing[] =
                    'biến thể hoạt động';
            }


            $message =
                'Sản phẩm chưa thể kích hoạt vì còn thiếu: '
                . implode(
                    ', ',
                    $missing
                )
                . '.';


            /*
             * AJAX.
             */
            if ($request->expectsJson()) {

                return response()->json(
                    [
                        'success' =>
                            false,

                        'message' =>
                            $message,

                        'has_real_image' =>
                            $hasRealImage,

                        'has_active_variant' =>
                            $hasActiveVariant,

                        'is_ready_for_sale' =>
                            false,
                    ],
                    422
                );
            }


            /*
             * Fallback cũ.
             */
            return redirect()
                ->route(
                    'admin.products.show',
                    $product
                )
                ->with(
                    'error',
                    $message
                );
        }


        /*
        |--------------------------------------------------------------------------
        | KÍCH HOẠT
        |--------------------------------------------------------------------------
        */

        $product->update([
            'is_active' => true,
        ]);


        /*
        |--------------------------------------------------------------------------
        | AJAX RESPONSE
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            return response()->json([

                'success' =>
                    true,


                'message' =>
                    'Sản phẩm đã được kích hoạt và có thể hiển thị trên website.',


                'product' => [

                    'id' =>
                        $product->id,

                    'name' =>
                        $product->name,

                    'sku' =>
                        $product->sku,

                    'is_active' =>
                        true,
                ],


                'has_real_image' =>
                    true,

                'has_active_variant' =>
                    true,

                'is_ready_for_sale' =>
                    true,


                'urls' => [

                    'show' =>
                        route(
                            'admin.products.show',
                            $product
                        ),

                    'customer_show' =>
                        route(
                            'products.show',
                            $product
                        ),

                    'index' =>
                        route(
                            'admin.products.index'
                        ),
                ],
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK CŨ
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'admin.products.show',
                $product
            )
            ->with(
                'success',
                'Sản phẩm đã được kích hoạt và có thể hiển thị trên website.'
            );
    }


    /**
     * Xóa sản phẩm.
     */
    public function destroy(
        Product $product
    ) {
        /*
         * Sản phẩm đã có đơn hàng:
         * không xóa hoàn toàn.
         */
        if (
            $product
                ->orderDetails()
                ->exists()
        ) {
            $product->update([
                'is_active' =>
                    false,
            ]);


            return back()
                ->with(
                    'error',
                    'Sản phẩm đã phát sinh đơn hàng nên không thể xóa. Hệ thống đã chuyển sản phẩm sang trạng thái không hoạt động.'
                );
        }


        /*
         * Soft Delete.
         */
        $product->delete();


        return redirect()
            ->route(
                'admin.products.index'
            )
            ->with(
                'success',
                'Xóa sản phẩm thành công.'
            );
    }


    /**
     * Sinh Slug duy nhất.
     */
    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $baseSlug =
            Str::slug(
                $name
            );


        $slug =
            $baseSlug;


        $counter =
            1;


        while (
            Product::withTrashed()

                ->where(
                    'slug',
                    $slug
                )

                ->when(
                    $ignoreId,
                    fn ($query) =>
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        )
                )

                ->exists()
        ) {
            $slug =
                $baseSlug
                . '-'
                . $counter;


            $counter++;
        }


        return $slug;
    }
}