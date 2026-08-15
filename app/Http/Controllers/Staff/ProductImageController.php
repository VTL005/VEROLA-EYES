<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProductImageController extends Controller
{
    /**
     * Upload ảnh cho sản phẩm.
     */
    public function store(
        StoreProductImageRequest $request,
        Product $product
    ) {
        $uploadedImages = $request->file(
            'images',
            []
        );


        /*
         * Chỉ đếm ảnh thật,
         * không tính placeholder.
         */
        $realImageCount = $product
            ->images()
            ->where(
                'image_path',
                '!=',
                'images/no-image.png'
            )
            ->count();


        /*
         * Mỗi Product tối đa 5 ảnh thật.
         */
        if (
            $realImageCount
            + count($uploadedImages)
            > 5
        ) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Mỗi sản phẩm chỉ được tối đa 5 ảnh.'
                );
        }


        /*
         * Đảm bảo thư mục tồn tại.
         */
        File::ensureDirectoryExists(
            public_path(
                'images/products'
            )
        );


        DB::transaction(
            function () use (
                $product,
                $uploadedImages,
                $realImageCount
            ) {
                /*
                 * Có ảnh thật thì xóa record placeholder.
                 * Không xóa file no-image.png vì dùng chung.
                 */
                $product
                    ->images()
                    ->where(
                        'image_path',
                        'images/no-image.png'
                    )
                    ->delete();


                foreach (
                    $uploadedImages as $index => $image
                ) {
                    $extension = strtolower(
                        $image->getClientOriginalExtension()
                    );


                    $fileName =
                        Str::uuid()
                        . '.'
                        . $extension;


                    $image->move(
                        public_path(
                            'images/products'
                        ),
                        $fileName
                    );


                    ProductImage::create([
                        'product_id' =>
                            $product->id,

                        'image_path' =>
                            'images/products/'
                            . $fileName,

                        'alt_text' =>
                            $product->name,

                        /*
                         * Nếu chưa có ảnh thật,
                         * ảnh đầu tiên tự trở thành ảnh chính.
                         */
                        'is_primary' =>
                            $realImageCount === 0
                            && $index === 0,

                        'sort_order' =>
                            $realImageCount
                            + $index
                            + 1,
                    ]);
                }
            }
        );


        return redirect()
            ->route(
                'staff.products.show',
                $product
            )
            ->with(
                'success',
                'Tải hình ảnh lên thành công.'
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


        if (
            $image->image_path
            === 'images/no-image.png'
        ) {
            return back()->with(
                'error',
                'Không thể đặt ảnh mặc định làm ảnh chính.'
            );
        }


        DB::transaction(
            function () use (
                $product,
                $image
            ) {
                $product
                    ->images()
                    ->update([
                        'is_primary' => false,
                    ]);


                $image->update([
                    'is_primary' => true,
                ]);
            }
        );


        return redirect()
            ->route(
                'staff.products.show',
                $product
            )
            ->with(
                'success',
                'Đã cập nhật ảnh chính.'
            );
    }


    /**
     * Xóa ảnh.
     */
    public function destroy(
        Product $product,
        ProductImage $image
    ) {
        $this->ensureImageBelongsToProduct(
            $product,
            $image
        );


        if (
            $image->image_path
            === 'images/no-image.png'
        ) {
            return back()->with(
                'error',
                'Không thể xóa ảnh mặc định.'
            );
        }


        $realImageCount = $product
            ->images()
            ->where(
                'image_path',
                '!=',
                'images/no-image.png'
            )
            ->count();


        /*
         * Product đang kinh doanh
         * phải còn ít nhất một ảnh thật.
         */
        if (
            $product->is_active
            && $realImageCount <= 1
        ) {
            return back()->with(
                'error',
                'Sản phẩm đang kinh doanh phải có ít nhất 1 ảnh.'
            );
        }


        $wasPrimary =
            $image->is_primary;

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
                 * Nếu xóa ảnh chính,
                 * chọn ảnh thật tiếp theo.
                 */
                if ($wasPrimary) {

                    $nextImage = $product
                        ->images()
                        ->where(
                            'image_path',
                            '!=',
                            'images/no-image.png'
                        )
                        ->orderBy('sort_order')
                        ->first();


                    if ($nextImage) {

                        $nextImage->update([
                            'is_primary' => true,
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


        if (File::exists($fullPath)) {

            File::delete(
                $fullPath
            );
        }


        return redirect()
            ->route(
                'staff.products.show',
                $product
            )
            ->with(
                'success',
                'Xóa hình ảnh thành công.'
            );
    }


    /**
     * Kiểm tra ảnh có thuộc Product không.
     */
    private function ensureImageBelongsToProduct(
        Product $product,
        ProductImage $image
    ): void {
        abort_if(
            $image->product_id
            !== $product->id,
            404
        );
    }
}