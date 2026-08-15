<?php

namespace App\Http\Controllers\Staff;

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
            (string) $request->query('keyword', '')
        );

        $categoryId =
            $request->query('category_id');

        $status =
            $request->query('status');


        if (
            $status
            && !in_array(
                $status,
                ['active', 'inactive'],
                true
            )
        ) {
            $status = null;
        }


        $products = Product::query()
            ->with([
                'category',
                'primaryImage',
            ])
            ->withCount('variants')

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


        $categories = Category::query()
            ->orderBy('name')
            ->get();


        return view(
            'staff.products.index',
            compact(
                'products',
                'categories',
                'keyword',
                'categoryId',
                'status'
            )
        );
    }


    /**
     * Form thêm sản phẩm.
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
            'staff.products.create',
            compact('categories')
        );
    }


    /**
     * Lưu sản phẩm.
     */
    public function store(
        StoreProductRequest $request
    ) {
        $product = Product::create([
            'category_id' =>
                $request->category_id,

            'name' =>
                trim($request->name),

            'slug' =>
                $this->generateUniqueSlug(
                    $request->name
                ),

            'sku' =>
                strtoupper(
                    trim($request->sku)
                ),

            'price' =>
                $request->price,

            'sale_price' =>
                $request->filled('sale_price')
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
             * Sản phẩm mới luôn Inactive.
             *
             * Staff phải thêm ảnh thật
             * và ít nhất 1 Variant hoạt động
             * trước khi bật kinh doanh.
             */
            'is_active' => false,
        ]);


        return redirect()
            ->route(
                'staff.products.show',
                $product
            )
            ->with(
                'success',
                'Thêm sản phẩm thành công. Hãy thêm hình ảnh và biến thể trước khi kích hoạt sản phẩm.'
            );
    }


    /**
     * Chi tiết sản phẩm.
     */
    public function show(
        Product $product,
        InventoryService $inventoryService
    ) {
        $product->load([
            'category',

            'images' => function ($query) {
                $query->orderBy('sort_order');
            },

            'variants' => function ($query) {
                $query->orderBy('id');
            },
        ]);


        return view(
            'staff.products.show',
            compact(
                'product',
                'inventoryService'
            )
        );
    }


    /**
     * Form sửa sản phẩm.
     */
    public function edit(
        Product $product
    ) {
        $categories = Category::query()
            ->orderBy('name')
            ->get();


        return view(
            'staff.products.edit',
            compact(
                'product',
                'categories'
            )
        );
    }


    /**
     * Cập nhật sản phẩm.
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
         * Không cho bật kinh doanh nếu chưa:
         *
         * - Có ảnh thật
         * - Có Variant hoạt động
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
                trim($request->name),

            'slug' =>
                $slug,

            'sku' =>
                strtoupper(
                    trim($request->sku)
                ),

            'price' =>
                $request->price,

            'sale_price' =>
                $request->filled('sale_price')
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
                'staff.products.show',
                $product
            )
            ->with(
                'success',
                'Cập nhật sản phẩm thành công.'
            );
    }


    /**
     * Sinh slug duy nhất.
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
                    function ($query) use ($ignoreId) {

                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        );
                    }
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