<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Staff - VELORA Eyes')
    </title>


    <link
        rel="stylesheet"
        href="{{ asset('css/style.css') }}"
    >

    @stack('styles')

</head>


<body class="staff-body">


<div class="staff-layout">


    {{-- =====================================================
        SIDEBAR
    ====================================================== --}}

    <aside
        class="staff-sidebar"
        id="staffSidebar"
    >


        {{-- BRAND --}}

        <div class="staff-sidebar-brand">

            <a
                href="{{ route('staff.dashboard') }}"
                class="staff-brand-link"
            >

                <span class="staff-brand-main">
                    VELORA
                </span>

                <span class="staff-brand-accent">
                    EYES
                </span>

            </a>


            <span class="staff-brand-role">
                STAFF PANEL
            </span>

        </div>



        {{-- USER --}}

        <div class="staff-user-card">

            <div class="staff-user-avatar">

                {{ strtoupper(
                    mb_substr(
                        auth()->user()->name,
                        0,
                        1
                    )
                ) }}

            </div>


            <div class="staff-user-info">

                <strong>
                    {{ auth()->user()->name }}
                </strong>

                <span>

                    {{ auth()->user()->position
                        ?: 'Nhân viên VELORA' }}

                </span>

            </div>

        </div>



        {{-- NAVIGATION --}}

        <nav class="staff-nav">


            <span class="staff-nav-label">
                TỔNG QUAN
            </span>


            <a
                href="{{ route('staff.dashboard') }}"
                class="staff-nav-item
                    {{ request()->routeIs('staff.dashboard')
                        ? 'active'
                        : '' }}"
            >

                <span class="staff-nav-icon">
                    ▦
                </span>

                <span>
                    Dashboard
                </span>

            </a>



            <span class="staff-nav-label">
                SẢN PHẨM & KHO
            </span>


            @if(Route::has('staff.categories.index'))

                <a
                    href="{{ route(
                        'staff.categories.index'
                    ) }}"
                    class="staff-nav-item
                        {{ request()->routeIs(
                            'staff.categories.*'
                        )
                            ? 'active'
                            : '' }}"
                >

                    <span class="staff-nav-icon">
                        ≡
                    </span>

                    <span>
                        Danh mục
                    </span>

                </a>

            @endif



            @if(Route::has('staff.products.index'))

                <a
                    href="{{ route(
                        'staff.products.index'
                    ) }}"
                    class="staff-nav-item
                        {{ request()->routeIs(
                            'staff.products.*'
                        )
                            ? 'active'
                            : '' }}"
                >

                    <span class="staff-nav-icon">
                        ◇
                    </span>

                    <span>
                        Sản phẩm
                    </span>

                </a>

            @endif



            @if(Route::has('staff.inventory.index'))

                <a
                    href="{{ route(
                        'staff.inventory.index'
                    ) }}"
                    class="staff-nav-item
                        {{ request()->routeIs(
                            'staff.inventory.*'
                        )
                            ? 'active'
                            : '' }}"
                >

                    <span class="staff-nav-icon">
                        ▤
                    </span>

                    <span>
                        Kho hàng
                    </span>

                </a>

            @endif



            <span class="staff-nav-label">
                BÁN HÀNG
            </span>


            @if(Route::has('staff.orders.index'))

                <a
                    href="{{ route(
                        'staff.orders.index'
                    ) }}"
                    class="staff-nav-item
                        {{ request()->routeIs(
                            'staff.orders.*'
                        )
                            ? 'active'
                            : '' }}"
                >

                    <span class="staff-nav-icon">
                        □
                    </span>

                    <span>
                        Đơn hàng
                    </span>

                </a>

            @endif



            <span class="staff-nav-label">
                CHĂM SÓC THỊ LỰC
            </span>


            @if(Route::has('staff.appointments.index'))

                <a
                    href="{{ route(
                        'staff.appointments.index'
                    ) }}"
                    class="staff-nav-item
                        {{ request()->routeIs(
                            'staff.appointments.*'
                        )
                        || request()->routeIs(
                            'staff.eye-prescriptions.*'
                        )
                            ? 'active'
                            : '' }}"
                >

                    <span class="staff-nav-icon">
                        ◷
                    </span>

                    <span>
                        Lịch đo mắt
                    </span>

                </a>

            @endif



            <span class="staff-nav-label">
                KHÁCH HÀNG
            </span>


            @if(Route::has('staff.reviews.index'))

                <a
                    href="{{ route(
                        'staff.reviews.index'
                    ) }}"
                    class="staff-nav-item
                        {{ request()->routeIs(
                            'staff.reviews.*'
                        )
                            ? 'active'
                            : '' }}"
                >

                    <span class="staff-nav-icon">
                        ★
                    </span>

                    <span>
                        Đánh giá
                    </span>

                </a>

            @endif

        </nav>



        {{-- BOTTOM --}}

        <div class="staff-sidebar-bottom">

            <a
                href="{{ route('home') }}"
                class="staff-nav-item"
            >

                <span class="staff-nav-icon">
                    ←
                </span>

                <span>
                    Về website
                </span>

            </a>


            <form
                action="{{ route('logout') }}"
                method="POST"
            >

                @csrf


                <button
                    type="submit"
                    class="staff-logout-button"
                >

                    <span class="staff-nav-icon">
                        ↪
                    </span>

                    <span>
                        Đăng xuất
                    </span>

                </button>

            </form>

        </div>

    </aside>



    {{-- =====================================================
        MAIN
    ====================================================== --}}

    <div class="staff-main">


        {{-- TOPBAR --}}

        <header class="staff-topbar">

            <div class="staff-topbar-left">

                <button
                    type="button"
                    class="staff-menu-toggle"
                    id="staffMenuToggle"
                    aria-label="Mở menu"
                >
                    ☰
                </button>


                <div>

                    <span class="staff-topbar-label">
                        VELORA Eyes
                    </span>

                    <strong>
                        @yield('page-title', 'Staff Dashboard')
                    </strong>

                </div>

            </div>


            <div class="staff-topbar-right">

                <div class="staff-topbar-user">

                    <div class="staff-topbar-avatar">

                        {{ strtoupper(
                            mb_substr(
                                auth()->user()->name,
                                0,
                                1
                            )
                        ) }}

                    </div>


                    <div>

                        <strong>
                            {{ auth()->user()->name }}
                        </strong>

                        <span>
                            Staff
                        </span>

                    </div>

                </div>

            </div>

        </header>



        {{-- PAGE CONTENT --}}

        <main class="staff-content">

            @if(session('success'))

                <div class="staff-alert staff-alert-success">
                    {{ session('success') }}
                </div>

            @endif


            @if(session('error'))

                <div class="staff-alert staff-alert-danger">
                    {{ session('error') }}
                </div>

            @endif


            @if($errors->any())

                <div class="staff-alert staff-alert-danger">

                    <strong>
                        Có lỗi xảy ra:
                    </strong>


                    <ul>

                        @foreach($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            @yield('content')

        </main>

    </div>

</div>



<script>

const staffMenuToggle =
    document.getElementById(
        'staffMenuToggle'
    );

const staffSidebar =
    document.getElementById(
        'staffSidebar'
    );


if (
    staffMenuToggle
    && staffSidebar
) {

    staffMenuToggle.addEventListener(
        'click',
        function () {

            staffSidebar.classList.toggle(
                'open'
            );

        }
    );

}

</script>


@stack('scripts')

</body>

</html>