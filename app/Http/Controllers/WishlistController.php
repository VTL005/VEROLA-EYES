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
    'wishlist_success',
    'Đã thêm vào danh sách yêu thích.'
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


        $wishlistItem = $wishlist
            ->items()
            ->where(
                'product_id',
                $product->id
            )
            ->first();

        if ($wishlistItem) {
            session()->put('wishlist_undo', [
                'product_id' => $product->id,
                'expires_at' => now()->addSeconds(8)->timestamp,
            ]);
        }

        $wishlistItem?->delete();


        return back()->with(
    'wishlist_success',
    'Đã bỏ khỏi danh sách yêu thích.'
);
    }

    /** Khôi phục sản phẩm vừa bỏ khỏi danh sách yêu thích. */
    public function undoDestroy()
    {
        $undo = session('wishlist_undo');

        if (! $undo || now()->timestamp > ($undo['expires_at'] ?? 0)) {
            return redirect()->route('wishlist.index')->with('error', 'Thời gian hoàn tác đã kết thúc.');
        }

        $product = Product::find($undo['product_id'] ?? null);

        if (! $product) {
            return redirect()->route('wishlist.index')->with('error', 'Sản phẩm không còn tồn tại để khôi phục.');
        }

        $wishlist = auth()->user()->wishlist()->firstOrCreate();
        $wishlist->items()->firstOrCreate(['product_id' => $product->id]);
        session()->forget('wishlist_undo');

        return redirect()->route('wishlist.index')->with('wishlist_success', 'Đã khôi phục sản phẩm yêu thích.');
    }
}
