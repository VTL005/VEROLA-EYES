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

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/velora-products.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product-review-form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product-highlights.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product-detail-editorial.css') }}">
@endpush

@section('content')

@php
    $primaryProductImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
@endphp

{{-- =========================================================
    BREADCRUMB
========================================================= --}}
<section class="product-breadcrumb-section">
    <div class="vl-container">
        <nav class="product-breadcrumb" aria-label="Đường dẫn trang">
            <a href="{{ route('home') }}">Trang chủ</a>
            <span class="breadcrumb-sep" aria-hidden="true">/</span>
            <a href="{{ route('products.index') }}">Sản phẩm</a>
            @if($product->category)
                <span class="breadcrumb-sep" aria-hidden="true">/</span>
                <a href="{{ route('categories.show', $product->category) }}">{{ $product->category->name }}</a>
            @endif
            <span class="breadcrumb-sep" aria-hidden="true">/</span>
            <span class="breadcrumb-current">{{ $product->name }}</span>
        </nav>
    </div>
</section>

{{-- =========================================================
    PRODUCT SHOWCASE (LIGHT GALLERY + DEEP NAVY PURCHASE PANEL)
========================================================= --}}
<section class="product-detail-section">
    <div class="vl-container">
        <div class="product-detail-grid">

            {{-- =================================================
                LEFT: LIGHT GALLERY PANEL (Warm Alabaster / Optical Glow)
            ================================================== --}}
            <div class="product-gallery">
                <div class="product-main-image adaptive-image-container">
                    @if($primaryProductImage)
                        <img
                            id="mainProductImage"
                            src="{{ asset($primaryProductImage->image_path) }}"
                            alt="{{ $primaryProductImage->alt_text ?? $product->name }}"
                            data-adaptive-image
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
                                aria-label="Xem ảnh {{ $loop->iteration }}"
                                onclick="
                                    const mainImg = document.getElementById('mainProductImage');
                                    mainImg.src='{{ asset($image->image_path) }}';
                                    document.querySelectorAll('.product-thumbnail').forEach(item => item.classList.remove('active'));
                                    this.classList.add('active');
                                    if(window.VeloraAdaptive) {
                                        if (mainImg.complete) {
                                            window.VeloraAdaptive.updateImage(mainImg);
                                        } else {
                                            mainImg.addEventListener('load', () => window.VeloraAdaptive.updateImage(mainImg), {once: true});
                                        }
                                    }
                                "
                            >
                                <img
                                    src="{{ asset($image->image_path) }}"
                                    alt="{{ $image->alt_text ?? $product->name }}"
                                    loading="lazy"
                                >
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- =================================================
                RIGHT: DEEP/MIDNIGHT NAVY PURCHASE PANEL
            ================================================== --}}
            <div class="product-detail-info">

                @if($product->category)
                    <a href="{{ route('categories.show', $product->category) }}" class="product-detail-category">
                        {{ $product->category->name }}
                    </a>
                @else
                    <span class="product-detail-category">VELORA COLLECTION</span>
                @endif

                <h1 class="product-detail-name">
                    {{ $product->name }}
                </h1>

                <div class="product-detail-meta">
                    <span class="product-meta-sku">
                        SKU: <strong>{{ $product->sku }}</strong>
                    </span>

                    <span class="product-meta-divider" aria-hidden="true">·</span>

                    @if($reviewCount > 0)
                        <span class="product-meta-rating">
                            <span class="star-icon" aria-hidden="true">★</span>
                            <strong>{{ number_format((float) $averageRating, 1) }}</strong>
                            <span class="product-meta-review-count">({{ $reviewCount }} đánh giá)</span>
                        </span>
                    @else
                        <span class="product-meta-rating text-muted">
                            Chưa có đánh giá
                        </span>
                    @endif
                </div>

                {{-- PRICE ROW --}}
                <div class="product-detail-price">
                    @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="detail-current-price">
                            {{ number_format((float) $product->sale_price, 0, ',', '.') }}đ
                        </span>
                        <span class="detail-old-price">
                            {{ number_format((float) $product->price, 0, ',', '.') }}đ
                        </span>
                        @php
                            $detailDiscount = round((($product->price - $product->sale_price) / $product->price) * 100);
                        @endphp
                        <span class="badge badge-sale">
                            -{{ $detailDiscount }}%
                        </span>
                    @else
                        <span class="detail-current-price">
                            {{ number_format((float) $product->price, 0, ',', '.') }}đ
                        </span>
                    @endif
                </div>

                {{-- SHORT DESCRIPTION --}}
                @if($product->description)
                    <p class="product-detail-description">
                        {{ \Illuminate\Support\Str::limit($product->description, 180) }}
                    </p>
                @endif

                {{-- SPECS MATRIX (2-COLUMN COMPACT ON NAVY) --}}
                @if($product->shape || $product->material || $product->gender || $product->dimensions)
                    <div class="product-specs-matrix">
                        @if($product->shape)
                            <div class="spec-cell">
                                <span class="spec-label">Kiểu dáng</span>
                                <span class="spec-value">{{ $product->shape }}</span>
                            </div>
                        @endif

                        @if($product->material)
                            <div class="spec-cell">
                                <span class="spec-label">Chất liệu</span>
                                <span class="spec-value">{{ $product->material }}</span>
                            </div>
                        @endif

                        @if($product->gender)
                            <div class="spec-cell">
                                <span class="spec-label">Đối tượng</span>
                                <span class="spec-value">
                                    @switch($product->gender)
                                        @case('male') Nam @break
                                        @case('female') Nữ @break
                                        @case('unisex') Unisex @break
                                        @default {{ $product->gender }}
                                    @endswitch
                                </span>
                            </div>
                        @endif

                        @if($product->dimensions)
                            <div class="spec-cell">
                                <span class="spec-label">Kích thước</span>
                                <span class="spec-value">{{ $product->dimensions }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- PURCHASE ACTIONS --}}
                <div class="product-purchase-box">
                    @auth
                        @if(auth()->user()->isCustomer())
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf

                                <div class="purchase-form-row">
                                    <div class="form-group purchase-variant-group">
                                        <label for="variant_id" class="form-label">
                                            Chọn phiên bản (Màu / Kích cỡ)
                                        </label>
                                        <select id="variant_id" name="variant_id" class="form-control purchase-select" required>
                                            <option value="">-- Chọn phiên bản gọng / tròng --</option>
                                            @foreach($product->variants as $variant)
                                                <option
                                                    value="{{ $variant->id }}"
                                                    data-price="{{ $variant->final_price }}"
                                                    {{ old('variant_id') == $variant->id ? 'selected' : '' }}
                                                    {{ $variant->stock_quantity <= 0 ? 'disabled' : '' }}
                                                >
                                                    {{ $variant->color }} / {{ $variant->size }}
                                                    — {{ number_format((float) $variant->final_price, 0, ',', '.') }}đ
                                                    — {{ $variant->stock_quantity > 0 ? 'Còn ' . $variant->stock_quantity : 'Hết hàng' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group purchase-qty-group">
                                        <label for="quantity" class="form-label">Số lượng</label>
                                        <input
                                            type="number"
                                            id="quantity"
                                            name="quantity"
                                            class="form-control purchase-qty-input"
                                            value="{{ old('quantity', 1) }}"
                                            min="1"
                                            max="99"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="product-purchase-actions">
                                    <button
                                        type="submit"
                                        name="checkout_action"
                                        value="add_to_cart"
                                        class="btn product-add-cart-button"
                                    >
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                                        <span>Thêm vào giỏ hàng</span>
                                    </button>

                                    <button
                                        type="submit"
                                        name="checkout_action"
                                        value="buy_now"
                                        class="btn product-buy-now-button"
                                    >
                                        <span>Mua ngay</span>
                                    </button>
                                </div>
                            </form>

                            {{-- WISHLIST ACTION --}}
                            <div class="product-wishlist-action-wrap">
                                @if($isWishlisted)
                                    <form action="{{ route('wishlist.destroy', $product) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="product-wishlist-btn is-active">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="#F43F5E" stroke="#F43F5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                            <span>Đã lưu vào yêu thích (Nhấn để bỏ)</span>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('wishlist.store', $product) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="product-wishlist-btn">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                                            <span>Lưu vào danh sách yêu thích</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @else
                            <div class="product-login-notice">
                                <p>Tài khoản Quản trị không hỗ trợ tạo đơn hàng bán lẻ.</p>
                            </div>
                        @endif
                    @else
                        <div class="product-login-notice">
                            <p>Đăng nhập để chọn phiên bản gọng kính và tiến hành đặt hàng trực tuyến.</p>
                            <a href="{{ route('login') }}" class="btn product-login-btn">
                                Đăng nhập tài khoản
                            </a>
                        </div>
                    @endauth
                </div>

            </div>

        </div>
    </div>
</section>

{{-- =========================================================
    PRODUCT DETAILS & SPECIFICATIONS (EDITORIAL SPLIT)
========================================================= --}}
<section class="product-story-section">
    <div class="vl-container">
        <div class="product-story-grid">

            <div class="product-story-main">
                <span class="section-kicker">HAUTE OPTIQUE</span>
                <h2 class="section-title">Thông tin chi tiết sản phẩm</h2>

                @if($product->description)
                    <div class="product-long-description">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @else
                    <p class="text-muted">Sản phẩm chưa có mô tả chi tiết.</p>
                @endif

                @php
                    $editorialImage = null;
                    if (isset($product->images) && $product->images->count() > 0) {
                        $editorialImage = $product->images->where('id', '!=', $primaryProductImage?->id)->first();
                        if (!$editorialImage) {
                            $editorialImage = $primaryProductImage;
                        }
                    }
                @endphp

                @if($editorialImage)
                    <div class="velora-product-editorial">
                        <div class="editorial-image-wrapper">
                            <img
                                src="{{ asset($editorialImage->image_path) }}"
                                alt="{{ $editorialImage->alt_text ?? 'Chi tiết hoàn thiện' }}"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>
                        <div class="editorial-overlay">
                            <span class="editorial-kicker">NGẮM CHI TIẾT</span>
                            <h4 class="editorial-caption">{{ $product->name }}</h4>
                        </div>
                    </div>
                @endif
            </div>

            <div class="product-specs-sidebar">
                <div class="specs-card-inner">
                    <span class="section-kicker">SPECIFICATIONS</span>
                    <h3 class="specs-card-title">Bảng thông số kỹ thuật</h3>
                    <dl class="specs-def-list">
                        <div class="specs-def-row">
                            <dt>Mã SKU</dt>
                            <dd>{{ $product->sku }}</dd>
                        </div>
                        <div class="specs-def-row">
                            <dt>Danh mục</dt>
                            <dd>{{ $product->category?->name ?? '-' }}</dd>
                        </div>
                        <div class="specs-def-row">
                            <dt>Kiểu dáng</dt>
                            <dd>{{ $product->shape ?? '-' }}</dd>
                        </div>
                        <div class="specs-def-row">
                            <dt>Chất liệu</dt>
                            <dd>{{ $product->material ?? '-' }}</dd>
                        </div>
                        <div class="specs-def-row">
                            <dt>Đối tượng</dt>
                            <dd>
                                @switch($product->gender)
                                    @case('male') Nam @break
                                    @case('female') Nữ @break
                                    @case('unisex') Unisex @break
                                    @default {{ $product->gender ?? '-' }}
                                @endswitch
                            </dd>
                        </div>
                        <div class="specs-def-row">
                            <dt>Kích thước</dt>
                            <dd>{{ $product->dimensions ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>

        {{-- DESIGN HIGHLIGHTS --}}
        @if($product->highlights)
            <div class="velora-product-highlights">
                <div class="vph-header">
                    <span class="vph-kicker">DESIGN HIGHLIGHTS</span>
                    <h3 class="vph-title">Điểm nổi bật trong thiết kế</h3>
                </div>
                <div class="vph-content-box">
                    {!! nl2br(e($product->highlights)) !!}
                </div>
            </div>
        @endif

        {{-- VARIANTS TABLE (HIDDEN TO AVOID VISUAL COMPETITION WITH DROPDOWN) --}}
        <div class="product-variants-card d-none" style="display: none;">
            <div class="variants-card-header">
                <span class="section-kicker">AVAILABLE VARIANTS</span>
                <h3 class="variants-card-title">Các phiên bản có sẵn</h3>
                <p class="variants-card-desc">Danh sách chi tiết màu sắc gọng, kích cỡ, mức giá và trạng thái tồn kho thực tế.</p>
            </div>

            @if($product->variants->count() > 0)
                <div class="variants-table-wrapper">
                    <table class="product-variants-table">
                        <thead>
                            <tr>
                                <th scope="col" class="th-color">Màu sắc</th>
                                <th scope="col" class="th-size">Kích cỡ</th>
                                <th scope="col" class="th-price" style="text-align:right;">Giá bán</th>
                                <th scope="col" class="th-stock" style="text-align:center;">Tình trạng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variants as $variant)
                                <tr>
                                    <td class="td-color">
                                        <strong class="variant-color-name">{{ $variant->color }}</strong>
                                    </td>
                                    <td class="td-size">{{ $variant->size }}</td>
                                    <td class="td-price" style="text-align:right;">
                                        <span class="variant-price-regular">
                                            {{ number_format((float) $variant->final_price, 0, ',', '.') }}đ
                                        </span>
                                    </td>
                                    <td class="td-stock" style="text-align:center;">
                                        @if($variant->stock_quantity > 0)
                                            <span class="variant-status-badge in-stock">
                                                <span class="status-dot" aria-hidden="true"></span>
                                                <span>Còn {{ $variant->stock_quantity }}</span>
                                            </span>
                                        @else
                                            <span class="variant-status-badge out-stock">
                                                <span class="status-dot" aria-hidden="true"></span>
                                                <span>Tạm hết</span>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Chưa có thông tin phiên bản cho sản phẩm này.</p>
            @endif
        </div>

    </div>
</section>

{{-- =========================================================
    REVIEWS SECTION
========================================================= --}}
<section class="section" style="background:var(--vl-soft-stone, #F4F7F9);">
    <div class="vl-container">
        <div class="reviews-section-header">
            <div>
                <span class="hero-kicker">CUSTOMER FEEDBACK</span>
                <h2>Đánh giá từ khách hàng</h2>
            </div>
            @if($reviewCount > 0)
                <div class="review-score-summary">
                    <span class="review-score-number">{{ number_format((float) $averageRating, 1) }}</span>
                    <span class="review-score-stars">★★★★★</span>
                    <span class="review-score-count">{{ $reviewCount }} lượt đánh giá</span>
                </div>
            @endif
        </div>

        {{-- REVIEW FORM --}}
        @auth
            @if(auth()->user()->isCustomer() && Route::has('reviews.store'))
                <div class="review-form-card">
                    <h3>Viết nhận xét của bạn</h3>
                    <p class="text-muted">Hệ thống ghi nhận đánh giá từ tài khoản đã trải nghiệm sản phẩm.</p>

                    <form class="product-review-form js-product-review-form" action="{{ route('reviews.store', $product) }}" method="POST">
                        @csrf
                        <fieldset class="product-review-rating">
                            <legend class="form-label" id="review-rating-label">Bạn cảm thấy sản phẩm thế nào?</legend>
                            <div class="product-review-stars" role="radiogroup" aria-labelledby="review-rating-label">
                                @for($rating = 5; $rating >= 1; $rating--)
                                    <input
                                        class="product-review-star-input"
                                        type="radio"
                                        name="rating"
                                        id="review-rating-{{ $rating }}"
                                        value="{{ $rating }}"
                                        {{ (int) old('rating') === $rating ? 'checked' : '' }}
                                        required
                                    >
                                    <label
                                        class="product-review-star"
                                        for="review-rating-{{ $rating }}"
                                        title="{{ $rating }} sao"
                                        aria-label="{{ $rating }} sao"
                                    >
                                        <span aria-hidden="true">★</span>
                                    </label>
                                @endfor
                            </div>
                            <p class="product-review-rating-text" id="reviewRatingText" aria-live="polite">
                                Chạm vào một ngôi sao để đánh giá
                            </p>
                            @error('rating')
                                <p class="product-review-error">{{ $message }}</p>
                            @enderror
                        </fieldset>

                        <div class="form-group product-review-comment-group">
                            <div class="product-review-comment-heading">
                                <label for="reviewComment" class="form-label">Nhận xét</label>
                                <span class="product-review-counter" id="reviewCommentCount">0/500</span>
                            </div>
                            <textarea
                                id="reviewComment"
                                name="comment"
                                class="form-control product-review-comment"
                                maxlength="500"
                                rows="4"
                                placeholder="Chia sẻ trải nghiệm của bạn về chất lượng gọng kính, độ thoải mái..."
                            >{{ old('comment') }}</textarea>
                            @error('comment')
                                <p class="product-review-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary product-review-submit">
                            Gửi đánh giá
                        </button>
                    </form>
                </div>
            @endif
        @endauth

        {{-- REVIEWS LIST --}}
        @if($product->reviews && $product->reviews->count() > 0)
            <div class="reviews-list">
                @foreach($product->reviews as $review)
                    <article class="review-card">
                        <div class="review-card-header">
                            <div>
                                <strong class="review-author">{{ $review->user?->name ?? 'Khách hàng' }}</strong>
                                <span class="review-date">{{ $review->created_at?->format('d/m/Y') }}</span>
                            </div>
                            <div class="review-card-rating" aria-label="{{ $review->rating }} sao">
                                @for($i = 1; $i <= 5; $i++)
                                    <span style="color: {{ $i <= $review->rating ? '#f59e0b' : '#cbd5e1' }};">★</span>
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="review-card-body">{{ $review->comment }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <div class="reviews-empty">
                <p>Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên chia sẻ cảm nhận!</p>
            </div>
        @endif
    </div>
</section>

{{-- =========================================================
    RECOMMENDED PRODUCTS
========================================================= --}}
@if($recommendedProducts && $recommendedProducts->count() > 0)
<section class="section section-white">
    <div class="vl-container">
        <div class="section-header-row">
            <div>
                <span class="hero-kicker">RECOMMENDED FOR YOU</span>
                <h2>Có thể bạn cũng thích</h2>
                <p class="text-muted mb-0">Các mẫu kính được tuyển chọn gợi ý dựa trên sở thích của bạn.</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline">
                Xem tất cả
            </a>
        </div>

        <div class="product-list-grid">
            @foreach($recommendedProducts as $recommended)
                @include('components.product-card', ['product' => $recommended])
            @endforeach
        </div>
    </div>
</section>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ----------------------------------------------------
    // Update main price when selecting variant
    // ----------------------------------------------------
    var variantSelect = document.getElementById('variant_id');
    var currentPriceDisplay = document.querySelector('.detail-current-price');

    if (variantSelect && currentPriceDisplay) {
        variantSelect.addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var price = selectedOption.getAttribute('data-price');

            if (price) {
                // Format price to display (e.g. 690.000đ)
                var formattedPrice = parseFloat(price).toLocaleString('vi-VN') + 'đ';
                currentPriceDisplay.textContent = formattedPrice;
            }
        });
    }

    var reviewForm = document.querySelector('.js-product-review-form');
    if (!reviewForm) return;

    var ratingLabels = {
        1: 'Không hài lòng',
        2: 'Chưa tốt',
        3: 'Bình thường',
        4: 'Tốt',
        5: 'Rất tốt'
    };

    var ratingInputs = reviewForm.querySelectorAll('input[name="rating"]');
    var ratingText = reviewForm.querySelector('#reviewRatingText');
    var comment = reviewForm.querySelector('#reviewComment');
    var commentCount = reviewForm.querySelector('#reviewCommentCount');

    function updateRatingText() {
        var selectedRating = reviewForm.querySelector('input[name="rating"]:checked');
        if (ratingText) {
            ratingText.textContent = selectedRating
                ? ratingLabels[selectedRating.value] + ' · ' + selectedRating.value + '/5 sao'
                : 'Chạm vào một ngôi sao để đánh giá';
        }
    }

    function updateCommentCount() {
        if (comment && commentCount) {
            commentCount.textContent = comment.value.length + '/500';
        }
    }

    ratingInputs.forEach(function(input) {
        input.addEventListener('change', updateRatingText);
    });

    if (comment) {
        comment.addEventListener('input', updateCommentCount);
    }

    updateRatingText();
    updateCommentCount();
});
</script>

@endsection
