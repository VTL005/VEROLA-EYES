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
         * Chỉ tính ảnh thật.
         *
         * no-image.png chỉ là Placeholder.
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
         * Tối đa 5 ảnh thật / Product.
         */
        if (
            $realImageCount
            + count($uploadedImages)
            > 5
        ) {
            return back()
                ->withErrors([
                    'images' =>
                        'Một sản phẩm chỉ được có tối đa 5 hình ảnh.',
                ]);
        }


        /*
         * Đảm bảo thư mục tồn tại.
         */
        File::ensureDirectoryExists(
            public_path(
                'images/products'
            )
        );


        /*
         * Danh sách file đã lưu vật lý.
         *
         * Nếu DB Transaction lỗi,
         * sẽ xóa các file này.
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
                     * Xóa record Placeholder.
                     *
                     * KHÔNG xóa file no-image.png
                     * vì đây là ảnh dùng chung.
                     */
                    $product->images()
                        ->where(
                            'image_path',
                            'images/no-image.png'
                        )
                        ->delete();


                    $nextSortOrder =
                        (int) $product->images()
                            ->max('sort_order')
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


                        $image->move(
                            public_path(
                                'images/products'
                            ),
                            $fileName
                        );


                        $storedFiles[] =
                            $fullPath;


                        /*
                         * Nếu chưa có ảnh thật,
                         * ảnh đầu tiên tự động
                         * trở thành ảnh chính.
                         */
                        $isPrimary =
                            $realImageCount === 0
                            && $index === 0;


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
             * file vật lý nên phải xóa thủ công.
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
         * Placeholder không được làm ảnh chính.
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
                 * Bỏ Primary cũ.
                 */
                $product->images()
                    ->update([
                        'is_primary' =>
                            false,
                    ]);


                /*
                 * Đặt Primary mới.
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
         * Không thao tác Placeholder.
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
            && $realImageCount <= 1
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
                 * chọn ảnh thật tiếp theo
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
         * Xóa file vật lý sau khi
         * DB Transaction thành công.
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