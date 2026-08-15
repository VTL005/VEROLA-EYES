<?php

namespace App\Http\Controllers\Admin;

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
            $request->query(
                'visibility'
            );


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE FILTER
        |--------------------------------------------------------------------------
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


        /*
        |--------------------------------------------------------------------------
        | LIST
        |--------------------------------------------------------------------------
        */

        $reviews =
            Review::query()

                ->with([
                    'user',
                    'product',
                ])

                /*
                 * Search Customer / Product / Comment.
                 */
                ->when(
                    $keyword !== '',
                    function ($query) use ($keyword) {

                        $query->where(
                            function ($subQuery) use ($keyword) {

                                $subQuery
                                    ->where(
                                        'comment',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhereHas(
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
                                                )

                                                ->orWhere(
                                                    'phone',
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
                 * Rating.
                 */
                ->when(
                    $rating !== null
                    && $rating !== '',
                    fn ($query) =>
                        $query->where(
                            'rating',
                            (int) $rating
                        )
                )

                /*
                 * Visible.
                 */
                ->when(
                    $visibility === 'visible',
                    fn ($query) =>
                        $query->where(
                            'is_visible',
                            true
                        )
                )

                /*
                 * Hidden.
                 */
                ->when(
                    $visibility === 'hidden',
                    fn ($query) =>
                        $query->where(
                            'is_visible',
                            false
                        )
                )

                ->latest()

                ->paginate(15)

                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
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


        $averageRating =
            Review::query()
                ->avg('rating');


        $averageRating =
            $averageRating !== null
                ? round(
                    (float) $averageRating,
                    1
                )
                : null;


        return view(
            'admin.reviews.index',
            compact(
                'reviews',
                'keyword',
                'rating',
                'visibility',
                'totalReviews',
                'visibleReviews',
                'hiddenReviews',
                'fiveStarReviews',
                'averageRating'
            )
        );
    }


    /**
     * Chi tiết Review.
     */
    public function show(
        Review $review
    ) {
        $review->load([
            'user',
            'product',
        ]);


        return view(
            'admin.reviews.show',
            compact('review')
        );
    }


    /**
     * Ẩn / hiện Review.
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
                : 'Đã ẩn đánh giá khỏi website.';


        return redirect()
            ->route(
                'admin.reviews.show',
                $review
            )
            ->with(
                'success',
                $message
            );
    }
}