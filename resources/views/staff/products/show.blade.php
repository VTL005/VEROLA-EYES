@extends('layouts.staff')


@section('title', $product->name . ' - Staff')

@section('page-title', 'Chi tiết sản phẩm')


@section('content')

@php

    $realImages =
        $product->images->filter(
            fn ($image) =>
                $image->image_path
                !== 'images/no-image.png'
        );

    $activeVariants =
        $product->variants->where(
            'is_active',
            true
        );

    $materialLabels = [
        'acetate' => 'Acetate',
        'tr90' => 'TR90',
        'metal' => 'Kim loại',
        'titanium' => 'Titanium',
    ];

    $shapeLabels = [
        'round' => 'Tròn',
        'square' => 'Vuông',
        'rectangle' => 'Chữ nhật',
        'oval' => 'Oval',
        'cat_eye' => 'Mắt mèo',
        'aviator' => 'Aviator',
        'browline' => 'Browline',
    ];

    $genderLabels = [
        'male' => 'Nam',
        'female' => 'Nữ',
        'unisex' => 'Unisex',
        'kids' => 'Trẻ em',
    ];

@endphp



<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            PRODUCT #{{ $product->id }}
        </span>

        <h1>
            {{ $product->name }}
        </h1>

        <p>
            SKU: {{ $product->sku }}
        </p>

    </div>


    <div class="staff-page-header-actions">

        <a
            href="{{ route(
                'staff.products.index'
            ) }}"
            class="staff-btn staff-btn-secondary"
        >
            ← Danh sách
        </a>


        <a
            href="{{ route(
                'staff.products.edit',
                $product
            ) }}"
            class="staff-btn staff-btn-primary"
        >
            Chỉnh sửa
        </a>

    </div>

</div>



{{-- READINESS --}}

<div class="staff-product-readiness">

    <div>

        <span class="staff-readiness-dot
            {{ $realImages->isNotEmpty() ? 'ok' : '' }}">
        </span>

        <div>

            <strong>
                Hình ảnh
            </strong>

            <span>
                {{ $realImages->count() }}/5 ảnh thật
            </span>

        </div>

    </div>


    <div>

        <span class="staff-readiness-dot
            {{ $activeVariants->isNotEmpty() ? 'ok' : '' }}">
        </span>

        <div>

            <strong>
                Biến thể
            </strong>

            <span>
                {{ $activeVariants->count() }}
                đang hoạt động
            </span>

        </div>

    </div>


    <div>

        @if($product->is_active)

            <span class="staff-status staff-status-success">
                Đang kinh doanh
            </span>

        @elseif($product->isReadyForSale())

            <span class="staff-status staff-status-warning">
                Đủ điều kiện kích hoạt
            </span>

        @else

            <span class="staff-status staff-status-muted">
                Chưa đủ điều kiện
            </span>

        @endif

    </div>

</div>



<div class="staff-product-detail-grid">


    <div class="staff-product-detail-main">


        {{-- INFORMATION --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thông tin sản phẩm
                </h2>

            </div>


            <div class="staff-product-info-grid">

                <div>
                    <span>Danh mục</span>
                    <strong>
                        {{ $product->category?->name
                            ?? 'Chưa cập nhật' }}
                    </strong>
                </div>

                <div>
                    <span>SKU</span>
                    <strong>{{ $product->sku }}</strong>
                </div>

                <div>
                    <span>Chất liệu</span>
                    <strong>
                        {{ $materialLabels[$product->material]
                            ?? 'Chưa cập nhật' }}
                    </strong>
                </div>

                <div>
                    <span>Kiểu dáng</span>
                    <strong>
                        {{ $shapeLabels[$product->shape]
                            ?? 'Chưa cập nhật' }}
                    </strong>
                </div>

                <div>
                    <span>Đối tượng</span>
                    <strong>
                        {{ $genderLabels[$product->gender]
                            ?? 'Chưa cập nhật' }}
                    </strong>
                </div>

                <div>
                    <span>Kích thước</span>
                    <strong>
                        {{ $product->dimensions
                            ?: 'Chưa cập nhật' }}
                    </strong>
                </div>

            </div>


            @if($product->description)

                <div class="staff-product-text-block">

                    <span>
                        Mô tả
                    </span>

                    <p>
                        {{ $product->description }}
                    </p>

                </div>

            @endif


            @if($product->highlights)

                <div class="staff-product-text-block">

                    <span>
                        Điểm nổi bật
                    </span>

                    <p>
                        {{ $product->highlights }}
                    </p>

                </div>

            @endif

        </section>



        {{-- IMAGES --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading staff-product-section-heading">

                <div>

                    <h2>
                        Hình ảnh sản phẩm
                    </h2>

                    <p>
                        Tối đa 5 ảnh thật.
                    </p>

                </div>

            </div>


            <form
                action="{{ route(
                    'staff.products.images.store',
                    $product
                ) }}"
                method="POST"
                enctype="multipart/form-data"
                class="staff-product-image-upload"
            >

                @csrf


                <input
                    type="file"
                    name="images[]"
                    multiple
                    accept=".jpg,.jpeg,.png,.webp"
                    class="staff-form-control"
                    required
                >


                <button
                    type="submit"
                    class="staff-btn staff-btn-primary"
                >
                    Tải ảnh lên
                </button>

            </form>


            <div class="staff-product-images-grid">

                @forelse($product->images as $image)

                    <div class="staff-product-image-card">

                        <div class="staff-product-image-preview">

                            <img
                                src="{{ asset(
                                    $image->image_path
                                ) }}"
                                alt="{{ $image->alt_text
                                    ?? $product->name }}"
                            >


                            @if($image->is_primary)

                                <span class="staff-image-primary-badge">
                                    Ảnh chính
                                </span>

                            @endif

                        </div>


                        @if(
                            $image->image_path
                            !== 'images/no-image.png'
                        )

                            <div class="staff-product-image-actions">

                                @unless($image->is_primary)

                                    <form
                                        action="{{ route(
                                            'staff.products.images.set-primary',
                                            [
                                                $product,
                                                $image
                                            ]
                                        ) }}"
                                        method="POST"
                                    >

                                        @csrf
                                        @method('PATCH')


                                        <button
                                            type="submit"
                                            class="staff-action-button"
                                        >
                                            Đặt ảnh chính
                                        </button>

                                    </form>

                                @endunless


                                <form
                                    action="{{ route(
                                        'staff.products.images.destroy',
                                        [
                                            $product,
                                            $image
                                        ]
                                    ) }}"
                                    method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa ảnh này?');"
                                >

                                    @csrf
                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="staff-action-button staff-action-danger"
                                    >
                                        Xóa
                                    </button>

                                </form>

                            </div>

                        @else

                            <span class="staff-table-muted">
                                Ảnh mặc định
                            </span>

                        @endif

                    </div>

                @empty

                    <div class="staff-empty-small">
                        Chưa có hình ảnh sản phẩm.
                    </div>

                @endforelse

            </div>

        </section>



        {{-- VARIANTS --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading staff-product-section-heading">

                <div>

                    <h2>
                        Biến thể sản phẩm
                    </h2>

                    <p>
                        Màu sắc, size, SKU và tồn kho.
                    </p>

                </div>


                <a
                    href="{{ route(
                        'staff.products.variants.create',
                        $product
                    ) }}"
                    class="staff-btn staff-btn-primary"
                >
                    + Thêm biến thể
                </a>

            </div>


            @if($product->variants->isEmpty())

                <div class="staff-empty-small">

                    Sản phẩm chưa có biến thể.

                </div>

            @else

                <div class="staff-table-responsive">

                    <table class="staff-table">

                        <thead>

                            <tr>
                                <th>SKU</th>
                                <th>Màu</th>
                                <th>Size</th>
                                <th>Tồn kho</th>
                                <th>Tình trạng</th>
                                <th>Điều chỉnh giá</th>
                                <th>Giá cuối</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>

                        </thead>


                        <tbody>

                            @foreach($product->variants as $variant)

                                @php

                                    $finalPrice =
                                        (float) $product->current_price
                                        + (float) $variant->price_adjustment;

                                @endphp


                                <tr>

                                    <td>
                                        <code class="staff-slug">
                                            {{ $variant->sku }}
                                        </code>
                                    </td>

                                    <td>
                                        {{ ucfirst($variant->color) }}
                                    </td>

                                    <td>
                                        {{ $variant->size }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $variant->stock_quantity }}
                                        </strong>
                                    </td>

                                    <td>

                                        @if(
                                            $inventoryService
                                                ->isOutOfStock($variant)
                                        )

                                            <span class="staff-status staff-status-danger">
                                                Hết hàng
                                            </span>

                                        @elseif(
                                            $inventoryService
                                                ->isLowStock($variant)
                                        )

                                            <span class="staff-status staff-status-warning">
                                                Sắp hết
                                            </span>

                                        @else

                                            <span class="staff-status staff-status-success">
                                                Còn hàng
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        {{ number_format(
                                            (float) $variant->price_adjustment,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                                    </td>

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

                                    <td>

                                        @if($variant->is_active)

                                            <span class="staff-status staff-status-success">
                                                Hoạt động
                                            </span>

                                        @else

                                            <span class="staff-status staff-status-muted">
                                                Ngừng bán
                                            </span>

                                        @endif

                                    </td>

                                    <td>

    <div class="staff-table-actions">

        <a
            href="{{ route(
                'staff.products.variants.edit',
                [
                    $product,
                    $variant
                ]
            ) }}"
            class="staff-action-button"
        >
            Sửa
        </a>


        @if($variant->is_active)

            <form
                action="{{ route(
                    'staff.products.variants.deactivate',
                    [
                        $product,
                        $variant
                    ]
                ) }}"
                method="POST"
                onsubmit="return confirm('Bạn có chắc muốn ngừng bán biến thể này?');"
            >

                @csrf
                @method('PATCH')


                <button
                    type="submit"
                    class="staff-action-button staff-action-danger"
                >
                    Ngừng bán
                </button>

            </form>

        @endif

    </div>

</td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </section>

    </div>



    {{-- RIGHT --}}

    <aside class="staff-product-detail-sidebar">


        <div class="staff-form-card">

            <div class="staff-form-card-heading">
                <h2>Giá bán</h2>
            </div>


            @if($product->sale_price !== null)

                <del class="staff-product-old-price">

                    {{ number_format(
                        (float) $product->price,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </del>

                <strong class="staff-product-big-price">

                    {{ number_format(
                        (float) $product->sale_price,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </strong>

            @else

                <strong class="staff-product-big-price">

                    {{ number_format(
                        (float) $product->price,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </strong>

            @endif

        </div>



        <div class="staff-form-card staff-category-meta">

            <span>
                Slug
            </span>

            <strong>
                {{ $product->slug }}
            </strong>


            <span>
                Tổng biến thể
            </span>

            <strong>
                {{ $product->variants->count() }}
            </strong>


            <span>
                Tổng ảnh thật
            </span>

            <strong>
                {{ $realImages->count() }}
            </strong>


            <span>
                Ngày tạo
            </span>

            <strong>
                {{ $product->created_at->format('d/m/Y H:i') }}
            </strong>

        </div>



        <div class="staff-form-card">

            <a
                href="{{ route(
                    'staff.products.edit',
                    $product
                ) }}"
                class="staff-btn staff-btn-primary staff-product-full-button"
            >
                Chỉnh sửa sản phẩm
            </a>

        </div>

    </aside>

</div>

@endsection