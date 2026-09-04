@extends('layouts.app')


@section('title', 'Đặt lại mật khẩu - VELORA Eyes')


@section(
    'meta_description',
    'Thiết lập mật khẩu mới cho tài khoản VELORA Eyes.'
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
                    Tạo mật khẩu
                    <br>
                    mới.
                </h1>


                <p>
                    Thiết lập mật khẩu mới để tiếp tục
                    sử dụng tài khoản VELORA Eyes
                    một cách an toàn.
                </p>


                <div class="auth-benefits">

                    <div>

                        <strong>
                            01
                        </strong>

                        <span>
                            Tối thiểu 8 ký tự
                        </span>

                    </div>


                    <div>

                        <strong>
                            02
                        </strong>

                        <span>
                            Có chữ hoa, chữ thường và số
                        </span>

                    </div>


                    <div>

                        <strong>
                            03
                        </strong>

                        <span>
                            Xác nhận lại mật khẩu
                        </span>

                    </div>

                </div>

            </div>


            {{-- ===============================================
                RESET PASSWORD CARD
            =============================================== --}}

            <div class="auth-card">

                <div class="auth-card-heading">

                    <span class="badge badge-blue">
                        Bảo mật tài khoản
                    </span>


                    <h2>
                        Đặt lại mật khẩu
                    </h2>


                    <p>
                        Nhập mật khẩu mới cho tài khoản của bạn.
                    </p>

                </div>


                <form
                    action="{{ route('password.update') }}"
                    method="POST"
                >

                    @csrf


                    {{-- TOKEN --}}

                    <input
                        type="hidden"
                        name="token"
                        value="{{ $token }}"
                    >


                    {{-- =======================================
                        EMAIL
                    ======================================== --}}

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
                            value="{{ old('email', $email) }}"
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


                    {{-- =======================================
                        NEW PASSWORD
                    ======================================== --}}

                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Mật khẩu mới
                        </label>


                        <div class="auth-password-wrap">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control auth-password-input @error('password') input-error @enderror"
                                placeholder="Nhập mật khẩu mới"
                                autocomplete="new-password"
                                required
                            >


                            <button
                                type="button"
                                class="auth-password-toggle"
                                data-password-toggle="password"
                                aria-label="Hiện mật khẩu"
                                title="Hiện mật khẩu"
                            >

                                <svg
                                    class="password-eye-open"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>


                                <svg
                                    class="password-eye-closed"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                    style="display: none;"
                                >
                                    <path d="M3 3l18 18"></path>
                                    <path d="M10.6 6.2A10.7 10.7 0 0 1 12 6c6 0 9.5 6 9.5 6a15.8 15.8 0 0 1-2.1 2.8"></path>
                                    <path d="M6.6 6.7C4 8.4 2.5 12 2.5 12s3.5 6 9.5 6a9.8 9.8 0 0 0 4-.8"></path>
                                </svg>

                            </button>

                        </div>


                        @error('password')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- =======================================
                        CONFIRM PASSWORD
                    ======================================== --}}

                    <div class="form-group">

                        <label
                            for="password_confirmation"
                            class="form-label"
                        >
                            Xác nhận mật khẩu mới
                        </label>


                        <div class="auth-password-wrap">

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control auth-password-input"
                                placeholder="Nhập lại mật khẩu mới"
                                autocomplete="new-password"
                                required
                            >


                            <button
                                type="button"
                                class="auth-password-toggle"
                                data-password-toggle="password_confirmation"
                                aria-label="Hiện mật khẩu"
                                title="Hiện mật khẩu"
                            >

                                <svg
                                    class="password-eye-open"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>


                                <svg
                                    class="password-eye-closed"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                    style="display: none;"
                                >
                                    <path d="M3 3l18 18"></path>
                                    <path d="M10.6 6.2A10.7 10.7 0 0 1 12 6c6 0 9.5 6 9.5 6a15.8 15.8 0 0 1-2.1 2.8"></path>
                                    <path d="M6.6 6.7C4 8.4 2.5 12 2.5 12s3.5 6 9.5 6a9.8 9.8 0 0 0 4-.8"></path>
                                </svg>

                            </button>

                        </div>

                    </div>


                    {{-- =======================================
                        SUBMIT
                    ======================================== --}}

                    <button
                        type="submit"
                        class="btn btn-primary auth-submit"
                    >
                        Đặt lại mật khẩu
                    </button>

                </form>


                <div class="auth-switch">

                    <span>
                        Đã nhớ mật khẩu?
                    </span>


                    <a href="{{ route('login') }}">
                        Quay lại đăng nhập
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const toggleButtons =
            document.querySelectorAll(
                '[data-password-toggle]'
            );


        toggleButtons.forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        const inputId =
                            button.getAttribute(
                                'data-password-toggle'
                            );


                        const input =
                            document.getElementById(
                                inputId
                            );


                        if (!input) {
                            return;
                        }


                        const eyeOpen =
                            button.querySelector(
                                '.password-eye-open'
                            );


                        const eyeClosed =
                            button.querySelector(
                                '.password-eye-closed'
                            );


                        const isHidden =
                            input.type === 'password';


                        input.type =
                            isHidden
                                ? 'text'
                                : 'password';


                        if (eyeOpen) {
                            eyeOpen.style.display =
                                isHidden
                                    ? 'none'
                                    : 'block';
                        }


                        if (eyeClosed) {
                            eyeClosed.style.display =
                                isHidden
                                    ? 'block'
                                    : 'none';
                        }


                        const label =
                            isHidden
                                ? 'Ẩn mật khẩu'
                                : 'Hiện mật khẩu';


                        button.setAttribute(
                            'aria-label',
                            label
                        );


                        button.setAttribute(
                            'title',
                            label
                        );


                        input.focus();

                    }
                );

            }
        );

    }
);
</script>

@endsection