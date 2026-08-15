<?php

namespace App\Services;

use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReviewService
{
    /**
     * Customer có được đánh giá Product không?
     */
    public function canReview(
        User $user,
        Product $product
    ): bool {
        /*
         * Phải từng mua Product này
         * trong một Order đã completed.
         */
        $hasPurchased =
            OrderDetail::query()
                ->where(
                    'product_id',
                    $product->id
                )
                ->whereHas(
                    'order',
                    function ($query) use ($user) {

                        $query
                            ->where(
                                'user_id',
                                $user->id
                            )
                            ->where(
                                'order_status',
                                'completed'
                            );
                    }
                )
                ->exists();


        if (!$hasPurchased) {
            return false;
        }


        /*
         * Mỗi Customer chỉ đánh giá
         * một Product một lần.
         */
        $alreadyReviewed =
            Review::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->where(
                    'product_id',
                    $product->id
                )
                ->exists();


        return !$alreadyReviewed;
    }


    /**
     * Customer tạo Review.
     */
    public function create(
        User $user,
        Product $product,
        array $data
    ): Review {
        return DB::transaction(
            function () use (
                $user,
                $product,
                $data
            ) {

                /*
                 * Kiểm tra Customer đã mua Product
                 * trong đơn completed.
                 */
                $hasPurchased =
                    OrderDetail::query()
                        ->where(
                            'product_id',
                            $product->id
                        )
                        ->whereHas(
                            'order',
                            function ($query) use ($user) {

                                $query
                                    ->where(
                                        'user_id',
                                        $user->id
                                    )
                                    ->where(
                                        'order_status',
                                        'completed'
                                    );
                            }
                        )
                        ->exists();


                if (!$hasPurchased) {

                    throw ValidationException::withMessages([
                        'review' =>
                            'Bạn chỉ có thể đánh giá sản phẩm đã mua trong đơn hàng hoàn thành.',
                    ]);
                }


                /*
                 * Chặn Review trùng.
                 */
                $existingReview =
                    Review::query()
                        ->where(
                            'user_id',
                            $user->id
                        )
                        ->where(
                            'product_id',
                            $product->id
                        )
                        ->lockForUpdate()
                        ->first();


                if ($existingReview) {

                    throw ValidationException::withMessages([
                        'review' =>
                            'Bạn đã đánh giá sản phẩm này rồi.',
                    ]);
                }


                return Review::create([
                    'user_id' =>
                        $user->id,

                    'product_id' =>
                        $product->id,

                    'rating' =>
                        $data['rating'],

                    'comment' =>
                        $data['comment'] ?? null,

                    /*
                     * Review mới mặc định hiển thị.
                     * Staff/Admin có thể ẩn sau.
                     */
                    'is_visible' =>
                        true,
                ]);
            }
        );
    }
}