<?php

namespace App\Http\Controllers;

use App\Models\Product;

class WishlistController extends Controller
{
    /**
     * Danh sách sản phẩm yêu thích.
     */
    public function index()
    {
        $user = auth()->user();

        $wishlist = $user
            ->wishlist()
            ->with([
                'items.product.category',
                'items.product.primaryImage',
            ])
            ->first();

        return view(
            'wishlist.index',
            compact('wishlist')
        );
    }


    /**
     * Thêm Product vào Wishlist.
     */
    public function store(Product $product)
    {
        /*
         * Chỉ cho thêm sản phẩm đang bán.
         */
        if (
            !$product->is_active
            || !$product->isReadyForSale()
        ) {
            return back()->with(
                'error',
                'Sản phẩm hiện không khả dụng.'
            );
        }

        $user = auth()->user();

        /*
         * Nếu Customer chưa có Wishlist
         * thì tự động tạo.
         */
        $wishlist = $user
            ->wishlist()
            ->firstOrCreate();


        /*
         * Không cho trùng Product.
         */
        $alreadyExists = $wishlist
            ->items()
            ->where(
                'product_id',
                $product->id
            )
            ->exists();

        if ($alreadyExists) {
            return back()->with(
                'error',
                'Sản phẩm đã có trong danh sách yêu thích.'
            );
        }


        $wishlist->items()->create([
            'product_id' => $product->id,
        ]);


        return back()->with(
            'success',
            'Đã thêm sản phẩm vào danh sách yêu thích.'
        );
    }


    /**
     * Xóa Product khỏi Wishlist.
     */
    public function destroy(Product $product)
    {
        $wishlist = auth()
            ->user()
            ->wishlist()
            ->first();

        if (!$wishlist) {
            return back()->with(
                'error',
                'Danh sách yêu thích không tồn tại.'
            );
        }


        $wishlist
            ->items()
            ->where(
                'product_id',
                $product->id
            )
            ->delete();


        return back()->with(
            'success',
            'Đã xóa sản phẩm khỏi danh sách yêu thích.'
        );
    }
}