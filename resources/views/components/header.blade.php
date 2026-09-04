<header class="site-header">

    <div class="velora-container header-inner">

        {{-- =====================================================
            LOGO
        ====================================================== --}}

        <a
            href="{{ route('home') }}"
            class="brand"
        >

            <div class="brand-name">
                VELORA <span>EYES</span>
            </div>

        </a>



        {{-- =====================================================
            MAIN NAVIGATION
        ====================================================== --}}

        <nav class="main-nav">

            <a
                href="{{ route('home') }}"
                class="{{ request()->routeIs('home') ? 'active' : '' }}"
            >
                Trang chủ
            </a>


            <a
                href="{{ route('products.index') }}"
                class="{{ request()->routeIs('products.*') ? 'active' : '' }}"
            >
                Sản phẩm
            </a>


            @if(Route::has('warranties.lookup-form'))

                <a
                    href="{{ route('warranties.lookup-form') }}"
                    class="{{ request()->routeIs('warranties.lookup*') ? 'active' : '' }}"
                >
                    Tra cứu bảo hành
                </a>

            @endif



            @auth

                @if(auth()->user()->isCustomer())

                    @if(Route::has('appointments.index'))

                        <a
                            href="{{ route('appointments.index') }}"
                            class="{{ request()->routeIs('appointments.*') ? 'active' : '' }}"
                        >
                            Lịch đo mắt
                        </a>

                    @endif


                    @if(Route::has('orders.index'))

                        <a
                            href="{{ route('orders.index') }}"
                            class="{{ request()->routeIs('orders.*') ? 'active' : '' }}"
                        >
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

                <a
                    href="{{ route('login') }}"
                    class="btn btn-outline btn-sm"
                >
                    Đăng nhập
                </a>


                <a
                    href="{{ route('register') }}"
                    class="btn btn-primary btn-sm"
                >
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

                        <a
                            href="{{ route('wishlist.index') }}"
                            class="btn btn-outline btn-sm"
                        >
                            ♡ Yêu thích
                        </a>

                    @endif



                    {{-- CART --}}

                    @if(Route::has('cart.index'))

                        <a
                            href="{{ route('cart.index') }}"
                            class="btn btn-outline btn-sm"
                        >

                            Giỏ hàng

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

                        <summary class="header-account-toggle">

                            <span class="header-account-avatar">

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


                            <span class="header-account-arrow">
                                ▾
                            </span>

                        </summary>



                        <div class="header-account-menu">


                            {{-- ACCOUNT --}}

                            @if(Route::has('profile.show'))

                                <a
                                    href="{{ route('profile.show') }}"
                                    class="{{ request()->routeIs('profile.show') ? 'active' : '' }}"
                                >

                                    <span class="account-menu-icon">
                                        ◉
                                    </span>

                                    <span>

                                        <strong>
                                            Tài khoản của tôi
                                        </strong>

                                        <small>
                                            Thông tin cá nhân
                                        </small>

                                    </span>

                                </a>

                            @endif



                            {{-- EDIT PROFILE --}}

                            @if(Route::has('profile.edit'))

                                <a
                                    href="{{ route('profile.edit') }}"
                                >

                                    <span class="account-menu-icon">
                                        ✎
                                    </span>

                                    <span>
                                        Chỉnh sửa hồ sơ
                                    </span>

                                </a>

                            @endif



                            {{-- ADDRESS --}}

                            @if(Route::has('addresses.index'))

                                <a
                                    href="{{ route('addresses.index') }}"
                                >

                                    <span class="account-menu-icon">
                                        ⌂
                                    </span>

                                    <span>
                                        Địa chỉ của tôi
                                    </span>

                                </a>

                            @endif



                            <div class="account-menu-divider">
                            </div>



                            {{-- ORDERS --}}

                            @if(Route::has('orders.index'))

                                <a
                                    href="{{ route('orders.index') }}"
                                >

                                    <span class="account-menu-icon">
                                        □
                                    </span>

                                    <span>
                                        Đơn hàng của tôi
                                    </span>

                                </a>

                            @endif



                            {{-- APPOINTMENTS --}}

                            @if(Route::has('appointments.index'))

                                <a
                                    href="{{ route('appointments.index') }}"
                                >

                                    <span class="account-menu-icon">
                                        ◷
                                    </span>

                                    <span>
                                        Lịch đo mắt
                                    </span>

                                </a>

                            @endif



                            {{-- EYE PRESCRIPTION --}}

                            @if(Route::has('eye-prescriptions.index'))

                                <a
                                    href="{{ route('eye-prescriptions.index') }}"
                                >

                                    <span class="account-menu-icon">
                                        ◉
                                    </span>

                                    <span>
                                        Hồ sơ thị lực
                                    </span>

                                </a>

                            @endif



                            {{-- WARRANTIES --}}

                            @if(Route::has('warranties.index'))

                                <a
                                    href="{{ route('warranties.index') }}"
                                >

                                    <span class="account-menu-icon">
                                        ◇
                                    </span>

                                    <span>
                                        Bảo hành của tôi
                                    </span>

                                </a>

                            @endif

{{-- CUSTOMER CHAT --}}

@if(Route::has('customer.chat.index'))

    <a
        href="{{ route('customer.chat.index') }}"
        class="{{ request()->routeIs('customer.chat.*') ? 'active' : '' }}"
    >

        <span class="account-menu-icon">
            ✉
        </span>

        <span>
            Tư vấn trực tuyến
        </span>

    </a>

@endif

                            <div class="account-menu-divider">
                            </div>



                            {{-- CHANGE PASSWORD --}}

                            @if(Route::has('profile.password.edit'))

                                <a
                                    href="{{ route(
                                        'profile.password.edit'
                                    ) }}"
                                >

                                    <span class="account-menu-icon">
                                        🔒
                                    </span>

                                    <span>
                                        Đổi mật khẩu
                                    </span>

                                </a>

                            @endif



                            {{-- LOGOUT --}}

                            <form
                                action="{{ route('logout') }}"
                                method="POST"
                                class="header-logout-form"
                            >

                                @csrf


                                <button
                                    type="submit"
                                    class="header-logout-button"
                                >

                                    <span class="account-menu-icon">
                                        ↪
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

                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="btn btn-primary btn-sm"
                    >
                        Admin Dashboard
                    </a>


                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                    >

                        @csrf


                        <button
                            type="submit"
                            class="btn btn-outline btn-sm"
                        >
                            Đăng xuất
                        </button>

                    </form>



                {{-- =============================================
                    STAFF
                ============================================== --}}

                @elseif(auth()->user()->isStaff())

                    <a
                        href="{{ route('staff.dashboard') }}"
                        class="btn btn-primary btn-sm"
                    >
                        Staff Dashboard
                    </a>


                    <form
                        action="{{ route('logout') }}"
                        method="POST"
                    >

                        @csrf


                        <button
                            type="submit"
                            class="btn btn-outline btn-sm"
                        >
                            Đăng xuất
                        </button>

                    </form>

                @endif

            @endguest

        </div>

    </div>

</header>