@extends('layouts.app')


@section('title', 'Giỏ hàng - VELORA Eyes')


@section('content')

@php

    $cartHasUnavailableItems =
        $cart->items->contains(
            function ($item) {

                $variant = $item->variant;

                $product = $variant
                    ? $variant->product
                    : null;

                return
                    !$variant
                    || !$product
                    || !$product->is_active
                    || !$variant->is_active
                    || $variant->stock_quantity < $item->quantity;
            }
        );

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
            SHOPPING CART
        </span>

        <h1 style="margin-bottom:10px;">
            Giỏ hàng của bạn
        </h1>

        <p class="text-muted mb-0">

            Kiểm tra sản phẩm, số lượng
            và voucher trước khi thanh toán.

        </p>

    </div>

</section>


<section class="section">

    <div class="velora-container">


        @if($voucherError)

            <div class="alert alert-danger">
                {{ $voucherError }}
            </div>

        @endif



        @if($cart->items->isEmpty())

            <div class="cart-empty">

                <div class="cart-empty-icon">
                    ◯
                </div>

                <h2>
                    Giỏ hàng đang trống
                </h2>

                <p>
                    Bạn chưa thêm sản phẩm nào
                    vào giỏ hàng.
                </p>

                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-primary"
                >
                    Khám phá sản phẩm
                </a>

            </div>

        @else


            <div class="cart-page-grid">


                {{-- =========================================
                    LEFT - CART ITEMS
                ========================================== --}}

                <div>


                    <div class="cart-list-heading">
                        <label class="cart-select-all">

                        <input
                            type="checkbox"
                            id="selectAllCheckout"
                            checked
                            >

                            Chọn tất cả sản phẩm có thể thanh toán

                        </label>

                        <div>

                            <h2 class="mb-0">
                                Sản phẩm
                            </h2>

                            <span class="text-muted">

                                {{ $cart->total_quantity }}
                                sản phẩm trong giỏ

                            </span>

                        </div>


                        <form
                            action="{{ route('cart.clear') }}"
                            method="POST"
                            onsubmit="
                                return confirm(
                                    'Bạn có chắc muốn xóa toàn bộ giỏ hàng?'
                                );
                            "
                        >

                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-outline btn-sm"
                            >
                                Xóa toàn bộ
                            </button>

                        </form>

                    </div>



                    <div class="cart-items-list">

                        @foreach($cart->items as $item)

                            @php

                                $variant = $item->variant;

                                $product = $variant
                                    ? $variant->product
                                    : null;

                                $available =
                                    $variant
                                    && $product
                                    && $product->is_active
                                    && $variant->is_active
                                    && $variant->stock_quantity > 0;

                            @endphp
                            @php

                                $checkoutEligible =
                                $available
                                &&
                                $variant->stock_quantity
                                >= $item->quantity;

                            @endphp


                            <article class="cart-item-card">
                                <div class="cart-select-item">

                            <input
                                    type="checkbox"
                                    class="checkout-item-checkbox"
                                    name="selected_items[]"
                                    value="{{ $item->id }}"
                                    data-subtotal="{{ (float) $item->subtotal }}"
                                    form="checkout-selection-form"
                                    {{ $checkoutEligible
                                    ? 'checked'
                                    : 'disabled' }}
                                        >

                                </div>


                                {{-- IMAGE --}}

                                <div class="cart-item-image">

                                    @if(
                                        $product
                                        && $product->primaryImage
                                    )

                                        <img
                                            src="{{ asset(
                                                $product
                                                    ->primaryImage
                                                    ->image_path
                                            ) }}"
                                            alt="{{ $product->name }}"
                                        >

                                    @else

                                        <div class="cart-image-placeholder">
                                            VELORA
                                        </div>

                                    @endif

                                </div>



                                {{-- INFO --}}

                                <div class="cart-item-info">

                                    @if($product)

                                        <div class="product-category">
                                            {{ $product->category?->name
                                                ?? 'VELORA Eyes' }}
                                        </div>


                                        <h3>

                                            @if(
                                                $product->is_active
                                                && $product->isReadyForSale()
                                            )

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


                                        <div class="cart-variant-info">

                                            @if($variant)

                                                <span>
                                                    Màu:
                                                    <strong>
                                                        {{ $variant->color }}
                                                    </strong>
                                                </span>

                                                <span>
                                                    Size:
                                                    <strong>
                                                        {{ $variant->size }}
                                                    </strong>
                                                </span>

                                                <span>
                                                    SKU:
                                                    {{ $variant->sku }}
                                                </span>

                                            @endif

                                        </div>


                                        @if(!$available)

                                            <div
                                                class="alert alert-danger"
                                                style="
                                                    margin:12px 0 0;
                                                    padding:9px 12px;
                                                "
                                            >

                                                Sản phẩm hoặc phiên bản
                                                hiện không khả dụng.

                                            </div>

                                        @elseif(
                                            $variant->stock_quantity
                                            < $item->quantity
                                        )

                                            <div
                                                class="alert alert-warning"
                                                style="
                                                    margin:12px 0 0;
                                                    padding:9px 12px;
                                                "
                                            >

                                                Chỉ còn
                                                {{ $variant->stock_quantity }}
                                                sản phẩm trong kho.

                                            </div>

                                        @endif

                                    @else

                                        <h3>
                                            Sản phẩm không còn tồn tại
                                        </h3>

                                    @endif

                                </div>



                                {{-- PRICE / QUANTITY --}}

                                <div class="cart-item-controls">

                                    @if($variant)

                                        <div class="cart-unit-price">

                                            {{ number_format(
                                                (float) $variant->final_price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                                        </div>


                                        @if($available)

                                            <form
                                                action="{{ route(
                                                    'cart.update',
                                                    $variant
                                                ) }}"
                                                method="POST"
                                                class="cart-quantity-form"
                                            >

                                                @csrf
                                                @method('PATCH')


                                                <input
                                                    type="number"
                                                    name="quantity"
                                                    value="{{ $item->quantity }}"
                                                    min="1"
                                                    max="{{ $variant->stock_quantity }}"
                                                    required
                                                >


                                                <button
                                                    type="submit"
                                                    class="btn btn-outline btn-sm"
                                                >
                                                    Cập nhật
                                                </button>

                                            </form>


                                            <small class="text-muted">

                                                Còn
                                                {{ $variant->stock_quantity }}
                                                sản phẩm

                                            </small>

                                        @endif


                                        <div class="cart-item-subtotal">

                                            Thành tiền

                                            <strong>

                                                {{ number_format(
                                                    (float) $item->subtotal,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}đ

                                            </strong>

                                        </div>


                                        <form
                                            action="{{ route(
                                                'cart.destroy',
                                                $variant
                                            ) }}"
                                            method="POST"
                                            onsubmit="
                                                return confirm(
                                                    'Xóa sản phẩm này khỏi giỏ hàng?'
                                                );
                                            "
                                        >

                                            @csrf
                                            @method('DELETE')


                                            <button
                                                type="submit"
                                                class="cart-remove-button"
                                            >
                                                Xóa sản phẩm
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </article>

                        @endforeach

                    </div>

                </div>



                {{-- =========================================
                    RIGHT - ORDER SUMMARY
                ========================================== --}}

                <aside class="cart-summary-card">

                    <h2>
                        Tóm tắt đơn hàng
                    </h2>



                    {{-- VOUCHER --}}

                    <div class="cart-voucher">

                        <label class="form-label">
                            Mã giảm giá
                        </label>


                        @if($appliedVoucher)

                            <div class="applied-voucher">

                                <div>

                                    <span>
                                        Đã áp dụng
                                    </span>

                                    <strong>
                                        {{ $appliedVoucher->code }}
                                    </strong>

                                </div>


                                <form
                                    action="{{ route(
                                        'cart.voucher.remove'
                                    ) }}"
                                    method="POST"
                                >

                                    @csrf
                                    @method('DELETE')


                                    <button
                                        type="submit"
                                        class="cart-voucher-remove"
                                    >
                                        Bỏ mã
                                    </button>

                                </form>

                            </div>

                        @else

                            <form
                                action="{{ route(
                                    'cart.voucher.apply'
                                ) }}"
                                method="POST"
                                class="voucher-form"
                            >

                                @csrf


                                <input
                                    type="text"
                                    name="voucher_code"
                                    class="form-control"
                                    value="{{ old('voucher_code') }}"
                                    placeholder="Nhập mã voucher"
                                    required
                                >


                                <button
                                    type="submit"
                                    class="btn btn-outline"
                                >
                                    Áp dụng
                                </button>

                            </form>

                        @endif

                    </div>



                    {{-- PRICE --}}

                    <div class="cart-summary-lines">

                        <div>

                            <span>
                                Tạm tính
                            </span>

                            <strong>

                                {{ number_format(
                                    (float) $subtotal,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

                            </strong>

                        </div>


                        @if($appliedVoucher)

                            <div>

                                <span>
                                    Giảm giá
                                </span>

                                <strong
                                    style="
                                        color:var(--velora-success);
                                    "
                                >

                                    -
                                    {{ number_format(
                                        (float) $discountAmount,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </div>

                        @endif


                        <div>

                            <span>
                                Phí vận chuyển
                            </span>

                            <strong>
                                Tính tại checkout
                            </strong>

                        </div>

                    </div>



                    <div class="cart-total">

                        <span>
                            Tổng thanh toán
                        </span>

                        <strong>

                            {{ number_format(
                                (float) $finalAmount,
                                0,
                                ',',
                                '.'
                            ) }}đ

                        </strong>

                    </div>



                    <div class="cart-selected-summary">

    <div>
        Đã chọn

        <strong id="selectedCheckoutCount">
            0
        </strong>

        sản phẩm
    </div>


    <div>

        Tạm tính sản phẩm đã chọn:

        <strong id="selectedCheckoutSubtotal">
            0đ
        </strong>

    </div>


    <small class="text-muted">

        Voucher sẽ được kiểm tra lại
        theo các sản phẩm được chọn
        ở bước thanh toán.

    </small>

</div>


<form
    id="checkout-selection-form"
    action="{{ route('checkout.prepare') }}"
    method="POST"
>

    @csrf


    <button
        type="submit"
        id="checkoutSelectedButton"
        class="btn btn-primary cart-checkout-button"
    >
        Thanh toán sản phẩm đã chọn
    </button>

</form>


                    <a
                        href="{{ route('products.index') }}"
                        class="cart-continue-shopping"
                    >
                        ← Tiếp tục mua sắm
                    </a>

                </aside>

            </div>

        @endif

    </div>

</section>
@push('scripts')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const checkboxes = Array.from(
            document.querySelectorAll(
                '.checkout-item-checkbox:not(:disabled)'
            )
        );

        const selectAll =
            document.getElementById(
                'selectAllCheckout'
            );

        const countElement =
            document.getElementById(
                'selectedCheckoutCount'
            );

        const subtotalElement =
            document.getElementById(
                'selectedCheckoutSubtotal'
            );

        const checkoutButton =
            document.getElementById(
                'checkoutSelectedButton'
            );


        function formatMoney(value) {

            return new Intl.NumberFormat(
                'vi-VN'
            ).format(value) + 'đ';

        }


        function refreshCheckoutSummary() {

            const selected =
                checkboxes.filter(
                    checkbox =>
                        checkbox.checked
                );


            const subtotal =
                selected.reduce(
                    (total, checkbox) => {

                        return total
                            + Number(
                                checkbox.dataset.subtotal
                                || 0
                            );

                    },
                    0
                );


            countElement.textContent =
                selected.length;


            subtotalElement.textContent =
                formatMoney(subtotal);


            checkoutButton.disabled =
                selected.length === 0;


            if (selectAll) {

                selectAll.checked =
                    checkboxes.length > 0
                    &&
                    selected.length
                        === checkboxes.length;


                selectAll.indeterminate =
                    selected.length > 0
                    &&
                    selected.length
                        < checkboxes.length;
            }
        }


        if (selectAll) {

            selectAll.addEventListener(
                'change',
                function () {

                    checkboxes.forEach(
                        checkbox => {

                            checkbox.checked =
                                selectAll.checked;

                        }
                    );


                    refreshCheckoutSummary();

                }
            );

        }


        checkboxes.forEach(
            checkbox => {

                checkbox.addEventListener(
                    'change',
                    refreshCheckoutSummary
                );

            }
        );


        refreshCheckoutSummary();

    }
);

</script>

@endpush
@endsection