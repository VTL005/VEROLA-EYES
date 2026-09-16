@extends('layouts.app')

@section('title', 'Sản phẩm yêu thích - VELORA Eyes')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/velora-products.css') }}">
@endpush

@section('content')

@php
    $wishlistItems = $wishlist?->items ?? collect();
@endphp

{{-- =========================================================
    WISHLIST HERO
========================================================= --}}
<section class="wishlist-page-hero">
    <div class="vl-container">
        <div class="wishlist-hero-content">
            <span class="hero-kicker">MY WISHLIST</span>
            <h1>Sản phẩm yêu thích</h1>
            <p>
                Lưu lại những mẫu gọng kính và tròng mắt cao cấp bạn quan tâm để dễ dàng so sánh và chọn mua khi sẵn sàng.
            </p>
        </div>
    </div>
</section>

{{-- =========================================================
    WISHLIST MAIN CONTENT
========================================================= --}}
<section class="wishlist-section">
    <div class="vl-container">

        <div class="wishlist-toolbar">
            <div class="product-count-summary">
                Tìm thấy <strong>{{ $wishlistItems->count() }}</strong> sản phẩm trong danh sách đã lưu
            </div>

            <a href="{{ route('products.index') }}" class="vl-btn vl-btn-outline vl-btn-sm" style="display:inline-flex;align-items:center;gap:6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                <span>Tiếp tục mua sắm</span>
            </a>
        </div>

        @if($wishlistItems->isEmpty())
            <div class="wishlist-empty-state">
                <div class="wishlist-empty-icon" aria-hidden="true">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                    </svg>
                </div>

                <h2>Danh sách yêu thích đang trống</h2>
                <p>
                    Hãy khám phá bộ sưu tập kính mắt thiết kế cao cấp tại VELORA và nhấn lưu những sản phẩm bạn yêu thích nhất.
                </p>

                <a href="{{ route('products.index') }}" class="vl-btn vl-btn-primary" style="display:inline-flex;align-items:center;gap:8px;">
                    <span>Khám phá bộ sưu tập</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
        @else
            <div class="wishlist-grid">
                @foreach($wishlistItems as $item)
                    @php
                        $product = $item->product;
                        $available = $product && $product->is_active && $product->isReadyForSale();
                    @endphp

                    <article class="wishlist-card">
                        {{-- MEDIA CONTAINER --}}
                        <div class="wishlist-image adaptive-image-container">
                            @if($product?->primaryImage)
                                <img
                                    src="{{ asset($product->primaryImage->image_path) }}"
                                    alt="{{ $product->name }}"
                                    loading="lazy"
                                    data-adaptive-image
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
                                <div class="wishlist-meta-top">
                                    @if($product->category)
                                        <span class="product-category">
                                            {{ $product->category->name }}
                                        </span>
                                    @endif

                                    <span class="product-status-tag {{ $available ? 'is-in-stock' : 'is-contact' }}">
                                        <span class="status-dot" aria-hidden="true"></span>
                                        <span>{{ $available ? 'Sẵn sàng' : 'Tạm ngưng' }}</span>
                                    </span>
                                </div>

                                <h3 class="wishlist-name">
                                    @if($available)
                                        <a href="{{ route('products.show', $product) }}">
                                            {{ $product->name }}
                                        </a>
                                    @else
                                        {{ $product->name }}
                                    @endif
                                </h3>

                                <div class="wishlist-price-row">
                                    @if($product->sale_price && $product->sale_price < $product->price)
                                        <span class="product-price">
                                            {{ number_format((float) $product->sale_price, 0, ',', '.') }}đ
                                        </span>
                                        <span class="product-old-price">
                                            {{ number_format((float) $product->price, 0, ',', '.') }}đ
                                        </span>
                                    @else
                                        <span class="product-price">
                                            {{ number_format((float) $product->current_price, 0, ',', '.') }}đ
                                        </span>
                                    @endif
                                </div>

                                <div class="wishlist-actions">
                                    @if($available)
                                        <a
                                            href="{{ route('products.show', $product) }}"
                                            class="vl-btn vl-btn-primary vl-btn-sm wishlist-view-btn"
                                        >
                                            <span>Xem chi tiết</span>
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                        </a>
                                    @else
                                        <span class="badge badge-warning" style="padding: 6px 12px;">
                                            Tạm ngưng
                                        </span>
                                    @endif

                                    <form
                                        action="{{ route('wishlist.destroy', $product) }}"
                                        method="POST"
                                        onsubmit="return confirm('Xóa sản phẩm này khỏi danh sách yêu thích?');"
                                        class="wishlist-delete-form"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="wishlist-delete-btn"
                                            title="Xóa khỏi yêu thích"
                                            aria-label="Xóa {{ $product->name }} khỏi yêu thích"
                                        >
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                            <span>Xóa</span>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <h3 style="color: var(--vl-error); font-size: 0.95rem;">
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