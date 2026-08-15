@extends('layouts.app')


@section('title', 'Đăng ký - VELORA Eyes')


@section(
    'meta_description',
    'Tạo tài khoản VELORA Eyes để mua sắm, đặt lịch đo mắt và sử dụng các dịch vụ dành cho khách hàng.'
)


@section('content')

<section class="auth-section">

    <div class="velora-container">

        <div class="auth-layout">


            {{-- ===============================================
                LEFT SIDE
            =============================================== --}}

            <div class="auth-intro">

                <span class="hero-kicker">
                    JOIN VELORA
                </span>


                <h1>
                    Bắt đầu hành trình
                    cùng VELORA.
                </h1>


                <p>
                    Tạo tài khoản để mua sắm thuận tiện hơn,
                    lưu thông tin đơn hàng và sử dụng
                    các dịch vụ chăm sóc thị lực.
                </p>


                <div class="auth-benefits">

                    <div>
                        <strong>01</strong>

                        <span>
                            Mua hàng và lưu giỏ hàng
                        </span>
                    </div>


                    <div>
                        <strong>02</strong>

                        <span>
                            Đặt lịch kiểm tra thị lực
                        </span>
                    </div>


                    <div>
                        <strong>03</strong>

                        <span>
                            Quản lý bảo hành điện tử
                        </span>
                    </div>

                </div>

            </div>



            {{-- ===============================================
                REGISTER CARD
            =============================================== --}}

            <div class="auth-card auth-card-register">

                <div class="auth-card-heading">

                    <span class="badge badge-blue">
                        Khách hàng mới
                    </span>

                    <h2>
                        Tạo tài khoản
                    </h2>

                    <p>
                        Điền thông tin bên dưới để đăng ký.
                    </p>

                </div>


                <form
                    action="{{ route('register.store') }}"
                    method="POST"
                >

                    @csrf


                    <div class="auth-form-grid">


                        {{-- NAME --}}

                        <div class="form-group">

                            <label
                                for="name"
                                class="form-label"
                            >
                                Họ và tên
                            </label>


                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') input-error @enderror"
                                value="{{ old('name') }}"
                                placeholder="Nguyễn Văn A"
                                autocomplete="name"
                                required
                                autofocus
                            >


                            @error('name')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- PHONE --}}

                        <div class="form-group">

                            <label
                                for="phone"
                                class="form-label"
                            >
                                Số điện thoại
                            </label>


                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-control @error('phone') input-error @enderror"
                                value="{{ old('phone') }}"
                                placeholder="09xxxxxxxx"
                                autocomplete="tel"
                                required
                            >


                            @error('phone')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- EMAIL --}}

                        <div class="form-group auth-grid-full">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email
                            </label>


                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control @error('email') input-error @enderror"
                                value="{{ old('email') }}"
                                placeholder="example@email.com"
                                autocomplete="email"
                                required
                            >


                            @error('email')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- PASSWORD --}}

                        <div class="form-group">

                            <label
                                for="password"
                                class="form-label"
                            >
                                Mật khẩu
                            </label>


                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') input-error @enderror"
                                placeholder="Nhập mật khẩu"
                                autocomplete="new-password"
                                required
                            >


                            @error('password')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- PASSWORD CONFIRMATION --}}

                        <div class="form-group">

                            <label
                                for="password_confirmation"
                                class="form-label"
                            >
                                Xác nhận mật khẩu
                            </label>


                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Nhập lại mật khẩu"
                                autocomplete="new-password"
                                required
                            >

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary auth-submit"
                    >
                        Tạo tài khoản
                    </button>

                </form>


                <div class="auth-switch">

                    <span>
                        Đã có tài khoản?
                    </span>

                    <a href="{{ route('login') }}">
                        Đăng nhập
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection