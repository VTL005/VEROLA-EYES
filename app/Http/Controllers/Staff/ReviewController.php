<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\ReviewModerationService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Danh sách đánh giá.
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


        $rating =
            $request->query('rating');


        $visibility =
            $request->query('visibility');


        /*
         * Chỉ chấp nhận rating 1 - 5.
         */
        if (
            $rating !== null
            && $rating !== ''
            && !in_array(
                (int) $rating,
                [1, 2, 3, 4, 5],
                true
            )
        ) {
            $rating = null;
        }


        /*
         * Chỉ chấp nhận visible / hidden.
         */
        if (
            $visibility
            && !in_array(
                $visibility,
                [
                    'visible',
                    'hidden',
                ],
                true
            )
        ) {
            $visibility = null;
        }


        $reviews =
            Review::query()

                ->with([
                    'user',
                    'product',
                ])

                /*
                 * Search Customer / Product.
                 */
                ->when(
                    $keyword !== '',
                    function ($query) use ($keyword) {

                        $query->where(
                            function ($subQuery) use ($keyword) {

                                $subQuery

                                    ->whereHas(
                                        'user',
                                        function ($userQuery) use ($keyword) {

                                            $userQuery
                                                ->where(
                                                    'name',
                                                    'like',
                                                    "%{$keyword}%"
                                                )

                                                ->orWhere(
                                                    'email',
                                                    'like',
                                                    "%{$keyword}%"
                                                );
                                        }
                                    )

                                    ->orWhereHas(
                                        'product',
                                        function ($productQuery) use ($keyword) {

                                            $productQuery
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
                        );
                    }
                )

                /*
                 * Filter số sao.
                 */
                ->when(
                    $rating !== null
                    && $rating !== '',
                    function ($query) use ($rating) {

                        $query->where(
                            'rating',
                            (int) $rating
                        );
                    }
                )

                /*
                 * Filter hiển thị.
                 */
                ->when(
                    $visibility === 'visible',
                    function ($query) {

                        $query->where(
                            'is_visible',
                            true
                        );
                    }
                )

                ->when(
                    $visibility === 'hidden',
                    function ($query) {

                        $query->where(
                            'is_visible',
                            false
                        );
                    }
                )

                ->latest()

                ->paginate(15)

                ->withQueryString();


        /*
         * Thống kê nhanh.
         */
        $totalReviews =
            Review::query()
                ->count();


        $visibleReviews =
            Review::query()
                ->where(
                    'is_visible',
                    true
                )
                ->count();


        $hiddenReviews =
            Review::query()
                ->where(
                    'is_visible',
                    false
                )
                ->count();


        $fiveStarReviews =
            Review::query()
                ->where(
                    'rating',
                    5
                )
                ->count();


        return view(
            'staff.reviews.index',
            compact(
                'reviews',
                'keyword',
                'rating',
                'visibility',
                'totalReviews',
                'visibleReviews',
                'hiddenReviews',
                'fiveStarReviews'
            )
        );
    }


    /**
     * Chi tiết đánh giá.
     */
    public function show(
        Review $review
    ) {
        $review->load([
            'user',
            'product',
        ]);


        return view(
            'staff.reviews.show',
            compact('review')
        );
    }


    /**
     * Ẩn / hiện đánh giá.
     */
    public function toggleVisibility(
        Review $review,
        ReviewModerationService $reviewModerationService
    ) {
        $updatedReview =
            $reviewModerationService
                ->toggleVisibility(
                    auth()->user(),
                    $review
                );


        $message =
            $updatedReview->is_visible
                ? 'Đã hiển thị lại đánh giá.'
                : 'Đã ẩn đánh giá.';


        return redirect()
            ->route(
                'staff.reviews.show',
                $updatedReview
            )
            ->with(
                'success',
                $message
            );
    }
}