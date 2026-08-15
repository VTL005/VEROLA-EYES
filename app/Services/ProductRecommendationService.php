<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductRecommendationService
{
    /**
     * Lấy danh sách sản phẩm gợi ý.
     */
    public function recommend(
        Product $product,
        int $limit = 4
    ): Collection {
        /*
         * Lấy một tập ứng viên đủ lớn,
         * sau đó tính điểm bằng PHP.
         */
        $candidates =
            Product::query()

                /*
                 * Không lấy chính Product
                 * Customer đang xem.
                 */
                ->where(
                    'id',
                    '!=',
                    $product->id
                )

                /*
                 * Chỉ Product đang kinh doanh.
                 */
                ->where(
                    'is_active',
                    true
                )

                /*
                 * Phải có ảnh thật.
                 */
                ->whereHas(
                    'images',
                    function ($query) {

                        $query->where(
                            'image_path',
                            '!=',
                            'images/no-image.png'
                        );
                    }
                )

                /*
                 * Phải có Variant đang hoạt động
                 * và còn tồn kho.
                 */
                ->whereHas(
                    'variants',
                    function ($query) {

                        $query
                            ->where(
                                'is_active',
                                true
                            )
                            ->where(
                                'stock_quantity',
                                '>',
                                0
                            );
                    }
                )

                ->with([
                    'category',
                    'images',

                    'variants' => function ($query) {

                        $query
                            ->where(
                                'is_active',
                                true
                            )
                            ->where(
                                'stock_quantity',
                                '>',
                                0
                            );
                    },
                ])

                ->latest()
                ->limit(40)
                ->get();


        /*
         * Tính điểm từng Product.
         */
        $scored =
            $candidates
                ->map(
                    function (
                        Product $candidate
                    ) use ($product) {

                        return [
                            'product' =>
                                $candidate,

                            'score' =>
                                $this->score(
                                    $product,
                                    $candidate
                                ),
                        ];
                    }
                )

                /*
                 * Điểm cao đứng trước.
                 */
                ->sortByDesc('score')

                ->values();


        /*
         * Trả lại Product,
         * không trả array score ra Controller.
         */
        return $scored
            ->take($limit)
            ->pluck('product')
            ->values();
    }


    /**
     * Tính mức độ phù hợp giữa
     * Product hiện tại và ứng viên.
     */
    private function score(
        Product $current,
        Product $candidate
    ): int {
        $score = 0;


        /*
         * Cùng danh mục.
         */
        if (
            $current->category_id
            === $candidate->category_id
        ) {
            $score += 5;
        }


        /*
         * Cùng kiểu dáng.
         */
        if (
            $current->shape
            && $candidate->shape
            && strtolower($current->shape)
                === strtolower($candidate->shape)
        ) {
            $score += 4;
        }


        /*
         * Cùng giới tính.
         */
        if (
            $current->gender
            && $candidate->gender
            && strtolower($current->gender)
                === strtolower($candidate->gender)
        ) {
            $score += 3;
        }


        /*
         * Cùng chất liệu.
         */
        if (
            $current->material
            && $candidate->material
            && strtolower($current->material)
                === strtolower($candidate->material)
        ) {
            $score += 2;
        }


        /*
         * So sánh khoảng giá.
         *
         * current_price tự dùng:
         * sale_price nếu có,
         * ngược lại dùng price.
         */
        $currentPrice =
            (float) $current->current_price;

        $candidatePrice =
            (float) $candidate->current_price;


        if (
            $currentPrice > 0
            && $candidatePrice > 0
        ) {
            $differenceRatio =
                abs(
                    $candidatePrice
                    - $currentPrice
                )
                / $currentPrice;


            if ($differenceRatio <= 0.10) {

                $score += 4;

            } elseif (
                $differenceRatio <= 0.25
            ) {

                $score += 2;

            } elseif (
                $differenceRatio <= 0.50
            ) {

                $score += 1;
            }
        }


        /*
         * Style tags giống nhau.
         */
        $currentStyles =
            is_array($current->style_tags)
                ? $current->style_tags
                : [];

        $candidateStyles =
            is_array($candidate->style_tags)
                ? $candidate->style_tags
                : [];


        $commonStyles =
            array_intersect(
                $currentStyles,
                $candidateStyles
            );


        /*
         * Tối đa cộng 3 điểm.
         */
        $score += min(
            count($commonStyles),
            3
        );


        /*
         * Khuôn mặt được đề xuất giống nhau.
         */
        $currentFaceShapes =
            is_array(
                $current->recommended_face_shapes
            )
                ? $current->recommended_face_shapes
                : [];

        $candidateFaceShapes =
            is_array(
                $candidate->recommended_face_shapes
            )
                ? $candidate->recommended_face_shapes
                : [];


        $commonFaceShapes =
            array_intersect(
                $currentFaceShapes,
                $candidateFaceShapes
            );


        /*
         * Tối đa cộng 2 điểm.
         */
        $score += min(
            count($commonFaceShapes),
            2
        );


        return $score;
    }
}