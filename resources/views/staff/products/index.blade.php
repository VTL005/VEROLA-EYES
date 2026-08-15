@extends('layouts.staff')


@section('title', 'Sản phẩm - Staff')

@section('page-title', 'Sản phẩm')


@section('content')


<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            SẢN PHẨM & KHO
        </span>

        <h1>
            Quản lý sản phẩm
        </h1>

        <p>
            Theo dõi sản phẩm, giá bán,
            biến thể và trạng thái kinh doanh.
        </p>

    </div>


    <a
        href="{{ route('staff.products.create') }}"
        class="staff-btn staff-btn-primary"
    >
        + Thêm sản phẩm
    </a>

</div>



{{-- FILTER --}}

<div class="staff-product-filter">

    <form
        action="{{ route('staff.products.index') }}"
        method="GET"
        class="staff-product-filter-form"
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
                class="staff-form-control"
                placeholder="Tên hoặc SKU..."
            >

        </div>


        <div>

            <label for="category_id">
                Danh mục
            </label>

            <select
                id="category_id"
                name="category_id"
                class="staff-form-control"
            >

                <option value="">
                    Tất cả danh mục
                </option>

                @foreach($categories as $category)

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
                class="staff-form-control"
            >

                <option value="">
                    Tất cả
                </option>

                <option
                    value="active"
                    {{ $status === 'active' ? 'selected' : '' }}
                >
                    Đang kinh doanh
                </option>

                <option
                    value="inactive"
                    {{ $status === 'inactive' ? 'selected' : '' }}
                >
                    Ngừng kinh doanh
                </option>

            </select>

        </div>


        <div class="staff-product-filter-actions">

            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Lọc
            </button>


            @if(
                $keyword !== ''
                || $categoryId
                || $status
            )

                <a
                    href="{{ route('staff.products.index') }}"
                    class="staff-btn staff-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



<div class="staff-table-card">

    <div class="staff-table-card-header">

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

        <div class="staff-category-empty">

            <div>
                ◇
            </div>

            <h3>
                Không tìm thấy sản phẩm
            </h3>

            <p>
                Hãy thay đổi bộ lọc
                hoặc tạo sản phẩm mới.
            </p>

        </div>

    @else

        <div class="staff-table-responsive">

            <table class="staff-table staff-product-table">

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
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($products as $product)

                        <tr>

                            <td>

                                <div class="staff-product-cell">

                                    @if(
                                        $product->primaryImage
                                        && $product->primaryImage->image_path
                                        !== 'images/no-image.png'
                                    )

                                        <img
                                            src="{{ asset(
                                                $product->primaryImage->image_path
                                            ) }}"
                                            alt="{{ $product->name }}"
                                        >

                                    @else

                                        <div class="staff-product-no-image">
                                            V
                                        </div>

                                    @endif


                                    <div>

                                        <strong>
                                            {{ $product->name }}
                                        </strong>

                                        <span>
                                            #{{ $product->id }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <code class="staff-slug">
                                    {{ $product->sku }}
                                </code>

                            </td>


                            <td>

                                {{ $product->category?->name
                                    ?? 'Không xác định' }}

                            </td>


                            <td>

                                @if($product->sale_price !== null)

                                    <div class="staff-product-price">

                                        <del>

                                            {{ number_format(
                                                (float) $product->price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                                        </del>

                                        <strong>

                                            {{ number_format(
                                                (float) $product->sale_price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                                        </strong>

                                    </div>

                                @else

                                    <strong>

                                        {{ number_format(
                                            (float) $product->price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                                    </strong>

                                @endif

                            </td>


                            <td>

                                <strong>
                                    {{ $product->variants_count }}
                                </strong>

                                <span class="staff-table-muted">
                                    biến thể
                                </span>

                            </td>


                            <td>

                                @if($product->is_active)

                                    <span class="staff-status staff-status-success">
                                        Đang kinh doanh
                                    </span>

                                @else

                                    <span class="staff-status staff-status-muted">
                                        Ngừng kinh doanh
                                    </span>

                                @endif

                            </td>


                            <td>

                                <div class="staff-table-actions">

                                    <a
                                        href="{{ route(
                                            'staff.products.show',
                                            $product
                                        ) }}"
                                        class="staff-action-button"
                                    >
                                        Xem
                                    </a>


                                    <a
                                        href="{{ route(
                                            'staff.products.edit',
                                            $product
                                        ) }}"
                                        class="staff-action-button"
                                    >
                                        Sửa
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="staff-table-pagination">
            {{ $products->links() }}
        </div>

    @endif

</div>

@endsection