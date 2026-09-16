@extends('layouts.app')

@section('title', 'Sản phẩm - VELORA Eyes')

@section(
    'meta_description',
    'Khám phá các mẫu kính mắt tại VELORA Eyes và tìm sản phẩm phù hợp với phong cách của bạn.'
)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/velora-products.css') }}">
@endpush

@section('content')

{{-- =========================================================
    COMPACT DARK EDITORIAL CATALOG BANNER
========================================================= --}}
<section class="product-page-hero">
    <div class="vl-container">
        <div class="product-hero-composition">
            <div class="product-hero-left">
                <span class="product-hero-kicker">VELORA COLLECTION</span>
                <h1 class="product-hero-title">Khám phá bộ sưu tập kính</h1>
                <p class="product-hero-subtitle">
                    Từ gọng thời trang đến kính bảo vệ thị lực — tìm chiếc kính phù hợp với phong cách và nhu cầu của bạn.
                </p>
            </div>

            <div class="product-hero-right">
                <div class="product-hero-stat-card">
                    <span class="stat-count">{{ $products->total() }}</span>
                    <span class="stat-label">Sản phẩm tuyển chọn</span>
                </div>
                {{-- OPTICAL LENS MOTIF (SVG) --}}
                <div class="product-hero-lens-motif" aria-hidden="true">
                    <svg viewBox="0 0 200 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="60" cy="60" r="48" stroke="rgba(197, 168, 128, 0.3)" stroke-width="1.5"/>
                        <circle cx="140" cy="60" r="48" stroke="rgba(56, 189, 248, 0.25)" stroke-width="1.5"/>
                        <line x1="60" y1="60" x2="140" y2="60" stroke="rgba(255, 255, 255, 0.2)" stroke-width="1.5" stroke-dasharray="3 3"/>
                        <circle cx="100" cy="60" r="8" fill="rgba(197, 168, 128, 0.15)" stroke="rgba(197, 168, 128, 0.4)" stroke-width="1"/>
                        <circle cx="60" cy="60" r="3" fill="#38BDF8"/>
                        <circle cx="140" cy="60" r="3" fill="#C5A880"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- =========================================================
    PRODUCT CATALOG & FILTERS
========================================================= --}}
<section class="product-catalog-section">
    <div class="vl-container">

        <div class="product-page-layout">

            {{-- MOBILE FILTER TOGGLE --}}
            <button
                type="button"
                class="filter-toggle-btn"
                id="filterToggleBtn"
                aria-expanded="false"
                aria-controls="filterPanel"
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                <span>Bộ lọc sản phẩm</span>
            </button>

            {{-- FILTER OVERLAY (mobile) --}}
            <div class="filter-drawer-overlay" id="filterOverlay"></div>

            {{-- =================================================
                FILTER SIDEBAR
            ================================================== --}}
            <aside class="product-filter-panel" id="filterPanel">

                {{-- CLOSE (mobile) --}}
                <button
                    type="button"
                    class="filter-drawer-close"
                    id="filterCloseBtn"
                    aria-label="Đóng bộ lọc"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>

                <div class="product-filter-header">
                    <div>
                        <h2 class="product-filter-title">Bộ lọc</h2>
                    </div>

                    @if(request()->hasAny([
                        'keyword', 'category_id', 'min_price', 'max_price',
                        'shape', 'material', 'gender', 'color', 'sort'
                    ]))
                        <a href="{{ route('products.index') }}" class="filter-reset">
                            Xóa lọc
                        </a>
                    @endif
                </div>

                <form action="{{ route('products.index') }}" method="GET">
                    {{-- SEARCH --}}
                    <div class="filter-group">
                        <label for="keyword" class="filter-label">Tìm kiếm</label>
                        <input
                            type="text"
                            id="keyword"
                            name="keyword"
                            class="form-control filter-input"
                            value="{{ request('keyword') }}"
                            placeholder="Tên kính, SKU..."
                        >
                    </div>

                    {{-- CATEGORY --}}
                    <div class="filter-group">
                        <label for="category_id" class="filter-label">Danh mục</label>
                        <select id="category_id" name="category_id" class="form-control filter-select">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PRICE --}}
                    <div class="filter-group">
                        <label class="filter-label">Khoảng giá (VNĐ)</label>
                        <div class="price-filter-grid">
                            <input
                                type="number"
                                name="min_price"
                                class="form-control filter-input"
                                min="0"
                                value="{{ request('min_price') }}"
                                placeholder="Từ"
                            >
                            <input
                                type="number"
                                name="max_price"
                                class="form-control filter-input"
                                min="0"
                                value="{{ request('max_price') }}"
                                placeholder="Đến"
                            >
                        </div>
                    </div>

                    {{-- SHAPE --}}
                    <div class="filter-group">
                        <label for="shape" class="filter-label">Kiểu dáng</label>
                        <select id="shape" name="shape" class="form-control filter-select">
                            <option value="">Tất cả kiểu dáng</option>
                            @php
                                $shapes = [
                                    'round' => 'Gọng tròn',
                                    'square' => 'Gọng vuông',
                                    'rectangle' => 'Chữ nhật',
                                    'aviator' => 'Phi công',
                                    'cat_eye' => 'Mắt mèo',
                                    'browline' => 'Browline',
                                    'oval' => 'Oval',
                                    'polygon' => 'Đa giác',
                                    'geometric' => 'Hình học',
                                ];
                            @endphp
                            @foreach($shapes as $key => $label)
                                <option value="{{ $key }}" {{ request('shape') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- MATERIAL --}}
                    <div class="filter-group">
                        <label for="material" class="filter-label">Chất liệu</label>
                        <select id="material" name="material" class="form-control filter-select">
                            <option value="">Tất cả chất liệu</option>
                            @php
                                $materials = [
                                    'titanium' => 'Titanium',
                                    'acetate' => 'Acetate',
                                    'metal' => 'Kim loại',
                                    'plastic' => 'Nhựa dẻo',
                                    'tr90' => 'TR90',
                                    'mixed' => 'Phối hợp',
                                ];
                            @endphp
                            @foreach($materials as $key => $label)
                                <option value="{{ $key }}" {{ request('material') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- GENDER --}}
                    <div class="filter-group">
                        <label for="gender" class="filter-label">Đối tượng</label>
                        <select id="gender" name="gender" class="form-control filter-select">
                            <option value="">Tất cả đối tượng</option>
                            <option value="unisex" {{ request('gender') === 'unisex' ? 'selected' : '' }}>Unisex</option>
                            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Nữ</option>
                        </select>
                    </div>

                    {{-- COLOR --}}
                    <div class="filter-group">
                        <label for="color" class="filter-label">Màu sắc</label>
                        <select id="color" name="color" class="form-control filter-select">
                            <option value="">Tất cả màu sắc</option>
                            @php
                                $colors = [
                                    'black' => 'Đen', 'gold' => 'Vàng kim',
                                    'silver' => 'Bạc', 'brown' => 'Nâu',
                                    'tortoise' => 'Đồi mồi', 'gray' => 'Xám',
                                    'clear' => 'Trong suốt', 'pink' => 'Hồng',
                                    'blue' => 'Xanh dương', 'red' => 'Đỏ',
                                    'green' => 'Xanh rêu', 'purple' => 'Tím',
                                    'mixed' => 'Đa sắc',
                                ];
                            @endphp
                            @foreach($colors as $key => $label)
                                <option value="{{ $key }}" {{ request('color') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SORT --}}
                    <div class="filter-group">
                        <label for="sort" class="filter-label">Sắp xếp</label>
                        <select id="sort" name="sort" class="form-control filter-select">
                            <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Tên A–Z</option>
                        </select>
                    </div>

                    <button
                        type="submit"
                        class="vl-btn vl-btn-primary filter-submit-btn"
                    >
                        Áp dụng bộ lọc
                    </button>
                </form>

            </aside>

            {{-- =================================================
                MAIN PRODUCT AREA
            ================================================== --}}
            <main class="product-catalog-content">

                {{-- Toolbar / summary --}}
                <div class="product-list-header">
                    <div class="product-count-summary">
                        Hiển thị <strong>{{ $products->total() }}</strong> sản phẩm
                    </div>
                </div>

                {{-- Active Filters Badges --}}
                @if(request()->hasAny(['keyword', 'category_id', 'shape', 'material', 'gender', 'color', 'min_price', 'max_price']))
                    <div class="active-filters-bar">
                        <span class="active-filters-label">Đang lọc:</span>

                        @if(request('keyword'))
                            <a href="{{ request()->fullUrlWithQuery(['keyword' => null]) }}" class="active-filter-chip" title="Bỏ lọc từ khóa">
                                <span>"{{ request('keyword') }}"</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </a>
                        @endif

                        @if(request('category_id'))
                            @php
                                $selectedCategory = $categories->firstWhere('id', (int) request('category_id'));
                            @endphp
                            @if($selectedCategory)
                                <a href="{{ request()->fullUrlWithQuery(['category_id' => null]) }}" class="active-filter-chip" title="Bỏ lọc danh mục">
                                    <span>{{ $selectedCategory->name }}</span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </a>
                            @endif
                        @endif

                        @if(request('shape'))
                            <a href="{{ request()->fullUrlWithQuery(['shape' => null]) }}" class="active-filter-chip" title="Bỏ lọc kiểu dáng">
                                <span>{{ $shapes[request('shape')] ?? request('shape') }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </a>
                        @endif

                        @if(request('material'))
                            <a href="{{ request()->fullUrlWithQuery(['material' => null]) }}" class="active-filter-chip" title="Bỏ lọc chất liệu">
                                <span>{{ $materials[request('material')] ?? request('material') }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </a>
                        @endif

                        @if(request('gender'))
                            <a href="{{ request()->fullUrlWithQuery(['gender' => null]) }}" class="active-filter-chip" title="Bỏ lọc đối tượng">
                                <span>{{ request('gender') === 'unisex' ? 'Unisex' : (request('gender') === 'male' ? 'Nam' : 'Nữ') }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </a>
                        @endif

                        @if(request('color'))
                            <a href="{{ request()->fullUrlWithQuery(['color' => null]) }}" class="active-filter-chip" title="Bỏ lọc màu sắc">
                                <span>Màu: {{ $colors[request('color')] ?? request('color') }}</span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </a>
                        @endif

                        @if(request('min_price') || request('max_price'))
                            <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="active-filter-chip" title="Bỏ lọc giá">
                                <span>
                                    {{ request('min_price') ? number_format((float) request('min_price'), 0, ',', '.') . 'đ' : '0đ' }}
                                    –
                                    {{ request('max_price') ? number_format((float) request('max_price'), 0, ',', '.') . 'đ' : '∞' }}
                                </span>
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </a>
                        @endif

                        <a href="{{ route('products.index') }}" class="active-filters-clear">
                            Xóa tất cả
                        </a>
                    </div>
                @endif

                {{-- Products Grid --}}
                @if($products->count() > 0)
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
                @else
                    <div class="product-empty-state">
                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="var(--vl-champagne-dark, #a08656)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin-bottom:12px;"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <h3>Không tìm thấy sản phẩm</h3>
                        <p>Thử thay đổi từ khóa hoặc điều chỉnh các điều kiện lọc.</p>
                        <a
                            href="{{ route('products.index') }}"
                            class="vl-btn vl-btn-primary"
                            style="display:inline-flex;align-items:center;gap:6px;"
                        >
                            <span>Xóa bộ lọc</span>
                        </a>
                    </div>
                @endif

            </main>

        </div>

    </div>
</section>

{{-- Mobile Filter Drawer JS --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.getElementById('filterToggleBtn');
    var closeBtn  = document.getElementById('filterCloseBtn');
    var overlay   = document.getElementById('filterOverlay');
    var panel     = document.getElementById('filterPanel');

    if (!toggleBtn || !panel) return;

    function openFilter() {
        panel.classList.add('is-open');
        overlay.classList.add('is-open');
        toggleBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeFilter() {
        panel.classList.remove('is-open');
        overlay.classList.remove('is-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        toggleBtn.focus();
    }

    toggleBtn.addEventListener('click', openFilter);

    if (closeBtn) {
        closeBtn.addEventListener('click', closeFilter);
    }

    if (overlay) {
        overlay.addEventListener('click', closeFilter);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && panel.classList.contains('is-open')) {
            closeFilter();
        }
    });
});
</script>

@endsection