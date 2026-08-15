<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductVariantRequest;
use App\Http\Requests\Product\UpdateProductVariantRequest;
use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantController extends Controller
{
    /**
     * Form thêm Variant.
     */
    public function create(
        Product $product
    ) {
        return view(
            'staff.products.variants.create',
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
        $finalPrice =
            (float) $product->current_price
            + (float) $request->price_adjustment;


        /*
         * Giá cuối phải > 0.
         */
        if ($finalPrice <= 0) {

            return back()
                ->withInput()
                ->withErrors([
                    'price_adjustment' =>
                        'Giá cuối của biến thể phải lớn hơn 0.',
                ]);
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
                'staff.products.show',
                $product
            )
            ->with(
                'success',
                'Thêm biến thể thành công.'
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
            'staff.products.variants.edit',
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
                ->withInput()
                ->withErrors([
                    'price_adjustment' =>
                        'Giá cuối của biến thể phải lớn hơn 0.',
                ]);
        }


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


        return redirect()
            ->route(
                'staff.products.show',
                $product
            )
            ->with(
                'success',
                'Cập nhật biến thể thành công.'
            );
    }


    /**
     * Ngừng bán Variant.
     */
    public function deactivate(
        Product $product,
        ProductVariant $variant
    ) {
        $this->ensureVariantBelongsToProduct(
            $product,
            $variant
        );


        $variant->update([
            'is_active' => false,
        ]);


        /*
         * Nếu Product đang Active nhưng sau khi
         * tắt Variant không còn Variant Active,
         * tự động tắt Product để tránh bán sai.
         */
        if (
            $product->is_active
            && !$product->hasActiveVariant()
        ) {
            $product->update([
                'is_active' => false,
            ]);
        }


        return redirect()
            ->route(
                'staff.products.show',
                $product
            )
            ->with(
                'success',
                'Đã ngừng bán biến thể.'
            );
    }


    /**
     * Variant phải thuộc Product.
     */
    private function ensureVariantBelongsToProduct(
        Product $product,
        ProductVariant $variant
    ): void {
        abort_if(
            $variant->product_id
            !== $product->id,
            404
        );
    }
}