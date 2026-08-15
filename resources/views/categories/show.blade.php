@extends('layouts.app')


@section(
    'title',
    $category->name . ' - VELORA Eyes'
)


@section('content')

<section class="velora-category-page">


    {{-- =====================================================
        HERO
    ====================================================== --}}

    <div class="velora-category-hero">

        <div class="container">

            <nav class="velora-category-breadcrumb">

                <a href="{{ route('home') }}">
                    Trang chủ
                </a>

                <i class="bi bi-chevron-right"></i>

                <a href="{{ route('products.index') }}">
                    Sản phẩm
                </a>

                <i class="bi bi-chevron-right"></i>

                <span>
                    {{ $category->name }}
                </span>

            </nav>


            <div class="velora-category-hero-grid">

                <div class="velora-category-hero-content">

                    <span class="velora-category-kicker">
                        DANH MỤC SẢN PHẨM
                    </span>


                    <h1>
                        {{ $category->name }}
                    </h1>


                    <p>

                        {{ $category->description
                            ?: 'Khám phá những sản phẩm kính mắt được VELORA Eyes tuyển chọn dành cho bạn.' }}

                    </p>


                    <div class="velora-category-hero-meta">

                        <div>

                            <i class="bi bi-eyeglasses"></i>

                            <span>

                                <strong>
                                    {{ $products->total() }}
                                </strong>

                                sản phẩm

                            </span>

                        </div>


                        <a
                            href="{{ route('products.index') }}"
                        >
                            Xem tất cả sản phẩm

                            <i class="bi bi-arrow-right"></i>
                        </a>

                    </div>

                </div>


                <div class="velora-category-hero-art">

                    <div class="velora-category-art-circle">

                        <i class="bi bi-eyeglasses"></i>

                    </div>


                    <span class="circle-one"></span>
                    <span class="circle-two"></span>

                </div>

            </div>

        </div>

    </div>



    {{-- =====================================================
        PRODUCTS
    ====================================================== --}}

    <div class="velora-category-products">

        <div class="container">


            <div class="velora-category-products-header">

                <div>

                    <span>
                        BỘ SƯU TẬP
                    </span>

                    <h2>
                        {{ $category->name }}
                    </h2>

                    <p>

                        Tìm thấy

                        <strong>
                            {{ $products->total() }}
                        </strong>

                        sản phẩm phù hợp.

                    </p>

                </div>


                <a
                    href="{{ route('products.index') }}"
                    class="velora-category-all-button"
                >
                    Tất cả sản phẩm

                    <i class="bi bi-grid"></i>
                </a>

            </div>



            @if($products->isEmpty())

                <div class="velora-category-empty">

                    <div>

                        <i class="bi bi-eyeglasses"></i>

                    </div>

                    <h3>
                        Danh mục chưa có sản phẩm
                    </h3>

                    <p>
                        Hiện tại chưa có sản phẩm nào
                        đang được kinh doanh trong danh mục này.
                    </p>


                    <a
                        href="{{ route('products.index') }}"
                        class="velora-category-empty-button"
                    >
                        Khám phá sản phẩm khác
                    </a>

                </div>

            @else

                <div class="velora-category-grid">

                    @foreach(
                        $products
                        as $product
                    )

                        @php

                            $imagePath =
                                $product
                                    ->primaryImage
                                    ?->image_path;


                            $hasSale =
                                $product->sale_price !== null
                                && (float) $product->sale_price > 0
                                && (float) $product->sale_price
                                    < (float) $product->price;


                            $discountPercent =
                                $hasSale
                                    && (float) $product->price > 0

                                    ? round(
                                        (
                                            (
                                                (float) $product->price
                                                -
                                                (float) $product->sale_price
                                            )
                                            /
                                            (float) $product->price
                                        )
                                        * 100
                                    )

                                    : null;

                        @endphp


                        <article class="velora-category-product-card">


                            {{-- IMAGE --}}

                            <a
                                href="{{ route(
                                    'products.show',
                                    $product
                                ) }}"
                                class="velora-category-product-image"
                            >

                                <img
                                    src="{{ $imagePath
                                        ? asset($imagePath)
                                        : asset('images/no-image.png') }}"
                                    alt="{{ $product->name }}"
                                    loading="lazy"
                                >


                                @if($hasSale)

                                    <span class="velora-category-sale-badge">

                                        -{{ $discountPercent }}%

                                    </span>

                                @endif


                                <span class="velora-category-view-overlay">

                                    <i class="bi bi-eye"></i>

                                    Xem chi tiết

                                </span>

                            </a>



                            {{-- BODY --}}

                            <div class="velora-category-product-body">


                                <div class="velora-category-product-top">

                                    <span class="velora-category-product-category">

                                        {{ $category->name }}

                                    </span>


                                    @if($product->material)

                                        <span class="velora-category-product-material">

                                            {{ strtoupper(
                                                $product->material
                                            ) }}

                                        </span>

                                    @endif

                                </div>



                                <a
                                    href="{{ route(
                                        'products.show',
                                        $product
                                    ) }}"
                                    class="velora-category-product-name"
                                >
                                    {{ $product->name }}
                                </a>



                                <span class="velora-category-product-sku">

                                    SKU:
                                    {{ $product->sku }}

                                </span>



                                <div class="velora-category-product-price">

                                    @if($hasSale)

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



                                <a
                                    href="{{ route(
                                        'products.show',
                                        $product
                                    ) }}"
                                    class="velora-category-product-button"
                                >

                                    Xem sản phẩm

                                    <i class="bi bi-arrow-right"></i>

                                </a>

                            </div>

                        </article>

                    @endforeach

                </div>



                {{-- PAGINATION --}}

                @if($products->hasPages())

                    <div class="velora-category-pagination">

                        {{ $products->links() }}

                    </div>

                @endif

            @endif

        </div>

    </div>

</section>

@endsection