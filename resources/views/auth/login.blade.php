@extends('layouts.app')


@section('title', 'Đăng nhập - VELORA Eyes')


@section(
    'meta_description',
    'Đăng nhập tài khoản VELORA Eyes để quản lý đơn hàng, lịch đo mắt và trải nghiệm mua sắm.'
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
                    VELORA EYES
                </span>


                <h1>
                    Chào mừng
                    <br>
                    bạn trở lại.
                </h1>


                <p>
                    Đăng nhập để tiếp tục mua sắm,
                    theo dõi đơn hàng, lịch đo mắt
                    và các dịch vụ chăm sóc tại VELORA.
                </p>


                <div class="auth-benefits">

                    <div>
                        <strong>01</strong>

                        <span>
                            Theo dõi đơn hàng dễ dàng
                        </span>
                    </div>


                    <div>
                        <strong>02</strong>

                        <span>
                            Quản lý lịch đo mắt
                        </span>
                    </div>


                    <div>
                        <strong>03</strong>

                        <span>
                            Xem bảo hành và kết quả thị lực
                        </span>
                    </div>

                </div>

            </div>


            {{-- ===============================================
                LOGIN CARD
            =============================================== --}}

            <div class="auth-card">

                <div class="auth-card-heading">

                    <span class="badge badge-blue">
                        Tài khoản
                    </span>

                    <h2>
                        Đăng nhập
                    </h2>

                    <p>
                        Nhập thông tin tài khoản của bạn.
                    </p>

                </div>


                <form
                    action="{{ route('login.store') }}"
                    method="POST"
                >

                    @csrf


                    {{-- EMAIL --}}

                    <div class="form-group">

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
                            autofocus
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
                            autocomplete="current-password"
                            required
                        >


                        @error('password')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- REMEMBER --}}

                    <div class="auth-options">

                        <label class="auth-checkbox">

                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                {{ old('remember')
                                    ? 'checked'
                                    : '' }}
                            >

                            <span>
                                Ghi nhớ đăng nhập
                            </span>

                        </label>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary auth-submit"
                    >
                        Đăng nhập
                    </button>

                </form>


                <div class="auth-switch">

                    <span>
                        Chưa có tài khoản?
                    </span>

                    <a href="{{ route('register') }}">
                        Đăng ký ngay
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection