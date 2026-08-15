<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Product;
use App\Services\ReviewService;

class ReviewController extends Controller
{
    /**
     * Form Customer đánh giá Product.
     */
    public function create(
        Product $product,
        ReviewService $reviewService
    ) {
        $user = auth()->user();


        /*
         * Nếu không đủ điều kiện Review
         * thì không cho mở form.
         */
        if (
            !$reviewService->canReview(
                $user,
                $product
            )
        ) {
            return redirect()
                ->route(
                    'products.show',
                    $product
                )
                ->with(
                    'error',
                    'Bạn chưa đủ điều kiện đánh giá sản phẩm này hoặc đã đánh giá trước đó.'
                );
        }


        return view(
            'reviews.create',
            compact('product')
        );
    }


    /**
     * Lưu Review.
     */
    public function store(
        StoreReviewRequest $request,
        Product $product,
        ReviewService $reviewService
    ) {
        $reviewService->create(
            auth()->user(),
            $product,
            $request->validated()
        );


        return redirect()
            ->route(
                'products.show',
                $product
            )
            ->with(
                'success',
                'Đánh giá sản phẩm thành công.'
            );
    }
}