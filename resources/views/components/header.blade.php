<header class="site-header">

  <div class="vl-container header-inner">

    {{-- =====================================================
            LOGO
        ====================================================== --}}

    <a href="{{ route('home') }}" class="brand" aria-label="VELORA EYES Trang chủ">

      <div class="brand-name">
        VELORA <span>EYES</span>
      </div>

    </a>



    {{-- =====================================================
            MAIN NAVIGATION
        ====================================================== --}}

    <nav class="main-nav" aria-label="Điều hướng chính">

      <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
        Trang chủ
      </a>


      <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
        Sản phẩm
      </a>


      @if(Route::has('warranties.lookup-form'))

      <a href="{{ route('warranties.lookup-form') }}"
        class="{{ request()->routeIs('warranties.lookup*') ? 'active' : '' }}">
        Tra cứu bảo hành
      </a>

      @endif



      @auth

      @if(auth()->user()->isCustomer())

      @if(Route::has('appointments.index'))

      <a href="{{ route('appointments.index') }}" class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}">
        Lịch đo mắt
      </a>

      @endif
      @if(Route::has('vouchers.index'))

      <a href="{{ route('vouchers.index') }}" class="{{ request()->routeIs('vouchers.*') ? 'active' : '' }}">
        Kho voucher
      </a>

      @endif

      @if(Route::has('orders.index'))

      <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
        Đơn hàng
      </a>

      @endif

      @endif

      @endauth

    </nav>



    {{-- =====================================================
            HEADER ACTIONS
        ====================================================== --}}

    <div class="header-actions">


      {{-- =================================================
                GUEST
            ================================================== --}}

      @guest

      <a href="{{ route('login') }}" class="vl-btn vl-btn-outline vl-btn-sm">
        Đăng nhập
      </a>


      <a href="{{ route('register') }}" class="vl-btn vl-btn-primary vl-btn-sm">
        Đăng ký
      </a>



      {{-- =================================================
                AUTHENTICATED
            ================================================== --}}

      @else


      {{-- =============================================
                    CUSTOMER
                ============================================== --}}

      @if(auth()->user()->isCustomer())

      @php

      $headerCart =
      auth()->user()
      ->cart()
      ->first();


      $headerCartQuantity =
      $headerCart
      ? $headerCart->total_quantity
      : 0;

      @endphp



      {{-- WISHLIST --}}

      @if(Route::has('wishlist.index'))

      <a href="{{ route('wishlist.index') }}" class="vl-btn vl-btn-outline vl-btn-sm" aria-label="Danh sách yêu thích">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
        </svg>
        <span>Yêu thích</span>
      </a>

      @endif



      {{-- CART --}}

      @if(Route::has('cart.index'))

      <a href="{{ route('cart.index') }}" class="vl-btn vl-btn-outline vl-btn-sm" aria-label="Giỏ hàng">
        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
          <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <path d="M16 10a4 4 0 0 1-8 0"></path>
        </svg>
        <span>Giỏ hàng</span>

        @if($headerCartQuantity > 0)

        <span class="header-cart-count">
          {{ $headerCartQuantity }}
        </span>

        @endif

      </a>

      @endif



      {{-- =========================================
                        CUSTOMER ACCOUNT DROPDOWN
                    ========================================== --}}

      <details class="header-account">

        <summary class="header-account-toggle" aria-haspopup="true">

          <span class="header-account-avatar" aria-hidden="true">

            {{ strtoupper(
                mb_substr(
                    auth()->user()->name,
                    0,
                    1
                )
            ) }}

          </span>


          <span class="header-account-name">

            <small>
              Xin chào
            </small>

            <strong>
              {{ auth()->user()->name }}
            </strong>

          </span>


          <span class="header-account-arrow" aria-hidden="true">
            ▾
          </span>

        </summary>



        <div class="header-account-menu" role="menu">


          {{-- ACCOUNT --}}

          @if(Route::has('profile.show'))

          <a href="{{ route('profile.show') }}" class="{{ request()->routeIs('profile.show') ? 'active' : '' }}">

            <span class="account-menu-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>
            </span>

            <span>
              <strong>Tài khoản của tôi</strong>
              <small style="display:block;color:var(--vl-text-muted);">Thông tin cá nhân</small>
            </span>

          </a>

          @endif



          {{-- EDIT PROFILE --}}

          @if(Route::has('profile.edit'))

          <a href="{{ route('profile.edit') }}">

            <span class="account-menu-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
              </svg>
            </span>

            <span>Chỉnh sửa hồ sơ</span>

          </a>

          @endif



          {{-- ADDRESS --}}

          @if(Route::has('addresses.index'))

          <a href="{{ route('addresses.index') }}">

            <span class="account-menu-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
            </span>

            <span>Địa chỉ của tôi</span>

          </a>

          @endif



          <div class="account-menu-divider" aria-hidden="true"></div>



          {{-- ORDERS --}}

          @if(Route::has('orders.index'))

          <a href="{{ route('orders.index') }}">

            <span class="account-menu-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
              </svg>
            </span>

            <span>Đơn hàng của tôi</span>

          </a>

          @endif



          {{-- APPOINTMENTS --}}

          @if(Route::has('appointments.index'))

          <a href="{{ route('appointments.index') }}">

            <span class="account-menu-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </span>

            <span>Lịch đo mắt</span>

          </a>

          @endif



          {{-- EYE PRESCRIPTION --}}

          @if(Route::has('eye-prescriptions.index'))

          <a href="{{ route('eye-prescriptions.index') }}">

            <span class="account-menu-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
              </svg>
            </span>

            <span>Hồ sơ thị lực</span>

          </a>

          @endif



          {{-- WARRANTIES --}}

          @if(Route::has('warranties.index'))

          <a href="{{ route('warranties.index') }}">

            <span class="account-menu-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
            </span>

            <span>Bảo hành của tôi</span>

          </a>

          @endif

          {{-- CUSTOMER CHAT --}}

          @if(Route::has('customer.chat.index'))

          <a href="{{ route('customer.chat.index') }}"
            class="{{ request()->routeIs('customer.chat.*') ? 'active' : '' }}">

            <span class="account-menu-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
            </span>

            <span>Tư vấn trực tuyến</span>

          </a>

          @endif

          <div class="account-menu-divider" aria-hidden="true"></div>



          {{-- CHANGE PASSWORD --}}

          @if(Route::has('profile.password.edit'))

          <a href="{{ route('profile.password.edit') }}">

            <span class="account-menu-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </span>

            <span>Đổi mật khẩu</span>

          </a>

          @endif



          {{-- LOGOUT --}}

          <form action="{{ route('logout') }}" method="POST" class="header-logout-form">

            @csrf


            <button type="submit" class="header-logout-button">

              <span class="account-menu-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                  <polyline points="16 17 21 12 16 7"></polyline>
                  <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
              </span>

              <span>
                Đăng xuất
              </span>

            </button>

          </form>

        </div>

      </details>



      {{-- =============================================
                    ADMIN
                ============================================== --}}

      @elseif(auth()->user()->isAdmin())

      <a href="{{ route('admin.dashboard') }}" class="vl-btn vl-btn-primary vl-btn-sm">
        Admin Dashboard
      </a>


      <form action="{{ route('logout') }}" method="POST" style="margin:0;">

        @csrf


        <button type="submit" class="vl-btn vl-btn-outline vl-btn-sm">
          Đăng xuất
        </button>

      </form>

      @endif

      @endguest

    </div>

  </div>

</header>
