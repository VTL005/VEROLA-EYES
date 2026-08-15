@extends('layouts.admin')


@section(
    'title',
    'Dashboard - VELORA Eyes'
)


@section(
    'page-title',
    'Dashboard'
)


@section('content')

@php

    $orderLabels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'preparing' => 'Đang chuẩn bị',
        'packed' => 'Đã đóng gói',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];


    $appointmentLabels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
        'no_show' => 'Không đến',
    ];


    $serviceLabels = [
        'eye_exam' => 'Đo mắt cận',
        'recheck' => 'Kiểm tra lại độ kính',
        'lens_consultation' => 'Tư vấn tròng kính',
        'frame_consultation' => 'Tư vấn chọn gọng',
    ];

@endphp



{{-- =========================================================
    WELCOME
========================================================= --}}

<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            OVERVIEW
        </span>

        <h1>
            Tổng quan hệ thống
        </h1>

        <p>
            Xin chào
            <strong>
                {{ auth()->user()->name }}
            </strong>,
            đây là tình hình hoạt động
            của VELORA Eyes.
        </p>

    </div>

</div>



{{-- =========================================================
    PRIMARY STATS
========================================================= --}}

<div class="admin-dashboard-stats">


    <div class="admin-dashboard-card">

        <div class="admin-dashboard-card-icon">
            P
        </div>


        <div>

            <span>
                Tổng sản phẩm
            </span>

            <strong>
                {{ number_format(
                    $productCount
                ) }}
            </strong>

            <small>
                sản phẩm trong hệ thống
            </small>

        </div>

    </div>



    <div class="admin-dashboard-card">

        <div class="admin-dashboard-card-icon">
            O
        </div>


        <div>

            <span>
                Tổng đơn hàng
            </span>

            <strong>
                {{ number_format(
                    $orderCount
                ) }}
            </strong>

            <small>
                đơn đã phát sinh
            </small>

        </div>

    </div>



    <div class="admin-dashboard-card revenue">

        <div class="admin-dashboard-card-icon">
            $
        </div>


        <div>

            <span>
                Doanh thu
            </span>

            <strong>

                {{ number_format(
                    (float) $revenue,
                    0,
                    ',',
                    '.'
                ) }}đ

            </strong>

            <small>
                từ đơn hoàn thành
            </small>

        </div>

    </div>



    <div class="admin-dashboard-card">

        <div class="admin-dashboard-card-icon">
            C
        </div>


        <div>

            <span>
                Khách hàng
            </span>

            <strong>
                {{ number_format(
                    $customerCount
                ) }}
            </strong>

            <small>
                tài khoản Customer
            </small>

        </div>

    </div>

</div>



{{-- =========================================================
    ACTION STATS
========================================================= --}}

<div class="admin-action-grid">


    <a
        href="{{ route(
            'admin.orders.index',
            [
                'order_status' =>
                    'pending',
            ]
        ) }}"
        class="admin-action-card warning"
    >

        <span>
            Đơn chờ xác nhận
        </span>

        <strong>
            {{ $pendingOrderCount }}
        </strong>

        <small>
            Xem đơn cần xử lý →
        </small>

    </a>



    <a
        href="{{ route(
            'admin.appointments.index',
            [
                'status' =>
                    'pending',
            ]
        ) }}"
        class="admin-action-card info"
    >

        <span>
            Lịch chờ xác nhận
        </span>

        <strong>
            {{ $pendingAppointmentCount }}
        </strong>

        <small>
            Xử lý lịch hẹn →
        </small>

    </a>



    <a
        href="{{ route(
            'admin.appointments.index',
            [
                'appointment_date' =>
                    today()->format(
                        'Y-m-d'
                    ),
            ]
        ) }}"
        class="admin-action-card success"
    >

        <span>
            Lịch hôm nay
        </span>

        <strong>
            {{ $todayAppointmentCount }}
        </strong>

        <small>
            Xem lịch trong ngày →
        </small>

    </a>



    <a
        href="{{ route(
            'admin.reviews.index'
        ) }}"
        class="admin-action-card purple"
    >

        <span>
            Đánh giá
        </span>

        <strong>
            {{ $reviewCount }}
        </strong>

        <small>
            Quản lý đánh giá →
        </small>

    </a>

</div>



{{-- =========================================================
    BUSINESS INFO
========================================================= --}}

<div class="admin-dashboard-info-grid">


    <a
        href="{{ route(
            'admin.staff.index'
        ) }}"
        class="admin-dashboard-mini-card"
    >

        <span>
            Nhân viên
        </span>

        <strong>
            {{ $staffCount }}
        </strong>

        <small>
            Quản lý nhân viên →
        </small>

    </a>


    <a
        href="{{ route(
            'admin.inventory.index'
        ) }}"
        class="admin-dashboard-mini-card"
    >

        <span>
            Kho hàng
        </span>

        <strong>
            Kiểm tra tồn kho
        </strong>

        <small>
            Xem cảnh báo tồn kho →
        </small>

    </a>


    <a
        href="{{ route(
            'admin.payments.index'
        ) }}"
        class="admin-dashboard-mini-card"
    >

        <span>
            Thanh toán
        </span>

        <strong>
            Giao dịch
        </strong>

        <small>
            Theo dõi Payment →
        </small>

    </a>


    <a
        href="{{ route(
            'admin.reports.index'
        ) }}"
        class="admin-dashboard-mini-card"
    >

        <span>
            Báo cáo
        </span>

        <strong>
            Thống kê
        </strong>

        <small>
            Xem báo cáo hệ thống →
        </small>

    </a>

</div>



{{-- =========================================================
    LATEST DATA
========================================================= --}}

<div class="admin-dashboard-columns">


    {{-- LATEST ORDERS --}}

    <section class="admin-panel">

        <div class="admin-panel-header">

            <div>

                <h2>
                    Đơn hàng mới nhất
                </h2>

                <p>
                    6 đơn gần đây
                </p>

            </div>


            <a
                href="{{ route(
                    'admin.orders.index'
                ) }}"
            >
                Xem tất cả →
            </a>

        </div>


        @if($latestOrders->isEmpty())

            <div class="admin-empty-state">

                Chưa có đơn hàng.

            </div>

        @else

            <div class="admin-table-responsive">

                <table class="admin-table">

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
                                Trạng thái
                            </th>

                            <th>
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach(
                            $latestOrders
                            as $order
                        )

                            <tr>

                                <td>

                                    <div class="admin-table-primary">

                                        <strong>
                                            {{ $order
                                                ->order_code }}
                                        </strong>

                                        <span>

                                            {{ $order
                                                ->created_at
                                                ->format(
                                                    'd/m H:i'
                                                ) }}

                                        </span>

                                    </div>

                                </td>


                                <td>

                                    {{ $order
                                        ->customer_name }}

                                </td>


                                <td>

                                    <strong class="admin-money">

                                        {{ number_format(
                                            (float) $order
                                                ->total,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                                    </strong>

                                </td>


                                <td>

                                    @switch(
                                        $order
                                            ->order_status
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

                                                {{ $orderLabels[
                                                    $order
                                                        ->order_status
                                                ] ?? $order
                                                    ->order_status }}

                                            </span>

                                    @endswitch

                                </td>


                                <td>

                                    <a
                                        href="{{ route(
                                            'admin.orders.show',
                                            $order
                                        ) }}"
                                        class="admin-table-action"
                                    >
                                        Xem
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @endif

    </section>



    {{-- LATEST APPOINTMENTS --}}

    <section class="admin-panel">

        <div class="admin-panel-header">

            <div>

                <h2>
                    Lịch hẹn mới nhất
                </h2>

                <p>
                    Lịch đo mắt gần đây
                </p>

            </div>


            <a
                href="{{ route(
                    'admin.appointments.index'
                ) }}"
            >
                Xem tất cả →
            </a>

        </div>


        @if(
            $latestAppointments
                ->isEmpty()
        )

            <div class="admin-empty-state">

                Chưa có lịch hẹn.

            </div>

        @else

            <div class="admin-appointment-list">

                @foreach(
                    $latestAppointments
                    as $appointment
                )

                    <a
                        href="{{ route(
                            'admin.appointments.show',
                            $appointment
                        ) }}"
                        class="admin-appointment-item"
                    >

                        <div class="admin-appointment-date">

                            <strong>

                                {{ $appointment
                                    ->appointment_date
                                    ->format('d') }}

                            </strong>

                            <span>

                                {{ $appointment
                                    ->appointment_date
                                    ->format('m/Y') }}

                            </span>

                        </div>


                        <div class="admin-appointment-info">

                            <strong>
                                {{ $appointment
                                    ->customer_name }}
                            </strong>

                            <span>

                                {{ $serviceLabels[
                                    $appointment
                                        ->service_type
                                ] ?? $appointment
                                    ->service_type }}

                                ·

                                {{ $appointment
                                    ->time_slot }}

                            </span>

                        </div>


                        <div>

                            @switch(
                                $appointment
                                    ->status
                            )

                                @case('pending')

                                    <span class="admin-status warning">
                                        Chờ xác nhận
                                    </span>

                                    @break


                                @case('confirmed')

                                    <span class="admin-status info">
                                        Đã xác nhận
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

                                    <span class="admin-status muted">

                                        {{ $appointmentLabels[
                                            $appointment
                                                ->status
                                        ] ?? $appointment
                                            ->status }}

                                    </span>

                            @endswitch

                        </div>

                    </a>

                @endforeach

            </div>

        @endif

    </section>

</div>

@endsection