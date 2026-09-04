@extends('layouts.app')


@section('title', 'Hoàn tất đăng ký Google - VELORA Eyes')


@section(
    'meta_description',
    'Hoàn tất hồ sơ VELORA Eyes sau khi đăng nhập bằng Google.'
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
                    Chỉ còn
                    <br>
                    một bước.
                </h1>


                <p>
                    Google đã xác thực tài khoản của bạn.
                    VELORA Eyes chỉ cần bổ sung số điện thoại
                    để hoàn tất hồ sơ khách hàng.
                </p>


                <div class="auth-benefits">

                    <div>

                        <strong>
                            01
                        </strong>

                        <span>
                            Email đã được Google xác thực
                        </span>

                    </div>


                    <div>

                        <strong>
                            02
                        </strong>

                        <span>
                            Bổ sung số điện thoại
                        </span>

                    </div>


                    <div>

                        <strong>
                            03
                        </strong>

                        <span>
                            Hoàn tất tài khoản Customer
                        </span>

                    </div>

                </div>

            </div>


            {{-- ===============================================
                COMPLETE PROFILE CARD
            =============================================== --}}

            <div class="auth-card">

                <div class="auth-card-heading">

                    <span class="badge badge-blue">
                        Google
                    </span>


                    <h2>
                        Hoàn tất hồ sơ
                    </h2>


                    <p>
                        Kiểm tra thông tin Google
                        và bổ sung số điện thoại.
                    </p>

                </div>


                {{-- ===========================================
                    GOOGLE INFO
                ============================================ --}}

                <div class="social-profile-summary">

                    <div class="social-profile-item">

                        <span>
                            Họ tên
                        </span>

                        <strong>
                            {{ $googleUser['name'] }}
                        </strong>

                    </div>


                    <div class="social-profile-item">

                        <span>
                            Email
                        </span>

                        <strong>
                            {{ $googleUser['email'] }}
                        </strong>

                    </div>

                </div>


                {{-- ===========================================
                    FORM
                ============================================ --}}

                <form
                    action="{{ route('social.google.complete.store') }}"
                    method="POST"
                >

                    @csrf


                    <div class="form-group">

                        <label
                            for="phone"
                            class="form-label"
                        >
                            Số điện thoại
                        </label>


                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            class="form-control @error('phone') input-error @enderror"
                            value="{{ old('phone') }}"
                            placeholder="Ví dụ: 0912345678"
                            inputmode="numeric"
                            autocomplete="tel"
                            maxlength="10"
                            required
                            autofocus
                        >


                        @error('phone')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary auth-submit"
                    >
                        Hoàn tất đăng ký
                    </button>

                </form>


                <div class="auth-switch">

                    <span>
                        Muốn dùng tài khoản khác?
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