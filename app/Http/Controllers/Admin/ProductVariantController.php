<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductVariantRequest;
use App\Http\Requests\Product\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    /**
     * Form thêm Variant.
     */
    public function create(
        Product $product
    ) {
        return view(
            'admin.products.variants.create',
            compact(
                'product'
            )
        );
    }


    /**
     * Lưu Variant.
     */
    public function store(
        StoreProductVariantRequest $request,
        Product $product
    ) {
        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA GIÁ CUỐI
        |--------------------------------------------------------------------------
        |
        | Giá cuối =
        | giá hiện tại Product
        | + chênh lệch giá Variant.
        |
        */

        $finalPrice =
            (float) $product->current_price
            +
            (float) $request->price_adjustment;


        if ($finalPrice <= 0) {

            if ($request->expectsJson()) {

                return response()->json(
                    [
                        'success' => false,

                        'message' =>
                            'Giá cuối của biến thể phải lớn hơn 0.',

                        'errors' => [
                            'price_adjustment' => [
                                'Giá cuối của biến thể phải lớn hơn 0.',
                            ],
                        ],
                    ],
                    422
                );
            }


            return back()
                ->withErrors([
                    'price_adjustment' =>
                        'Giá cuối của biến thể phải lớn hơn 0.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | CREATE VARIANT
        |--------------------------------------------------------------------------
        */

        $variant = ProductVariant::create([

            'product_id' =>
                $product->id,

            'color' =>
                $request->color,

            'size' =>
                $request->size,

            'sku' =>
                $request->sku,

            'stock_quantity' =>
                $request->stock_quantity,

            'price_adjustment' =>
                $request->price_adjustment,

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),
        ]);


        /*
        |--------------------------------------------------------------------------
        | SINGLE-PAGE / AJAX RESPONSE
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            return response()->json(
                array_merge(
                    [
                        'success' => true,

                        'message' =>
                            'Thêm biến thể sản phẩm thành công.',
                    ],
                    $this->buildVariantState(
                        $product,
                        $variant->id
                    )
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        |
        | Luồng chính của Admin hiện nay là quản lý tất cả
        | ngay trên trang Sửa sản phẩm.
        |
        */

        return redirect()
            ->to(
                route(
                    'admin.products.edit',
                    $product
                )
                . '#edit-product-variants'
            )
            ->with(
                'success',
                'Thêm biến thể sản phẩm thành công.'
            );
    }


    /**
     * Form sửa Variant riêng.
     *
     * Vẫn giữ route này làm fallback.
     */
    public function edit(
        Product $product,
        ProductVariant $variant
    ) {
        $this->ensureVariantBelongsToProduct(
            $product,
            $variant
        );


        return view(
            'admin.products.variants.edit',
            compact(
                'product',
                'variant'
            )
        );
    }


    /**
     * Cập nhật Variant.
     *
     * Hỗ trợ cả:
     * - form truyền thống
     * - AJAX trên trang Sửa sản phẩm một trang
     */
    public function update(
        UpdateProductVariantRequest $request,
        Product $product,
        ProductVariant $variant
    ) {
        $this->ensureVariantBelongsToProduct(
            $product,
            $variant
        );


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA GIÁ CUỐI
        |--------------------------------------------------------------------------
        */

        $finalPrice =
            (float) $product->current_price
            +
            (float) $request->price_adjustment;


        if ($finalPrice <= 0) {

            if ($request->expectsJson()) {

                return response()->json(
                    [
                        'success' => false,

                        'message' =>
                            'Giá cuối của biến thể phải lớn hơn 0.',

                        'errors' => [
                            'price_adjustment' => [
                                'Giá cuối của biến thể phải lớn hơn 0.',
                            ],
                        ],
                    ],
                    422
                );
            }


            return back()
                ->withErrors([
                    'price_adjustment' =>
                        'Giá cuối của biến thể phải lớn hơn 0.',
                ])
                ->withInput();
        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE VARIANT
        |--------------------------------------------------------------------------
        */

        $productWasDeactivated = false;


        DB::transaction(
            function () use (
                $request,
                $product,
                $variant,
                &$productWasDeactivated
            ) {

                $variant->update([

                    'color' =>
                        $request->color,

                    'size' =>
                        $request->size,

                    'sku' =>
                        $request->sku,

                    'stock_quantity' =>
                        $request->stock_quantity,

                    'price_adjustment' =>
                        $request->price_adjustment,

                    'is_active' =>
                        $request->boolean(
                            'is_active'
                        ),
                ]);


                /*
                 * Nếu Product đang kinh doanh nhưng sau khi sửa
                 * không còn Variant active nào thì phải tự ngừng bán.
                 */
                if (
                    $product->is_active
                    &&
                    ! $product
                        ->variants()
                        ->where(
                            'is_active',
                            true
                        )
                        ->exists()
                ) {
                    $product->update([
                        'is_active' => false,
                    ]);


                    $productWasDeactivated = true;
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | MESSAGE
        |--------------------------------------------------------------------------
        */

        $message =
            $productWasDeactivated
                ? 'Biến thể đã được cập nhật. Vì sản phẩm không còn biến thể hoạt động nên hệ thống đã tự chuyển sản phẩm sang trạng thái chưa bán.'
                : 'Cập nhật biến thể thành công.';


        /*
        |--------------------------------------------------------------------------
        | AJAX RESPONSE
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            return response()->json(
                array_merge(
                    [
                        'success' => true,

                        'message' =>
                            $message,

                        'product_was_deactivated' =>
                            $productWasDeactivated,
                    ],
                    $this->buildVariantState(
                        $product,
                        $variant->id
                    )
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->to(
                route(
                    'admin.products.edit',
                    $product
                )
                . '#edit-product-variants'
            )
            ->with(
                $productWasDeactivated
                    ? 'error'
                    : 'success',
                $message
            );
    }


    /**
     * Xóa hoặc vô hiệu hóa Variant.
     *
     * Hỗ trợ cả form truyền thống và AJAX.
     */
    public function destroy(
        Request $request,
        Product $product,
        ProductVariant $variant
    ) {
        $this->ensureVariantBelongsToProduct(
            $product,
            $variant
        );


        $message = null;

        $messageType = 'success';

        $productWasDeactivated = false;


        DB::transaction(
            function () use (
                $product,
                $variant,
                &$message,
                &$messageType,
                &$productWasDeactivated
            ) {

                /*
                 * Variant đã có trong Order:
                 * không xóa, chỉ ngừng bán.
                 */
                if (
                    $variant
                        ->orderDetails()
                        ->exists()
                ) {
                    $variant->update([
                        'is_active' => false,
                    ]);


                    $message =
                        'Biến thể đã phát sinh đơn hàng nên không thể xóa. Hệ thống đã chuyển biến thể sang trạng thái ngừng bán.';


                    $messageType = 'error';
                }


                /*
                 * Variant đang trong Cart:
                 * không xóa, chỉ ngừng bán.
                 */
                elseif (
                    $variant
                        ->cartItems()
                        ->exists()
                ) {
                    $variant->update([
                        'is_active' => false,
                    ]);


                    $message =
                        'Biến thể đang tồn tại trong giỏ hàng của khách nên không thể xóa. Hệ thống đã chuyển biến thể sang trạng thái ngừng bán.';


                    $messageType = 'error';
                }


                /*
                 * Không có lịch sử quan trọng:
                 * xóa Variant.
                 */
                else {
                    $variant->delete();


                    $message =
                        'Xóa biến thể thành công.';
                }


                /*
                 * Product đang kinh doanh nhưng không còn
                 * Variant active thì tự chuyển sang chưa bán.
                 */
                if (
                    $product->is_active
                    &&
                    ! $product
                        ->variants()
                        ->where(
                            'is_active',
                            true
                        )
                        ->exists()
                ) {
                    $product->update([
                        'is_active' => false,
                    ]);


                    $productWasDeactivated = true;
                }
            }
        );


        if ($productWasDeactivated) {

            $message .=
                ' Sản phẩm cũng đã được chuyển sang trạng thái chưa bán vì không còn biến thể hoạt động.';
        }


        /*
        |--------------------------------------------------------------------------
        | AJAX RESPONSE
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            return response()->json(
                array_merge(
                    [
                        'success' => true,

                        'message' =>
                            $message,

                        'message_type' =>
                            $messageType,

                        'product_was_deactivated' =>
                            $productWasDeactivated,
                    ],
                    $this->buildVariantState(
                        $product
                    )
                )
            );
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->to(
                route(
                    'admin.products.edit',
                    $product
                )
                . '#edit-product-variants'
            )
            ->with(
                $messageType,
                $message
            );
    }


    /**
     * Chuẩn hóa dữ liệu Variant + trạng thái Product
     * cho giao diện one-page/AJAX.
     */
    private function buildVariantState(
        Product $product,
        ?int $focusVariantId = null
    ): array {
        /*
         * Refresh để lấy trạng thái Product mới nhất,
         * đặc biệt khi Product vừa bị auto-deactivate.
         */
        $product->refresh();


        $variants =
            $product->variants()
                ->orderBy(
                    'sku'
                )
                ->get();


        $activeVariants =
            $variants->filter(
                function ($item) {

                    return (bool) $item->is_active;
                }
            );


        $activeVariantCount =
            $activeVariants->count();


        $hasActiveVariant =
            $activeVariantCount > 0;


        $totalStock =
            (int) $activeVariants
                ->sum(
                    'stock_quantity'
                );


        $hasRealImage =
            $product->images()
                ->where(
                    'image_path',
                    '!=',
                    'images/no-image.png'
                )
                ->exists();


        $isReadyForSale =
            $hasRealImage
            &&
            $hasActiveVariant;


        $variant =
            $focusVariantId !== null
                ? $variants->firstWhere(
                    'id',
                    $focusVariantId
                )
                : null;


        return [

            'variant' =>
                $variant
                    ? $this->variantToArray(
                        $variant
                    )
                    : null,


            'variants' =>
                $variants
                    ->map(
                        function ($item) {

                            return $this->variantToArray(
                                $item
                            );
                        }
                    )
                    ->values(),


            'active_variant_count' =>
                $activeVariantCount,


            'has_active_variant' =>
                $hasActiveVariant,


            'total_stock' =>
                $totalStock,


            'has_real_image' =>
                $hasRealImage,


            'is_ready_for_sale' =>
                $isReadyForSale,


            'product' => [

                'id' =>
                    $product->id,

                'is_active' =>
                    (bool) $product->is_active,
            ],


            'urls' => [

                'activate' =>
                    route(
                        'admin.products.activate',
                        $product
                    ),

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
            ],
        ];
    }


    /**
     * Chuẩn hóa một Variant thành JSON-friendly array.
     */
    private function variantToArray(
        ProductVariant $variant
    ): array {
        return [

            'id' =>
                $variant->id,

            'sku' =>
                $variant->sku,

            'color' =>
                $variant->color,

            'size' =>
                $variant->size,

            'stock_quantity' =>
                (int) $variant->stock_quantity,

            'price_adjustment' =>
                (float) $variant->price_adjustment,

            'final_price' =>
                (float) $variant->final_price,

            'is_active' =>
                (bool) $variant->is_active,
        ];
    }


    /**
     * Chống sửa ID trên URL
     * để thao tác Variant của Product khác.
     */
    private function ensureVariantBelongsToProduct(
        Product $product,
        ProductVariant $variant
    ): void {
        abort_unless(
            $variant->product_id
            === $product->id,
            404
        );
    }
}