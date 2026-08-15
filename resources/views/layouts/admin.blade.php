<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield(
            'title',
            'Admin - VELORA Eyes'
        )
    </title>


    <link
        rel="stylesheet"
        href="{{ asset(
            'css/style.css'
        ) }}"
    >
    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

</head>


<body class="admin-body">


<div
    class="admin-sidebar-overlay"
    id="adminSidebarOverlay"
></div>



{{-- =========================================================
    SIDEBAR
========================================================= --}}

<aside
    class="admin-sidebar"
    id="adminSidebar"
>

    {{-- BRAND --}}

    <div class="admin-sidebar-brand">

        <a href="{{ route('admin.dashboard') }}">

            <strong>
                VELORA
            </strong>

            <span>
                EYES
            </span>

        </a>


        <small>
            ADMIN PANEL
        </small>

    </div>



    {{-- USER --}}

    <div class="admin-user-card">

        <div class="admin-user-avatar">

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
                Administrator
            </span>

        </div>

    </div>



    {{-- NAVIGATION --}}

    <nav class="admin-nav">


        <span class="admin-nav-label">
            TỔNG QUAN
        </span>


        <a
            href="{{ route(
                'admin.dashboard'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.dashboard'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
            <i class="bi bi-grid-1x2-fill"></i>
            </span>

            Dashboard
        </a>



        <span class="admin-nav-label">
            NGƯỜI DÙNG
        </span>


        <a
            href="{{ route(
                'admin.customers.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.customers.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-people"></i>
            </span>

            Khách hàng
        </a>


        <a
            href="{{ route(
                'admin.staff.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.staff.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-person-badge"></i>
            </span>

            Nhân viên
        </a>



        <span class="admin-nav-label">
            SẢN PHẨM
        </span>


        <a
            href="{{ route(
                'admin.categories.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.categories.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-tags"></i>
            </span>

            Danh mục
        </a>


        <a
            href="{{ route(
                'admin.products.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.products.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-eyeglasses"></i>
            </span>

            Sản phẩm
        </a>


        <a
            href="{{ route(
                'admin.inventory.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.inventory.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-box-seam"></i>
            </span>

            Tồn kho
        </a>


        <a
            href="{{ route(
                'admin.vouchers.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.vouchers.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-ticket-perforated"></i>
            </span>

            Voucher
        </a>



        <span class="admin-nav-label">
            KINH DOANH
        </span>


        <a
            href="{{ route(
                'admin.orders.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.orders.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-receipt"></i>
            </span>

            Đơn hàng
        </a>


        <a
            href="{{ route(
                'admin.payments.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.payments.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-credit-card"></i>
            </span>

            Thanh toán
        </a>



        <span class="admin-nav-label">
            CHĂM SÓC MẮT
        </span>


        <a
            href="{{ route(
                'admin.appointments.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.appointments.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-calendar2-check"></i>
            </span>

            Lịch đo mắt
        </a>


        <a
            href="{{ route(
                'admin.eye-prescriptions.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.eye-prescriptions.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-eye"></i>
            </span>

            Hồ sơ thị lực
        </a>


        <a
            href="{{ route(
                'admin.warranties.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.warranties.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-shield-check"></i>
            </span>

            Bảo hành
        </a>



        <span class="admin-nav-label">
            HỆ THỐNG
        </span>


        <a
            href="{{ route(
                'admin.reviews.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.reviews.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                 <i class="bi bi-star"></i>
            </span>

            Đánh giá
        </a>


        <a
            href="{{ route(
                'admin.reports.index'
            ) }}"
            class="{{
                request()->routeIs(
                    'admin.reports.*'
                )
                    ? 'active'
                    : ''
            }}"
        >
            <span class="admin-nav-icon">
                <i class="bi bi-bar-chart-line"></i>
            </span>

            Báo cáo
        </a>

    </nav>



    {{-- FOOTER SIDEBAR --}}

    <div class="admin-sidebar-footer">

        <a
            href="{{ route('home') }}"
            target="_blank"
        >
            ← Xem website
        </a>


        <form
            action="{{ route('logout') }}"
            method="POST"
        >

            @csrf


            <button type="submit">
                Đăng xuất
            </button>

        </form>

    </div>

</aside>



{{-- =========================================================
    MAIN
========================================================= --}}

<div class="admin-main">


    {{-- TOPBAR --}}

    <header class="admin-topbar">

        <div>

            <button
                type="button"
                class="admin-mobile-menu"
                id="adminMobileMenu"
                aria-label="Mở menu"
            >
                ☰
            </button>


            <div class="admin-topbar-title">

                <small>
                    VELORA EYES
                </small>

                <strong>
                    @yield(
                        'page-title',
                        'Admin'
                    )
                </strong>

            </div>

        </div>


        <div class="admin-topbar-user">

            <span>
                {{ auth()->user()->name }}
            </span>


            <div>

                {{ strtoupper(
                    mb_substr(
                        auth()->user()->name,
                        0,
                        1
                    )
                ) }}

            </div>

        </div>

    </header>



    {{-- CONTENT --}}

    <main class="admin-content">


        {{-- FLASH SUCCESS --}}

        @if(session('success'))

            <div class="admin-alert admin-alert-success">

                <strong>
                    Thành công
                </strong>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif



        {{-- FLASH ERROR --}}

        @if(session('error'))

            <div class="admin-alert admin-alert-danger">

                <strong>
                    Có lỗi xảy ra
                </strong>

                <span>
                    {{ session('error') }}
                </span>

            </div>

        @endif



        {{-- VALIDATION --}}

        @if($errors->any())

            <div class="admin-alert admin-alert-danger">

                <strong>
                    Vui lòng kiểm tra lại dữ liệu
                </strong>

                <span>
                    {{ $errors->first() }}
                </span>

            </div>

        @endif



        @yield('content')

    </main>

</div>



<script>

    const adminSidebar =
        document.getElementById(
            'adminSidebar'
        );

    const adminOverlay =
        document.getElementById(
            'adminSidebarOverlay'
        );

    const adminMobileMenu =
        document.getElementById(
            'adminMobileMenu'
        );


    function openAdminSidebar() {

        adminSidebar
            ?.classList
            .add('open');

        adminOverlay
            ?.classList
            .add('show');

    }


    function closeAdminSidebar() {

        adminSidebar
            ?.classList
            .remove('open');

        adminOverlay
            ?.classList
            .remove('show');

    }


    adminMobileMenu
        ?.addEventListener(
            'click',
            openAdminSidebar
        );


    adminOverlay
        ?.addEventListener(
            'click',
            closeAdminSidebar
        );

</script>


@stack('scripts')

</body>

</html>