<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Danh sách Category.
     */
    public function index(Request $request)
    {
        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );


        $status = $request->query('status');


        /*
         * Chỉ chấp nhận:
         *
         * active
         * inactive
         */
        if (
            $status
            && !in_array(
                $status,
                [
                    'active',
                    'inactive',
                ],
                true
            )
        ) {
            $status = null;
        }


        $categories = Category::query()

            /*
             * Đếm số Product của Category.
             */
            ->withCount('products')


            /*
             * Tìm theo tên hoặc slug.
             */
            ->when(
                $keyword !== '',
                function ($query) use ($keyword) {

                    $query->where(
                        function ($subQuery) use ($keyword) {

                            $subQuery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$keyword}%"
                                )
                                ->orWhere(
                                    'slug',
                                    'like',
                                    "%{$keyword}%"
                                );
                        }
                    );
                }
            )


            /*
             * Filter trạng thái.
             */
            ->when(
                $status === 'active',
                function ($query) {

                    $query->where(
                        'is_active',
                        true
                    );
                }
            )

            ->when(
                $status === 'inactive',
                function ($query) {

                    $query->where(
                        'is_active',
                        false
                    );
                }
            )


            ->latest()

            ->paginate(10)

            ->withQueryString();


        return view(
            'staff.categories.index',
            compact(
                'categories',
                'keyword',
                'status'
            )
        );
    }


    /**
     * Form thêm Category.
     */
    public function create()
    {
        return view(
            'staff.categories.create'
        );
    }


    /**
     * Lưu Category.
     */
    public function store(
        StoreCategoryRequest $request
    ) {
        $slug = $this->generateUniqueSlug(
            $request->name
        );


        $imagePath = null;


        /*
         * Upload ảnh Category.
         */
        if ($request->hasFile('image')) {

            /*
             * Đảm bảo folder tồn tại.
             */
            File::ensureDirectoryExists(
                public_path(
                    'images/categories'
                )
            );


            $image = $request->file('image');


            $fileName =
                time()
                . '-'
                . Str::random(8)
                . '.'
                . $image->getClientOriginalExtension();


            $image->move(
                public_path(
                    'images/categories'
                ),
                $fileName
            );


            $imagePath =
                'images/categories/'
                . $fileName;
        }


        Category::create([
            'name' =>
                trim($request->name),

            'slug' =>
                $slug,

            'description' =>
                $request->description,

            'image' =>
                $imagePath,

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),
        ]);


        return redirect()
            ->route(
                'staff.categories.index'
            )
            ->with(
                'success',
                'Thêm danh mục thành công.'
            );
    }


    /**
     * Form sửa Category.
     */
    public function edit(
        Category $category
    ) {
        return view(
            'staff.categories.edit',
            compact('category')
        );
    }


    /**
     * Cập nhật Category.
     */
    public function update(
        UpdateCategoryRequest $request,
        Category $category
    ) {
        $slug = $category->slug;


        /*
         * Chỉ sinh lại Slug
         * nếu Name thay đổi.
         */
        if (
            $category->name
            !== trim($request->name)
        ) {
            $slug =
                $this->generateUniqueSlug(
                    $request->name,
                    $category->id
                );
        }


        $imagePath =
            $category->image;


        /*
         * Có ảnh mới.
         */
        if ($request->hasFile('image')) {

            File::ensureDirectoryExists(
                public_path(
                    'images/categories'
                )
            );


            /*
             * Xóa ảnh cũ.
             */
            $this->deleteCategoryImage(
                $category->image
            );


            $image =
                $request->file('image');


            $fileName =
                time()
                . '-'
                . Str::random(8)
                . '.'
                . $image->getClientOriginalExtension();


            $image->move(
                public_path(
                    'images/categories'
                ),
                $fileName
            );


            $imagePath =
                'images/categories/'
                . $fileName;
        }


        $category->update([
            'name' =>
                trim($request->name),

            'slug' =>
                $slug,

            'description' =>
                $request->description,

            'image' =>
                $imagePath,

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),
        ]);


        return redirect()
            ->route(
                'staff.categories.index'
            )
            ->with(
                'success',
                'Cập nhật danh mục thành công.'
            );
    }


    /**
     * Sinh Slug không trùng.
     */
    private function generateUniqueSlug(
        string $name,
        ?int $ignoreId = null
    ): string {
        $baseSlug =
            Str::slug($name);


        $slug =
            $baseSlug;


        $counter = 1;


        while (
            Category::query()
                ->where(
                    'slug',
                    $slug
                )
                ->when(
                    $ignoreId,
                    function ($query) use ($ignoreId) {

                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        );
                    }
                )
                ->exists()
        ) {
            $slug =
                $baseSlug
                . '-'
                . $counter;


            $counter++;
        }


        return $slug;
    }


    /**
     * Xóa ảnh Category.
     */
    private function deleteCategoryImage(
        ?string $imagePath
    ): void {
        if (!$imagePath) {
            return;
        }


        $fullPath =
            public_path(
                $imagePath
            );


        if (File::exists($fullPath)) {

            File::delete(
                $fullPath
            );
        }
    }
}