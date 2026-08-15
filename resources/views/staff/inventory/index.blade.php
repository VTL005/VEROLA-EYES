@extends('layouts.staff')


@section(
    'title',
    'Cảnh báo tồn kho - Staff'
)


@section(
    'page-title',
    'Kho hàng'
)


@section('content')


{{-- =========================================================
    HEADER
========================================================= --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            INVENTORY
        </span>

        <h1>
            Cảnh báo tồn kho
        </h1>

        <p>
            Theo dõi các biến thể đã hết hàng
            hoặc có số lượng tồn kho thấp.
        </p>

    </div>


    <a
        href="{{ route(
            'staff.products.index'
        ) }}"
        class="staff-btn staff-btn-secondary"
    >
        Quản lý sản phẩm
    </a>

</div>



{{-- =========================================================
    SUMMARY
========================================================= --}}

<div class="staff-inventory-stats">


    <a
        href="{{ route(
            'staff.inventory.index',
            [
                'stock_status'
                    => 'out_of_stock'
            ]
        ) }}"
        class="staff-inventory-stat-card danger"
    >

        <div class="staff-inventory-stat-icon">
            !
        </div>


        <div>

            <span>
                Hết hàng
            </span>

            <strong>
                {{ $outOfStockCount }}
            </strong>

            <small>
                biến thể cần nhập thêm
            </small>

        </div>

    </a>



    <a
        href="{{ route(
            'staff.inventory.index',
            [
                'stock_status'
                    => 'low_stock'
            ]
        ) }}"
        class="staff-inventory-stat-card warning"
    >

        <div class="staff-inventory-stat-icon">
            ↓
        </div>


        <div>

            <span>
                Sắp hết hàng
            </span>

            <strong>
                {{ $lowStockCount }}
            </strong>

            <small>
                từ 1 đến
                {{ $lowStockThreshold }}
                sản phẩm
            </small>

        </div>

    </a>



    <div class="staff-inventory-stat-card info">

        <div class="staff-inventory-stat-icon">
            i
        </div>


        <div>

            <span>
                Ngưỡng cảnh báo
            </span>

            <strong>
                ≤ {{ $lowStockThreshold }}
            </strong>

            <small>
                đơn vị tồn kho
            </small>

        </div>

    </div>

</div>



{{-- =========================================================
    FILTER
========================================================= --}}

<div class="staff-inventory-filter">

    <form
        action="{{ route(
            'staff.inventory.index'
        ) }}"
        method="GET"
        class="staff-inventory-filter-form"
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
                placeholder="Tên sản phẩm hoặc SKU..."
            >

        </div>



        <div>

            <label for="stock_status">
                Tình trạng kho
            </label>


            <select
                id="stock_status"
                name="stock_status"
                class="staff-form-control"
            >

                <option value="">
                    Tất cả cảnh báo
                </option>


                <option
                    value="out_of_stock"
                    {{
                        $stockStatus
                        === 'out_of_stock'
                            ? 'selected'
                            : ''
                    }}
                >
                    Hết hàng
                </option>


                <option
                    value="low_stock"
                    {{
                        $stockStatus
                        === 'low_stock'
                            ? 'selected'
                            : ''
                    }}
                >
                    Sắp hết hàng
                </option>

            </select>

        </div>



        <div class="staff-inventory-filter-actions">

            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Lọc
            </button>


            @if(
                $keyword !== ''
                || $stockStatus
            )

                <a
                    href="{{ route(
                        'staff.inventory.index'
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
                Danh sách cần chú ý
            </h2>


            <p>
                {{ $variants->total() }}
                biến thể
            </p>

        </div>

    </div>



    @if($variants->isEmpty())

        <div class="staff-inventory-empty">

            <div class="staff-inventory-empty-icon">
                ✓
            </div>


            <h3>
                Không có cảnh báo phù hợp
            </h3>


            <p>

                @if(
                    $keyword !== ''
                    || $stockStatus
                )

                    Không tìm thấy biến thể
                    phù hợp với bộ lọc hiện tại.

                @else

                    Các biến thể đang hoạt động
                    hiện chưa có vấn đề về tồn kho.

                @endif

            </p>

        </div>

    @else

        <div class="staff-table-responsive">

            <table class="staff-table">

                <thead>

                    <tr>

                        <th>
                            Sản phẩm
                        </th>

                        <th>
                            SKU Variant
                        </th>

                        <th>
                            Màu
                        </th>

                        <th>
                            Size
                        </th>

                        <th>
                            Tồn kho
                        </th>

                        <th>
                            Tình trạng
                        </th>

                        <th>
                            Giá bán
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $variants
                        as $variant
                    )

                        @php

                            $finalPrice =
                                (float) $variant
                                    ->product
                                    ->current_price
                                + (float) $variant
                                    ->price_adjustment;

                        @endphp


                        <tr
                            class="{{
                                $variant->stock_quantity === 0
                                    ? 'staff-inventory-row-danger'
                                    : ''
                            }}"
                        >


                            {{-- PRODUCT --}}

                            <td>

                                <div class="staff-inventory-product">

                                    <div class="staff-inventory-product-icon">
                                        ◇
                                    </div>


                                    <div>

                                        <strong>
                                            {{ $variant
                                                ->product
                                                ->name }}
                                        </strong>


                                        <span>

                                            {{ $variant
                                                ->product
                                                ->category
                                                ?->name
                                                ?? 'Chưa có danh mục' }}

                                        </span>

                                    </div>

                                </div>

                            </td>



                            {{-- SKU --}}

                            <td>

                                <code class="staff-slug">
                                    {{ $variant->sku }}
                                </code>

                            </td>



                            {{-- COLOR --}}

                            <td>

                                {{ ucfirst(
                                    $variant->color
                                ) }}

                            </td>



                            {{-- SIZE --}}

                            <td>

                                <strong>
                                    {{ $variant->size }}
                                </strong>

                            </td>



                            {{-- STOCK --}}

                            <td>

                                <strong
                                    class="staff-inventory-stock
                                    {{
                                        $variant->stock_quantity === 0
                                            ? 'danger'
                                            : 'warning'
                                    }}"
                                >

                                    {{ $variant
                                        ->stock_quantity }}

                                </strong>

                            </td>



                            {{-- STATUS --}}

                            <td>

                                @if(
                                    $inventoryService
                                        ->isOutOfStock(
                                            $variant
                                        )
                                )

                                    <span class="staff-status staff-status-danger">
                                        Hết hàng
                                    </span>

                                @elseif(
                                    $inventoryService
                                        ->isLowStock(
                                            $variant
                                        )
                                )

                                    <span class="staff-status staff-status-warning">
                                        Sắp hết hàng
                                    </span>

                                @else

                                    <span class="staff-status staff-status-success">
                                        Còn hàng
                                    </span>

                                @endif

                            </td>



                            {{-- PRICE --}}

                            <td>

                                <strong>

                                    {{ number_format(
                                        $finalPrice,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </td>



                            {{-- ACTION --}}

                            <td>

                                <div class="staff-table-actions">

                                    <a
                                        href="{{ route(
                                            'staff.products.variants.edit',
                                            [
                                                $variant->product,
                                                $variant
                                            ]
                                        ) }}"
                                        class="staff-action-button"
                                    >
                                        Cập nhật kho
                                    </a>


                                    <a
                                        href="{{ route(
                                            'staff.products.show',
                                            $variant->product
                                        ) }}"
                                        class="staff-action-button"
                                    >
                                        Sản phẩm
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>



        <div class="staff-table-pagination">

            {{ $variants->links() }}

        </div>

    @endif

</div>

@endsection