@extends('layouts.app')

@section(
    'title',
    $category->name . ' - VELORA Eyes'
)

@section(
    'meta_description',
    $category->description
        ?: 'Khám phá các sản phẩm kính mắt thuộc danh mục ' . $category->name . ' tại VELORA Eyes.'
)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/velora-products.css') }}">
@endpush

@section('content')

{{-- =========================================================
    COMPACT CATEGORY HERO
========================================================= --}}
<section class="category-page-hero">
    <div class="vl-container">
        <nav class="category-breadcrumb" aria-label="Đường dẫn trang">
            <a href="{{ route('home') }}">Trang chủ</a>
            <span class="breadcrumb-sep" aria-hidden="true">/</span>
            <a href="{{ route('products.index') }}">Sản phẩm</a>
            <span class="breadcrumb-sep" aria-hidden="true">/</span>
            <span class="breadcrumb-current">{{ $category->name }}</span>
        </nav>

        <div class="category-hero-content">
            <span class="hero-kicker">DANH MỤC SẢN PHẨM</span>
            <h1>{{ $category->name }}</h1>
            <p>
                {{ $category->description
                    ?: 'Khám phá những sản phẩm kính mắt được VELORA Eyes tuyển chọn với tiêu chuẩn quang học quốc tế và phong cách thời trang vượt thời gian.' }}
            </p>
            <div class="category-hero-stats">
                <span class="category-count-badge">
                    <strong>{{ $products->total() }}</strong> mẫu kính
                </span>
                <a href="{{ route('products.index') }}" class="category-all-link">
                    <span>Xem tất cả sản phẩm</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- =========================================================
    CATEGORY PRODUCTS LIST
========================================================= --}}
<section class="category-products-section">
    <div class="vl-container">

        <div class="category-toolbar">
            <div class="product-count-summary">
                Hiển thị <strong>{{ $products->total() }}</strong> sản phẩm thuộc danh mục {{ $category->name }}
            </div>
        </div>

        @if($products->isEmpty())
            <div class="product-empty-state">
                <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--vl-champagne-dark, #a08656)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-bottom:12px;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <h3>Danh mục chưa có sản phẩm</h3>
                <p>Hiện tại chưa có sản phẩm nào trong danh mục này. Hãy khám phá các bộ sưu tập khác tại VELORA.</p>
                <a href="{{ route('products.index') }}" class="vl-btn vl-btn-primary">
                    <span>Khám phá sản phẩm khác</span>
                </a>
            </div>
        @else
            <div class="product-list-grid">
                @foreach($products as $product)
                    @include('components.product-card', ['product' => $product])
                @endforeach
            </div>

            @if($products->hasPages())
                <div class="product-pagination">
                    {{ $products->links() }}
                </div>
            @endif
        @endif

    </div>
</section>

@endsection