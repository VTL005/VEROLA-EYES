@extends('layouts.admin')


@section(
    'title',
    'Sản phẩm - VELORA Eyes'
)


@section(
    'page-title',
    'Sản phẩm'
)


@section('content')


{{-- =========================================================
    HEADER
========================================================= --}}

<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            PRODUCT MANAGEMENT
        </span>

        <h1>
            Quản lý sản phẩm
        </h1>

        <p>
            Quản lý thông tin kính,
            hình ảnh, biến thể và trạng thái kinh doanh.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.products.create'
        ) }}"
        class="admin-btn admin-btn-primary"
    >
        <i class="bi bi-plus-lg"></i>

        Thêm sản phẩm
    </a>

</div>



{{-- =========================================================
    STATS
========================================================= --}}

<div class="admin-product-stats">


    <div class="admin-product-stat">

        <div>
            <i class="bi bi-eyeglasses"></i>
        </div>

        <span>

            <small>
                Tổng sản phẩm
            </small>

            <strong>
                {{ $totalProducts }}
            </strong>

        </span>

    </div>



    <a
        href="{{ route(
            'admin.products.index',
            [
                'status' => 'active',
            ]
        ) }}"
        class="admin-product-stat active"
    >

        <div>
            <i class="bi bi-check-circle"></i>
        </div>

        <span>

            <small>
                Đang kinh doanh
            </small>

            <strong>
                {{ $activeProducts }}
            </strong>

        </span>

    </a>



    <a
        href="{{ route(
            'admin.products.index',
            [
                'status' => 'inactive',
            ]
        ) }}"
        class="admin-product-stat inactive"
    >

        <div>
            <i class="bi bi-pause-circle"></i>
        </div>

        <span>

            <small>
                Không hoạt động
            </small>

            <strong>
                {{ $inactiveProducts }}
            </strong>

        </span>

    </a>



    <div class="admin-product-stat ready">

        <div>
            <i class="bi bi-box-seam"></i>
        </div>

        <span>

            <small>
                Sẵn sàng kích hoạt
            </small>

            <strong>
                {{ $readyProducts }}
            </strong>

        </span>

    </div>

</div>



{{-- =========================================================
    FILTER
========================================================= --}}

<div class="admin-product-filter">

    <form
        action="{{ route(
            'admin.products.index'
        ) }}"
        method="GET"
        class="admin-product-filter-form"
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
                    placeholder="Tên sản phẩm hoặc SKU..."
                >

            </div>

        </div>



        <div>

            <label for="category_id">
                Danh mục
            </label>

            <select
                id="category_id"
                name="category_id"
                class="admin-form-control"
            >

                <option value="">
                    Tất cả danh mục
                </option>

                @foreach(
                    $categories
                    as $category
                )

                    <option
                        value="{{ $category->id }}"
                        {{
                            (string) $categoryId
                            === (string) $category->id
                                ? 'selected'
                                : ''
                        }}
                    >
                        {{ $category->name }}
                    </option>

                @endforeach

            </select>

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
                    Đang kinh doanh
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



        <div class="admin-product-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-funnel"></i>

                Lọc
            </button>


            @if(
                $keyword !== ''
                || $categoryId
                || $status
            )

                <a
                    href="{{ route(
                        'admin.products.index'
                    ) }}"
                    class="admin-btn admin-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



{{-- =========================================================
    PRODUCT TABLE
========================================================= --}}

<div class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Danh sách sản phẩm
            </h2>

            <p>
                {{ $products->total() }}
                sản phẩm
            </p>

        </div>

    </div>



    @if($products->isEmpty())

        <div class="admin-product-empty">

            <div>
                <i class="bi bi-eyeglasses"></i>
            </div>

            <h3>
                Không tìm thấy sản phẩm
            </h3>

            <p>
                Hãy thử thay đổi từ khóa
                hoặc bộ lọc.
            </p>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table admin-product-table">

                <thead>

                    <tr>

                        <th>
                            Sản phẩm
                        </th>

                        <th>
                            SKU
                        </th>

                        <th>
                            Danh mục
                        </th>

                        <th>
                            Giá bán
                        </th>

                        <th>
                            Biến thể
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
                        $products
                        as $product
                    )

                        <tr>


                            {{-- PRODUCT --}}

                            <td>

                                <div class="admin-product-info">

                                    <div class="admin-product-image">

                                        @if(
                                            $product->primaryImage
                                            && $product
                                                ->primaryImage
                                                ->image_path
                                                !== 'images/no-image.png'
                                        )

                                            <img
                                                src="{{ asset(
                                                    $product
                                                        ->primaryImage
                                                        ->image_path
                                                ) }}"
                                                alt="{{ $product->name }}"
                                            >

                                        @else

                                            <i class="bi bi-eyeglasses"></i>

                                        @endif

                                    </div>


                                    <div>

                                        <strong>

                                            {{ $product->name }}

                                        </strong>


                                        <span>

                                            {{ $product->shape
                                                ?: 'Chưa cập nhật kiểu dáng' }}

                                            @if($product->material)

                                                ·

                                                {{ strtoupper(
                                                    $product->material
                                                ) }}

                                            @endif

                                        </span>

                                    </div>

                                </div>

                            </td>



                            {{-- SKU --}}

                            <td>

                                <code class="admin-product-sku">

                                    {{ $product->sku }}

                                </code>

                            </td>



                            {{-- CATEGORY --}}

                            <td>

                                @if($product->category)

                                    <span class="admin-product-category">

                                        {{ $product
                                            ->category
                                            ->name }}

                                    </span>

                                @else

                                    <span class="admin-table-muted">
                                        Không xác định
                                    </span>

                                @endif

                            </td>



                            {{-- PRICE --}}

                            <td>

                                <div class="admin-product-price">

                                    @if(
                                        $product->sale_price
                                        !== null
                                    )

                                        <strong>

                                            {{ number_format(
                                                (float) $product
                                                    ->sale_price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                                        </strong>


                                        <del>

                                            {{ number_format(
                                                (float) $product
                                                    ->price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                                        </del>

                                    @else

                                        <strong>

                                            {{ number_format(
                                                (float) $product
                                                    ->price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                                        </strong>

                                    @endif

                                </div>

                            </td>



                            {{-- VARIANT --}}

                            <td>

                                <div class="admin-product-variant-count">

                                    <strong>
                                        {{ $product
                                            ->variants_count }}
                                    </strong>

                                    <span>
                                        biến thể
                                    </span>

                                </div>

                            </td>



                            {{-- STATUS --}}

                            <td>

                                @if($product->is_active)

                                    <span class="admin-status success">

                                        <i class="bi bi-check-circle"></i>

                                        Đang bán

                                    </span>

                                @else

                                    <span class="admin-status muted">

                                        <i class="bi bi-pause-circle"></i>

                                        Chưa bán

                                    </span>

                                @endif

                            </td>



                            {{-- CREATED --}}

                            <td>

                                <div class="admin-table-primary">

                                    <strong>

                                        {{ $product
                                            ->created_at
                                            ->format(
                                                'd/m/Y'
                                            ) }}

                                    </strong>


                                    <span>

                                        {{ $product
                                            ->created_at
                                            ->format(
                                                'H:i'
                                            ) }}

                                    </span>

                                </div>

                            </td>



                            {{-- ACTION --}}

                            <td>

                                <div class="admin-product-actions">

                                    <a
                                        href="{{ route(
                                            'admin.products.show',
                                            $product
                                        ) }}"
                                        title="Xem chi tiết"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>


                                    <a
                                        href="{{ route(
                                            'admin.products.edit',
                                            $product
                                        ) }}"
                                        title="Chỉnh sửa"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>


                                    <form
                                        action="{{ route(
                                            'admin.products.destroy',
                                            $product
                                        ) }}"
                                        method="POST"
                                        onsubmit="
                                            return confirm(
                                                'Bạn có chắc muốn xóa sản phẩm này? Nếu sản phẩm đã có lịch sử đơn hàng hoặc Wishlist, hệ thống chỉ chuyển sản phẩm sang trạng thái không hoạt động.'
                                            );
                                        "
                                    >

                                        @csrf
                                        @method('DELETE')


                                        <button
                                            type="submit"
                                            title="Xóa sản phẩm"
                                        >
                                            <i class="bi bi-trash"></i>
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

            {{ $products->links() }}

        </div>

    @endif

</div>

@endsection