@extends('layouts.app')


@section('title', 'Đơn hàng của tôi - VELORA Eyes')


@section('content')


{{-- =========================================================
    HERO
========================================================= --}}

<section class="orders-hero">

    <div class="velora-container">

        <span class="hero-kicker">
            MY ORDERS
        </span>

        <h1>
            Đơn hàng của tôi
        </h1>

        <p class="text-muted mb-0">
            Theo dõi trạng thái và lịch sử
            mua hàng của bạn tại VELORA Eyes.
        </p>

    </div>

</section>



<section class="section">

    <div class="velora-container">


        {{-- =================================================
            FILTER
        ================================================== --}}

        <div class="order-filter-tabs">

            <a
                href="{{ route('orders.index') }}"
                class="order-filter-tab {{ !$status ? 'active' : '' }}"
            >
                Tất cả
            </a>


            <a
                href="{{ route(
                    'orders.index',
                    ['status' => 'pending']
                ) }}"
                class="order-filter-tab {{ $status === 'pending' ? 'active' : '' }}"
            >
                Chờ xác nhận
            </a>


            <a
                href="{{ route(
                    'orders.index',
                    ['status' => 'confirmed']
                ) }}"
                class="order-filter-tab {{ $status === 'confirmed' ? 'active' : '' }}"
            >
                Đã xác nhận
            </a>


            <a
                href="{{ route(
                    'orders.index',
                    ['status' => 'preparing']
                ) }}"
                class="order-filter-tab {{ $status === 'preparing' ? 'active' : '' }}"
            >
                Đang chuẩn bị
            </a>


            <a
                href="{{ route(
                    'orders.index',
                    ['status' => 'packed']
                ) }}"
                class="order-filter-tab {{ $status === 'packed' ? 'active' : '' }}"
            >
                Đã đóng gói
            </a>


            <a
                href="{{ route(
                    'orders.index',
                    ['status' => 'shipping']
                ) }}"
                class="order-filter-tab {{ $status === 'shipping' ? 'active' : '' }}"
            >
                Đang giao
            </a>


            <a
                href="{{ route(
                    'orders.index',
                    ['status' => 'completed']
                ) }}"
                class="order-filter-tab {{ $status === 'completed' ? 'active' : '' }}"
            >
                Hoàn thành
            </a>


            <a
                href="{{ route(
                    'orders.index',
                    ['status' => 'cancelled']
                ) }}"
                class="order-filter-tab {{ $status === 'cancelled' ? 'active' : '' }}"
            >
                Đã hủy
            </a>

        </div>



        {{-- =================================================
            EMPTY
        ================================================== --}}

        @if($orders->isEmpty())

            <div class="orders-empty">

                <div class="orders-empty-icon">
                    □
                </div>


                @if($status)

                    <h2>
                        Không có đơn hàng ở trạng thái này
                    </h2>

                    <p>
                        Hãy thử xem tất cả đơn hàng
                        của bạn.
                    </p>

                    <a
                        href="{{ route('orders.index') }}"
                        class="btn btn-outline"
                    >
                        Xem tất cả đơn hàng
                    </a>

                @else

                    <h2>
                        Bạn chưa có đơn hàng nào
                    </h2>

                    <p>
                        Khám phá các mẫu kính của VELORA
                        và bắt đầu đơn hàng đầu tiên.
                    </p>

                    <a
                        href="{{ route('products.index') }}"
                        class="btn btn-primary"
                    >
                        Mua sắm ngay
                    </a>

                @endif

            </div>

        @else


            {{-- =================================================
                ORDERS
            ================================================== --}}

            <div class="customer-order-list">

                @foreach($orders as $order)

                    @php

                        $firstDetail =
                            $order->details->first();

                    @endphp


                    <article class="customer-order-card">


                        {{-- HEADER --}}

                        <div class="customer-order-header">

                            <div>

                                <span class="customer-order-label">
                                    Mã đơn hàng
                                </span>

                                <strong class="customer-order-code">
                                    {{ $order->order_code }}
                                </strong>

                                <span class="customer-order-date">

                                    {{ $order
                                        ->created_at
                                        ->format('d/m/Y H:i') }}

                                </span>

                            </div>


                            <div>

                                @switch($order->order_status)

                                    @case('pending')

                                        <span class="order-status status-pending">
                                            Chờ xác nhận
                                        </span>

                                        @break


                                    @case('confirmed')

                                        <span class="order-status status-confirmed">
                                            Đã xác nhận
                                        </span>

                                        @break


                                    @case('preparing')

                                        <span class="order-status status-preparing">
                                            Đang chuẩn bị
                                        </span>

                                        @break


                                    @case('packed')

                                        <span class="order-status status-packed">
                                            Đã đóng gói
                                        </span>

                                        @break


                                    @case('shipping')

                                        <span class="order-status status-shipping">
                                            Đang giao hàng
                                        </span>

                                        @break


                                    @case('completed')

                                        <span class="order-status status-completed">
                                            Hoàn thành
                                        </span>

                                        @break


                                    @case('cancelled')

                                        <span class="order-status status-cancelled">
                                            Đã hủy
                                        </span>

                                        @break

                                @endswitch

                            </div>

                        </div>



                        {{-- PRODUCT PREVIEW --}}

                        <div class="customer-order-body">

                            <div class="customer-order-product">

                                @if($firstDetail)

                                    <div class="order-product-placeholder">
                                        VELORA
                                    </div>


                                    <div>

                                        <strong>
                                            {{ $firstDetail->product_name }}
                                        </strong>


                                        <span>

                                            {{ $firstDetail->color ?? '-' }}

                                            /

                                            {{ $firstDetail->size ?? '-' }}

                                            ×
                                            {{ $firstDetail->quantity }}

                                        </span>


                                        @if($order->details->count() > 1)

                                            <small>

                                                + {{ $order->details->count() - 1 }}
                                                sản phẩm khác

                                            </small>

                                        @endif

                                    </div>

                                @endif

                            </div>



                            {{-- PAYMENT --}}

                            <div class="customer-order-payment">

                                <span>
                                    Thanh toán
                                </span>


                                <strong>

                                    @switch($order->payment_method)

                                        @case('cod')
                                            COD
                                            @break

                                        @case('qr')
                                            QR
                                            @break

                                        @case('vnpay')
                                            VNPay
                                            @break

                                        @default
                                            {{ strtoupper(
                                                $order->payment_method
                                            ) }}

                                    @endswitch

                                </strong>


                                @switch($order->payment_status)

                                    @case('unpaid')

                                        <small class="payment-unpaid">
                                            Chưa thanh toán
                                        </small>

                                        @break


                                    @case('pending')

                                        <small class="payment-pending">
                                            Chờ thanh toán
                                        </small>

                                        @break


                                    @case('paid')

                                        <small class="payment-paid">
                                            Đã thanh toán
                                        </small>

                                        @break


                                    @case('failed')

                                        <small class="payment-failed">
                                            Thanh toán thất bại
                                        </small>

                                        @break


                                    @case('refunded')

                                        <small class="payment-refunded">
                                            Đã hoàn tiền
                                        </small>

                                        @break

                                @endswitch

                            </div>



                            {{-- TOTAL --}}

                            <div class="customer-order-total">

                                <span>
                                    Tổng thanh toán
                                </span>

                                <strong>

                                    {{ number_format(
                                        (float) $order->total,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </div>

                        </div>



                        {{-- FOOTER --}}

                        <div class="customer-order-footer">

                            <a
                                href="{{ route(
                                    'orders.show',
                                    $order
                                ) }}"
                                class="btn btn-primary btn-sm"
                            >
                                Xem chi tiết
                            </a>


                            @if(
                                $order->isCancellableByCustomer()
                                && $order->payment_status !== 'paid'
                            )

                                <form
                                    action="{{ route(
                                        'orders.cancel',
                                        $order
                                    ) }}"
                                    method="POST"
                                    onsubmit="
                                        return confirm(
                                            'Bạn có chắc muốn hủy đơn hàng này?'
                                        );
                                    "
                                >

                                    @csrf
                                    @method('PATCH')


                                    <button
                                        type="submit"
                                        class="btn btn-outline btn-sm"
                                    >
                                        Hủy đơn
                                    </button>

                                </form>

                            @endif

                        </div>

                    </article>

                @endforeach

            </div>



            <div class="orders-pagination">
                {{ $orders->links() }}
            </div>

        @endif

    </div>

</section>

@endsection