<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductVariantRequest;
use App\Http\Requests\Product\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;
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
            compact('product')
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
         * Giá cuối =
         * giá hiện tại Product
         * + mức điều chỉnh Variant.
         */
        $finalPrice =
            (float) $product->current_price
            + (float) $request->price_adjustment;


        if ($finalPrice <= 0) {

            return back()
                ->withErrors([
                    'price_adjustment' =>
                        'Giá cuối của biến thể phải lớn hơn 0.',
                ])
                ->withInput();
        }


        ProductVariant::create([
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


        return redirect()
            ->route(
                'admin.products.show',
                $product
            )
            ->with(
                'success',
                'Thêm biến thể sản phẩm thành công.'
            );
    }


    /**
     * Form sửa Variant.
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


        $finalPrice =
            (float) $product->current_price
            + (float) $request->price_adjustment;


        if ($finalPrice <= 0) {

            return back()
                ->withErrors([
                    'price_adjustment' =>
                        'Giá cuối của biến thể phải lớn hơn 0.',
                ])
                ->withInput();
        }


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
                 * Nếu Product đang bán nhưng
                 * sau khi sửa không còn Variant
                 * hoạt động nào thì phải tự
                 * ngừng kinh doanh Product.
                 */
                if (
                    $product->is_active
                    && !$product
                        ->variants()
                        ->where(
                            'is_active',
                            true
                        )
                        ->exists()
                ) {
                    $product->update([
                        'is_active' =>
                            false,
                    ]);


                    $productWasDeactivated =
                        true;
                }
            }
        );


        if ($productWasDeactivated) {

            return redirect()
                ->route(
                    'admin.products.show',
                    $product
                )
                ->with(
                    'error',
                    'Biến thể đã được cập nhật. Vì sản phẩm không còn biến thể hoạt động nên hệ thống đã tự chuyển sản phẩm sang trạng thái chưa bán.'
                );
        }


        return redirect()
            ->route(
                'admin.products.show',
                $product
            )
            ->with(
                'success',
                'Cập nhật biến thể thành công.'
            );
    }


    /**
     * Xóa hoặc vô hiệu hóa Variant.
     */
    public function destroy(
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
                 * không được xóa dữ liệu.
                 */
                if (
                    $variant
                        ->orderDetails()
                        ->exists()
                ) {
                    $variant->update([
                        'is_active' =>
                            false,
                    ]);


                    $message =
                        'Biến thể đã phát sinh đơn hàng nên không thể xóa. Hệ thống đã chuyển biến thể sang trạng thái ngừng bán.';


                    $messageType =
                        'error';
                }

                /*
                 * Variant đang nằm trong Cart:
                 * không xóa, chỉ ngừng bán.
                 */
                elseif (
                    $variant
                        ->cartItems()
                        ->exists()
                ) {
                    $variant->update([
                        'is_active' =>
                            false,
                    ]);


                    $message =
                        'Biến thể đang tồn tại trong giỏ hàng của khách nên không thể xóa. Hệ thống đã chuyển biến thể sang trạng thái ngừng bán.';


                    $messageType =
                        'error';
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
                 * Sau mọi trường hợp trên,
                 * kiểm tra Product còn Variant
                 * Active hay không.
                 */
                if (
                    $product->is_active
                    && !$product
                        ->variants()
                        ->where(
                            'is_active',
                            true
                        )
                        ->exists()
                ) {
                    $product->update([
                        'is_active' =>
                            false,
                    ]);


                    $productWasDeactivated =
                        true;
                }
            }
        );


        if ($productWasDeactivated) {

            $message .=
                ' Sản phẩm cũng đã được chuyển sang trạng thái chưa bán vì không còn biến thể hoạt động.';
        }


        return back()->with(
            $messageType,
            $message
        );
    }


    /**
     * Chống thay ID trên URL để
     * thao tác Variant của Product khác.
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