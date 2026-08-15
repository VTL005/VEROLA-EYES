@extends('layouts.admin')


@section(
    'title',
    'Danh mục - VELORA Eyes'
)


@section(
    'page-title',
    'Danh mục'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            CATEGORY MANAGEMENT
        </span>

        <h1>
            Quản lý danh mục
        </h1>

        <p>
            Quản lý nhóm sản phẩm kính mắt
            đang sử dụng trong hệ thống.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.categories.create'
        ) }}"
        class="admin-btn admin-btn-primary"
    >
        <i class="bi bi-plus-lg"></i>

        Thêm danh mục
    </a>

</div>



{{-- STATS --}}

<div class="admin-category-stats">

    <div class="admin-category-stat">

        <i class="bi bi-tags"></i>

        <span>

            <small>
                Tổng danh mục
            </small>

            <strong>
                {{ $totalCategories }}
            </strong>

        </span>

    </div>


    <a
        href="{{ route(
            'admin.categories.index',
            ['status' => 'active']
        ) }}"
        class="admin-category-stat active"
    >

        <i class="bi bi-check-circle"></i>

        <span>

            <small>
                Đang hoạt động
            </small>

            <strong>
                {{ $activeCategories }}
            </strong>

        </span>

    </a>


    <a
        href="{{ route(
            'admin.categories.index',
            ['status' => 'inactive']
        ) }}"
        class="admin-category-stat inactive"
    >

        <i class="bi bi-slash-circle"></i>

        <span>

            <small>
                Không hoạt động
            </small>

            <strong>
                {{ $inactiveCategories }}
            </strong>

        </span>

    </a>

</div>



{{-- FILTER --}}

<div class="admin-category-filter">

    <form
        action="{{ route(
            'admin.categories.index'
        ) }}"
        method="GET"
        class="admin-category-filter-form"
    >

        <div>

            <label for="keyword">
                Tìm kiếm
            </label>

            <div class="admin-input-icon">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    class="admin-form-control"
                    placeholder="Tên danh mục hoặc slug..."
                >

            </div>

        </div>


        <div>

            <label for="status">
                Trạng thái
            </label>

            <select
                id="status"
                name="status"
                class="admin-form-control"
            >

                <option value="">
                    Tất cả
                </option>

                <option
                    value="active"
                    {{
                        $status === 'active'
                            ? 'selected'
                            : ''
                    }}
                >
                    Hoạt động
                </option>

                <option
                    value="inactive"
                    {{
                        $status === 'inactive'
                            ? 'selected'
                            : ''
                    }}
                >
                    Không hoạt động
                </option>

            </select>

        </div>


        <div class="admin-category-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-funnel"></i>

                Lọc
            </button>


            @if(
                $keyword !== ''
                || $status
            )

                <a
                    href="{{ route(
                        'admin.categories.index'
                    ) }}"
                    class="admin-btn admin-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



{{-- TABLE --}}

<div class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Danh sách danh mục
            </h2>

            <p>
                {{ $categories->total() }}
                danh mục
            </p>

        </div>

    </div>


    @if($categories->isEmpty())

        <div class="admin-category-empty">

            <i class="bi bi-tags"></i>

            <h3>
                Không có danh mục phù hợp
            </h3>

            <p>
                Hãy thử thay đổi bộ lọc.
            </p>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>
                            Danh mục
                        </th>

                        <th>
                            Slug
                        </th>

                        <th>
                            Sản phẩm
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Ngày tạo
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $categories
                        as $category
                    )

                        <tr>

                            <td>

                                <div class="admin-category-info">

                                    <div class="admin-category-image">

                                        @if($category->image)

                                            <img
                                                src="{{ asset(
                                                    $category->image
                                                ) }}"
                                                alt="{{ $category->name }}"
                                            >

                                        @else

                                            <i class="bi bi-image"></i>

                                        @endif

                                    </div>


                                    <div>

                                        <strong>
                                            {{ $category->name }}
                                        </strong>

                                        <span>

                                            {{ \Illuminate\Support\Str::limit(
                                                $category->description
                                                    ?: 'Chưa có mô tả',
                                                55
                                            ) }}

                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <code class="admin-category-slug">

                                    {{ $category->slug }}

                                </code>

                            </td>


                            <td>

                                <strong class="admin-category-product-count">

                                    {{ $category
                                        ->products_count }}

                                </strong>

                                <small>
                                    sản phẩm
                                </small>

                            </td>


                            <td>

                                @if($category->is_active)

                                    <span class="admin-status success">
                                        Hoạt động
                                    </span>

                                @else

                                    <span class="admin-status muted">
                                        Không hoạt động
                                    </span>

                                @endif

                            </td>


                            <td>

                                <div class="admin-table-primary">

                                    <strong>

                                        {{ $category
                                            ->created_at
                                            ->format('d/m/Y') }}

                                    </strong>

                                    <span>

                                        {{ $category
                                            ->created_at
                                            ->format('H:i') }}

                                    </span>

                                </div>

                            </td>


                            <td>

                                <div class="admin-category-actions">

                                    <a
                                        href="{{ route(
                                            'admin.categories.edit',
                                            $category
                                        ) }}"
                                        class="admin-table-action"
                                    >
                                        <i class="bi bi-pencil"></i>

                                        Sửa
                                    </a>


                                    <form
                                        action="{{ route(
                                            'admin.categories.destroy',
                                            $category
                                        ) }}"
                                        method="POST"
                                        onsubmit="
                                            return confirm(
                                                'Bạn có chắc muốn xóa danh mục này?'
                                            );
                                        "
                                    >

                                        @csrf
                                        @method('DELETE')


                                        <button
                                            type="submit"
                                            class="admin-category-delete"
                                        >
                                            <i class="bi bi-trash"></i>

                                            Xóa
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="admin-pagination">
            {{ $categories->links() }}
        </div>

    @endif

</div>

@endsection