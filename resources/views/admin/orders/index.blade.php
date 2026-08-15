@extends('layouts.admin')


@section(
    'title',
    'Đơn hàng - VELORA Eyes'
)


@section(
    'page-title',
    'Đơn hàng'
)


@section('content')

@php

    $orderStatusLabels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'preparing' => 'Đang chuẩn bị',
        'packed' => 'Đã đóng gói',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];


    $paymentStatusLabels = [
        'unpaid' => 'Chưa thanh toán',
        'pending' => 'Chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thanh toán thất bại',
        'refunded' => 'Đã hoàn tiền',
    ];


    $paymentMethodLabels = [
        'cod' => 'COD',
        'qr' => 'QR',
        'vnpay' => 'VNPay',
    ];

@endphp



<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            ORDER MANAGEMENT
        </span>

        <h1>
            Quản lý đơn hàng
        </h1>

        <p>
            Theo dõi và xử lý toàn bộ
            vòng đời đơn hàng VELORA Eyes.
        </p>

    </div>

</div>



{{-- STATS --}}

<div class="admin-order-stats">

    <div class="admin-order-stat">

        <div class="all">
            <i class="bi bi-receipt"></i>
        </div>

        <span>

            <small>
                Tổng đơn hàng
            </small>

            <strong>
                {{ $totalOrders }}
            </strong>

        </span>

    </div>


    <a
        href="{{ route(
            'admin.orders.index',
            ['order_status' => 'pending']
        ) }}"
        class="admin-order-stat"
    >

        <div class="pending">
            <i class="bi bi-clock-history"></i>
        </div>

        <span>

            <small>
                Chờ xác nhận
            </small>

            <strong>
                {{ $pendingOrders }}
            </strong>

        </span>

    </a>


    <a
        href="{{ route(
            'admin.orders.index',
            ['order_status' => 'shipping']
        ) }}"
        class="admin-order-stat"
    >

        <div class="shipping">
            <i class="bi bi-truck"></i>
        </div>

        <span>

            <small>
                Đang giao
            </small>

            <strong>
                {{ $shippingOrders }}
            </strong>

        </span>

    </a>


    <a
        href="{{ route(
            'admin.orders.index',
            ['order_status' => 'completed']
        ) }}"
        class="admin-order-stat"
    >

        <div class="completed">
            <i class="bi bi-check-circle"></i>
        </div>

        <span>

            <small>
                Hoàn thành
            </small>

            <strong>
                {{ $completedOrders }}
            </strong>

        </span>

    </a>

</div>



{{-- FILTER --}}

<div class="admin-order-filter">

    <form
        action="{{ route(
            'admin.orders.index'
        ) }}"
        method="GET"
        class="admin-order-filter-form"
    >

        <div>

            <label for="keyword">
                Tìm kiếm
            </label>


            <div class="admin-input-icon">

                <i class="bi bi-search"></i>


                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    class="admin-form-control"
                    placeholder="Mã đơn, tên, email hoặc SĐT..."
                >

            </div>

        </div>


        <div>

            <label for="order_status">
                Trạng thái đơn
            </label>


            <select
                id="order_status"
                name="order_status"
                class="admin-form-control"
            >

                <option value="">
                    Tất cả
                </option>


                @foreach(
                    $orderStatusLabels
                    as $value => $label
                )

                    <option
                        value="{{ $value }}"
                        {{
                            $orderStatus === $value
                                ? 'selected'
                                : ''
                        }}
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

        </div>


        <div>

            <label for="payment_status">
                Thanh toán
            </label>


            <select
                id="payment_status"
                name="payment_status"
                class="admin-form-control"
            >

                <option value="">
                    Tất cả
                </option>


                @foreach(
                    $paymentStatusLabels
                    as $value => $label
                )

                    <option
                        value="{{ $value }}"
                        {{
                            $paymentStatus === $value
                                ? 'selected'
                                : ''
                        }}
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

        </div>


        <div>

            <label for="payment_method">
                Phương thức
            </label>


            <select
                id="payment_method"
                name="payment_method"
                class="admin-form-control"
            >

                <option value="">
                    Tất cả
                </option>


                @foreach(
                    $paymentMethodLabels
                    as $value => $label
                )

                    <option
                        value="{{ $value }}"
                        {{
                            $paymentMethod === $value
                                ? 'selected'
                                : ''
                        }}
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

        </div>


        <div class="admin-order-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-funnel"></i>

                Lọc
            </button>


            @if(
                $keyword !== ''
                || $orderStatus
                || $paymentStatus
                || $paymentMethod
            )

                <a
                    href="{{ route(
                        'admin.orders.index'
                    ) }}"
                    class="admin-btn admin-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



{{-- TABLE --}}

<div class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Danh sách đơn hàng
            </h2>

            <p>
                {{ $orders->total() }}
                đơn hàng
            </p>

        </div>

    </div>


    @if($orders->isEmpty())

        <div class="admin-order-empty">

            <i class="bi bi-receipt"></i>

            <h3>
                Không tìm thấy đơn hàng
            </h3>

            <p>
                Hãy thử thay đổi bộ lọc.
            </p>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table admin-order-table">

                <thead>

                    <tr>

                        <th>
                            Đơn hàng
                        </th>

                        <th>
                            Khách hàng
                        </th>

                        <th>
                            Tổng tiền
                        </th>

                        <th>
                            Phương thức
                        </th>

                        <th>
                            Thanh toán
                        </th>

                        <th>
                            Trạng thái đơn
                        </th>

                        <th>
                            Ngày đặt
                        </th>

                        <th>
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $orders
                        as $order
                    )

                        <tr>

                            <td>

                                <div class="admin-order-code">

                                    <strong>
                                        {{ $order->order_code }}
                                    </strong>

                                    <span>
                                        #{{ $order->id }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                <div class="admin-order-customer">

                                    <strong>
                                        {{ $order->customer_name }}
                                    </strong>

                                    <span>
                                        {{ $order->phone }}
                                    </span>

                                    <small>
                                        {{ $order->email }}
                                    </small>

                                </div>

                            </td>


                            <td>

                                <strong class="admin-money">

                                    {{ number_format(
                                        (float) $order->total,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </td>


                            <td>

                                <span class="admin-order-payment-method">

                                    {{ $paymentMethodLabels[
                                        $order->payment_method
                                    ] ?? strtoupper(
                                        $order->payment_method
                                    ) }}

                                </span>

                            </td>


                            <td>

                                @switch(
                                    $order->payment_status
                                )

                                    @case('paid')

                                        <span class="admin-status success">
                                            Đã thanh toán
                                        </span>

                                        @break


                                    @case('failed')

                                        <span class="admin-status danger">
                                            Thất bại
                                        </span>

                                        @break


                                    @case('refunded')

                                        <span class="admin-status info">
                                            Đã hoàn tiền
                                        </span>

                                        @break


                                    @case('pending')

                                        <span class="admin-status warning">
                                            Chờ thanh toán
                                        </span>

                                        @break


                                    @default

                                        <span class="admin-status muted">
                                            Chưa thanh toán
                                        </span>

                                @endswitch

                            </td>


                            <td>

                                @switch(
                                    $order->order_status
                                )

                                    @case('pending')

                                        <span class="admin-status warning">
                                            Chờ xác nhận
                                        </span>

                                        @break


                                    @case('completed')

                                        <span class="admin-status success">
                                            Hoàn thành
                                        </span>

                                        @break


                                    @case('cancelled')

                                        <span class="admin-status danger">
                                            Đã hủy
                                        </span>

                                        @break


                                    @default

                                        <span class="admin-status info">

                                            {{ $orderStatusLabels[
                                                $order->order_status
                                            ] ?? $order->order_status }}

                                        </span>

                                @endswitch

                            </td>


                            <td>

                                <div class="admin-table-primary">

                                    <strong>

                                        {{ $order
                                            ->created_at
                                            ->format(
                                                'd/m/Y'
                                            ) }}

                                    </strong>

                                    <span>

                                        {{ $order
                                            ->created_at
                                            ->format(
                                                'H:i'
                                            ) }}

                                    </span>

                                </div>

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'admin.orders.show',
                                        $order
                                    ) }}"
                                    class="admin-order-view"
                                    title="Chi tiết đơn hàng"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="admin-pagination">

            {{ $orders->links() }}

        </div>

    @endif

</div>

@endsection