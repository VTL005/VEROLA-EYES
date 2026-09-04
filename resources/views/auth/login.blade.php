@extends('layouts.app')


@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/social-auth.css') }}"
>
@endpush


@section('title', 'Đăng nhập - VELORA Eyes')


@section(
    'meta_description',
    'Đăng nhập tài khoản VELORA Eyes để quản lý đơn hàng, sản phẩm yêu thích, bảo hành và trải nghiệm mua sắm.'
)


@section('content')

<section class="auth-section">

    <div class="velora-container">

        <div class="auth-layout">

            {{-- =========================================================
                LEFT SIDE
            ========================================================== --}}

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
                    theo dõi đơn hàng và sử dụng
                    các tiện ích dành riêng cho khách hàng
                    tại VELORA Eyes.
                </p>


                <div class="auth-benefits">

                    <div>

                        <strong>
                            01
                        </strong>

                        <span>
                            Theo dõi đơn hàng dễ dàng
                        </span>

                    </div>


                    <div>

                        <strong>
                            02
                        </strong>

                        <span>
                            Quản lý sản phẩm yêu thích
                        </span>

                    </div>


                    <div>

                        <strong>
                            03
                        </strong>

                        <span>
                            Tra cứu bảo hành sản phẩm
                        </span>

                    </div>

                </div>

            </div>


            {{-- =========================================================
                LOGIN CARD
            ========================================================== --}}

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


                {{-- =====================================================
                    SOCIAL LOGIN ERROR
                ====================================================== --}}

                @error('social')

                    <div class="social-auth-error">
                        {{ $message }}
                    </div>

                @enderror


                {{-- =====================================================
                    NORMAL LOGIN FORM
                ====================================================== --}}

                <form
                    action="{{ route('login.store') }}"
                    method="POST"
                >

                    @csrf


                    {{-- =================================================
                        EMAIL
                    ================================================== --}}

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


                    {{-- =================================================
                        PASSWORD
                    ================================================== --}}

                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Mật khẩu
                        </label>


                        <div class="auth-password-wrap">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control auth-password-input @error('password') input-error @enderror"
                                placeholder="Nhập mật khẩu"
                                autocomplete="current-password"
                                required
                            >


                            <button
                                type="button"
                                id="togglePassword"
                                class="auth-password-toggle"
                                aria-label="Hiện mật khẩu"
                                title="Hiện mật khẩu"
                            >

                                {{-- EYE OPEN --}}

                                <svg
                                    id="passwordEyeOpen"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >

                                    <path
                                        d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"
                                    ></path>

                                    <circle
                                        cx="12"
                                        cy="12"
                                        r="3"
                                    ></circle>

                                </svg>


                                {{-- EYE CLOSED --}}

                                <svg
                                    id="passwordEyeClosed"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                    style="display: none;"
                                >

                                    <path
                                        d="M3 3l18 18"
                                    ></path>


                                    <path
                                        d="M10.6 6.2A10.7 10.7 0 0 1 12 6c6 0 9.5 6 9.5 6a15.8 15.8 0 0 1-2.1 2.8"
                                    ></path>


                                    <path
                                        d="M6.6 6.7C4 8.4 2.5 12 2.5 12s3.5 6 9.5 6a9.8 9.8 0 0 0 4-.8"
                                    ></path>

                                </svg>

                            </button>

                        </div>


                        @error('password')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- =================================================
                        REMEMBER + FORGOT PASSWORD
                    ================================================== --}}

                    <div class="auth-options">

                        <label class="auth-checkbox">

                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                {{ old('remember') ? 'checked' : '' }}
                            >


                            <span>
                                Ghi nhớ đăng nhập
                            </span>

                        </label>


                        <a
                            href="{{ route('password.request') }}"
                            class="auth-forgot-link"
                        >
                            Quên mật khẩu?
                        </a>

                    </div>


                    {{-- =================================================
                        SUBMIT
                    ================================================== --}}

                    <button
                        type="submit"
                        class="btn btn-primary auth-submit"
                    >
                        Đăng nhập
                    </button>

                </form>


                {{-- =====================================================
                    SOCIAL LOGIN DIVIDER
                ====================================================== --}}

                <div class="social-auth-divider">

                    <span>
                        Hoặc tiếp tục với
                    </span>

                </div>


                {{-- =====================================================
                    GOOGLE LOGIN
                ====================================================== --}}

                <a
                    href="{{ route('social.google.redirect') }}"
                    class="social-auth-button"
                >

                    <svg
                        class="social-auth-icon"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >

                        <path
                            fill="#4285F4"
                            d="M21.6 12.23c0-.71-.06-1.24-.2-1.79H12v3.41h5.52a4.72 4.72 0 0 1-2.05 3.09l-.02.11 2.98 2.31.21.02c1.94-1.79 3.06-4.43 3.06-7.15Z"
                        ></path>


                        <path
                            fill="#34A853"
                            d="M12 22c2.7 0 4.96-.89 6.61-2.42l-3.15-2.44c-.84.57-1.97.97-3.46.97-2.6 0-4.81-1.76-5.6-4.19l-.1.01-3.1 2.4-.03.1A9.99 9.99 0 0 0 12 22Z"
                        ></path>


                        <path
                            fill="#FBBC05"
                            d="M6.4 13.92A6.08 6.08 0 0 1 6.08 12c0-.67.12-1.31.31-1.92l-.01-.13-3.14-2.44-.1.05A10.02 10.02 0 0 0 2 12c0 1.61.39 3.14 1.08 4.49l3.32-2.57Z"
                        ></path>


                        <path
                            fill="#EA4335"
                            d="M12 5.89c1.88 0 3.15.81 3.88 1.48l2.8-2.73C16.96 3.03 14.7 2 12 2a9.99 9.99 0 0 0-8.84 5.56l3.24 2.52C7.2 7.65 9.4 5.89 12 5.89Z"
                        ></path>

                    </svg>


                    <span>
                        Đăng nhập bằng Google
                    </span>

                </a>


                {{-- =====================================================
                    REGISTER
                ====================================================== --}}

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


{{-- =========================================================
    SHOW / HIDE PASSWORD
========================================================= --}}

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const passwordInput =
            document.getElementById(
                'password'
            );


        const toggleButton =
            document.getElementById(
                'togglePassword'
            );


        const eyeOpen =
            document.getElementById(
                'passwordEyeOpen'
            );


        const eyeClosed =
            document.getElementById(
                'passwordEyeClosed'
            );


        if (
            !passwordInput ||
            !toggleButton ||
            !eyeOpen ||
            !eyeClosed
        ) {
            return;
        }


        toggleButton.addEventListener(
            'click',
            function () {

                const isHidden =
                    passwordInput.type ===
                    'password';


                /*
                |--------------------------------------------------------------------------
                | ĐỔI KIỂU INPUT
                |--------------------------------------------------------------------------
                */

                passwordInput.type =
                    isHidden
                        ? 'text'
                        : 'password';


                /*
                |--------------------------------------------------------------------------
                | ĐỔI ICON
                |--------------------------------------------------------------------------
                */

                eyeOpen.style.display =
                    isHidden
                        ? 'none'
                        : 'block';


                eyeClosed.style.display =
                    isHidden
                        ? 'block'
                        : 'none';


                /*
                |--------------------------------------------------------------------------
                | ACCESSIBILITY
                |--------------------------------------------------------------------------
                */

                const label =
                    isHidden
                        ? 'Ẩn mật khẩu'
                        : 'Hiện mật khẩu';


                toggleButton.setAttribute(
                    'aria-label',
                    label
                );


                toggleButton.setAttribute(
                    'title',
                    label
                );


                /*
                |--------------------------------------------------------------------------
                | GIỮ FOCUS Ở PASSWORD
                |--------------------------------------------------------------------------
                */

                passwordInput.focus();

            }
        );

    }
);
</script>

@endsection