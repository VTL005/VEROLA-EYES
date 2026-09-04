<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

class ProductImageController extends Controller
{
    /**
     * Upload hình ảnh Product.
     */
    public function store(
        StoreProductImageRequest $request,
        Product $product
    ) {
        $uploadedImages =
            $request->file(
                'images',
                []
            );


        /*
        |--------------------------------------------------------------------------
        | SỐ ẢNH THẬT HIỆN TẠI
        |--------------------------------------------------------------------------
        */

        $realImageCount =
            $product->images()
                ->where(
                    'image_path',
                    '!=',
                    'images/no-image.png'
                )
                ->count();


        /*
        |--------------------------------------------------------------------------
        | TỐI ĐA 5 ẢNH
        |--------------------------------------------------------------------------
        */

        if (
            $realImageCount
            + count($uploadedImages)
            > 5
        ) {
            /*
             * Single-page / AJAX.
             */
            if ($request->expectsJson()) {

                return response()->json(
                    [
                        'success' => false,

                        'message' =>
                            'Một sản phẩm chỉ được có tối đa 5 hình ảnh.',

                        'errors' => [
                            'images' => [
                                'Một sản phẩm chỉ được có tối đa 5 hình ảnh.',
                            ],
                        ],
                    ],
                    422
                );
            }


            /*
             * Fallback cũ.
             */
            return back()
                ->withErrors([
                    'images' =>
                        'Một sản phẩm chỉ được có tối đa 5 hình ảnh.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | ĐẢM BẢO THƯ MỤC ẢNH TỒN TẠI
        |--------------------------------------------------------------------------
        */

        File::ensureDirectoryExists(
            public_path(
                'images/products'
            )
        );


        /*
         * Danh sách file đã lưu vật lý.
         *
         * Nếu DB lỗi thì xóa lại
         * những file này.
         */
        $storedFiles = [];


        try {

            DB::transaction(
                function () use (
                    $product,
                    $uploadedImages,
                    $realImageCount,
                    &$storedFiles
                ) {

                    /*
                     * Xóa record placeholder.
                     *
                     * Không xóa file no-image.png
                     * vì file đó được dùng chung.
                     */
                    $product->images()
                        ->where(
                            'image_path',
                            'images/no-image.png'
                        )
                        ->delete();


                    $nextSortOrder =
                        (int) $product->images()
                            ->max(
                                'sort_order'
                            )
                        + 1;


                    foreach (
                        $uploadedImages
                        as $index => $image
                    ) {

                        $extension =
                            strtolower(
                                $image
                                    ->getClientOriginalExtension()
                            );


                        $fileName =
                            Str::uuid()
                            . '.'
                            . $extension;


                        $fullPath =
                            public_path(
                                'images/products/'
                                . $fileName
                            );


                        /*
                         * Lưu file vật lý.
                         */
                        $image->move(
                            public_path(
                                'images/products'
                            ),
                            $fileName
                        );


                        $storedFiles[] =
                            $fullPath;


                        /*
                         * Nếu Product chưa có ảnh thật,
                         * ảnh đầu tiên được upload
                         * tự động làm ảnh chính.
                         */
                        $isPrimary =
                            $realImageCount === 0
                            &&
                            $index === 0;


                        ProductImage::create([

                            'product_id' =>
                                $product->id,


                            'image_path' =>
                                'images/products/'
                                . $fileName,


                            'alt_text' =>
                                $product->name,


                            'is_primary' =>
                                $isPrimary,


                            'sort_order' =>
                                $nextSortOrder
                                + $index,
                        ]);
                    }
                }
            );

        } catch (Throwable $exception) {

            /*
             * DB rollback không thể tự rollback
             * file vật lý.
             */
            foreach (
                $storedFiles
                as $storedFile
            ) {
                if (
                    File::exists(
                        $storedFile
                    )
                ) {
                    File::delete(
                        $storedFile
                    );
                }
            }


            throw $exception;
        }


        /*
        |--------------------------------------------------------------------------
        | SINGLE-PAGE / AJAX RESPONSE
        |--------------------------------------------------------------------------
        */

        if ($request->expectsJson()) {

            /*
             * Load lại danh sách ảnh thật
             * sau khi upload.
             */
            $images =
                $product->images()
                    ->where(
                        'image_path',
                        '!=',
                        'images/no-image.png'
                    )
                    ->orderByDesc(
                        'is_primary'
                    )
                    ->orderBy(
                        'sort_order'
                    )
                    ->get();


            $newRealImageCount =
                $images->count();


            /*
             * Kiểm tra Product hiện có
             * Variant active chưa.
             */
            $hasActiveVariant =
                $product->variants()
                    ->where(
                        'is_active',
                        true
                    )
                    ->exists();


            /*
             * Product chỉ sẵn sàng khi:
             * - có ảnh thật
             * - có Variant active
             */
            $isReadyForSale =
                $newRealImageCount > 0
                &&
                $hasActiveVariant;


            return response()->json([
                'success' => true,

                'message' =>
                    'Tải hình ảnh sản phẩm thành công.',


                'real_image_count' =>
                    $newRealImageCount,


                'has_real_image' =>
                    $newRealImageCount > 0,


                'has_active_variant' =>
                    $hasActiveVariant,


                'is_ready_for_sale' =>
                    $isReadyForSale,


                /*
                 * Trả danh sách ảnh về JavaScript
                 * để hiển thị ngay trên trang.
                 */
                'images' =>
                    $images->map(
                        function ($image) {

                            return [

                                'id' =>
                                    $image->id,


                                'image_path' =>
                                    $image->image_path,


                                'image_url' =>
                                    asset(
                                        $image->image_path
                                    ),


                                'alt_text' =>
                                    $image->alt_text,


                                'is_primary' =>
                                    (bool) $image->is_primary,


                                'sort_order' =>
                                    (int) $image->sort_order,
                            ];
                        }
                    )
                    ->values(),


                'urls' => [

                    'activate' =>
                        route(
                            'admin.products.activate',
                            $product
                        ),

                    'show' =>
                        route(
                            'admin.products.show',
                            $product
                        ),
                ],
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | FALLBACK CŨ
        |--------------------------------------------------------------------------
        |
        | Các trang cũ vẫn hoạt động như trước.
        |
        */

        return redirect()
            ->route(
                'admin.products.show',
                $product
            )
            ->with(
                'success',
                'Tải hình ảnh sản phẩm thành công.'
            );
    }


    /**
     * Đặt ảnh chính.
     */
    public function setPrimary(
        Product $product,
        ProductImage $image
    ) {
        $this->ensureImageBelongsToProduct(
            $product,
            $image
        );


        /*
         * Placeholder không được
         * làm ảnh chính.
         */
        if (
            $image->image_path
            === 'images/no-image.png'
        ) {
            return back()->with(
                'error',
                'Ảnh mặc định không thể được đặt làm ảnh chính.'
            );
        }


        DB::transaction(
            function () use (
                $product,
                $image
            ) {

                /*
                 * Bỏ ảnh chính cũ.
                 */
                $product->images()
                    ->update([
                        'is_primary' =>
                            false,
                    ]);


                /*
                 * Đặt ảnh mới làm ảnh chính.
                 */
                $image->update([
                    'is_primary' =>
                        true,
                ]);
            }
        );


        return back()->with(
            'success',
            'Đã cập nhật ảnh chính.'
        );
    }


    /**
     * Xóa hình ảnh Product.
     */
    public function destroy(
        Product $product,
        ProductImage $image
    ) {
        $this->ensureImageBelongsToProduct(
            $product,
            $image
        );


        /*
         * Không thao tác placeholder.
         */
        if (
            $image->image_path
            === 'images/no-image.png'
        ) {
            return back()->with(
                'error',
                'Không thể xóa ảnh mặc định của hệ thống.'
            );
        }


        $realImageCount =
            $product->images()
                ->where(
                    'image_path',
                    '!=',
                    'images/no-image.png'
                )
                ->count();


        /*
         * Product đang bán phải giữ
         * ít nhất 1 ảnh thật.
         */
        if (
            $product->is_active
            &&
            $realImageCount <= 1
        ) {
            return back()->with(
                'error',
                'Sản phẩm đang kinh doanh phải có ít nhất một hình ảnh. Hãy tải ảnh mới trước khi xóa ảnh này.'
            );
        }


        $wasPrimary =
            (bool) $image->is_primary;


        $imagePath =
            $image->image_path;


        DB::transaction(
            function () use (
                $product,
                $image,
                $wasPrimary
            ) {

                $image->delete();


                /*
                 * Nếu vừa xóa ảnh chính,
                 * chọn ảnh tiếp theo
                 * làm ảnh chính.
                 */
                if ($wasPrimary) {

                    $nextImage =
                        $product->images()
                            ->where(
                                'image_path',
                                '!=',
                                'images/no-image.png'
                            )
                            ->orderBy(
                                'sort_order'
                            )
                            ->first();


                    if ($nextImage) {

                        $nextImage->update([
                            'is_primary' =>
                                true,
                        ]);
                    }
                }
            }
        );


        /*
         * Xóa file vật lý.
         */
        $fullPath =
            public_path(
                $imagePath
            );


        if (
            File::exists(
                $fullPath
            )
        ) {
            File::delete(
                $fullPath
            );
        }


        return back()->with(
            'success',
            'Xóa hình ảnh thành công.'
        );
    }


    /**
     * Chống sửa ID trên URL
     * để thao tác ảnh của Product khác.
     */
    private function ensureImageBelongsToProduct(
        Product $product,
        ProductImage $image
    ): void {
        abort_unless(
            $image->product_id
            === $product->id,
            404
        );
    }
}