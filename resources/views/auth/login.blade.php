@extends('layouts.app')


@section('title', 'Đăng nhập - VELORA Eyes')


@section(
'meta_description',
'Đăng nhập tài khoản VELORA Eyes để quản lý đơn hàng, sản phẩm yêu thích, bảo hành và trải nghiệm mua sắm.'
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


        <form action="{{ route('login.store') }}" method="POST">

          @csrf


          {{-- =======================================
                        EMAIL
                    ======================================== --}}

          <div class="form-group">

            <label for="email" class="form-label">
              Email
            </label>


            <input type="email" id="email" name="email" class="form-control @error('email') input-error @enderror"
              value="{{ old('email') }}" placeholder="example@email.com" autocomplete="email" required autofocus>


            @error('email')

            <div class="field-error">
              {{ $message }}
            </div>

            @enderror

          </div>


          {{-- =======================================
                        PASSWORD
                    ======================================== --}}

          <div class="form-group">

            <label for="password" class="form-label">
              Mật khẩu
            </label>


            <div class="auth-password-wrap">

              <input type="password" id="password" name="password"
                class="form-control auth-password-input @error('password') input-error @enderror"
                placeholder="Nhập mật khẩu" autocomplete="current-password" required>


              <button type="button" id="togglePassword" class="auth-password-toggle" aria-label="Hiện mật khẩu"
                title="Hiện mật khẩu">

                {{-- EYE OPEN --}}

                <svg id="passwordEyeOpen" viewBox="0 0 24 24" aria-hidden="true">

                  <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>

                  <circle cx="12" cy="12" r="3"></circle>

                </svg>


                {{-- EYE CLOSED --}}

                <svg id="passwordEyeClosed" viewBox="0 0 24 24" aria-hidden="true" style="display: none;">

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
                        REMEMBER
                    ======================================== --}}

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


          {{-- =======================================
                        SUBMIT
                    ======================================== --}}

          <button type="submit" class="btn btn-primary auth-submit">
            Đăng nhập
          </button>

        </form>


        {{-- ===========================================
                    REGISTER
                ============================================ --}}

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
  function() {

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
      function() {

        const isHidden =
          passwordInput.type ===
          'password';


        /*
        |--------------------------------------------------------------------------
        | ĐỔI KIỂU INPUT
        |--------------------------------------------------------------------------
        */

        passwordInput.type =
          isHidden ?
          'text' :
          'password';


        /*
        |--------------------------------------------------------------------------
        | ĐỔI ICON
        |--------------------------------------------------------------------------
        */

        eyeOpen.style.display =
          isHidden ?
          'none' :
          'block';


        eyeClosed.style.display =
          isHidden ?
          'block' :
          'none';


        /*
        |--------------------------------------------------------------------------
        | ACCESSIBILITY
        |--------------------------------------------------------------------------
        */

        const label =
          isHidden ?
          'Ẩn mật khẩu' :
          'Hiện mật khẩu';


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