<?php

namespace App\Services;

use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReviewModerationService
{
    /**
     * Admin ẩn hoặc hiện Review.
     */
    public function toggleVisibility(
        User $operator,
        Review $review
    ): Review {
        /*
         * Chỉ Admin.
         */
        if (
            !$operator->isAdmin()
        ) {
            abort(403);
        }


        return DB::transaction(
            function () use ($review) {

                $lockedReview =
                    Review::query()
                        ->where(
                            'id',
                            $review->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                $lockedReview->update([
                    'is_visible' =>
                        !$lockedReview->is_visible,
                ]);


                return $lockedReview->fresh([
                    'user',
                    'product',
                ]);
            }
        );
    }
}
