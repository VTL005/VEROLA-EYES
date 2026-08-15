<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * Lấy Cart của User.
     * Nếu chưa có thì tự động tạo.
     */
    public function getOrCreateCart(
        User $user
    ): Cart {
        return $user
            ->cart()
            ->firstOrCreate();
    }


    /**
     * Thêm Variant vào Cart.
     */
    public function add(
        User $user,
        ProductVariant $variant,
        int $quantity = 1
    ): Cart {
        /*
         * Quantity phải >= 1.
         */
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng phải lớn hơn hoặc bằng 1.',
            ]);
        }


        /*
         * Load Product của Variant.
         */
        $variant->loadMissing('product');

        $product = $variant->product;


        /*
         * Product phải tồn tại
         * và đang kinh doanh.
         */
        if (
            !$product
            || !$product->is_active
        ) {
            throw ValidationException::withMessages([
                'variant_id' =>
                    'Sản phẩm hiện không khả dụng.',
            ]);
        }


        /*
         * Variant phải đang hoạt động.
         */
        if (!$variant->is_active) {
            throw ValidationException::withMessages([
                'variant_id' =>
                    'Biến thể này hiện đã ngừng bán.',
            ]);
        }


        /*
         * Variant phải còn hàng.
         */
        if ($variant->stock_quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Biến thể này hiện đã hết hàng.',
            ]);
        }


        $cart = $this->getOrCreateCart(
            $user
        );


        /*
         * Kiểm tra Variant đã có trong Cart chưa.
         */
        $cartItem = $cart
            ->items()
            ->where(
                'variant_id',
                $variant->id
            )
            ->first();


        /*
         * Nếu đã có thì cộng quantity.
         */
        $newQuantity = $cartItem
            ? $cartItem->quantity + $quantity
            : $quantity;


        /*
         * Không được vượt Stock hiện tại.
         */
        if (
            $newQuantity
            > $variant->stock_quantity
        ) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng yêu cầu vượt quá tồn kho hiện tại. Chỉ còn '
                    . $variant->stock_quantity
                    . ' sản phẩm.',
            ]);
        }


        /*
         * Nếu Item đã tồn tại → Update.
         */
        if ($cartItem) {

            $cartItem->update([
                'quantity' =>
                    $newQuantity,
            ]);

        } else {

            /*
             * Nếu chưa tồn tại → Create.
             */
            $cart->items()->create([
                'variant_id' =>
                    $variant->id,

                'quantity' =>
                    $quantity,
            ]);
        }


        /*
         * Load lại Cart để dùng cho View.
         */
        return $cart->load([
            'items.variant.product.primaryImage',
        ]);
    }


    /**
     * Cập nhật quantity của CartItem.
     */
    public function updateQuantity(
        User $user,
        ProductVariant $variant,
        int $quantity
    ): Cart {
        if ($quantity < 1) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng phải lớn hơn hoặc bằng 1.',
            ]);
        }


        $variant->loadMissing('product');

        if (
            !$variant->product
            || !$variant->product->is_active
            || !$variant->is_active
        ) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Sản phẩm hoặc biến thể hiện không khả dụng.',
            ]);
        }


        if (
            $quantity
            > $variant->stock_quantity
        ) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Số lượng vượt quá tồn kho hiện tại. Chỉ còn '
                    . $variant->stock_quantity
                    . ' sản phẩm.',
            ]);
        }


        $cart = $this->getOrCreateCart(
            $user
        );


        $cartItem = $cart
            ->items()
            ->where(
                'variant_id',
                $variant->id
            )
            ->first();


        if (!$cartItem) {
            throw ValidationException::withMessages([
                'quantity' =>
                    'Sản phẩm không tồn tại trong giỏ hàng.',
            ]);
        }


        $cartItem->update([
            'quantity' =>
                $quantity,
        ]);


        return $cart->load([
            'items.variant.product.primaryImage',
        ]);
    }


    /**
     * Xóa Variant khỏi Cart.
     */
    public function remove(
        User $user,
        ProductVariant $variant
    ): Cart {
        $cart = $this->getOrCreateCart(
            $user
        );


        $cart
            ->items()
            ->where(
                'variant_id',
                $variant->id
            )
            ->delete();


        return $cart->load([
            'items.variant.product.primaryImage',
        ]);
    }


    /**
     * Xóa toàn bộ Cart.
     */
    public function clear(
        User $user
    ): void {
        $cart = $this->getOrCreateCart(
            $user
        );

        $cart
            ->items()
            ->delete();
    }
}