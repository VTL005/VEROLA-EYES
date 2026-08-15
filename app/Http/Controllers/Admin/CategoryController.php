<?php

namespace App\Http\Controllers\Admin;

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
    public function index(
        Request $request
    ) {
        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );

        $status =
            $request->query('status');


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


        $categories =
            Category::query()

                ->withCount('products')

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

                ->when(
                    $status === 'active',
                    fn ($query) =>
                        $query->where(
                            'is_active',
                            true
                        )
                )

                ->when(
                    $status === 'inactive',
                    fn ($query) =>
                        $query->where(
                            'is_active',
                            false
                        )
                )

                ->latest()

                ->paginate(10)

                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | THỐNG KÊ
        |--------------------------------------------------------------------------
        */

        $totalCategories =
            Category::query()
                ->count();

        $activeCategories =
            Category::query()
                ->where(
                    'is_active',
                    true
                )
                ->count();

        $inactiveCategories =
            Category::query()
                ->where(
                    'is_active',
                    false
                )
                ->count();


        return view(
            'admin.categories.index',
            compact(
                'categories',
                'keyword',
                'status',
                'totalCategories',
                'activeCategories',
                'inactiveCategories'
            )
        );
    }


    /**
     * Form thêm Category.
     */
    public function create()
    {
        return view(
            'admin.categories.create'
        );
    }


    /**
     * Lưu Category.
     */
    public function store(
        StoreCategoryRequest $request
    ) {
        $slug =
            $this->generateUniqueSlug(
                $request->name
            );


        $imagePath = null;


        if ($request->hasFile('image')) {

            $image =
                $request->file('image');


            $fileName =
                time()
                . '-'
                . Str::random(8)
                . '.'
                . strtolower(
                    $image
                        ->getClientOriginalExtension()
                );


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
                'admin.categories.index'
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
            'admin.categories.edit',
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
        $slug =
            $category->slug;


        if (
            trim($request->name)
            !== $category->name
        ) {
            $slug =
                $this->generateUniqueSlug(
                    $request->name,
                    $category->id
                );
        }


        $imagePath =
            $category->image;


        if ($request->hasFile('image')) {

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
                . strtolower(
                    $image
                        ->getClientOriginalExtension()
                );


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
                'admin.categories.index'
            )
            ->with(
                'success',
                'Cập nhật danh mục thành công.'
            );
    }


    /**
     * Xóa Category.
     *
     * Chỉ Admin có route này.
     */
    public function destroy(
        Category $category
    ) {
        /*
         * Không được xóa Category
         * khi vẫn còn Product.
         */
        if (
            $category
                ->products()
                ->exists()
        ) {
            return back()->with(
                'error',
                'Không thể xóa danh mục vì vẫn còn sản phẩm. Hãy chuyển danh mục sang trạng thái không hoạt động.'
            );
        }


        $this->deleteCategoryImage(
            $category->image
        );


        $category->delete();


        return redirect()
            ->route(
                'admin.categories.index'
            )
            ->with(
                'success',
                'Xóa danh mục thành công.'
            );
    }


    /**
     * Sinh slug duy nhất.
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
                    fn ($query) =>
                        $query->where(
                            'id',
                            '!=',
                            $ignoreId
                        )
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


        if (
            File::exists(
                $fullPath
            )
        ) {
            File::delete(
                $fullPath
            );
        }
    }
}