<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewReply;
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
                    'adminReply.admin',
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


        $repliedReviews = Review::query()->whereHas('adminReply')->count();
        $waitingReplies = max(0, $totalReviews - $repliedReviews);


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
                'totalReviews',
                'repliedReviews',
                'waitingReplies',
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
            'adminReply.admin',
        ]);


        return view(
            'admin.reviews.show',
            compact('review')
        );
    }


    /**
     * Tạo hoặc cập nhật phản hồi chính thức của VELORA.
     */
    public function reply(Request $request, Review $review)
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'min:2', 'max:1000'],
        ], [
            'content.required' => 'Vui lòng nhập nội dung phản hồi.',
            'content.max' => 'Phản hồi không được vượt quá 1.000 ký tự.',
        ]);

        ReviewReply::updateOrCreate(
            ['review_id' => $review->id],
            ['admin_id' => auth()->id(), 'content' => trim($data['content'])]
        );

        return redirect()
            ->route(
                'admin.reviews.show',
                $review
            )
            ->with(
                'success',
                'Đã gửi phản hồi chính thức đến khách hàng.'
            )
            ->with('review_reply_updated', true);
    }
}
