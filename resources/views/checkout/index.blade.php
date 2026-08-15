@extends('layouts.app')


@section('title', 'Thanh toán - VELORA Eyes')


@section('content')

<section class="checkout-hero">

    <div class="velora-container">

        <span class="hero-kicker">
            SECURE CHECKOUT
        </span>

        <h1>
            Thanh toán
        </h1>

        <p class="text-muted mb-0">
            Kiểm tra thông tin nhận hàng và
            hoàn tất đơn hàng của bạn.
        </p>

    </div>

</section>


<section class="section">

    <div class="velora-container">

        <form
            action="{{ route('checkout.store') }}"
            method="POST"
            id="checkoutForm"
        >

            @csrf


            <div class="checkout-layout">


                {{-- =============================================
                    LEFT
                ============================================== --}}

                <div class="checkout-main">


                    {{-- ADDRESS --}}

                    <div class="checkout-card">

                        <div class="checkout-card-heading">

                            <div class="checkout-step">
                                1
                            </div>

                            <div>

                                <h2>
                                    Địa chỉ nhận hàng
                                </h2>

                                <p>
                                    Chọn địa chỉ bạn muốn
                                    nhận đơn hàng.
                                </p>

                            </div>

                        </div>


                        <div class="checkout-address-list">

                            @foreach($addresses as $address)

                                <label class="checkout-address">

                                    <input
                                        type="radio"
                                        name="address_id"
                                        value="{{ $address->id }}"
                                        {{
                                            (string) old(
                                                'address_id',
                                                $addresses
                                                    ->firstWhere(
                                                        'is_default',
                                                        true
                                                    )
                                                    ?->id
                                                ?? $addresses->first()?->id
                                            )
                                            === (string) $address->id
                                                ? 'checked'
                                                : ''
                                        }}
                                        required
                                    >


                                    <div>

                                        <div class="checkout-address-title">

                                            <strong>
                                                {{ $address->recipient_name }}
                                            </strong>


                                            @if($address->is_default)

                                                <span class="badge badge-success">
                                                    Mặc định
                                                </span>

                                            @endif


                                            @if($address->label)

                                                <span class="badge">
                                                    {{ $address->label }}
                                                </span>

                                            @endif

                                        </div>


                                        <p class="mb-1">
                                            {{ $address->phone }}
                                        </p>


                                        <p class="text-muted mb-0">

                                            {{ $address->detail_address }},
                                            {{ $address->ward }},
                                            {{ $address->district }},
                                            {{ $address->province }}

                                        </p>

                                    </div>

                                </label>

                            @endforeach

                        </div>


                        <div class="checkout-address-actions">

                            <a
                                href="{{ route('addresses.create') }}"
                                class="btn btn-outline btn-sm"
                            >
                                + Thêm địa chỉ mới
                            </a>


                            <a
                                href="{{ route('addresses.index') }}"
                                class="btn btn-outline btn-sm"
                            >
                                Quản lý địa chỉ
                            </a>

                        </div>

                    </div>



                    {{-- PRODUCTS --}}

                    <div class="checkout-card">

                        <div class="checkout-card-heading">

                            <div class="checkout-step">
                                2
                            </div>

                            <div>

                                <h2>
                                    Sản phẩm thanh toán
                                </h2>

                                <p>
                                    Chỉ những sản phẩm bạn
                                    đã chọn từ giỏ hàng.
                                </p>

                            </div>

                        </div>


                        <div class="checkout-product-list">

                            @foreach($cart->items as $item)

                                @php

                                    $variant =
                                        $item->variant;

                                    $product =
                                        $variant
                                            ? $variant->product
                                            : null;

                                @endphp


                                <div class="checkout-product">

                                    <div class="checkout-product-image">

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

                                            <span>
                                                VELORA
                                            </span>

                                        @endif

                                    </div>


                                    <div class="checkout-product-info">

                                        <strong>
                                            {{ $product?->name
                                                ?? 'Sản phẩm' }}
                                        </strong>


                                        @if($variant)

                                            <span>
                                                {{ $variant->color }}
                                                /
                                                {{ $variant->size }}
                                            </span>


                                            <span>
                                                SKU:
                                                {{ $variant->sku }}
                                            </span>

                                        @endif

                                    </div>


                                    <div class="checkout-product-quantity">

                                        × {{ $item->quantity }}

                                    </div>


                                    <div class="checkout-product-price">

                                        {{ number_format(
                                            (float) $item->subtotal,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                                    </div>

                                </div>

                            @endforeach

                        </div>


                        <a
                            href="{{ route('cart.index') }}"
                            class="checkout-edit-cart"
                        >
                            ← Thay đổi sản phẩm đã chọn
                        </a>

                    </div>



                    {{-- PAYMENT METHOD --}}

                    <div class="checkout-card">

                        <div class="checkout-card-heading">

                            <div class="checkout-step">
                                3
                            </div>

                            <div>

                                <h2>
                                    Phương thức thanh toán
                                </h2>

                                <p>
                                    Chọn cách bạn muốn
                                    thanh toán đơn hàng.
                                </p>

                            </div>

                        </div>


                        <div class="payment-method-grid">


                            {{-- COD --}}

                            <label class="payment-method-card">

                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="cod"
                                    {{
                                        old(
                                            'payment_method',
                                            'cod'
                                        ) === 'cod'
                                            ? 'checked'
                                            : ''
                                    }}
                                >

                                <div class="payment-method-icon">
                                    COD
                                </div>


                                <div>

                                    <strong>
                                        Thanh toán khi nhận hàng
                                    </strong>

                                    <span>
                                        Thanh toán trực tiếp
                                        khi đơn hàng được giao.
                                    </span>

                                </div>

                            </label>



                            {{-- QR --}}

                            <label class="payment-method-card">

                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="qr"
                                    {{
                                        old('payment_method')
                                        === 'qr'
                                            ? 'checked'
                                            : ''
                                    }}
                                >

                                <div class="payment-method-icon">
                                    QR
                                </div>


                                <div>

                                    <strong>
                                        Chuyển khoản QR
                                    </strong>

                                    <span>
                                        Thanh toán bằng mã QR
                                        mô phỏng của VELORA.
                                    </span>

                                </div>

                            </label>



                            {{-- VNPAY --}}

                            <label class="payment-method-card">

                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="vnpay"
                                    {{
                                        old('payment_method')
                                        === 'vnpay'
                                            ? 'checked'
                                            : ''
                                    }}
                                >

                                <div class="payment-method-icon">
                                    VNP
                                </div>


                                <div>

                                    <strong>
                                        VNPay
                                    </strong>

                                    <span>
                                        Thanh toán qua
                                        cổng VNPay mô phỏng.
                                    </span>

                                </div>

                            </label>

                        </div>

                    </div>



                    {{-- NOTE --}}

                    <div class="checkout-card">

                        <div class="checkout-card-heading">

                            <div class="checkout-step">
                                4
                            </div>

                            <div>

                                <h2>
                                    Ghi chú
                                </h2>

                                <p>
                                    Thông tin thêm cho đơn hàng.
                                </p>

                            </div>

                        </div>


                        <textarea
                            name="note"
                            class="form-control"
                            rows="4"
                            maxlength="500"
                            placeholder="Ví dụ: Giao hàng giờ hành chính..."
                        >{{ old('note') }}</textarea>

                    </div>

                </div>



                {{-- =============================================
                    ORDER SUMMARY
                ============================================== --}}

                <aside class="checkout-summary">

                    <h2>
                        Tóm tắt thanh toán
                    </h2>


                    <div class="checkout-summary-row">

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

                        <div class="checkout-voucher-box">

                            <div>

                                <span>
                                    Voucher
                                </span>

                                <strong>
                                    {{ $appliedVoucher->code }}
                                </strong>

                            </div>


                            <span class="badge badge-success">
                                Đã áp dụng
                            </span>

                        </div>


                        <div class="checkout-summary-row">

                            <span>
                                Giảm giá
                            </span>

                            <strong class="checkout-discount">

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


                    <div class="checkout-summary-row">

                        <span>
                            Phí vận chuyển
                        </span>

                        <strong>

                            @if((float) $shippingFee === 0.0)

                                Miễn phí

                            @else

                                {{ number_format(
                                    (float) $shippingFee,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

                            @endif

                        </strong>

                    </div>


                    <div class="checkout-summary-total">

                        <span>
                            Tổng thanh toán
                        </span>

                        <strong>

                            {{ number_format(
                                (float) $total,
                                0,
                                ',',
                                '.'
                            ) }}đ

                        </strong>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary checkout-submit"
                        onclick="
                            return confirm(
                                'Bạn xác nhận đặt đơn hàng này?'
                            );
                        "
                    >
                        Đặt hàng
                    </button>


                    <p class="checkout-security-note">

                        Bằng việc đặt hàng,
                        bạn xác nhận thông tin
                        trên là chính xác.

                    </p>


                    <a
                        href="{{ route('cart.index') }}"
                        class="checkout-back-cart"
                    >
                        ← Quay lại giỏ hàng
                    </a>

                </aside>

            </div>

        </form>

    </div>

</section>

@endsection