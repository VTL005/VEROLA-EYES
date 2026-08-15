@extends('layouts.app')


@section('title', 'Đổi mật khẩu - VELORA Eyes')


@section('content')

<section class="profile-form-section">

    <div class="velora-container">

        <div class="password-page-layout">


            {{-- INTRO --}}

            <div class="profile-form-intro">

                <span class="hero-kicker">
                    ACCOUNT SECURITY
                </span>

                <h1>
                    Đổi mật khẩu
                </h1>


                <p>
                    Sử dụng mật khẩu đủ mạnh để
                    bảo vệ tài khoản VELORA Eyes
                    của bạn.
                </p>


                <div class="password-security-tip">

                    <strong>
                        Gợi ý bảo mật
                    </strong>

                    <span>
                        Mật khẩu nên có ít nhất
                        8 ký tự và khó đoán.
                    </span>

                </div>


                <a
                    href="{{ route('profile.show') }}"
                    class="profile-back-link"
                >
                    ← Quay lại tài khoản
                </a>

            </div>



            {{-- FORM --}}

            <div class="profile-form-card">

                <div class="password-form-heading">

                    <h2>
                        Cập nhật mật khẩu
                    </h2>


                    <p>
                        Trước tiên, hãy xác nhận
                        mật khẩu hiện tại của bạn.
                    </p>

                </div>


                <form
                    action="{{ route(
                        'profile.password.update'
                    ) }}"
                    method="POST"
                >

                    @csrf
                    @method('PATCH')



                    {{-- CURRENT PASSWORD --}}

                    <div class="form-group">

                        <label
                            for="current_password"
                            class="form-label"
                        >
                            Mật khẩu hiện tại
                        </label>


                        <div class="password-input-wrapper">

                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                class="form-control @error('current_password') input-error @enderror"
                                autocomplete="current-password"
                                required
                            >


                            <button
                                type="button"
                                class="password-toggle"
                                data-password-toggle="current_password"
                            >
                                Hiện
                            </button>

                        </div>


                        @error('current_password')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>



                    {{-- NEW PASSWORD --}}

                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label"
                        >
                            Mật khẩu mới
                        </label>


                        <div class="password-input-wrapper">

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') input-error @enderror"
                                autocomplete="new-password"
                                required
                            >


                            <button
                                type="button"
                                class="password-toggle"
                                data-password-toggle="password"
                            >
                                Hiện
                            </button>

                        </div>


                        <small class="password-helper">
                            Ít nhất 8 ký tự.
                        </small>


                        @error('password')

                            <div class="field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>



                    {{-- CONFIRM --}}

                    <div class="form-group">

                        <label
                            for="password_confirmation"
                            class="form-label"
                        >
                            Xác nhận mật khẩu mới
                        </label>


                        <div class="password-input-wrapper">

                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                autocomplete="new-password"
                                required
                            >


                            <button
                                type="button"
                                class="password-toggle"
                                data-password-toggle="password_confirmation"
                            >
                                Hiện
                            </button>

                        </div>

                    </div>



                    <div class="profile-form-actions">

                        <a
                            href="{{ route('profile.show') }}"
                            class="btn btn-outline"
                        >
                            Hủy
                        </a>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Cập nhật mật khẩu
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</section>



<script>

document.querySelectorAll(
    '[data-password-toggle]'
).forEach(function (button) {

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


            if (input.type === 'password') {

                input.type = 'text';

                button.textContent =
                    'Ẩn';

            } else {

                input.type =
                    'password';

                button.textContent =
                    'Hiện';
            }
        }
    );

});

</script>

@endsection