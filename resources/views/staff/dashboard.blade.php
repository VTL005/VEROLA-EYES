@extends('layouts.staff')


@section(
    'title',
    'Staff Dashboard - VELORA Eyes'
)


@section(
    'page-title',
    'Dashboard'
)


@section('content')


{{-- =========================================================
    PAGE HEADER
========================================================= --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            TỔNG QUAN
        </span>

        <h1>
            Xin chào,
            {{ auth()->user()->name }}
        </h1>

        <p>
            Đây là tổng quan hoạt động
            của VELORA Eyes dành cho nhân viên.
        </p>

    </div>


    <div class="staff-page-header-actions">

        <a
            href="{{ route(
                'staff.orders.index'
            ) }}"
            class="staff-btn staff-btn-primary"
        >
            Xử lý đơn hàng
        </a>

    </div>

</div>



{{-- =========================================================
    STATS
========================================================= --}}

<div class="staff-stats-grid">


    <a
        href="{{ route(
            'staff.products.index'
        ) }}"
        class="staff-stat-card"
    >

        <div class="staff-stat-icon staff-stat-blue">
            ◇
        </div>


        <div>

            <span>
                Sản phẩm
            </span>

            <strong>
                {{ $totalProducts }}
            </strong>

            <small>
                Tổng sản phẩm
            </small>

        </div>

    </a>



    <a
        href="{{ route(
            'staff.orders.index'
        ) }}"
        class="staff-stat-card"
    >

        <div class="staff-stat-icon staff-stat-orange">
            □
        </div>


        <div>

            <span>
                Đơn chờ xử lý
            </span>

            <strong>
                {{ $pendingOrders }}
            </strong>

            <small>
                Chờ xác nhận
            </small>

        </div>

    </a>



    <a
        href="{{ route(
            'staff.appointments.index'
        ) }}"
        class="staff-stat-card"
    >

        <div class="staff-stat-icon staff-stat-purple">
            ◷
        </div>


        <div>

            <span>
                Lịch đo mắt
            </span>

            <strong>
                {{ $pendingAppointments }}
            </strong>

            <small>
                Chờ xác nhận
            </small>

        </div>

    </a>



    <a
        href="{{ route(
            'staff.reviews.index'
        ) }}"
        class="staff-stat-card"
    >

        <div class="staff-stat-icon staff-stat-green">
            ★
        </div>


        <div>

            <span>
                Đánh giá
            </span>

            <strong>
                {{ $totalReviews }}
            </strong>

            <small>
                Tổng đánh giá
            </small>

        </div>

    </a>

</div>



{{-- =========================================================
    QUICK ACTIONS
========================================================= --}}

<div class="staff-dashboard-section">

    <div class="staff-section-heading">

        <div>

            <h2>
                Thao tác nhanh
            </h2>

            <p>
                Truy cập nhanh các nghiệp vụ thường dùng.
            </p>

        </div>

    </div>


    <div class="staff-quick-grid">


        <a
            href="{{ route(
                'staff.products.create'
            ) }}"
            class="staff-quick-card"
        >

            <span class="staff-quick-icon">
                +
            </span>

            <div>

                <strong>
                    Thêm sản phẩm
                </strong>

                <span>
                    Tạo sản phẩm mới
                </span>

            </div>

        </a>



        <a
            href="{{ route(
                'staff.inventory.index'
            ) }}"
            class="staff-quick-card"
        >

            <span class="staff-quick-icon">
                ▤
            </span>

            <div>

                <strong>
                    Kiểm tra kho
                </strong>

                <span>
                    Theo dõi tồn kho
                </span>

            </div>

        </a>



        <a
            href="{{ route(
                'staff.orders.index'
            ) }}"
            class="staff-quick-card"
        >

            <span class="staff-quick-icon">
                □
            </span>

            <div>

                <strong>
                    Quản lý đơn hàng
                </strong>

                <span>
                    Xác nhận và xử lý đơn
                </span>

            </div>

        </a>



        <a
            href="{{ route(
                'staff.appointments.index'
            ) }}"
            class="staff-quick-card"
        >

            <span class="staff-quick-icon">
                ◷
            </span>

            <div>

                <strong>
                    Lịch đo mắt
                </strong>

                <span>
                    Tiếp nhận lịch hẹn
                </span>

            </div>

        </a>

    </div>

</div>



{{-- =========================================================
    RECENT DATA
========================================================= --}}

<div class="staff-dashboard-two-columns">


    {{-- RECENT ORDERS --}}

    <section class="staff-dashboard-card">

        <div class="staff-section-heading">

            <div>

                <h2>
                    Đơn hàng gần đây
                </h2>

                <p>
                    5 đơn hàng mới nhất.
                </p>

            </div>


            <a
                href="{{ route(
                    'staff.orders.index'
                ) }}"
                class="staff-section-link"
            >
                Xem tất cả →
            </a>

        </div>


        @if($recentOrders->isEmpty())

            <div class="staff-empty-small">
                Chưa có đơn hàng.
            </div>

        @else

            <div class="staff-dashboard-list">

                @foreach(
                    $recentOrders
                    as $order
                )

                    <a
                        href="{{ route(
                            'staff.orders.show',
                            $order
                        ) }}"
                        class="staff-dashboard-list-item"
                    >

                        <div>

                            <strong>
                                {{ $order->order_code }}
                            </strong>

                            <span>

                                {{ $order
                                    ->created_at
                                    ->format('d/m/Y H:i') }}

                            </span>

                        </div>


                        <div class="staff-list-right">

                            <strong>

                                {{ number_format(
                                    (float) $order->total,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

                            </strong>


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

                                    <span class="staff-status staff-status-purple">
                                        Đã đóng gói
                                    </span>

                                    @break


                                @case('shipping')

                                    <span class="staff-status staff-status-purple">
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

                        </div>

                    </a>

                @endforeach

            </div>

        @endif

    </section>



    {{-- RECENT APPOINTMENTS --}}

    <section class="staff-dashboard-card">

        <div class="staff-section-heading">

            <div>

                <h2>
                    Lịch đo mắt gần đây
                </h2>

                <p>
                    5 lịch hẹn mới nhất.
                </p>

            </div>


            <a
                href="{{ route(
                    'staff.appointments.index'
                ) }}"
                class="staff-section-link"
            >
                Xem tất cả →
            </a>

        </div>


        @if($recentAppointments->isEmpty())

            <div class="staff-empty-small">
                Chưa có lịch hẹn.
            </div>

        @else

            <div class="staff-dashboard-list">

                @foreach(
                    $recentAppointments
                    as $appointment
                )

                    <a
                        href="{{ route(
                            'staff.appointments.show',
                            $appointment
                        ) }}"
                        class="staff-dashboard-list-item"
                    >

                        <div>

                            <strong>
                                {{ $appointment->appointment_code }}
                            </strong>

                            <span>
                                {{ $appointment->customer_name }}
                            </span>

                        </div>


                        <div class="staff-list-right">

                            <strong>

                                {{ $appointment
                                    ->appointment_date
                                    ->format('d/m/Y') }}

                            </strong>


                            @switch($appointment->status)

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


                                @case('no_show')

                                    <span class="staff-status staff-status-muted">
                                        Không đến
                                    </span>

                                    @break

                            @endswitch

                        </div>

                    </a>

                @endforeach

            </div>

        @endif

    </section>

</div>

@endsection