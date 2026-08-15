@extends('layouts.admin')


@section(
    'title',
    'Tồn kho - VELORA Eyes'
)


@section(
    'page-title',
    'Tồn kho'
)


@section('content')


{{-- =========================================================
    HEADER
========================================================= --}}

<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            INVENTORY MANAGEMENT
        </span>

        <h1>
            Theo dõi tồn kho
        </h1>

        <p>
            Cảnh báo các biến thể đang
            hết hàng hoặc sắp hết hàng.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.products.index'
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-eyeglasses"></i>

        Quản lý sản phẩm
    </a>

</div>



{{-- =========================================================
    INFO
========================================================= --}}

<div class="admin-inventory-notice">

    <i class="bi bi-info-circle"></i>

    <div>

        <strong>
            Ngưỡng cảnh báo hiện tại:
            {{ \App\Services\InventoryService::LOW_STOCK_THRESHOLD }}
            sản phẩm
        </strong>

        <span>
            Variant có tồn kho từ
            1 đến
            {{ \App\Services\InventoryService::LOW_STOCK_THRESHOLD }}
            được xem là sắp hết hàng.
        </span>

    </div>

</div>



{{-- =========================================================
    STATS
========================================================= --}}

<div class="admin-inventory-stats">


    <div class="admin-inventory-stat">

        <div class="all">

            <i class="bi bi-exclamation-triangle"></i>

        </div>


        <span>

            <small>
                Tổng cảnh báo
            </small>

            <strong>
                {{ $totalAlerts }}
            </strong>

            <em>
                biến thể cần chú ý
            </em>

        </span>

    </div>



    <a
        href="{{ route(
            'admin.inventory.index',
            [
                'stock_status' =>
                    'out_of_stock',
            ]
        ) }}"
        class="admin-inventory-stat"
    >

        <div class="out">

            <i class="bi bi-x-circle"></i>

        </div>


        <span>

            <small>
                Hết hàng
            </small>

            <strong>
                {{ $outOfStockCount }}
            </strong>

            <em>
                tồn kho bằng 0
            </em>

        </span>

    </a>



    <a
        href="{{ route(
            'admin.inventory.index',
            [
                'stock_status' =>
                    'low_stock',
            ]
        ) }}"
        class="admin-inventory-stat"
    >

        <div class="low">

            <i class="bi bi-box-seam"></i>

        </div>


        <span>

            <small>
                Sắp hết hàng
            </small>

            <strong>
                {{ $lowStockCount }}
            </strong>

            <em>
                cần nhập thêm hàng
            </em>

        </span>

    </a>

</div>



{{-- =========================================================
    FILTER
========================================================= --}}

<div class="admin-inventory-filter">

    <form
        action="{{ route(
            'admin.inventory.index'
        ) }}"
        method="GET"
        class="admin-inventory-filter-form"
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
                    placeholder="Tên sản phẩm, SKU, màu hoặc size..."
                >

            </div>

        </div>



        <div>

            <label for="stock_status">
                Tình trạng kho
            </label>


            <select
                id="stock_status"
                name="stock_status"
                class="admin-form-control"
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



        <div class="admin-inventory-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-funnel"></i>

                Lọc
            </button>


            @if(
                $keyword !== ''
                || $stockStatus
            )

                <a
                    href="{{ route(
                        'admin.inventory.index'
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
    TABLE
========================================================= --}}

<div class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Cảnh báo tồn kho
            </h2>

            <p>
                {{ $variants->total() }}
                biến thể
            </p>

        </div>

    </div>



    @if($variants->isEmpty())

        <div class="admin-inventory-empty">

            <div>
                <i class="bi bi-check-circle"></i>
            </div>

            <h3>
                Không có cảnh báo tồn kho
            </h3>

            <p>
                Không tìm thấy biến thể
                phù hợp với bộ lọc hiện tại.
            </p>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table admin-inventory-table">

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
                            Giá cuối
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

                        <tr class="{{
                            $inventoryService
                                ->isOutOfStock(
                                    $variant
                                )
                                ? 'inventory-out-row'
                                : ''
                        }}">


                            {{-- PRODUCT --}}

                            <td>

                                <div class="admin-inventory-product">

                                    <div>

                                        <i class="bi bi-eyeglasses"></i>

                                    </div>


                                    <span>

                                        <strong>

                                            {{ $variant
                                                ->product
                                                ->name }}

                                        </strong>


                                        <small>

                                            {{ $variant
                                                ->product
                                                ->category
                                                ?->name
                                                ?? 'Chưa phân loại' }}

                                        </small>

                                    </span>

                                </div>

                            </td>



                            {{-- SKU --}}

                            <td>

                                <code class="admin-product-sku">

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

                                <div class="admin-inventory-quantity">

                                    <strong class="{{
                                        $inventoryService
                                            ->isOutOfStock(
                                                $variant
                                            )
                                            ? 'danger'
                                            : 'warning'
                                    }}">

                                        {{ $variant
                                            ->stock_quantity }}

                                    </strong>

                                    <span>
                                        sản phẩm
                                    </span>

                                </div>

                            </td>



                            {{-- STATUS --}}

                            <td>

                                @if(
                                    $inventoryService
                                        ->isOutOfStock(
                                            $variant
                                        )
                                )

                                    <span class="admin-status danger">

                                        <i class="bi bi-x-circle"></i>

                                        Hết hàng

                                    </span>

                                @else

                                    <span class="admin-status warning">

                                        <i class="bi bi-exclamation-circle"></i>

                                        Sắp hết

                                    </span>

                                @endif

                            </td>



                            {{-- PRICE --}}

                            <td>

                                <strong class="admin-money">

                                    {{ number_format(
                                        (float) $variant
                                            ->final_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </td>



                            {{-- ACTION --}}

                            <td>

                                <div class="admin-inventory-actions">

                                    <a
                                        href="{{ route(
                                            'admin.products.show',
                                            $variant->product
                                        ) }}"
                                        title="Xem sản phẩm"
                                    >
                                        <i class="bi bi-eye"></i>
                                    </a>


                                    <a
                                        href="{{ route(
                                            'admin.products.variants.edit',
                                            [
                                                $variant->product,
                                                $variant,
                                            ]
                                        ) }}"
                                        title="Sửa biến thể"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="admin-pagination">

            {{ $variants->links() }}

        </div>

    @endif

</div>



{{-- =========================================================
    GUIDE
========================================================= --}}

<div class="admin-inventory-guide">

    <div>

        <i class="bi bi-lightbulb"></i>

        <span>

            <strong>
                Cách cập nhật tồn kho
            </strong>

            <small>
                Hiện hệ thống chưa có route nhập kho riêng.
                Bạn có thể mở biến thể tương ứng và cập nhật
                trường Tồn kho.
            </small>

        </span>

    </div>

</div>

@endsection