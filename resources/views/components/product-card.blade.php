<article class="product-card">

    <a
        href="{{ route('products.show', $product) }}"
        class="product-card-image adaptive-image-container"
        aria-label="{{ $product->name }}"
    >
        @if($product->primaryImage)
            <img
                src="{{ asset($product->primaryImage->image_path) }}"
                alt="{{ $product->name }}"
                loading="lazy"
                data-adaptive-image
            >
        @else
            <img
                src="{{ asset('images/no-image.png') }}"
                alt="{{ $product->name }}"
                loading="lazy"
                data-adaptive-image
            >
        @endif

        @if(
            $product->sale_price
            &&
            $product->sale_price < $product->price
        )
            @php
                $discountPercent = round(
                    (($product->price - $product->sale_price) / $product->price) * 100
                );
            @endphp
            <span class="product-discount-badge">
                -{{ $discountPercent }}%
            </span>
        @endif
    </a>

    <div class="product-card-body">
        <div class="product-card-meta-top">
            @if($product->category)
                <a
                    href="{{ route('categories.show', $product->category) }}"
                    class="product-category"
                >
                    {{ $product->category->name }}
                </a>
            @else
                <span class="product-category">VELORA EYES</span>
            @endif

            @if(method_exists($product, 'isReadyForSale') && $product->isReadyForSale())
                <span class="product-status-tag is-in-stock" title="Còn hàng">
                    <span class="status-dot" aria-hidden="true"></span>
                    <span>Sẵn sàng</span>
                </span>
            @else
                <span class="product-status-tag is-contact" title="Liên hệ tư vấn">
                    <span class="status-dot" aria-hidden="true"></span>
                    <span>Liên hệ</span>
                </span>
            @endif
        </div>

        <h3 class="product-title">
            <a href="{{ route('products.show', $product) }}" title="{{ $product->name }}">
                {{ $product->name }}
            </a>
        </h3>

        <div class="product-price-row">
            @if($product->sale_price && $product->sale_price < $product->price)
                <span class="product-price">
                    {{ number_format((float) $product->sale_price, 0, ',', '.') }}đ
                </span>
                <span class="product-old-price">
                    {{ number_format((float) $product->price, 0, ',', '.') }}đ
                </span>
            @else
                <span class="product-price">
                    {{ number_format((float) $product->price, 0, ',', '.') }}đ
                </span>
            @endif
        </div>

        <div class="product-card-footer">
            <a
                href="{{ route('products.show', $product) }}"
                class="product-card-cta"
                aria-label="Xem chi tiết {{ $product->name }}"
            >
                <span>Xem chi tiết</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>

</article>