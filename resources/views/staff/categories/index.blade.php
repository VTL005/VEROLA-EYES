@extends('layouts.staff')


@section(
    'title',
    'Danh mục sản phẩm - Staff'
)


@section(
    'page-title',
    'Danh mục sản phẩm'
)


@section('content')


{{-- =========================================================
    HEADER
========================================================= --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            SẢN PHẨM & KHO
        </span>

        <h1>
            Danh mục sản phẩm
        </h1>

        <p>
            Quản lý các nhóm sản phẩm
            đang được sử dụng trên VELORA Eyes.
        </p>

    </div>


    <div class="staff-page-header-actions">

        <a
            href="{{ route(
                'staff.categories.create'
            ) }}"
            class="staff-btn staff-btn-primary"
        >
            + Thêm danh mục
        </a>

    </div>

</div>



{{-- =========================================================
    FILTER
========================================================= --}}

<div class="staff-category-filter">

    <form
        action="{{ route(
            'staff.categories.index'
        ) }}"
        method="GET"
        class="staff-category-filter-form"
    >

        <div>

            <label for="keyword">
                Tìm kiếm
            </label>


            <input
                type="text"
                id="keyword"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="Tên hoặc slug..."
                class="staff-form-control"
            >

        </div>


        <div>

            <label for="status">
                Trạng thái
            </label>


            <select
                id="status"
                name="status"
                class="staff-form-control"
            >

                <option value="">
                    Tất cả
                </option>


                <option
                    value="active"
                    {{ $status === 'active'
                        ? 'selected'
                        : '' }}
                >
                    Hoạt động
                </option>


                <option
                    value="inactive"
                    {{ $status === 'inactive'
                        ? 'selected'
                        : '' }}
                >
                    Đang ẩn
                </option>

            </select>

        </div>


        <div class="staff-category-filter-actions">

            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Tìm kiếm
            </button>


            @if(
                $keyword !== ''
                || $status
            )

                <a
                    href="{{ route(
                        'staff.categories.index'
                    ) }}"
                    class="staff-btn staff-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



{{-- =========================================================
    TABLE
========================================================= --}}

<div class="staff-table-card">

    <div class="staff-table-card-header">

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

        <div class="staff-category-empty">

            <div>
                ≡
            </div>


            <h3>
                Chưa có danh mục phù hợp
            </h3>


            <p>
                Hãy tạo danh mục mới
                hoặc thay đổi điều kiện tìm kiếm.
            </p>

        </div>

    @else

        <div class="staff-table-responsive">

            <table class="staff-table">

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

                        <th class="staff-table-action-column">
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


                            {{-- CATEGORY --}}

                            <td>

                                <div class="staff-category-cell">


                                    @if($category->image)

                                        <img
                                            src="{{ asset(
                                                $category->image
                                            ) }}"
                                            alt="{{ $category->name }}"
                                            class="staff-category-thumbnail"
                                        >

                                    @else

                                        <div class="staff-category-thumbnail staff-category-thumbnail-empty">
                                            V
                                        </div>

                                    @endif


                                    <div>

                                        <strong>
                                            {{ $category->name }}
                                        </strong>


                                        @if($category->description)

                                            <span>

                                                {{ \Illuminate\Support\Str::limit(
                                                    $category->description,
                                                    55
                                                ) }}

                                            </span>

                                        @else

                                            <span>
                                                Chưa có mô tả
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </td>



                            {{-- SLUG --}}

                            <td>

                                <code class="staff-slug">
                                    {{ $category->slug }}
                                </code>

                            </td>



                            {{-- PRODUCTS --}}

                            <td>

                                <strong>
                                    {{ $category->products_count }}
                                </strong>

                                <span class="staff-table-muted">
                                    sản phẩm
                                </span>

                            </td>



                            {{-- STATUS --}}

                            <td>

                                @if($category->is_active)

                                    <span class="staff-status staff-status-success">
                                        Hoạt động
                                    </span>

                                @else

                                    <span class="staff-status staff-status-muted">
                                        Đang ẩn
                                    </span>

                                @endif

                            </td>



                            {{-- ACTION --}}

                            <td>

                                <div class="staff-table-actions">

                                    <a
                                        href="{{ route(
                                            'staff.categories.edit',
                                            $category
                                        ) }}"
                                        class="staff-action-button"
                                    >
                                        Chỉnh sửa
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="staff-table-pagination">
            {{ $categories->links() }}
        </div>

    @endif

</div>

@endsection
