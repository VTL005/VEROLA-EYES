@extends('layouts.app')

@section(
    'title',
    'VELORA Eyes - Kính mắt thời trang và chăm sóc thị lực'
)

@section(
    'meta_description',
    'Khám phá kính mắt thời trang, đặt lịch đo mắt và các dịch vụ chăm sóc thị lực tại VELORA Eyes.'
)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/home-redesign.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home-curated-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home-sale-products-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home-new-products-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/home-motion-enhanced.css') }}">
@endpush

@section('content')

<div class="vl-home-page">

    {{-- =========================================================
        1. EDITORIAL ASYMMETRIC HERO (Large Cinematic Video Canvas)
    ========================================================= --}}
    <section class="vl-hero-section">

        {{-- Edge-to-edge Cinematic Video Stage --}}
        <div class="vl-hero-video-stage" aria-hidden="true">
            <video
                class="vl-hero-video"
                autoplay
                muted
                loop
                playsinline
                preload="metadata"
            >
                <source
                    src="{{ asset('videos/velora-hero.mp4') }}"
                    type="video/mp4"
                >
            </video>

            {{-- Feathered edge blur with optical reflection --}}
            <div class="vl-hero-video-feather"></div>
        </div>

        {{-- Ambient Depth Glow --}}
        <div class="vl-hero-glow" aria-hidden="true"></div>

        <div class="vl-hero-container">
            <div class="vl-hero-content">

                <span class="vl-hero-badge vl-reveal vl-reveal-fade">
                    VELORA EYES — HAUTE OPTIQUE
                </span>

                <h1 class="vl-hero-title vl-reveal vl-reveal-up">
                    Nhìn rõ hơn
                    <br>
                    Sống <span class="vl-hero-accent">chất</span> hơn.
                </h1>

                <p class="vl-hero-description vl-reveal vl-reveal-up vl-delay-1">
                    Khám phá những mẫu kính phù hợp với khuôn mặt, phong cách và nhu cầu thị lực của riêng bạn.
                </p>

                <div class="vl-hero-actions vl-reveal vl-reveal-up vl-delay-2">
                    <a
                        href="{{ route('products.index') }}"
                        class="vl-btn vl-btn-primary"
                    >
                        Khám phá sản phẩm
                    </a>

                    <a
                        href="{{ route('appointments.create') }}"
                        class="vl-btn vl-btn-outline"
                    >
                        Đặt lịch đo mắt
                    </a>
                </div>

            </div>
        </div>

    </section>



    {{-- =========================================================
        2. FLOATING BENEFITS STRIP (Warm Pearl Surface)
    ========================================================= --}}
    <div class="vl-strip-wrapper">
        <div class="vl-container">
            <div class="vl-strip-card vl-reveal vl-reveal-up">

                {{-- Item 1 --}}
                <div class="vl-strip-item">
                    <div class="vl-strip-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="6" cy="14" r="4"></circle>
                            <circle cx="18" cy="14" r="4"></circle>
                            <line x1="10" y1="14" x2="14" y2="14"></line>
                            <path d="M2 14l2-6a4 4 0 0 1 3.8-2.7h.4"></path>
                            <path d="M22 14l-2-6a4 4 0 0 0-3.8-2.7h-.4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="vl-strip-title">
                            Kính mắt chọn lọc
                        </h3>
                        <p class="vl-strip-text">
                            Nhiều kiểu dáng, màu sắc và chất liệu phù hợp với nhiều phong cách khác nhau.
                        </p>
                    </div>
                </div>

                {{-- Item 2 --}}
                <div class="vl-strip-item">
                    <div class="vl-strip-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="vl-strip-title">
                            Đo mắt chuyên nghiệp
                        </h3>
                        <p class="vl-strip-text">
                            Chủ động đặt lịch đo mắt, lưu kết quả và theo dõi lịch sử thị lực.
                        </p>
                    </div>
                </div>

                {{-- Item 3 --}}
                <div class="vl-strip-item">
                    <div class="vl-strip-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <polyline points="9 12 11 14 15 10"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 class="vl-strip-title">
                            Bảo hành điện tử
                        </h3>
                        <p class="vl-strip-text">
                            Tra cứu bảo hành thuận tiện bằng mã bảo hành của sản phẩm.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>



    {{-- =========================================================
        3. CATEGORY — BENTO LOOKBOOK (Deep Ocean Navy Surface)
    ========================================================= --}}
    <section class="vl-section-categories">
        <div class="vl-container">

            <div class="vl-section-header vl-reveal vl-reveal-up">
                <span class="vl-kicker">
                    DANH MỤC
                </span>
                <h2 class="vl-section-title">
                    Tìm kính theo nhu cầu
                </h2>
                <p class="vl-section-subtitle">
                    Khám phá các dòng sản phẩm chọn lọc tại VELORA Eyes.
                </p>
            </div>

            @if($primaryCategory || $secondaryCategories->count() > 0)
                <div
                    class="home-categories__layout"
                    data-home-categories-layout
                >
                    @if($primaryCategory)
                        <article
                            class="home-category-featured vl-reveal vl-reveal-up"
                            data-home-featured-category
                        >
                            <a
                                href="{{ route('categories.show', $primaryCategory) }}"
                                class="home-category-card__media"
                                aria-label="{{ $primaryCategory->name }}"
                            >
                                @if($primaryCategory->homepage_image_path)
                                    <img
                                        src="{{ asset($primaryCategory->homepage_image_path) }}"
                                        alt="{{ $primaryCategory->name }}"
                                        loading="lazy"
                                    >
                                @elseif($primaryCategory->image)
                                    <img
                                        src="{{ asset($primaryCategory->image) }}"
                                        alt="{{ $primaryCategory->name }}"
                                        loading="lazy"
                                    >
                                @else
                                    <img
                                        src="{{ asset('images/no-image.png') }}"
                                        alt="{{ $primaryCategory->name }}"
                                        loading="lazy"
                                    >
                                @endif
                            </a>

                            <div class="home-category-card__content">
                                <span class="home-category-badge">BỘ SƯU TẬP CHỦ ĐẠO</span>
                                <h3 class="home-category-title">
                                    {{ $primaryCategory->name }}
                                </h3>

                                @if($primaryCategory->description)
                                    <p class="home-category-desc">
                                        {{ \Illuminate\Support\Str::limit($primaryCategory->description, 100) }}
                                    </p>
                                @endif

                                <div>
                                    <a
                                        href="{{ route('categories.show', $primaryCategory) }}"
                                        class="home-category-cta"
                                    >
                                        <span>Xem danh mục</span>
                                        <span aria-hidden="true">&rarr;</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endif

                    @if($secondaryCategories->count() > 0)
                        <div
                            class="home-categories__secondary-grid"
                            data-home-category-secondary-grid
                        >
                            @foreach($secondaryCategories->take(4) as $category)
                                <article class="home-category-secondary vl-reveal vl-reveal-up vl-delay-{{ $loop->iteration }}" data-home-reveal-item>
                                    <a
                                        href="{{ route('categories.show', $category) }}"
                                        class="home-category-card__media"
                                        aria-label="{{ $category->name }}"
                                    >
                                        @if($category->homepage_image_path)
                                            <img
                                                src="{{ asset($category->homepage_image_path) }}"
                                                alt="{{ $category->name }}"
                                                loading="lazy"
                                            >
                                        @elseif($category->image)
                                            <img
                                                src="{{ asset($category->image) }}"
                                                alt="{{ $category->name }}"
                                                loading="lazy"
                                            >
                                        @else
                                            <img
                                                src="{{ asset('images/no-image.png') }}"
                                                alt="{{ $category->name }}"
                                                loading="lazy"
                                            >
                                        @endif
                                    </a>

                                    <div class="home-category-card__content">
                                        <h3 class="home-category-title">
                                            {{ $category->name }}
                                        </h3>

                                        <div>
                                            <a
                                                href="{{ route('categories.show', $category) }}"
                                                class="home-category-cta"
                                            >
                                                <span>Khám phá</span>
                                                <span aria-hidden="true">&rarr;</span>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="empty-state">
                    Hiện chưa có danh mục sản phẩm.
                </div>
            @endif

        </div>
    </section>



    {{-- =========================================================
        4. NEW ARRIVALS — EDITORIAL SHOWCASE (Pearl/Ivory Surface)
    ========================================================= --}}
    <section class="vl-section-arrivals home-new-products">
        <div class="vl-container">

            <div class="vl-section-header-row vl-reveal vl-reveal-up">
                <div>
                    <span class="vl-kicker">
                        NEW ARRIVALS
                    </span>
                    <h2 class="vl-section-title">
                        Sản phẩm mới
                    </h2>
                    <p class="vl-section-subtitle">
                        Những thiết kế mới vừa được cập nhật tại VELORA.
                    </p>
                </div>

                <a
                    href="{{ route('products.index') }}"
                    class="vl-btn vl-btn-outline vl-btn-sm"
                >
                    Xem tất cả
                </a>
            </div>

            @if($primaryProduct)
                @if($secondaryProducts->count() > 0)
                    <div
                        class="home-new-products__layout"
                        data-home-new-products-layout
                    >
                        {{-- Spotlight Primary Product Card --}}
                        <div class="home-new-product-reveal" data-home-reveal>
                            <article class="home-new-product-card home-new-product-card--primary">
                                <span class="home-new-product-card__badge-top">
                                    Mới cập nhật
                                </span>

                                <a
                                    href="{{ route('products.show', $primaryProduct) }}"
                                    class="home-new-product-card__media"
                                    aria-label="{{ $primaryProduct->name }}"
                                >
                                    @if($primaryProduct->primaryImage)
                                        <img
                                            src="{{ asset($primaryProduct->primaryImage->image_path) }}"
                                            alt="{{ $primaryProduct->name }}"
                                            loading="lazy"
                                        >
                                    @else
                                        <img
                                            src="{{ asset('images/no-image.png') }}"
                                            alt="{{ $primaryProduct->name }}"
                                            loading="lazy"
                                        >
                                    @endif

                                    @if(
                                        $primaryProduct->sale_price
                                        &&
                                        $primaryProduct->sale_price < $primaryProduct->price
                                    )
                                        @php
                                            $featuredDiscount = round(
                                                (
                                                    ($primaryProduct->price - $primaryProduct->sale_price)
                                                    / $primaryProduct->price
                                                ) * 100
                                            );
                                        @endphp
                                        @if($featuredDiscount > 0)
                                            <span class="home-new-product-card__discount">
                                                -{{ $featuredDiscount }}%
                                            </span>
                                        @endif
                                    @endif
                                </a>

                                <div class="home-new-product-card__body">
                                    <div class="home-new-product-card__meta">
                                        @if($primaryProduct->category)
                                            <a
                                                href="{{ route('categories.show', $primaryProduct->category) }}"
                                                class="home-new-product-card__category"
                                            >
                                                {{ $primaryProduct->category->name }}
                                            </a>
                                        @else
                                            <span class="home-new-product-card__category">VELORA EYES</span>
                                        @endif

                                        @if(
                                            method_exists($primaryProduct, 'isReadyForSale')
                                            && $primaryProduct->isReadyForSale()
                                        )
                                            <span class="home-new-product-card__status is-in-stock" title="Còn hàng">
                                                <span class="status-dot" aria-hidden="true"></span>
                                                <span>Sẵn sàng</span>
                                            </span>
                                        @else
                                            <span class="home-new-product-card__status is-contact" title="Liên hệ tư vấn">
                                                <span class="status-dot" aria-hidden="true"></span>
                                                <span>Liên hệ</span>
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="home-new-product-card__title">
                                        <a href="{{ route('products.show', $primaryProduct) }}" title="{{ $primaryProduct->name }}">
                                            {{ $primaryProduct->name }}
                                        </a>
                                    </h3>

                                    <div class="home-new-product-card__pricing">
                                        @if(
                                            $primaryProduct->sale_price
                                            &&
                                            $primaryProduct->sale_price < $primaryProduct->price
                                        )
                                            <span class="home-new-product-card__price-curr">
                                                {{ number_format((float) $primaryProduct->sale_price, 0, ',', '.') }}đ
                                            </span>
                                            <span class="home-new-product-card__price-old">
                                                {{ number_format((float) $primaryProduct->price, 0, ',', '.') }}đ
                                            </span>
                                        @else
                                            <span class="home-new-product-card__price-curr">
                                                {{ number_format((float) $primaryProduct->price, 0, ',', '.') }}đ
                                            </span>
                                        @endif
                                    </div>

                                    <div class="home-new-product-card__footer">
                                        <a
                                            href="{{ route('products.show', $primaryProduct) }}"
                                            class="home-new-product-card__cta home-new-product-card__cta--primary"
                                            aria-label="Xem chi tiết {{ $primaryProduct->name }}"
                                        >
                                            <span>Xem chi tiết</span>
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>

                        {{-- Secondary Products Subgrid (Exactly 4 items in 2x2) --}}
                        <div
                            class="home-new-products__secondary-grid"
                            data-home-new-products-secondary-grid
                        >
                            @foreach($secondaryProducts->take(4) as $product)
                                @php
                                    $discountPercent = ($product->sale_price && $product->sale_price < $product->price)
                                        ? round((($product->price - $product->sale_price) / $product->price) * 100)
                                        : 0;
                                @endphp
                                <div class="home-new-product-reveal" data-home-reveal>
                                    <article class="home-new-product-card home-new-product-card--secondary">
                                        <a
                                            href="{{ route('products.show', $product) }}"
                                            class="home-new-product-card__media"
                                            aria-label="{{ $product->name }}"
                                        >
                                            @if($product->primaryImage)
                                                <img
                                                    src="{{ asset($product->primaryImage->image_path) }}"
                                                    alt="{{ $product->name }}"
                                                    loading="lazy"
                                                >
                                            @else
                                                <img
                                                    src="{{ asset('images/no-image.png') }}"
                                                    alt="{{ $product->name }}"
                                                    loading="lazy"
                                                >
                                            @endif

                                            @if($discountPercent > 0)
                                                <span class="home-new-product-card__discount">
                                                    -{{ $discountPercent }}%
                                                </span>
                                            @endif
                                        </a>

                                        <div class="home-new-product-card__body">
                                            <div class="home-new-product-card__meta">
                                                @if($product->category)
                                                    <a
                                                        href="{{ route('categories.show', $product->category) }}"
                                                        class="home-new-product-card__category"
                                                    >
                                                        {{ $product->category->name }}
                                                    </a>
                                                @else
                                                    <span class="home-new-product-card__category">VELORA EYES</span>
                                                @endif

                                                @if(method_exists($product, 'isReadyForSale') && $product->isReadyForSale())
                                                    <span class="home-new-product-card__status is-in-stock" title="Còn hàng">
                                                        <span class="status-dot" aria-hidden="true"></span>
                                                        <span>Sẵn sàng</span>
                                                    </span>
                                                @else
                                                    <span class="home-new-product-card__status is-contact" title="Liên hệ tư vấn">
                                                        <span class="status-dot" aria-hidden="true"></span>
                                                        <span>Liên hệ</span>
                                                    </span>
                                                @endif
                                            </div>

                                            <h3 class="home-new-product-card__title">
                                                <a href="{{ route('products.show', $product) }}" title="{{ $product->name }}">
                                                    {{ $product->name }}
                                                </a>
                                            </h3>

                                            <div class="home-new-product-card__pricing">
                                                @if($product->sale_price && $product->sale_price < $product->price)
                                                    <span class="home-new-product-card__price-curr">
                                                        {{ number_format((float) $product->sale_price, 0, ',', '.') }}đ
                                                    </span>
                                                    <span class="home-new-product-card__price-old">
                                                        {{ number_format((float) $product->price, 0, ',', '.') }}đ
                                                    </span>
                                                @else
                                                    <span class="home-new-product-card__price-curr">
                                                        {{ number_format((float) $product->price, 0, ',', '.') }}đ
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="home-new-product-card__footer">
                                                <a
                                                    href="{{ route('products.show', $product) }}"
                                                    class="home-new-product-card__cta"
                                                    aria-label="Xem chi tiết {{ $product->name }}"
                                                >
                                                    <span>Xem chi tiết</span>
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Single Product Fallback --}}
                    <div style="max-width:480px;margin:0 auto;" class="home-new-product-reveal" data-home-reveal>
                        <article class="home-new-product-card home-new-product-card--secondary">
                            <a
                                href="{{ route('products.show', $primaryProduct) }}"
                                class="home-new-product-card__media"
                                aria-label="{{ $primaryProduct->name }}"
                            >
                                @if($primaryProduct->primaryImage)
                                    <img
                                        src="{{ asset($primaryProduct->primaryImage->image_path) }}"
                                        alt="{{ $primaryProduct->name }}"
                                        loading="lazy"
                                    >
                                @else
                                    <img
                                        src="{{ asset('images/no-image.png') }}"
                                        alt="{{ $primaryProduct->name }}"
                                        loading="lazy"
                                    >
                                @endif
                            </a>
                            <div class="home-new-product-card__body">
                                <h3 class="home-new-product-card__title">
                                    <a href="{{ route('products.show', $primaryProduct) }}">
                                        {{ $primaryProduct->name }}
                                    </a>
                                </h3>
                            </div>
                        </article>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    Hiện chưa có sản phẩm mới.
                </div>
            @endif

        </div>
    </section>



    {{-- =========================================================
        5. SALE PRODUCTS — PRIVATE COLLECTION (Pure CSS Grid)
    ========================================================= --}}
    @if($saleProducts->count() > 0)
        <section class="vl-section-sale home-sale-products">
            <div class="vl-container">

                <div class="vl-section-header-row vl-reveal vl-reveal-up">
                    <div>
                        <span class="vl-sale-badge-header">
                            PRIVATE COLLECTION
                        </span>
                        <h2 class="vl-section-title">
                            Sản phẩm đang ưu đãi
                        </h2>
                        <p class="vl-section-subtitle">
                            Cơ hội sở hữu những thiết kế kính cao cấp với mức giá ưu đãi đặc biệt.
                        </p>
                    </div>

                    <a
                        href="{{ route('products.index') }}"
                        class="vl-btn vl-btn-outline vl-btn-sm"
                    >
                        Khám phá thêm
                    </a>
                </div>

                <div
                    class="home-sale-products__grid has-{{ min($saleProducts->count(), 4) }}"
                    data-home-sale-grid
                >
                    @foreach($saleProducts as $product)
                        @php
                            $discountPercent = ($product->sale_price && $product->sale_price < $product->price)
                                ? round((($product->price - $product->sale_price) / $product->price) * 100)
                                : 0;
                        @endphp
                        <div class="home-sale-product-reveal" data-home-reveal>
                            <article class="home-sale-product-card">
                                <a
                                    href="{{ route('products.show', $product) }}"
                                    class="home-sale-product-card__media"
                                    aria-label="{{ $product->name }}"
                                >
                                    @if($product->primaryImage)
                                        <img
                                            src="{{ asset($product->primaryImage->image_path) }}"
                                            alt="{{ $product->name }}"
                                            loading="lazy"
                                        >
                                    @else
                                        <img
                                            src="{{ asset('images/no-image.png') }}"
                                            alt="{{ $product->name }}"
                                            loading="lazy"
                                        >
                                    @endif

                                    @if($discountPercent > 0)
                                        <span class="home-sale-product-card__discount">
                                            -{{ $discountPercent }}%
                                        </span>
                                    @endif
                                </a>

                                <div class="home-sale-product-card__body">
                                    <div class="home-sale-product-card__meta">
                                        @if($product->category)
                                            <a
                                                href="{{ route('categories.show', $product->category) }}"
                                                class="home-sale-product-card__category"
                                            >
                                                {{ $product->category->name }}
                                            </a>
                                        @else
                                            <span class="home-sale-product-card__category">VELORA EYES</span>
                                        @endif

                                        @if(method_exists($product, 'isReadyForSale') && $product->isReadyForSale())
                                            <span class="home-sale-product-card__status is-in-stock" title="Còn hàng">
                                                <span class="status-dot" aria-hidden="true"></span>
                                                <span>Sẵn sàng</span>
                                            </span>
                                        @else
                                            <span class="home-sale-product-card__status is-contact" title="Liên hệ tư vấn">
                                                <span class="status-dot" aria-hidden="true"></span>
                                                <span>Liên hệ</span>
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="home-sale-product-card__title">
                                        <a href="{{ route('products.show', $product) }}" title="{{ $product->name }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>

                                    <div class="home-sale-product-card__pricing">
                                        @if($product->sale_price && $product->sale_price < $product->price)
                                            <span class="home-sale-product-card__price-curr">
                                                {{ number_format((float) $product->sale_price, 0, ',', '.') }}đ
                                            </span>
                                            <span class="home-sale-product-card__price-old">
                                                {{ number_format((float) $product->price, 0, ',', '.') }}đ
                                            </span>
                                        @else
                                            <span class="home-sale-product-card__price-curr">
                                                {{ number_format((float) $product->price, 0, ',', '.') }}đ
                                            </span>
                                        @endif
                                    </div>

                                    <div class="home-sale-product-card__footer">
                                        <a
                                            href="{{ route('products.show', $product) }}"
                                            class="home-sale-product-card__cta"
                                            aria-label="Xem chi tiết {{ $product->name }}"
                                        >
                                            <span>Xem chi tiết</span>
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    @endif



    {{-- =========================================================
        6. VISION CARE — SIGNATURE SECTION (Haute Optique Split)
    ========================================================= --}}
    <section class="vl-section-vision">

        {{-- Concentric Optical Lens Motif --}}
        <div class="vl-vision-optical-rings" aria-hidden="true">
            <div class="vl-vision-ring-1"></div>
            <div class="vl-vision-ring-2"></div>
            <div class="vl-vision-ring-3"></div>
            <div class="vl-vision-ring-center"></div>
        </div>

        <div class="vl-container">
            <div class="vl-vision-grid">

                {{-- Left Column: Copy & Actions --}}
                <div class="vl-reveal vl-reveal-left">
                    <span class="vl-vision-badge">
                        VELORA VISION CARE
                    </span>

                    <h2 class="vl-vision-title">
                        Đã bao lâu bạn chưa kiểm tra thị lực?
                    </h2>

                    <p class="vl-vision-text">
                        Một cặp kính chuẩn mực khởi đầu từ độ chính xác quang học. Trải nghiệm dịch vụ đo khám thị lực chuẩn chuyên môn và tư vấn tròng kính tối ưu tại VELORA.
                    </p>

                    <div>
                        @if(Route::has('appointments.create'))
                            <a
                                href="{{ route('appointments.create') }}"
                                class="vl-btn vl-btn-accent"
                            >
                                Đặt lịch đo mắt
                            </a>
                        @elseif(Route::has('appointments.index'))
                            @auth
                                @if(auth()->user()->isCustomer())
                                    <a
                                        href="{{ route('appointments.index') }}"
                                        class="vl-btn vl-btn-accent"
                                    >
                                        Quản lý lịch hẹn
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>

                {{-- Right Column: Optometry Features Box --}}
                <div class="vl-vision-card-box vl-reveal vl-reveal-right">
                    <div class="vl-vision-spec-item">
                        <div class="vl-vision-spec-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <circle cx="12" cy="12" r="6"></circle>
                                <circle cx="12" cy="12" r="2"></circle>
                            </svg>
                        </div>
                        <div class="vl-vision-spec-content">
                            <h4>Đo mắt chuyên nghiệp</h4>
                            <p>Chủ động hẹn giờ và kiểm tra tình trạng thị lực chính xác.</p>
                        </div>
                    </div>

                    <div class="vl-vision-spec-item">
                        <div class="vl-vision-spec-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path>
                            </svg>
                        </div>
                        <div class="vl-vision-spec-content">
                            <h4>Tư vấn kính phù hợp</h4>
                            <p>Lựa chọn thiết kế gọng và tròng kính theo nhu cầu thực tế.</p>
                        </div>
                    </div>

                    <div class="vl-vision-spec-item">
                        <div class="vl-vision-spec-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                <polyline points="7 3 7 8 15 8"></polyline>
                            </svg>
                        </div>
                        <div class="vl-vision-spec-content">
                            <h4>Lưu trữ hồ sơ thị lực</h4>
                            <p>Lưu kết quả và theo dõi lịch sử thị lực thuận tiện.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>



    {{-- =========================================================
        7. THE VELORA EXPERIENCE — OPTICAL JOURNEY (Warm Ivory)
    ========================================================= --}}
    <section class="vl-section-experience">
        <div class="vl-container">

            <div class="vl-section-header text-center vl-reveal vl-reveal-up">
                <span class="vl-kicker">
                    WHY VELORA
                </span>
                <h2 class="vl-section-title">
                    Trải nghiệm mua kính dễ dàng hơn
                </h2>
            </div>

            <div class="vl-experience-timeline">
                {{-- Step 01 --}}
                <div class="vl-step-card vl-reveal vl-reveal-up vl-delay-1">
                    <div class="vl-step-badge">01</div>
                    <h3 class="vl-step-title">Tìm kính phù hợp</h3>
                    <p class="vl-step-desc">
                        Lọc theo kiểu dáng, giới tính, chất liệu, mức giá và phong cách.
                    </p>
                </div>

                {{-- Step 02 --}}
                <div class="vl-step-card vl-reveal vl-reveal-up vl-delay-2">
                    <div class="vl-step-badge">02</div>
                    <h3 class="vl-step-title">Mua hàng trực tuyến</h3>
                    <p class="vl-step-desc">
                        Giỏ hàng, voucher, checkout và nhiều phương thức thanh toán thuận tiện.
                    </p>
                </div>

                {{-- Step 03 --}}
                <div class="vl-step-card vl-reveal vl-reveal-up vl-delay-3">
                    <div class="vl-step-badge">03</div>
                    <h3 class="vl-step-title">Theo dõi đơn hàng</h3>
                    <p class="vl-step-desc">
                        Theo dõi trạng thái đơn từ lúc đặt hàng tới khi hoàn tất.
                    </p>
                </div>

                {{-- Step 04 --}}
                <div class="vl-step-card vl-reveal vl-reveal-up vl-delay-4">
                    <div class="vl-step-badge">04</div>
                    <h3 class="vl-step-title">Chăm sóc sau mua</h3>
                    <p class="vl-step-desc">
                        Bảo hành điện tử, lịch sử đo mắt và hỗ trợ khách hàng lâu dài.
                    </p>
                </div>
            </div>

        </div>
    </section>



    {{-- =========================================================
        8. WARRANTY & SUPPORT — COMPACT CONCIERGE BANNER
    ========================================================= --}}
    @if(Route::has('warranties.lookup-form'))
        <section class="vl-section-warranty">
            <div class="vl-container">
                <div class="vl-warranty-box vl-reveal vl-reveal-up">
                    <div>
                        <span class="vl-warranty-badge">
                            Bảo hành điện tử
                        </span>
                        <h2 class="vl-warranty-title">
                            Bạn đã có mã bảo hành?
                        </h2>
                        <p class="vl-warranty-desc">
                            Tra cứu nhanh tình trạng và thời hạn bảo hành sản phẩm VELORA.
                        </p>
                    </div>

                    <a
                        href="{{ route('warranties.lookup-form') }}"
                        class="vl-btn vl-btn-accent"
                    >
                        Tra cứu bảo hành
                    </a>
                </div>
            </div>
        </section>
    @endif

</div>

@endsection