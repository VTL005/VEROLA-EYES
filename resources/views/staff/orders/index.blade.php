@extends('layouts.staff')


@section('title', 'Đơn hàng - Staff')

@section('page-title', 'Đơn hàng')


@section('content')

@php

    $orderLabels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'preparing' => 'Đang chuẩn bị',
        'packed' => 'Đã đóng gói',
        'shipping' => 'Đang giao hàng',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];

    $paymentLabels = [
        'unpaid' => 'Chưa thanh toán',
        'pending' => 'Đang thanh toán',
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


{{-- =========================================================
    HEADER
========================================================= --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            ORDER MANAGEMENT
        </span>

        <h1>
            Quản lý đơn hàng
        </h1>

        <p>
            Theo dõi và xử lý toàn bộ
            đơn hàng của khách hàng.
        </p>

    </div>

</div>



{{-- =========================================================
    QUICK STATS
========================================================= --}}

<div class="staff-order-stats">


    <a
        href="{{ route(
            'staff.orders.index',
            [
                'order_status' => 'pending'
            ]
        ) }}"
        class="staff-order-stat-card warning"
    >

        <span>
            Chờ xác nhận
        </span>

        <strong>
            {{ $pendingCount }}
        </strong>

        <small>
            đơn cần xử lý
        </small>

    </a>



    <a
        href="{{ route(
            'staff.orders.index',
            [
                'order_status' => 'shipping'
            ]
        ) }}"
        class="staff-order-stat-card info"
    >

        <span>
            Đang giao
        </span>

        <strong>
            {{ $shippingCount }}
        </strong>

        <small>
            đơn đang vận chuyển
        </small>

    </a>



    <a
        href="{{ route(
            'staff.orders.index',
            [
                'order_status' => 'completed'
            ]
        ) }}"
        class="staff-order-stat-card success"
    >

        <span>
            Hoàn thành
        </span>

        <strong>
            {{ $completedCount }}
        </strong>

        <small>
            đơn đã hoàn tất
        </small>

    </a>

</div>



{{-- =========================================================
    FILTER
========================================================= --}}

<div class="staff-order-filter">

    <form
        action="{{ route(
            'staff.orders.index'
        ) }}"
        method="GET"
        class="staff-order-filter-form"
    >

        <div class="staff-order-filter-keyword">

            <label for="keyword">
                Tìm kiếm
            </label>

            <input
                type="text"
                id="keyword"
                name="keyword"
                value="{{ $keyword }}"
                class="staff-form-control"
                placeholder="Mã đơn, khách hàng, SĐT..."
            >

        </div>



        <div>

            <label for="order_status">
                Trạng thái đơn
            </label>

            <select
                id="order_status"
                name="order_status"
                class="staff-form-control"
            >

                <option value="">
                    Tất cả trạng thái
                </option>

                @foreach(
                    $orderLabels
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
                class="staff-form-control"
            >

                <option value="">
                    Tất cả
                </option>

                @foreach(
                    $paymentLabels
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
                class="staff-form-control"
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



        <div class="staff-order-filter-actions">

            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
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
                        'staff.orders.index'
                    ) }}"
                    class="staff-btn staff-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



{{-- =========================================================
    LIST
========================================================= --}}

<div class="staff-table-card">

    <div class="staff-table-card-header">

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

        <div class="staff-order-empty">

            <div>
                □
            </div>

            <h3>
                Không tìm thấy đơn hàng
            </h3>

            <p>
                Hãy thay đổi bộ lọc
                hoặc từ khóa tìm kiếm.
            </p>

        </div>

    @else

        <div class="staff-table-responsive">

            <table class="staff-table">

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
                            Thanh toán
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Ngày đặt
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($orders as $order)

                        <tr>

                            {{-- ORDER --}}

                            <td>

                                <div class="staff-order-code-cell">

                                    <strong>
                                        {{ $order->order_code }}
                                    </strong>

                                    <span>
                                        #{{ $order->id }}
                                    </span>

                                </div>

                            </td>



                            {{-- CUSTOMER --}}

                            <td>

                                <div class="staff-order-customer">

                                    <strong>
                                        {{ $order->customer_name }}
                                    </strong>

                                    <span>
                                        {{ $order->phone }}
                                    </span>

                                </div>

                            </td>



                            {{-- TOTAL --}}

                            <td>

                                <strong class="staff-order-total">

                                    {{ number_format(
                                        (float) $order->total,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </td>



                            {{-- PAYMENT --}}

                            <td>

                                <div class="staff-order-payment">

                                    <strong>

                                        {{ $paymentMethodLabels[
                                            $order->payment_method
                                        ] ?? strtoupper(
                                            $order->payment_method
                                        ) }}

                                    </strong>


                                    @switch($order->payment_status)

                                        @case('paid')

                                            <span class="staff-status staff-status-success">
                                                Đã thanh toán
                                            </span>

                                            @break


                                        @case('pending')

                                            <span class="staff-status staff-status-warning">
                                                Đang thanh toán
                                            </span>

                                            @break


                                        @case('refunded')

                                            <span class="staff-status staff-status-info">
                                                Đã hoàn tiền
                                            </span>

                                            @break


                                        @case('failed')

                                            <span class="staff-status staff-status-danger">
                                                Thất bại
                                            </span>

                                            @break


                                        @default

                                            <span class="staff-status staff-status-muted">
                                                Chưa thanh toán
                                            </span>

                                    @endswitch

                                </div>

                            </td>



                            {{-- ORDER STATUS --}}

                            <td>

                                @switch($order->order_status)

                                    @case('pending')

                                        <span class="staff-status staff-status-warning">
                                            Chờ xác nhận
                                        </span>

                                        @break


                                    @case('confirmed')

                                        <span class="staff-status staff-status-info">
                                            Đã xác nhận
                                        </span>

                                        @break


                                    @case('preparing')

                                        <span class="staff-status staff-status-info">
                                            Đang chuẩn bị
                                        </span>

                                        @break


                                    @case('packed')

                                        <span class="staff-status staff-status-info">
                                            Đã đóng gói
                                        </span>

                                        @break


                                    @case('shipping')

                                        <span class="staff-status staff-status-info">
                                            Đang giao
                                        </span>

                                        @break


                                    @case('completed')

                                        <span class="staff-status staff-status-success">
                                            Hoàn thành
                                        </span>

                                        @break


                                    @case('cancelled')

                                        <span class="staff-status staff-status-danger">
                                            Đã hủy
                                        </span>

                                        @break

                                @endswitch

                            </td>



                            {{-- DATE --}}

                            <td>

                                <div class="staff-order-date">

                                    <strong>
                                        {{ $order
                                            ->created_at
                                            ->format('d/m/Y') }}
                                    </strong>

                                    <span>
                                        {{ $order
                                            ->created_at
                                            ->format('H:i') }}
                                    </span>

                                </div>

                            </td>



                            {{-- ACTION --}}

                            <td>

                                <a
                                    href="{{ route(
                                        'staff.orders.show',
                                        $order
                                    ) }}"
                                    class="staff-action-button"
                                >
                                    Xem chi tiết
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="staff-table-pagination">

            {{ $orders->links() }}

        </div>

    @endif

</div>

@endsection