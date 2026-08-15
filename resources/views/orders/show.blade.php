@extends('layouts.app')


@section(
    'title',
    'Đơn hàng ' . $order->order_code . ' - VELORA Eyes'
)


@section('content')

@php

    $statusSteps = [
        'pending',
        'confirmed',
        'preparing',
        'packed',
        'shipping',
        'completed',
    ];


    $statusLabels = [
        'pending' =>
            'Chờ xác nhận',

        'confirmed' =>
            'Đã xác nhận',

        'preparing' =>
            'Đang chuẩn bị',

        'packed' =>
            'Đã đóng gói',

        'shipping' =>
            'Đang giao hàng',

        'completed' =>
            'Hoàn thành',

        'cancelled' =>
            'Đã hủy',
    ];


    $currentStatusIndex =
        array_search(
            $order->order_status,
            $statusSteps,
            true
        );

@endphp



{{-- =========================================================
    HERO
========================================================= --}}

<section class="order-detail-hero">

    <div class="velora-container">

        <a
            href="{{ route('orders.index') }}"
            class="order-back-link"
        >
            ← Đơn hàng của tôi
        </a>


        <div class="order-detail-hero-row">

            <div>

                <span class="hero-kicker">
                    ORDER DETAIL
                </span>

                <h1>
                    {{ $order->order_code }}
                </h1>

                <p class="text-muted mb-0">

                    Đặt lúc

                    {{ $order
                        ->created_at
                        ->format('d/m/Y H:i') }}

                </p>

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

    </div>

</section>



<section class="section">

    <div class="velora-container">

        <div class="order-detail-layout">


            {{-- =================================================
                LEFT
            ================================================== --}}

            <div class="order-detail-main">


                {{-- ORDER PROGRESS --}}

                <div class="order-detail-card">

                    <h2>
                        Hành trình đơn hàng
                    </h2>


                    @if($order->isCancelled())

                        <div class="order-cancelled-box">

                            <div class="order-cancelled-icon">
                                ×
                            </div>

                            <div>

                                <strong>
                                    Đơn hàng đã bị hủy
                                </strong>

                                <p class="mb-0">

                                    Đơn hàng này không tiếp tục
                                    quá trình vận chuyển.

                                </p>

                            </div>

                        </div>

                    @else

                        <div class="order-progress">

                            @foreach(
                                $statusSteps
                                as $index => $step
                            )

                                @php

                                    $isDone =
                                        $currentStatusIndex !== false
                                        && $index <= $currentStatusIndex;

                                    $isCurrent =
                                        $order->order_status
                                        === $step;

                                @endphp


                                <div
                                    class="order-progress-item {{ $isDone ? 'done' : '' }} {{ $isCurrent ? 'current' : '' }}"
                                >

                                    <div class="order-progress-dot">

                                        @if($isDone)
                                            ✓
                                        @else
                                            {{ $index + 1 }}
                                        @endif

                                    </div>


                                    <div>

                                        <strong>
                                            {{ $statusLabels[$step] }}
                                        </strong>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @endif

                </div>



                {{-- HISTORY --}}

                <div class="order-detail-card">

                    <h2>
                        Lịch sử cập nhật
                    </h2>


                    @if($order->statusHistories->isEmpty())

                        <div class="empty-state">
                            Chưa có lịch sử trạng thái.
                        </div>

                    @else

                        <div class="order-history-list">

                            @foreach(
                                $order->statusHistories
                                as $history
                            )

                                <div class="order-history-item">

                                    <div class="order-history-marker">
                                    </div>


                                    <div>

                                        <strong>

                                            {{ $statusLabels[
                                                $history->status
                                            ]
                                            ?? $history->status }}

                                        </strong>


                                        @if($history->description)

                                            <p>

                                                {{ $history->description }}

                                            </p>

                                        @endif


                                        <small>

                                            {{ $history
                                                ->created_at
                                                ->format('d/m/Y H:i') }}

                                            @if($history->updater)

                                                · Cập nhật bởi
                                                {{ $history->updater->name }}

                                            @endif

                                        </small>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @endif

                </div>



                {{-- PRODUCTS --}}

                <div class="order-detail-card">

                    <h2>
                        Sản phẩm
                    </h2>


                    <div class="table-wrapper">

                        <table class="velora-table">

                            <thead>

                                <tr>

                                    <th>
                                        Sản phẩm
                                    </th>

                                    <th>
                                        Màu
                                    </th>

                                    <th>
                                        Size
                                    </th>

                                    <th>
                                        Đơn giá
                                    </th>

                                    <th>
                                        SL
                                    </th>

                                    <th>
                                        Thành tiền
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($order->details as $detail)

                                    <tr>

                                        <td>

                                            <strong>
                                                {{ $detail->product_name }}
                                            </strong>

                                            <br>

                                            <small>
                                                {{ $detail->sku }}
                                            </small>

                                        </td>


                                        <td>
                                            {{ $detail->color ?? '-' }}
                                        </td>


                                        <td>
                                            {{ $detail->size ?? '-' }}
                                        </td>


                                        <td>

                                            {{ number_format(
                                                (float) $detail->unit_price,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                                        </td>


                                        <td>
                                            {{ $detail->quantity }}
                                        </td>


                                        <td>

                                            <strong>

                                                {{ number_format(
                                                    (float) $detail->subtotal,
                                                    0,
                                                    ',',
                                                    '.'
                                                ) }}đ

                                            </strong>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>



                {{-- RECEIVER --}}

                <div class="order-detail-card">

                    <h2>
                        Thông tin nhận hàng
                    </h2>


                    <div class="order-receiver-grid">

                        <div>

                            <span>
                                Người nhận
                            </span>

                            <strong>
                                {{ $order->customer_name }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Số điện thoại
                            </span>

                            <strong>
                                {{ $order->phone }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Email
                            </span>

                            <strong>
                                {{ $order->email }}
                            </strong>

                        </div>


                        <div class="order-receiver-full">

                            <span>
                                Địa chỉ
                            </span>

                            <strong>
                                {{ $order->address }}
                            </strong>

                        </div>


                        @if($order->note)

                            <div class="order-receiver-full">

                                <span>
                                    Ghi chú
                                </span>

                                <p class="mb-0">
                                    {{ $order->note }}
                                </p>

                            </div>

                        @endif

                    </div>

                </div>

            </div>



            {{-- =================================================
                RIGHT SUMMARY
            ================================================== --}}

            <aside class="order-detail-sidebar">


                {{-- TOTAL --}}

                <div class="order-detail-card">

                    <h2>
                        Tổng kết đơn hàng
                    </h2>


                    <div class="order-summary-row">

                        <span>
                            Tạm tính
                        </span>

                        <strong>

                            {{ number_format(
                                (float) $order->subtotal,
                                0,
                                ',',
                                '.'
                            ) }}đ

                        </strong>

                    </div>


                    @if(
                        (float) $order->discount_amount > 0
                    )

                        <div class="order-summary-row">

                            <span>
                                Giảm giá
                            </span>

                            <strong class="order-discount">

                                -
                                {{ number_format(
                                    (float) $order->discount_amount,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

                            </strong>

                        </div>

                    @endif


                    <div class="order-summary-row">

                        <span>
                            Phí vận chuyển
                        </span>

                        <strong>

                            {{ number_format(
                                (float) $order->shipping_fee,
                                0,
                                ',',
                                '.'
                            ) }}đ

                        </strong>

                    </div>


                    <div class="order-summary-total">

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



                {{-- PAYMENT --}}

                <div class="order-detail-card">

                    <h2>
                        Thanh toán
                    </h2>


                    <div class="order-payment-info">

                        <div>

                            <span>
                                Phương thức
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

                                @endswitch

                            </strong>

                        </div>


                        <div>

                            <span>
                                Trạng thái
                            </span>


                            @switch($order->payment_status)

                                @case('unpaid')

                                    <strong class="payment-unpaid">
                                        Chưa thanh toán
                                    </strong>

                                    @break


                                @case('pending')

                                    <strong class="payment-pending">
                                        Chờ thanh toán
                                    </strong>

                                    @break


                                @case('paid')

                                    <strong class="payment-paid">
                                        Đã thanh toán
                                    </strong>

                                    @break


                                @case('failed')

                                    <strong class="payment-failed">
                                        Thanh toán thất bại
                                    </strong>

                                    @break


                                @case('refunded')

                                    <strong class="payment-refunded">
                                        Đã hoàn tiền
                                    </strong>

                                    @break

                            @endswitch

                        </div>


                        @if(
                            $order->payment?->transaction_code
                        )

                            <div>

                                <span>
                                    Mã giao dịch
                                </span>

                                <strong>
                                    {{ $order
                                        ->payment
                                        ->transaction_code }}
                                </strong>

                            </div>

                        @endif


                        @if($order->payment?->paid_at)

                            <div>

                                <span>
                                    Thanh toán lúc
                                </span>

                                <strong>

                                    {{ $order
                                        ->payment
                                        ->paid_at
                                        ->format('d/m/Y H:i') }}

                                </strong>

                            </div>

                        @endif

                    </div>

                </div>



                {{-- CANCEL --}}

                @if(
                    $order->isCancellableByCustomer()
                    && $order->payment_status !== 'paid'
                )

                    <div class="order-cancel-card">

                        <h3>
                            Muốn hủy đơn?
                        </h3>

                        <p>

                            Đơn hàng vẫn đang chờ xác nhận,
                            bạn có thể hủy ở thời điểm này.

                        </p>


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
                                class="btn btn-danger"
                                style="width:100%;"
                            >
                                Hủy đơn hàng
                            </button>

                        </form>

                    </div>

                @elseif(
                    $order->order_status === 'pending'
                    && $order->payment_status === 'paid'
                )

                    <div class="alert alert-warning">

                        Đơn hàng đã thanh toán trực tuyến.
                        Việc hủy cần được xử lý hoàn tiền
                        theo quy trình của VELORA.

                    </div>

                @endif



                <a
                    href="{{ route('orders.index') }}"
                    class="btn btn-outline"
                    style="width:100%;"
                >
                    ← Quay lại danh sách
                </a>

            </aside>

        </div>

    </div>

</section>

@endsection