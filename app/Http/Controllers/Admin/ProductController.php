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
    public function index(
        Request $request
    ) {
        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );


        $categoryId =
            $request->query('category_id');


        $status =
            $request->query('status');


        if (
            $status
            && !in_array(
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

        $products =
            Product::query()

                ->with([
                    'category',
                    'primaryImage',
                ])

                ->withCount([
                    'variants',
                ])

                /*
                 * Search tên / SKU.
                 */
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

                /*
                 * Category.
                 */
                ->when(
                    $categoryId,
                    function ($query) use ($categoryId) {

                        $query->where(
                            'category_id',
                            $categoryId
                        );
                    }
                )

                /*
                 * Active.
                 */
                ->when(
                    $status === 'active',
                    function ($query) {

                        $query->where(
                            'is_active',
                            true
                        );
                    }
                )

                /*
                 * Inactive.
                 */
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

        $categories =
            Category::query()
                ->orderBy('name')
                ->get();


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $totalProducts =
            Product::query()
                ->count();


        $activeProducts =
            Product::query()
                ->where(
                    'is_active',
                    true
                )
                ->count();


        $inactiveProducts =
            Product::query()
                ->where(
                    'is_active',
                    false
                )
                ->count();


        $readyProducts =
            Product::query()
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
     * Form tạo Product.
     */
    public function create()
    {
        $categories =
            Category::query()
                ->where(
                    'is_active',
                    true
                )
                ->orderBy('name')
                ->get();


        return view(
            'admin.products.create',
            compact('categories')
        );
    }


    /**
     * Lưu Product.
     */
    public function store(
        StoreProductRequest $request
    ) {
        $product =
            Product::create([
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

                'sale_price' =>
                    $request->filled(
                        'sale_price'
                    )
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
                 * Product mới luôn Inactive.
                 */
                'is_active' =>
                    false,
            ]);


        return redirect()
            ->route(
                'admin.products.show',
                $product
            )
            ->with(
                'success',
                'Thêm sản phẩm thành công. Hãy thêm hình ảnh và biến thể trước khi kích hoạt sản phẩm.'
            );
    }


    /**
     * Chi tiết Product.
     */
    public function show(
        Product $product,
        InventoryService $inventoryService
    ) {
        $product->load([
            'category',
            'images',
            'variants',
        ]);


        return view(
            'admin.products.show',
            compact(
                'product',
                'inventoryService'
            )
        );
    }


    /**
     * Form sửa Product.
     */
    public function edit(
        Product $product
    ) {
        $categories =
            Category::query()
                ->orderBy('name')
                ->get();


        return view(
            'admin.products.edit',
            compact(
                'product',
                'categories'
            )
        );
    }


    /**
     * Cập nhật Product.
     */
    public function update(
        UpdateProductRequest $request,
        Product $product
    ) {
        $slug =
            $product->slug;


        if (
            trim($request->name)
            !== $product->name
        ) {
            $slug =
                $this->generateUniqueSlug(
                    $request->name,
                    $product->id
                );
        }


        $wantsToActivate =
            $request->boolean(
                'is_active'
            );


        /*
         * Chỉ được Active khi Product
         * đã sẵn sàng để bán.
         */
        if (
            $wantsToActivate
            && !$product->isReadyForSale()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'is_active' =>
                        'Sản phẩm phải có ít nhất 1 ảnh thật và 1 biến thể đang hoạt động trước khi được kinh doanh.',
                ]);
        }


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

            'sale_price' =>
                $request->filled(
                    'sale_price'
                )
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
     * Xóa Product.
     */
    public function destroy(
        Product $product
    ) {
        /*
         * Đã phát sinh Order:
         * không được xóa.
         */
        if (
            $product
                ->orderDetails()
                ->exists()
        ) {
            $product->update([
                'is_active' => false,
            ]);


            return back()->with(
                'error',
                'Sản phẩm đã phát sinh đơn hàng nên không thể xóa. Hệ thống đã chuyển sản phẩm sang trạng thái không hoạt động.'
            );
        }


        /*
         * Đang tồn tại trong Wishlist:
         * không được xóa.
         */
        if (
            $product
                ->wishlistItems()
                ->exists()
        ) {
            $product->update([
                'is_active' => false,
            ]);


            return back()->with(
                'error',
                'Sản phẩm đang tồn tại trong Wishlist của khách hàng nên không thể xóa. Hệ thống đã chuyển sang trạng thái không hoạt động.'
            );
        }


        /*
         * Chưa có lịch sử quan trọng:
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
            Str::slug($name);


        $slug =
            $baseSlug;


        $counter = 1;


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