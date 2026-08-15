@extends('layouts.admin')


@section(
    'title',
    $product->name . ' - VELORA Eyes'
)


@section(
    'page-title',
    'Chi tiết sản phẩm'
)


@section('content')

@php

    $materials = [
        'acetate' => 'Acetate',
        'tr90' => 'TR90',
        'metal' => 'Kim loại',
        'titanium' => 'Titanium',
    ];


    $shapes = [
        'round' => 'Tròn',
        'square' => 'Vuông',
        'rectangle' => 'Chữ nhật',
        'oval' => 'Oval',
        'cat_eye' => 'Mắt mèo',
        'aviator' => 'Aviator',
        'browline' => 'Browline',
    ];


    $genders = [
        'male' => 'Nam',
        'female' => 'Nữ',
        'unisex' => 'Unisex',
        'kids' => 'Trẻ em',
    ];


    $faceShapes = [
        'round' => 'Mặt tròn',
        'square' => 'Mặt vuông',
        'oval' => 'Mặt oval',
        'heart' => 'Mặt trái tim',
    ];


    $styles = [
        'minimal' => 'Tối giản',
        'elegant' => 'Thanh lịch',
        'bold' => 'Cá tính',
        'vintage' => 'Vintage',
    ];


    $realImages =
        $product->images->filter(
            fn ($image) =>
                $image->image_path
                !== 'images/no-image.png'
        );


    $realImageCount =
        $realImages->count();


    $activeVariantCount =
        $product->variants
            ->where(
                'is_active',
                true
            )
            ->count();


    $totalStock =
        $inventoryService
            ->totalStock(
                $product
            );


    $readyForSale =
        $product->isReadyForSale();

@endphp



{{-- =========================================================
    HEADER
========================================================= --}}

<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            PRODUCT DETAIL
        </span>

        <h1>
            {{ $product->name }}
        </h1>

        <p>
            {{ $product->sku }}
            ·
            {{ $product->category?->name
                ?? 'Chưa có danh mục' }}
        </p>

    </div>


    <div class="admin-product-show-header-actions">

        <a
            href="{{ route(
                'admin.products.index'
            ) }}"
            class="admin-btn admin-btn-secondary"
        >
            <i class="bi bi-arrow-left"></i>

            Danh sách
        </a>


        <a
            href="{{ route(
                'admin.products.edit',
                $product
            ) }}"
            class="admin-btn admin-btn-primary"
        >
            <i class="bi bi-pencil"></i>

            Chỉnh sửa
        </a>

    </div>

</div>



{{-- =========================================================
    READINESS
========================================================= --}}

@if($readyForSale)

    <div class="admin-product-notice success">

        <i class="bi bi-check-circle"></i>

        <div>

            <strong>
                Sản phẩm đã sẵn sàng kinh doanh
            </strong>

            <span>
                Đã có ảnh thật và biến thể hoạt động.
            </span>

        </div>

    </div>

@else

    <div class="admin-product-notice warning">

        <i class="bi bi-exclamation-triangle"></i>

        <div>

            <strong>
                Sản phẩm chưa đủ điều kiện kinh doanh
            </strong>

            <span>

                @if($realImageCount === 0)

                    Cần thêm ít nhất 1 ảnh thật.

                @endif


                @if(
                    $realImageCount === 0
                    && $activeVariantCount === 0
                )
                    ·
                @endif


                @if($activeVariantCount === 0)

                    Cần thêm ít nhất 1 biến thể hoạt động.

                @endif

            </span>

        </div>

    </div>

@endif



{{-- =========================================================
    SUMMARY
========================================================= --}}

<div class="admin-product-show-summary">


    <div class="admin-product-show-cover">

        @php

            $primaryImage =
                $realImages
                    ->firstWhere(
                        'is_primary',
                        true
                    )
                ?? $realImages->first();

        @endphp


        @if($primaryImage)

            <img
                src="{{ asset(
                    $primaryImage->image_path
                ) }}"
                alt="{{ $product->name }}"
            >

        @else

            <div class="admin-product-show-no-image">

                <i class="bi bi-eyeglasses"></i>

                <span>
                    Chưa có hình ảnh
                </span>

            </div>

        @endif

    </div>



    <div class="admin-product-show-main-info">

        <div class="admin-product-show-title-row">

            <div>

                <span>
                    {{ $product->category?->name
                        ?? 'Chưa phân loại' }}
                </span>

                <h2>
                    {{ $product->name }}
                </h2>

                <code>
                    {{ $product->sku }}
                </code>

            </div>


            @if($product->is_active)

                <span class="admin-status success">
                    <i class="bi bi-check-circle"></i>
                    Đang kinh doanh
                </span>

            @else

                <span class="admin-status muted">
                    <i class="bi bi-pause-circle"></i>
                    Chưa bán
                </span>

            @endif

        </div>



        <div class="admin-product-show-price">

            @if($product->sale_price !== null)

                <strong>

                    {{ number_format(
                        (float) $product->sale_price,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </strong>

                <del>

                    {{ number_format(
                        (float) $product->price,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </del>

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

        </div>



        <div class="admin-product-show-stats">

            <div>

                <i class="bi bi-images"></i>

                <span>

                    <strong>
                        {{ $realImageCount }}
                    </strong>

                    <small>
                        Hình ảnh
                    </small>

                </span>

            </div>


            <div>

                <i class="bi bi-boxes"></i>

                <span>

                    <strong>
                        {{ $product->variants->count() }}
                    </strong>

                    <small>
                        Biến thể
                    </small>

                </span>

            </div>


            <div>

                <i class="bi bi-box-seam"></i>

                <span>

                    <strong>
                        {{ $totalStock }}
                    </strong>

                    <small>
                        Tổng tồn kho
                    </small>

                </span>

            </div>

        </div>

    </div>

</div>



{{-- =========================================================
    PRODUCT INFO
========================================================= --}}

<div class="admin-product-show-grid">

    <section class="admin-panel">

        <div class="admin-panel-header">

            <div>

                <h2>
                    Thông tin sản phẩm
                </h2>

            </div>

        </div>


        <div class="admin-product-show-info-grid">

            <div>
                <span>Danh mục</span>
                <strong>
                    {{ $product->category?->name ?? '—' }}
                </strong>
            </div>

            <div>
                <span>Chất liệu</span>
                <strong>
                    {{ $materials[$product->material]
                        ?? 'Chưa cập nhật' }}
                </strong>
            </div>

            <div>
                <span>Kiểu dáng</span>
                <strong>
                    {{ $shapes[$product->shape]
                        ?? 'Chưa cập nhật' }}
                </strong>
            </div>

            <div>
                <span>Đối tượng</span>
                <strong>
                    {{ $genders[$product->gender]
                        ?? 'Chưa cập nhật' }}
                </strong>
            </div>

            <div>
                <span>Kích thước</span>
                <strong>
                    {{ $product->dimensions ?? '—' }}
                </strong>
            </div>

            <div>
                <span>Slug</span>
                <strong>
                    {{ $product->slug }}
                </strong>
            </div>

        </div>

    </section>



    <section class="admin-panel">

        <div class="admin-panel-header">

            <div>
                <h2>Điều kiện kinh doanh</h2>
            </div>

        </div>


        <div class="admin-product-readiness">

            <div class="{{
                $realImageCount > 0
                    ? 'complete'
                    : 'missing'
            }}">

                <i class="bi {{
                    $realImageCount > 0
                        ? 'bi-check-circle'
                        : 'bi-x-circle'
                }}"></i>

                <span>
                    <strong>Hình ảnh</strong>
                    <small>
                        {{ $realImageCount }}/5 ảnh thật
                    </small>
                </span>

            </div>


            <div class="{{
                $activeVariantCount > 0
                    ? 'complete'
                    : 'missing'
            }}">

                <i class="bi {{
                    $activeVariantCount > 0
                        ? 'bi-check-circle'
                        : 'bi-x-circle'
                }}"></i>

                <span>
                    <strong>Biến thể</strong>
                    <small>
                        {{ $activeVariantCount }}
                        biến thể hoạt động
                    </small>
                </span>

            </div>


            <div class="{{
                $product->is_active
                    ? 'complete'
                    : 'pending'
            }}">

                <i class="bi {{
                    $product->is_active
                        ? 'bi-check-circle'
                        : 'bi-clock'
                }}"></i>

                <span>
                    <strong>Kinh doanh</strong>
                    <small>
                        {{ $product->is_active
                            ? 'Đang hiển thị cho khách'
                            : 'Chưa kích hoạt' }}
                    </small>
                </span>

            </div>

        </div>

    </section>

</div>



{{-- =========================================================
    DESCRIPTION
========================================================= --}}

@if(
    $product->description
    || $product->highlights
)

    <div class="admin-product-content-grid">

        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Mô tả sản phẩm</h2>
                </div>
            </div>

            <div class="admin-product-text-content">

                {{ $product->description
                    ?: 'Chưa có mô tả.' }}

            </div>

        </section>


        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Thông tin nổi bật</h2>
                </div>
            </div>

            <div class="admin-product-text-content">

                {{ $product->highlights
                    ?: 'Chưa có thông tin.' }}

            </div>

        </section>

    </div>

@endif



{{-- =========================================================
    FACE SHAPE / STYLE
========================================================= --}}

<div class="admin-product-content-grid">

    <section class="admin-panel">

        <div class="admin-panel-header">
            <div>
                <h2>Khuôn mặt phù hợp</h2>
            </div>
        </div>


        <div class="admin-product-tag-list">

            @forelse(
                $product->recommended_face_shapes ?? []
                as $faceShape
            )

                <span>

                    {{ $faceShapes[$faceShape]
                        ?? $faceShape }}

                </span>

            @empty

                <small>
                    Chưa cấu hình.
                </small>

            @endforelse

        </div>

    </section>


    <section class="admin-panel">

        <div class="admin-panel-header">
            <div>
                <h2>Phong cách</h2>
            </div>
        </div>


        <div class="admin-product-tag-list">

            @forelse(
                $product->style_tags ?? []
                as $style
            )

                <span>

                    {{ $styles[$style]
                        ?? $style }}

                </span>

            @empty

                <small>
                    Chưa cấu hình.
                </small>

            @endforelse

        </div>

    </section>

</div>



{{-- =========================================================
    IMAGE MANAGEMENT
========================================================= --}}

<section class="admin-panel admin-product-images-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Hình ảnh sản phẩm
            </h2>

            <p>
                {{ $realImageCount }}/5 ảnh
            </p>

        </div>

    </div>



    <div class="admin-product-image-manager">


        {{-- UPLOAD --}}

        <div class="admin-product-image-upload">

            <div class="admin-product-image-upload-icon">

                <i class="bi bi-cloud-arrow-up"></i>

            </div>


            <h3>
                Tải hình ảnh
            </h3>


            <p>
                JPG, JPEG, PNG hoặc WEBP.
                Mỗi ảnh tối đa 2MB.
            </p>


            @if($realImageCount < 5)

                <form
                    action="{{ route(
                        'admin.products.images.store',
                        $product
                    ) }}"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    @csrf


                    <input
                        type="file"
                        name="images[]"
                        accept=".jpg,.jpeg,.png,.webp"
                        multiple
                        required
                    >


                    @error('images')

                        <div class="admin-field-error">
                            {{ $message }}
                        </div>

                    @enderror


                    @error('images.*')

                        <div class="admin-field-error">
                            {{ $message }}
                        </div>

                    @enderror


                    <button
                        type="submit"
                        class="admin-btn admin-btn-primary admin-btn-full"
                    >
                        <i class="bi bi-upload"></i>

                        Tải ảnh lên
                    </button>

                </form>

            @else

                <div class="admin-product-image-limit">

                    <i class="bi bi-info-circle"></i>

                    Đã đạt giới hạn 5 hình ảnh.

                </div>

            @endif

        </div>



        {{-- GALLERY --}}

        <div class="admin-product-image-gallery">

            @forelse(
                $realImages
                    ->sortBy('sort_order')
                as $image
            )

                <div class="admin-product-image-card">


                    <div class="admin-product-image-card-photo">

                        <img
                            src="{{ asset(
                                $image->image_path
                            ) }}"
                            alt="{{ $image->alt_text
                                ?: $product->name }}"
                        >


                        @if($image->is_primary)

                            <span>
                                <i class="bi bi-star-fill"></i>
                                Ảnh chính
                            </span>

                        @endif

                    </div>



                    <div class="admin-product-image-card-footer">

                        <small>
                            Ảnh #{{ $loop->iteration }}
                        </small>


                        <div>

                            @unless($image->is_primary)

                                <form
                                    action="{{ route(
                                        'admin.products.images.set-primary',
                                        [
                                            $product,
                                            $image,
                                        ]
                                    ) }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('PATCH')


                                    <button
                                        type="submit"
                                        title="Đặt làm ảnh chính"
                                    >
                                        <i class="bi bi-star"></i>
                                    </button>

                                </form>

                            @endunless



                            @if(
                                !$product->is_active
                                || $realImageCount > 1
                            )

                                <form
                                    action="{{ route(
                                        'admin.products.images.destroy',
                                        [
                                            $product,
                                            $image,
                                        ]
                                    ) }}"
                                    method="POST"
                                    onsubmit="
                                        return confirm(
                                            'Bạn có chắc muốn xóa hình ảnh này?'
                                        );
                                    "
                                >

                                    @csrf
                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="danger"
                                        title="Xóa ảnh"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>

                                </form>

                            @else

                                <span
                                    class="admin-product-image-locked"
                                    title="Sản phẩm đang bán phải giữ ít nhất 1 ảnh"
                                >
                                    <i class="bi bi-lock"></i>
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                <div class="admin-product-no-gallery">

                    <i class="bi bi-images"></i>

                    <strong>
                        Chưa có hình ảnh thật
                    </strong>

                    <span>
                        Hãy tải ảnh lên để hoàn thiện sản phẩm.
                    </span>

                </div>

            @endforelse

        </div>

    </div>

</section>



{{-- =========================================================
    VARIANT OVERVIEW
========================================================= --}}

<section class="admin-panel admin-product-variants-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Biến thể sản phẩm
            </h2>

            <p>
                Màu sắc, size, giá và tồn kho
            </p>

        </div>


        <a
            href="{{ route(
                'admin.products.variants.create',
                $product
            ) }}"
            class="admin-btn admin-btn-primary"
        >
            <i class="bi bi-plus-lg"></i>

            Thêm biến thể
        </a>

    </div>


    @if($product->variants->isEmpty())

        <div class="admin-empty-state">

            Sản phẩm chưa có biến thể.

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table">

                <thead>

                    <tr>
                        <th>SKU</th>
                        <th>Màu</th>
                        <th>Size</th>
                        <th>Tồn kho</th>
                        <th>Tình trạng kho</th>
                        <th>Điều chỉnh giá</th>
                        <th>Giá cuối</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $product->variants
                            ->sortBy('sku')
                        as $variant
                    )

                        <tr>

                            <td>

                                <code class="admin-product-sku">

                                    {{ $variant->sku }}

                                </code>

                            </td>


                            <td>
                                {{ ucfirst(
                                    $variant->color
                                ) }}
                            </td>


                            <td>

                                <strong>
                                    {{ $variant->size }}
                                </strong>

                            </td>


                            <td>

                                <strong>
                                    {{ $variant->stock_quantity }}
                                </strong>

                            </td>


                            <td>

                                @if(
                                    $inventoryService
                                        ->isOutOfStock(
                                            $variant
                                        )
                                )

                                    <span class="admin-status danger">
                                        Hết hàng
                                    </span>

                                @elseif(
                                    $inventoryService
                                        ->isLowStock(
                                            $variant
                                        )
                                )

                                    <span class="admin-status warning">
                                        Sắp hết
                                    </span>

                                @else

                                    <span class="admin-status success">
                                        Còn hàng
                                    </span>

                                @endif

                            </td>


                            <td>

                                {{ number_format(
                                    (float) $variant
                                        ->price_adjustment,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

                            </td>


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


                            <td>

                                @if($variant->is_active)

                                    <span class="admin-status success">
                                        Đang bán
                                    </span>

                                @else

                                    <span class="admin-status muted">
                                        Ngừng bán
                                    </span>

                                @endif

                            </td>


                            <td>

                                <div class="admin-product-actions">

                                    <a
                                        href="{{ route(
                                            'admin.products.variants.edit',
                                            [
                                                $product,
                                                $variant,
                                            ]
                                        ) }}"
                                        title="Sửa biến thể"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>


                                    <form
                                        action="{{ route(
                                            'admin.products.variants.destroy',
                                            [
                                                $product,
                                                $variant,
                                            ]
                                        ) }}"
                                        method="POST"
                                        onsubmit="
                                            return confirm(
                                                'Bạn có chắc muốn xóa biến thể này?'
                                            );
                                        "
                                    >

                                        @csrf
                                        @method('DELETE')


                                        <button
                                            type="submit"
                                            title="Xóa biến thể"
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

    @endif

</section>



{{-- =========================================================
    DANGER ZONE
========================================================= --}}

<section class="admin-product-danger-zone">

    <div>

        <i class="bi bi-exclamation-triangle"></i>

        <span>

            <strong>
                Xóa sản phẩm
            </strong>

            <small>
                Nếu sản phẩm đã có Order hoặc Wishlist,
                hệ thống sẽ chuyển sang trạng thái không hoạt động
                thay vì xóa dữ liệu.
            </small>

        </span>

    </div>


    <form
        action="{{ route(
            'admin.products.destroy',
            $product
        ) }}"
        method="POST"
        onsubmit="
            return confirm(
                'Bạn có chắc muốn xóa sản phẩm này?'
            );
        "
    >

        @csrf
        @method('DELETE')


        <button
            type="submit"
            class="admin-btn admin-btn-danger"
        >
            <i class="bi bi-trash"></i>

            Xóa sản phẩm
        </button>

    </form>

</section>

@endsection