@extends('layouts.app')


@section('title', 'Sản phẩm yêu thích - VELORA Eyes')


@section('content')

@php
    $wishlistItems = $wishlist?->items ?? collect();
@endphp


<section
    style="
        padding:54px 0 42px;
        background:linear-gradient(135deg,#f8fbff,#edf5fc);
        border-bottom:1px solid var(--velora-border);
    "
>
    <div class="velora-container">

        <span class="hero-kicker">
            MY WISHLIST
        </span>

        <h1 style="margin-bottom:10px;">
            Sản phẩm yêu thích
        </h1>

        <p class="text-muted mb-0">
            Lưu lại những mẫu kính bạn quan tâm
            để dễ dàng xem lại sau.
        </p>

    </div>
</section>


<section class="section">

    <div class="velora-container">

        <div class="section-heading-row">

            <div>
                <strong>
                    {{ $wishlistItems->count() }}
                </strong>

                <span class="text-muted">
                    sản phẩm đã lưu
                </span>
            </div>

            <a
                href="{{ route('products.index') }}"
                class="btn btn-outline"
            >
                Tiếp tục mua sắm
            </a>

        </div>


        @if($wishlistItems->isEmpty())

            <div class="wishlist-empty">

                <div class="wishlist-empty-icon">
                    ♡
                </div>

                <h2>
                    Danh sách yêu thích đang trống
                </h2>

                <p>
                    Hãy khám phá các mẫu kính tại VELORA
                    và lưu những sản phẩm bạn yêu thích.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-primary"
                >
                    Khám phá sản phẩm
                </a>

            </div>

        @else

            <div class="wishlist-grid">

                @foreach($wishlistItems as $item)

                    @php

                        $product = $item->product;

                        $available =
                            $product
                            && $product->is_active
                            && $product->isReadyForSale();

                    @endphp


                    <article class="wishlist-card">


                        {{-- IMAGE --}}

                        <div class="wishlist-image">

                            @if($product?->primaryImage)

                                <img
                                    src="{{ asset(
                                        $product
                                            ->primaryImage
                                            ->image_path
                                    ) }}"
                                    alt="{{ $product->name }}"
                                >

                            @else

                                <div class="wishlist-placeholder">
                                    VELORA
                                </div>

                            @endif


                            @if(!$available)

                                <span class="wishlist-unavailable-badge">
                                    Ngừng kinh doanh
                                </span>

                            @endif

                        </div>



                        {{-- BODY --}}

                        <div class="wishlist-body">

                            @if($product)

                                @if($product->category)

                                    <div class="product-category">
                                        {{ $product->category->name }}
                                    </div>

                                @endif


                                <h3 class="wishlist-name">

                                    @if($available)

                                        <a
                                            href="{{ route(
                                                'products.show',
                                                $product
                                            ) }}"
                                        >
                                            {{ $product->name }}
                                        </a>

                                    @else

                                        {{ $product->name }}

                                    @endif

                                </h3>


                                <div class="wishlist-price">

                                    @if(
                                        $product->sale_price
                                        &&
                                        $product->sale_price
                                            < $product->price
                                    )

                                        <span class="product-old-price">

                                            {{ number_format(
                                                (float) $product->price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                                        </span>

                                    @endif


                                    <span class="product-price">

                                        {{ number_format(
                                            (float) $product->current_price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                                    </span>

                                </div>


                                <div class="wishlist-actions">

                                    @if($available)

                                        <a
                                            href="{{ route(
                                                'products.show',
                                                $product
                                            ) }}"
                                            class="btn btn-primary btn-sm"
                                        >
                                            Xem sản phẩm
                                        </a>

                                    @else

                                        <span
                                            class="badge badge-warning"
                                        >
                                            Không thể mua lúc này
                                        </span>

                                    @endif


                                    <form
                                        action="{{ route(
                                            'wishlist.destroy',
                                            $product
                                        ) }}"
                                        method="POST"
                                        onsubmit="
                                            return confirm(
                                                'Xóa sản phẩm này khỏi danh sách yêu thích?'
                                            );
                                        "
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-outline btn-sm"
                                        >
                                            Xóa
                                        </button>

                                    </form>

                                </div>

                            @else

                                <h3>
                                    Sản phẩm không còn tồn tại
                                </h3>

                            @endif

                        </div>

                    </article>

                @endforeach

            </div>

        @endif

    </div>

</section>

@endsection