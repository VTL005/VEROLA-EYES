@extends('layouts.app')


@section('title', 'Quên mật khẩu - VELORA Eyes')


@section(
    'meta_description',
    'Khôi phục mật khẩu tài khoản VELORA Eyes bằng địa chỉ email đã đăng ký.'
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
                    Khôi phục
                    <br>
                    tài khoản.
                </h1>


                <p>
                    Nhập địa chỉ email đã đăng ký.
                    VELORA Eyes sẽ gửi cho bạn
                    liên kết để thiết lập lại mật khẩu.
                </p>


                <div class="auth-benefits">

                    <div>

                        <strong>
                            01
                        </strong>

                        <span>
                            Xác nhận qua email
                        </span>

                    </div>


                    <div>

                        <strong>
                            02
                        </strong>

                        <span>
                            Tạo mật khẩu mới an toàn
                        </span>

                    </div>


                    <div>

                        <strong>
                            03
                        </strong>

                        <span>
                            Tiếp tục sử dụng tài khoản
                        </span>

                    </div>

                </div>

            </div>


            {{-- ===============================================
                FORGOT PASSWORD CARD
            =============================================== --}}

            <div class="auth-card">

                <div class="auth-card-heading">

                    <span class="badge badge-blue">
                        Bảo mật tài khoản
                    </span>


                    <h2>
                        Quên mật khẩu?
                    </h2>


                    <p>
                        Nhập email của Customer hoặc Staff
                        để nhận liên kết đặt lại mật khẩu.
                    </p>

                </div>


                {{-- ===========================================
                    STATUS
                ============================================ --}}

                @if (session('status'))

                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>

                @endif


                {{-- ===========================================
                    FORM
                ============================================ --}}

                <form
                    action="{{ route('password.email') }}"
                    method="POST"
                >

                    @csrf


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


                    <button
                        type="submit"
                        class="btn btn-primary auth-submit"
                    >
                        Gửi liên kết khôi phục
                    </button>

                </form>


                {{-- ===========================================
                    BACK TO LOGIN
                ============================================ --}}

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

@endsection