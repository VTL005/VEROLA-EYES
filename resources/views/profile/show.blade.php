@extends('layouts.app')


@section('title', 'Tài khoản của tôi - VELORA Eyes')


@section('content')

@php

    $initial = strtoupper(
        mb_substr(
            $user->name,
            0,
            1
        )
    );

@endphp



{{-- =========================================================
    HERO
========================================================= --}}

<section class="profile-hero">

    <div class="velora-container">

        <span class="hero-kicker">
            MY ACCOUNT
        </span>

        <h1>
            Tài khoản của tôi
        </h1>

        <p class="text-muted mb-0">
            Quản lý thông tin cá nhân và các dịch vụ
            của bạn tại VELORA Eyes.
        </p>

    </div>

</section>



<section class="section">

    <div class="velora-container">

        <div class="profile-layout">


            {{-- =================================================
                SIDEBAR
            ================================================== --}}

            <aside class="profile-sidebar">

                <div class="profile-user-card">


                    {{-- AVATAR --}}

                    @if($user->avatar)

                        <img
                            src="{{ asset(
                                'storage/' . $user->avatar
                            ) }}"
                            alt="{{ $user->name }}"
                            class="profile-avatar"
                        >

                    @else

                        <div class="profile-avatar profile-avatar-fallback">
                            {{ $initial }}
                        </div>

                    @endif



                    <h2>
                        {{ $user->name }}
                    </h2>


                    <p>
                        {{ $user->email }}
                    </p>


                    @if($user->is_active)

                        <span class="profile-account-status active">
                            Tài khoản đang hoạt động
                        </span>

                    @else

                        <span class="profile-account-status inactive">
                            Tài khoản đã bị khóa
                        </span>

                    @endif



                    <div class="profile-user-actions">

                        <a
                            href="{{ route('profile.edit') }}"
                            class="btn btn-primary"
                        >
                            Chỉnh sửa hồ sơ
                        </a>


                        <a
                            href="{{ route(
                                'profile.password.edit'
                            ) }}"
                            class="btn btn-outline"
                        >
                            Đổi mật khẩu
                        </a>

                    </div>

                </div>


                <div class="profile-security-note">

                    <strong>
                        Bảo mật tài khoản
                    </strong>

                    <p>
                        Không chia sẻ mật khẩu hoặc thông tin
                        đăng nhập của bạn với người khác.
                    </p>

                </div>

            </aside>



            {{-- =================================================
                MAIN
            ================================================== --}}

            <main class="profile-main">


                {{-- PERSONAL INFO --}}

                <div class="profile-card">

                    <div class="profile-card-heading">

                        <div>

                            <span class="hero-kicker">
                                PERSONAL INFORMATION
                            </span>

                            <h2>
                                Thông tin cá nhân
                            </h2>

                        </div>


                        <a
                            href="{{ route('profile.edit') }}"
                            class="btn btn-outline btn-sm"
                        >
                            Chỉnh sửa
                        </a>

                    </div>



                    <div class="profile-info-grid">


                        <div>

                            <span>
                                Họ và tên
                            </span>

                            <strong>
                                {{ $user->name }}
                            </strong>

                        </div>



                        <div>

                            <span>
                                Số điện thoại
                            </span>

                            <strong>
                                {{ $user->phone ?: 'Chưa cập nhật' }}
                            </strong>

                        </div>



                        <div class="profile-info-full">

                            <span>
                                Email
                            </span>

                            <strong>
                                {{ $user->email }}
                            </strong>

                            <small>
                                Email tài khoản không thể thay đổi
                                trong phần chỉnh sửa hồ sơ.
                            </small>

                        </div>



                        <div>

                            <span>
                                Vai trò
                            </span>

                            <strong>
                                {{ ucfirst(
                                    $user->role?->name
                                    ?? 'customer'
                                ) }}
                            </strong>

                        </div>



                        <div>

                            <span>
                                Trạng thái
                            </span>

                            <strong>

                                {{ $user->is_active
                                    ? 'Đang hoạt động'
                                    : 'Đã khóa' }}

                            </strong>

                        </div>

                    </div>

                </div>



                {{-- SERVICES --}}

                <div class="profile-card">

                    <div class="profile-card-heading">

                        <div>

                            <span class="hero-kicker">
                                MY SERVICES
                            </span>

                            <h2>
                                Dịch vụ của tôi
                            </h2>

                        </div>

                    </div>


                    <div class="profile-service-grid">


                        <a
                            href="{{ route('orders.index') }}"
                            class="profile-service-card"
                        >

                            <div class="profile-service-icon">
                                □
                            </div>


                            <div>

                                <strong>
                                    Đơn hàng
                                </strong>

                                <span>
                                    {{ $user->orders_count }}
                                    đơn hàng
                                </span>

                            </div>

                        </a>



                        <a
                            href="{{ route('addresses.index') }}"
                            class="profile-service-card"
                        >

                            <div class="profile-service-icon">
                                ⌂
                            </div>


                            <div>

                                <strong>
                                    Địa chỉ
                                </strong>

                                <span>
                                    {{ $user->addresses_count }}
                                    địa chỉ
                                </span>

                            </div>

                        </a>



                        <a
                            href="{{ route(
                                'appointments.index'
                            ) }}"
                            class="profile-service-card"
                        >

                            <div class="profile-service-icon">
                                ◷
                            </div>


                            <div>

                                <strong>
                                    Lịch đo mắt
                                </strong>

                                <span>
                                    {{ $user->appointments_count }}
                                    lịch hẹn
                                </span>

                            </div>

                        </a>



                        <a
                            href="{{ route(
                                'eye-prescriptions.index'
                            ) }}"
                            class="profile-service-card"
                        >

                            <div class="profile-service-icon">
                                ◉
                            </div>


                            <div>

                                <strong>
                                    Hồ sơ thị lực
                                </strong>

                                <span>
                                    {{ $user->eye_prescriptions_count }}
                                    kết quả
                                </span>

                            </div>

                        </a>



                        <a
                            href="{{ route(
                                'warranties.index'
                            ) }}"
                            class="profile-service-card"
                        >

                            <div class="profile-service-icon">
                                ◇
                            </div>


                            <div>

                                <strong>
                                    Bảo hành điện tử
                                </strong>

                                <span>
                                    {{ $user->warranties_count }}
                                    bảo hành
                                </span>

                            </div>

                        </a>



                        <a
                            href="{{ route(
                                'wishlist.index'
                            ) }}"
                            class="profile-service-card"
                        >

                            <div class="profile-service-icon">
                                ♡
                            </div>


                            <div>

                                <strong>
                                    Wishlist
                                </strong>

                                <span>
                                    Sản phẩm yêu thích
                                </span>

                            </div>

                        </a>



                        <a
                            href="{{ route('cart.index') }}"
                            class="profile-service-card"
                        >

                            <div class="profile-service-icon">
                                ▣
                            </div>


                            <div>

                                <strong>
                                    Giỏ hàng
                                </strong>

                                <span>
                                    Tiếp tục mua sắm
                                </span>

                            </div>

                        </a>



                        <a
                            href="{{ route(
                                'warranties.lookup-form'
                            ) }}"
                            class="profile-service-card"
                        >

                            <div class="profile-service-icon">
                                ?
                            </div>


                            <div>

                                <strong>
                                    Tra cứu bảo hành
                                </strong>

                                <span>
                                    Kiểm tra bằng mã
                                </span>

                            </div>

                        </a>

                    </div>

                </div>



                {{-- ADDRESSES --}}

                <div class="profile-card">

                    <div class="profile-card-heading">

                        <div>

                            <span class="hero-kicker">
                                DELIVERY
                            </span>

                            <h2>
                                Địa chỉ nhận hàng
                            </h2>

                        </div>


                        <a
                            href="{{ route('addresses.index') }}"
                            class="btn btn-outline btn-sm"
                        >
                            Quản lý
                        </a>

                    </div>



                    @if($user->addresses->isEmpty())

                        <div class="profile-address-empty">

                            <p>
                                Bạn chưa có địa chỉ nhận hàng.
                            </p>


                            <a
                                href="{{ route(
                                    'addresses.create'
                                ) }}"
                                class="btn btn-primary btn-sm"
                            >
                                + Thêm địa chỉ
                            </a>

                        </div>

                    @else

                        <div class="profile-address-list">

                            @foreach(
                                $user->addresses->take(2)
                                as $address
                            )

                                <div class="profile-address-card">

                                    <div>

                                        <div class="profile-address-labels">

                                            @if($address->label)

                                                <span class="badge badge-blue">
                                                    {{ $address->label }}
                                                </span>

                                            @endif


                                            @if($address->is_default)

                                                <span class="badge badge-success">
                                                    Mặc định
                                                </span>

                                            @endif

                                        </div>


                                        <strong>
                                            {{ $address->recipient_name }}
                                        </strong>


                                        <span>
                                            {{ $address->phone }}
                                        </span>


                                        <p>

                                            {{ $address->detail_address }},
                                            {{ $address->ward }},
                                            {{ $address->district }},
                                            {{ $address->province }}

                                        </p>

                                    </div>


                                    <a
                                        href="{{ route(
                                            'addresses.edit',
                                            $address
                                        ) }}"
                                        class="btn btn-outline btn-sm"
                                    >
                                        Sửa
                                    </a>

                                </div>

                            @endforeach

                        </div>



                        @if($user->addresses->count() > 2)

                            <a
                                href="{{ route('addresses.index') }}"
                                class="profile-view-all"
                            >
                                Xem tất cả
                                {{ $user->addresses->count() }}
                                địa chỉ →
                            </a>

                        @endif

                    @endif

                </div>

            </main>

        </div>

    </div>

</section>

@endsection