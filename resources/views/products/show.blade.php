@extends('layouts.app')


@section(
    'title',
    $product->name . ' - VELORA Eyes'
)


@section(
    'meta_description',
    $product->description
        ? \Illuminate\Support\Str::limit($product->description, 155)
        : 'Chi tiết sản phẩm kính mắt tại VELORA Eyes.'
)


@section('content')

@php

    $primaryProductImage =
        $product->images->firstWhere(
            'is_primary',
            true
        )
        ?? $product->images->first();

@endphp


{{-- =========================================================
    BREADCRUMB
========================================================= --}}

<section
    style="
        padding:22px 0;
        background:white;
        border-bottom:1px solid var(--velora-border);
    "
>

    <div class="velora-container">

        <div class="product-breadcrumb">

            <a href="{{ route('home') }}">
                Trang chủ
            </a>

            <span>/</span>

            <a href="{{ route('products.index') }}">
                Sản phẩm
            </a>

            @if($product->category)

                <span>/</span>

                <a
                    href="{{ route(
                        'categories.show',
                        $product->category
                    ) }}"
                >
                    {{ $product->category->name }}
                </a>

            @endif

            <span>/</span>

            <strong>
                {{ $product->name }}
            </strong>

        </div>

    </div>

</section>



{{-- =========================================================
    PRODUCT MAIN
========================================================= --}}

<section class="section">

    <div class="velora-container">

        <div class="product-detail-grid">


            {{-- =================================================
                GALLERY
            ================================================== --}}

            <div class="product-gallery">

                <div class="product-main-image">

                    @if($primaryProductImage)

                        <img
                            id="mainProductImage"
                            src="{{ asset(
                                $primaryProductImage->image_path
                            ) }}"
                            alt="{{ $primaryProductImage->alt_text
                                ?? $product->name }}"
                        >

                    @else

                        <div class="empty-state">
                            Chưa có hình ảnh sản phẩm.
                        </div>

                    @endif

                </div>


                @if($product->images->count() > 1)

                    <div class="product-thumbnails">

                        @foreach($product->images as $image)

                            <button
                                type="button"
                                class="product-thumbnail {{ $image->id === $primaryProductImage?->id ? 'active' : '' }}"
                                onclick="
                                    document.getElementById(
                                        'mainProductImage'
                                    ).src='{{ asset($image->image_path) }}';

                                    document
                                        .querySelectorAll(
                                            '.product-thumbnail'
                                        )
                                        .forEach(
                                            item =>
                                                item.classList.remove(
                                                    'active'
                                                )
                                        );

                                    this.classList.add(
                                        'active'
                                    );
                                "
                            >

                                <img
                                    src="{{ asset(
                                        $image->image_path
                                    ) }}"
                                    alt="{{ $image->alt_text
                                        ?? $product->name }}"
                                >

                            </button>

                        @endforeach

                    </div>

                @endif

            </div>



            {{-- =================================================
                PRODUCT INFO
            ================================================== --}}

            <div class="product-detail-info">


                @if($product->category)

                    <a
                        href="{{ route(
                            'categories.show',
                            $product->category
                        ) }}"
                        class="product-detail-category"
                    >
                        {{ $product->category->name }}
                    </a>

                @endif


                <h1 class="product-detail-name">
                    {{ $product->name }}
                </h1>


                <div class="product-detail-meta">

                    <span>
                        SKU:
                        <strong>
                            {{ $product->sku }}
                        </strong>
                    </span>


                    @if($reviewCount > 0)

                        <span>
                            ★
                            <strong>
                                {{ number_format(
                                    (float) $averageRating,
                                    1
                                ) }}
                            </strong>

                            ({{ $reviewCount }} đánh giá)
                        </span>

                    @else

                        <span>
                            Chưa có đánh giá
                        </span>

                    @endif

                </div>



                {{-- PRICE --}}

                <div class="product-detail-price">

                    @if(
                        $product->sale_price
                        &&
                        $product->sale_price < $product->price
                    )

                        <span class="detail-old-price">

                            {{ number_format(
                                (float) $product->price,
                                0,
                                ',',
                                '.'
                            ) }}đ

                        </span>


                        <span class="detail-current-price">

                            {{ number_format(
                                (float) $product->sale_price,
                                0,
                                ',',
                                '.'
                            ) }}đ

                        </span>


                        @php

                            $detailDiscount = round(
                                (
                                    (
                                        $product->price
                                        - $product->sale_price
                                    )
                                    / $product->price
                                )
                                * 100
                            );

                        @endphp


                        <span class="badge badge-danger">

                            -{{ $detailDiscount }}%

                        </span>

                    @else

                        <span class="detail-current-price">

                            {{ number_format(
                                (float) $product->price,
                                0,
                                ',',
                                '.'
                            ) }}đ

                        </span>

                    @endif

                </div>



                {{-- SHORT DESCRIPTION --}}

                @if($product->description)

                    <p class="product-detail-description">

                        {{ \Illuminate\Support\Str::limit(
                            $product->description,
                            240
                        ) }}

                    </p>

                @endif



                {{-- BASIC INFORMATION --}}

                <div class="product-spec-grid">

                    @if($product->shape)

                        <div class="product-spec-item">

                            <span>
                                Kiểu dáng
                            </span>

                            <strong>
                                {{ $product->shape }}
                            </strong>

                        </div>

                    @endif


                    @if($product->material)

                        <div class="product-spec-item">

                            <span>
                                Chất liệu
                            </span>

                            <strong>
                                {{ $product->material }}
                            </strong>

                        </div>

                    @endif


                    @if($product->gender)

                        <div class="product-spec-item">

                            <span>
                                Đối tượng
                            </span>

                            <strong>

                                @switch($product->gender)

                                    @case('male')
                                        Nam
                                        @break

                                    @case('female')
                                        Nữ
                                        @break

                                    @case('unisex')
                                        Unisex
                                        @break

                                    @default
                                        {{ $product->gender }}

                                @endswitch

                            </strong>

                        </div>

                    @endif


                    @if($product->dimensions)

                        <div class="product-spec-item">

                            <span>
                                Kích thước
                            </span>

                            <strong>
                                {{ $product->dimensions }}
                            </strong>

                        </div>

                    @endif

                </div>



                {{-- =================================================
                    ADD TO CART
                ================================================== --}}

                <div class="product-purchase-box">

                    <h3>
                        Chọn phiên bản sản phẩm
                    </h3>


                    @auth

                        @if(auth()->user()->isCustomer())

                            <form
                                action="{{ route('cart.store') }}"
                                method="POST"
                            >

                                @csrf


                                <div class="form-group">

                                    <label
                                        for="variant_id"
                                        class="form-label"
                                    >
                                        Màu / Size
                                    </label>


                                    <select
                                        id="variant_id"
                                        name="variant_id"
                                        class="form-control"
                                        required
                                    >

                                        <option value="">
                                            -- Chọn phiên bản --
                                        </option>


                                        @foreach($product->variants as $variant)

                                            <option
                                                value="{{ $variant->id }}"
                                                {{ old('variant_id')
                                                    == $variant->id
                                                    ? 'selected'
                                                    : '' }}
                                                {{ $variant->stock_quantity <= 0
                                                    ? 'disabled'
                                                    : '' }}
                                            >

                                                {{ $variant->color }}

                                                /

                                                {{ $variant->size }}

                                                —

                                                {{ number_format(
                                                    (float) $variant->final_price,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}đ

                                                —

                                                @if(
                                                    $variant->stock_quantity > 0
                                                )

                                                    Còn
                                                    {{ $variant->stock_quantity }}

                                                @else

                                                    Hết hàng

                                                @endif

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                <div class="form-group">

                                    <label
                                        for="quantity"
                                        class="form-label"
                                    >
                                        Số lượng
                                    </label>


                                    <input
                                        type="number"
                                        id="quantity"
                                        name="quantity"
                                        class="form-control"
                                        style="max-width:150px;"
                                        value="{{ old('quantity', 1) }}"
                                        min="1"
                                        required
                                    >

                                </div>


                                <div class="product-purchase-actions">

                                    <button
                                        type="submit"
                                        class="btn btn-primary product-add-cart-button"
                                    >
                                        Thêm vào giỏ hàng
                                    </button>


                                    <form
                                        action="{{ route(
                                            'wishlist.store',
                                            $product
                                        ) }}"
                                        method="POST"
                                    >

                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn btn-outline"
                                        >
                                            ♡ Yêu thích
                                        </button>

                                    </form>
                                    <form
    action="{{ route(
        'wishlist.store',
        $product
    ) }}"
    method="POST"
    style="margin-top:10px;"
>

    @csrf


                                </form>

                                </div>

                            </form>

                        @else

                            <div class="alert alert-warning">

                                Chức năng mua hàng
                                chỉ dành cho tài khoản Customer.

                            </div>

                        @endif

                    @else

                        <div class="product-login-notice">

                            <p>
                                Đăng nhập để chọn phiên bản
                                và mua sản phẩm.
                            </p>


                            <a
                                href="{{ route('login') }}"
                                class="btn btn-primary"
                            >
                                Đăng nhập để mua hàng
                            </a>

                        </div>

                    @endauth

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    PRODUCT INFORMATION
========================================================= --}}

<section class="section section-white">

    <div class="velora-container">

        <div class="product-information-grid">

            <div>

                <span class="hero-kicker">
                    PRODUCT DETAILS
                </span>

                <h2>
                    Thông tin sản phẩm
                </h2>


                @if($product->description)

                    <div class="product-long-description">

                        {!! nl2br(
                            e($product->description)
                        ) !!}

                    </div>

                @else

                    <p class="text-muted">
                        Sản phẩm chưa có mô tả chi tiết.
                    </p>

                @endif


                @if($product->highlights)

                    <div class="product-highlight-box">

                        <h3>
                            Điểm nổi bật
                        </h3>

                        <p class="mb-0">

                            {!! nl2br(
                                e($product->highlights)
                            ) !!}

                        </p>

                    </div>

                @endif

            </div>


            <div class="card">

                <h3>
                    Thông số
                </h3>


                <div class="detail-spec-list">

                    <div>

                        <span>
                            SKU
                        </span>

                        <strong>
                            {{ $product->sku }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Danh mục
                        </span>

                        <strong>
                            {{ $product->category?->name ?? '-' }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Kiểu dáng
                        </span>

                        <strong>
                            {{ $product->shape ?? '-' }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Chất liệu
                        </span>

                        <strong>
                            {{ $product->material ?? '-' }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Kích thước
                        </span>

                        <strong>
                            {{ $product->dimensions ?? '-' }}
                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    VARIANTS
========================================================= --}}

<section class="section">

    <div class="velora-container">

        <div class="section-heading">

            <span class="hero-kicker">
                OPTIONS
            </span>

            <h2>
                Màu sắc và kích thước
            </h2>

            <p>
                Kiểm tra giá và tình trạng tồn kho
                của từng phiên bản.
            </p>

        </div>


        <div class="table-wrapper">

            <table class="velora-table">

                <thead>

                    <tr>

                        <th>
                            Màu
                        </th>

                        <th>
                            Size
                        </th>

                        <th>
                            SKU
                        </th>

                        <th>
                            Giá
                        </th>

                        <th>
                            Tồn kho
                        </th>

                        <th>
                            Tình trạng
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($product->variants as $variant)

                        <tr>

                            <td>
                                <strong>
                                    {{ $variant->color }}
                                </strong>
                            </td>


                            <td>
                                {{ $variant->size }}
                            </td>


                            <td>
                                {{ $variant->sku }}
                            </td>


                            <td>

                                <strong>

                                    {{ number_format(
                                        (float) $variant->final_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </td>


                            <td>
                                {{ $variant->stock_quantity }}
                            </td>


                            <td>

                                @if(
                                    $inventoryService
                                        ->isOutOfStock(
                                            $variant
                                        )
                                )

                                    <span class="badge badge-danger">
                                        Hết hàng
                                    </span>

                                @elseif(
                                    $inventoryService
                                        ->isLowStock(
                                            $variant
                                        )
                                )

                                    <span class="badge badge-warning">
                                        Sắp hết hàng
                                    </span>

                                @else

                                    <span class="badge badge-success">
                                        Còn hàng
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                style="text-align:center;"
                            >
                                Chưa có phiên bản sản phẩm.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</section>



{{-- =========================================================
    REVIEWS
========================================================= --}}

<section class="section section-white">

    <div class="velora-container">

        <div class="section-heading-row">

            <div>

                <span class="hero-kicker">
                    CUSTOMER REVIEWS
                </span>

                <h2 class="mb-1">
                    Đánh giá khách hàng
                </h2>


                @if($reviewCount > 0)

                    <p class="text-muted mb-0">

                        Điểm trung bình

                        <strong
                            style="
                                color:var(--velora-navy);
                                font-size:1.2rem;
                            "
                        >
                            ★
                            {{ number_format(
                                (float) $averageRating,
                                1
                            ) }}/5
                        </strong>

                        từ {{ $reviewCount }} đánh giá.

                    </p>

                @else

                    <p class="text-muted mb-0">
                        Sản phẩm chưa có đánh giá.
                    </p>

                @endif

            </div>

        </div>



        @if($product->reviews->count() > 0)

            <div class="review-list">

                @foreach($product->reviews as $review)

                    <article class="review-card">

                        <div class="review-header">

                            <div>

                                <strong>
                                    {{ $review->user?->name
                                        ?? 'Khách hàng VELORA' }}
                                </strong>


                                <div class="review-stars">

                                    @for($i = 1; $i <= 5; $i++)

                                        <span
                                            class="{{ $i <= $review->rating
                                                ? 'active'
                                                : '' }}"
                                        >
                                            ★
                                        </span>

                                    @endfor

                                </div>

                            </div>


                            <span class="text-muted">

                                {{ $review->created_at
                                    ?->format('d/m/Y') }}

                            </span>

                        </div>


                        @if($review->comment)

                            <p class="review-comment mb-0">

                                {{ $review->comment }}

                            </p>

                        @endif

                    </article>

                @endforeach

            </div>

        @else

            <div class="empty-state">

                Chưa có khách hàng nào
                đánh giá sản phẩm này.

            </div>

        @endif



        {{-- REVIEW FORM --}}

        @auth

            @if(
                auth()->user()->isCustomer()
                && Route::has('reviews.store')
            )

                <div class="review-form-card">

                    <h3>
                        Viết đánh giá của bạn
                    </h3>

                    <p class="text-muted">
                        Hệ thống sẽ kiểm tra điều kiện
                        mua hàng trước khi chấp nhận đánh giá.
                    </p>


                    <form
                        action="{{ route(
                            'reviews.store',
                            $product
                        ) }}"
                        method="POST"
                    >

                        @csrf


                        <div class="form-group">

                            <label
                                for="rating"
                                class="form-label"
                            >
                                Số sao
                            </label>

                            <select
                                name="rating"
                                id="rating"
                                class="form-control"
                                required
                            >

                                <option value="">
                                    -- Chọn đánh giá --
                                </option>

                                @for($rating = 5; $rating >= 1; $rating--)

                                    <option
                                        value="{{ $rating }}"
                                        {{ old('rating')
                                            == $rating
                                            ? 'selected'
                                            : '' }}
                                    >
                                        {{ $rating }} sao
                                    </option>

                                @endfor

                            </select>

                        </div>


                        <div class="form-group">

                            <label
                                for="comment"
                                class="form-label"
                            >
                                Nhận xét
                            </label>

                            <textarea
                                id="comment"
                                name="comment"
                                class="form-control"
                                maxlength="500"
                                placeholder="Chia sẻ trải nghiệm của bạn..."
                            >{{ old('comment') }}</textarea>

                        </div>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Gửi đánh giá
                        </button>

                    </form>

                </div>

            @endif

        @endauth

    </div>

</section>



{{-- =========================================================
    RECOMMENDED PRODUCTS
========================================================= --}}

@if($recommendedProducts->count() > 0)

    <section class="section">

        <div class="velora-container">

            <div class="section-heading-row">

                <div>

                    <span class="hero-kicker">
                        RECOMMENDED FOR YOU
                    </span>

                    <h2 class="mb-1">
                        Có thể bạn cũng thích
                    </h2>

                    <p class="text-muted mb-0">
                        Các sản phẩm được VELORA
                        gợi ý dựa trên sản phẩm bạn đang xem.
                    </p>

                </div>


                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-outline"
                >
                    Xem thêm
                </a>

            </div>


            <div class="grid grid-4">

                @foreach($recommendedProducts as $recommended)

                    @include(
                        'components.product-card',
                        [
                            'product' => $recommended
                        ]
                    )

                @endforeach

            </div>

        </div>

    </section>

@endif


@endsection