@extends('layouts.admin')


@section(
    'title',
    $customer->name . ' - Customer'
)


@section(
    'page-title',
    'Chi tiết khách hàng'
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

@endphp



{{-- =========================================================
    HEADER
========================================================= --}}

<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            CUSTOMER PROFILE
        </span>

        <h1>
            {{ $customer->name }}
        </h1>

        <p>
            Customer #{{ $customer->id }}
            · tham gia
            {{ $customer
                ->created_at
                ->format('d/m/Y') }}
        </p>

    </div>


    <a
        href="{{ route(
            'admin.customers.index'
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Danh sách khách hàng
    </a>

</div>



{{-- =========================================================
    PROFILE
========================================================= --}}

<div class="admin-customer-profile-card">

    <div class="admin-customer-profile-avatar">

        {{ strtoupper(
            mb_substr(
                $customer->name,
                0,
                1
            )
        ) }}

    </div>


    <div class="admin-customer-profile-main">

        <h2>
            {{ $customer->name }}
        </h2>

        <span>
            {{ $customer->email }}
        </span>

        <small>
            {{ $customer->phone ?: 'Chưa có số điện thoại' }}
        </small>

    </div>


    <div>

        @if($customer->is_active)

            <span class="admin-status success">
                Hoạt động
            </span>

        @else

            <span class="admin-status danger">
                Đã khóa
            </span>

        @endif

    </div>

</div>



<div class="admin-customer-detail-layout">


    {{-- =====================================================
        MAIN
    ====================================================== --}}

    <div class="admin-customer-detail-main">


        {{-- ADDRESSES --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Địa chỉ giao hàng
                    </h2>

                    <p>
                        {{ $customer
                            ->addresses
                            ->count() }}
                        địa chỉ
                    </p>

                </div>

            </div>


            @if(
                $customer
                    ->addresses
                    ->isEmpty()
            )

                <div class="admin-empty-state">
                    Khách hàng chưa có địa chỉ.
                </div>

            @else

                <div class="admin-customer-address-list">

                    @foreach(
                        $customer->addresses
                        as $address
                    )

                        <div class="admin-customer-address">

                            <div class="admin-customer-address-icon">

                                <i class="bi bi-geo-alt"></i>

                            </div>


                            <div>

                                <div class="admin-customer-address-name">

                                    <strong>

                                        {{ $address
                                            ->recipient_name }}

                                    </strong>


                                    @if($address->is_default)

                                        <span>
                                            Mặc định
                                        </span>

                                    @endif

                                </div>


                                <small>
                                    {{ $address->phone }}
                                </small>


                                <p>

                                    {{ $address
                                        ->detail_address }}

                                    @if($address->ward)
                                        , {{ $address->ward }}
                                    @endif

                                    @if($address->district)
                                        , {{ $address->district }}
                                    @endif

                                    @if($address->province)
                                        , {{ $address->province }}
                                    @endif

                                </p>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </section>



        {{-- ORDERS --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Đơn hàng gần nhất
                    </h2>

                    <p>
                        Tối đa 10 đơn
                    </p>

                </div>


                <a
                    href="{{ route(
                        'admin.orders.index',
                        [
                            'keyword' =>
                                $customer->email,
                        ]
                    ) }}"
                >
                    Xem đơn hàng →
                </a>

            </div>


            @if(
                $customer
                    ->orders
                    ->isEmpty()
            )

                <div class="admin-empty-state">
                    Khách hàng chưa có đơn hàng.
                </div>

            @else

                <div class="admin-table-responsive">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>
                                    Mã đơn
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
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach(
                                $customer->orders
                                as $order
                            )

                                <tr>

                                    <td>

                                        <strong>
                                            {{ $order
                                                ->order_code }}
                                        </strong>

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

                                        {{ strtoupper(
                                            $order
                                                ->payment_method
                                        ) }}

                                    </td>


                                    <td>

                                        @if(
                                            $order->order_status
                                            === 'completed'
                                        )

                                            <span class="admin-status success">
                                                Hoàn thành
                                            </span>

                                        @elseif(
                                            $order->order_status
                                            === 'cancelled'
                                        )

                                            <span class="admin-status danger">
                                                Đã hủy
                                            </span>

                                        @elseif(
                                            $order->order_status
                                            === 'pending'
                                        )

                                            <span class="admin-status warning">
                                                Chờ xác nhận
                                            </span>

                                        @else

                                            <span class="admin-status info">

                                                {{ $orderLabels[
                                                    $order
                                                        ->order_status
                                                ] ?? $order
                                                    ->order_status }}

                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        {{ $order
                                            ->created_at
                                            ->format(
                                                'd/m/Y H:i'
                                            ) }}

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



        {{-- APPOINTMENTS --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Lịch đo mắt
                    </h2>

                    <p>
                        10 lịch gần nhất
                    </p>

                </div>

            </div>


            @if(
                $customer
                    ->appointments
                    ->isEmpty()
            )

                <div class="admin-empty-state">
                    Khách hàng chưa có lịch hẹn.
                </div>

            @else

                <div class="admin-table-responsive">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>
                                    Mã lịch
                                </th>

                                <th>
                                    Ngày
                                </th>

                                <th>
                                    Khung giờ
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
                                $customer
                                    ->appointments
                                as $appointment
                            )

                                <tr>

                                    <td>

                                        <strong>

                                            {{ $appointment
                                                ->appointment_code }}

                                        </strong>

                                    </td>


                                    <td>

                                        {{ $appointment
                                            ->appointment_date
                                            ->format('d/m/Y') }}

                                    </td>


                                    <td>
                                        {{ $appointment->time_slot }}
                                    </td>


                                    <td>

                                        @if(
                                            $appointment->status
                                            === 'completed'
                                        )

                                            <span class="admin-status success">
                                                Hoàn thành
                                            </span>

                                        @elseif(
                                            $appointment->status
                                            === 'cancelled'
                                        )

                                            <span class="admin-status danger">
                                                Đã hủy
                                            </span>

                                        @elseif(
                                            $appointment->status
                                            === 'pending'
                                        )

                                            <span class="admin-status warning">
                                                Chờ xác nhận
                                            </span>

                                        @else

                                            <span class="admin-status info">

                                                {{ $appointmentLabels[
                                                    $appointment
                                                        ->status
                                                ] ?? $appointment
                                                    ->status }}

                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        <a
                                            href="{{ route(
                                                'admin.appointments.show',
                                                $appointment
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



        {{-- EYE PRESCRIPTION --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Hồ sơ thị lực
                    </h2>

                    <p>
                        Kết quả đo gần nhất
                    </p>

                </div>

            </div>


            @if(
                $customer
                    ->eyePrescriptions
                    ->isEmpty()
            )

                <div class="admin-empty-state">
                    Chưa có hồ sơ thị lực.
                </div>

            @else

                <div class="admin-customer-prescriptions">

                    @foreach(
                        $customer
                            ->eyePrescriptions
                        as $prescription
                    )

                        <a
                            href="{{ route(
                                'admin.eye-prescriptions.show',
                                $prescription
                            ) }}"
                            class="admin-customer-prescription"
                        >

                            <div>

                                <i class="bi bi-eye"></i>

                            </div>


                            <span>

                                <strong>
                                    Hồ sơ #{{ $prescription->id }}
                                </strong>

                                <small>

                                    {{ $prescription
                                        ->exam_date
                                        ->format('d/m/Y') }}

                                    · PD:

                                    {{ $prescription->pd
                                        ?? '—' }}

                                </small>

                            </span>


                            <i class="bi bi-chevron-right"></i>

                        </a>

                    @endforeach

                </div>

            @endif

        </section>



        {{-- WARRANTY + REVIEW --}}

        <div class="admin-customer-two-columns">


            {{-- WARRANTY --}}

            <section class="admin-panel">

                <div class="admin-panel-header">

                    <div>

                        <h2>
                            Bảo hành
                        </h2>

                        <p>
                            Hồ sơ gần đây
                        </p>

                    </div>

                </div>


                @if(
                    $customer
                        ->warranties
                        ->isEmpty()
                )

                    <div class="admin-empty-state">
                        Chưa có bảo hành.
                    </div>

                @else

                    <div class="admin-customer-simple-list">

                        @foreach(
                            $customer->warranties
                            as $warranty
                        )

                            <a
                                href="{{ route(
                                    'admin.warranties.show',
                                    $warranty
                                ) }}"
                            >

                                <span>

                                    <strong>
                                        {{ $warranty
                                            ->warranty_code }}
                                    </strong>

                                    <small>

                                        {{ $warranty
                                            ->start_date
                                            ->format(
                                                'd/m/Y'
                                            ) }}

                                        →

                                        {{ $warranty
                                            ->end_date
                                            ->format(
                                                'd/m/Y'
                                            ) }}

                                    </small>

                                </span>


                                @if(
                                    $warranty->isActive()
                                )

                                    <span class="admin-status success">
                                        Hiệu lực
                                    </span>

                                @elseif(
                                    $warranty->isExpired()
                                )

                                    <span class="admin-status warning">
                                        Hết hạn
                                    </span>

                                @else

                                    <span class="admin-status danger">
                                        Đã hủy
                                    </span>

                                @endif

                            </a>

                        @endforeach

                    </div>

                @endif

            </section>



            {{-- REVIEW --}}

            <section class="admin-panel">

                <div class="admin-panel-header">

                    <div>

                        <h2>
                            Đánh giá
                        </h2>

                        <p>
                            Review gần đây
                        </p>

                    </div>

                </div>


                @if(
                    $customer
                        ->reviews
                        ->isEmpty()
                )

                    <div class="admin-empty-state">
                        Chưa có đánh giá.
                    </div>

                @else

                    <div class="admin-customer-review-list">

                        @foreach(
                            $customer->reviews
                            as $review
                        )

                            <a
                                href="{{ route(
                                    'admin.reviews.show',
                                    $review
                                ) }}"
                            >

                                <div>

                                    <strong>

                                        {{ $review
                                            ->product
                                            ?->name
                                            ?? 'Sản phẩm' }}

                                    </strong>


                                    <span class="admin-customer-stars">

                                        @for(
                                            $i = 1;
                                            $i <= 5;
                                            $i++
                                        )

                                            <i
                                                class="bi {{
                                                    $i <= $review->rating
                                                        ? 'bi-star-fill'
                                                        : 'bi-star'
                                                }}"
                                            ></i>

                                        @endfor

                                    </span>

                                </div>


                                @if($review->is_visible)

                                    <span class="admin-status success">
                                        Hiển thị
                                    </span>

                                @else

                                    <span class="admin-status danger">
                                        Đã ẩn
                                    </span>

                                @endif

                            </a>

                        @endforeach

                    </div>

                @endif

            </section>

        </div>

    </div>



    {{-- =====================================================
        SIDEBAR
    ====================================================== --}}

    <aside class="admin-customer-detail-sidebar">


        <section class="admin-panel admin-customer-account">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin tài khoản
                    </h2>

                </div>

            </div>


            <div class="admin-customer-meta">

                <span>
                    ID
                </span>

                <strong>
                    #{{ $customer->id }}
                </strong>


                <span>
                    Vai trò
                </span>

                <strong>
                    Customer
                </strong>


                <span>
                    Email
                </span>

                <strong>
                    {{ $customer->email }}
                </strong>


                <span>
                    Số điện thoại
                </span>

                <strong>
                    {{ $customer->phone ?: '—' }}
                </strong>


                <span>
                    Ngày đăng ký
                </span>

                <strong>

                    {{ $customer
                        ->created_at
                        ->format(
                            'd/m/Y H:i'
                        ) }}

                </strong>

            </div>

        </section>



        {{-- ACCOUNT ACTION --}}

        <section class="admin-panel admin-customer-account-action">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Quản lý tài khoản
                    </h2>

                </div>

            </div>


            @if($customer->is_active)

                <div class="admin-customer-account-status active">

                    <i class="bi bi-check-circle"></i>

                    <div>

                        <strong>
                            Tài khoản đang hoạt động
                        </strong>

                        <span>
                            Customer có thể đăng nhập hệ thống.
                        </span>

                    </div>

                </div>

            @else

                <div class="admin-customer-account-status inactive">

                    <i class="bi bi-slash-circle"></i>

                    <div>

                        <strong>
                            Tài khoản đang bị khóa
                        </strong>

                        <span>
                            Customer không thể sử dụng tài khoản.
                        </span>

                    </div>

                </div>

            @endif


            <form
                action="{{ route(
                    'admin.customers.toggle-active',
                    $customer
                ) }}"
                method="POST"
                onsubmit="
                    return confirm(
                        '{{ $customer->is_active
                            ? 'Bạn có chắc muốn khóa tài khoản khách hàng này?'
                            : 'Bạn có chắc muốn mở khóa tài khoản khách hàng này?' }}'
                    );
                "
            >

                @csrf
                @method('PATCH')


                @if($customer->is_active)

                    <button
                        type="submit"
                        class="admin-btn admin-btn-danger admin-btn-full"
                    >
                        <i class="bi bi-lock"></i>

                        Khóa tài khoản
                    </button>

                @else

                    <button
                        type="submit"
                        class="admin-btn admin-btn-primary admin-btn-full"
                    >
                        <i class="bi bi-unlock"></i>

                        Mở khóa tài khoản
                    </button>

                @endif

            </form>

        </section>

    </aside>

</div>

@endsection