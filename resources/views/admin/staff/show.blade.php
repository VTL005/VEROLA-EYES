@extends('layouts.admin')


@section(
    'title',
    $staff->name . ' - Staff'
)


@section(
    'page-title',
    'Chi tiết nhân viên'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            STAFF PROFILE
        </span>

        <h1>
            {{ $staff->name }}
        </h1>

        <p>
            Staff #{{ $staff->id }}
            ·
            {{ $staff->position
                ?: 'Chưa cập nhật chức vụ' }}
        </p>

    </div>


    <div class="admin-staff-header-actions">

        <a
            href="{{ route(
                'admin.staff.index'
            ) }}"
            class="admin-btn admin-btn-secondary"
        >
            <i class="bi bi-arrow-left"></i>

            Danh sách
        </a>


        <a
            href="{{ route(
                'admin.staff.edit',
                $staff
            ) }}"
            class="admin-btn admin-btn-primary"
        >
            <i class="bi bi-pencil"></i>

            Chỉnh sửa
        </a>

    </div>

</div>



<div class="admin-staff-profile-card">

    <div class="admin-staff-profile-avatar">

        {{ strtoupper(
            mb_substr(
                $staff->name,
                0,
                1
            )
        ) }}

    </div>


    <div class="admin-staff-profile-main">

        <h2>
            {{ $staff->name }}
        </h2>

        <span>
            {{ $staff->email }}
        </span>

        <small>
            {{ $staff->position
                ?: 'Nhân viên VELORA Eyes' }}
        </small>

    </div>


    @if($staff->is_active)

        <span class="admin-status success">
            Hoạt động
        </span>

    @else

        <span class="admin-status danger">
            Đã khóa
        </span>

    @endif

</div>



<div class="admin-staff-show-layout">


    <div class="admin-staff-show-main">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin nhân viên
                    </h2>

                </div>

            </div>


            <div class="admin-staff-info-grid">

                <div>

                    <span>
                        Họ và tên
                    </span>

                    <strong>
                        {{ $staff->name }}
                    </strong>

                </div>


                <div>

                    <span>
                        Chức vụ
                    </span>

                    <strong>
                        {{ $staff->position
                            ?: 'Chưa cập nhật' }}
                    </strong>

                </div>


                <div>

                    <span>
                        Email
                    </span>

                    <strong>
                        {{ $staff->email }}
                    </strong>

                </div>


                <div>

                    <span>
                        Số điện thoại
                    </span>

                    <strong>
                        {{ $staff->phone }}
                    </strong>

                </div>


                <div>

                    <span>
                        Vai trò
                    </span>

                    <strong>
                        Staff
                    </strong>

                </div>


                <div>

                    <span>
                        ID tài khoản
                    </span>

                    <strong>
                        #{{ $staff->id }}
                    </strong>

                </div>

            </div>

        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Lịch sử tài khoản
                    </h2>

                </div>

            </div>


            <div class="admin-staff-timestamps">

                <div>

                    <i class="bi bi-calendar-plus"></i>

                    <span>

                        <small>
                            Ngày tạo
                        </small>

                        <strong>

                            {{ $staff
                                ->created_at
                                ->format(
                                    'd/m/Y H:i'
                                ) }}

                        </strong>

                    </span>

                </div>


                <div>

                    <i class="bi bi-clock-history"></i>

                    <span>

                        <small>
                            Cập nhật gần nhất
                        </small>

                        <strong>

                            {{ $staff
                                ->updated_at
                                ->format(
                                    'd/m/Y H:i'
                                ) }}

                        </strong>

                    </span>

                </div>

            </div>

        </section>

    </div>



    {{-- SIDEBAR --}}

    <aside class="admin-staff-show-sidebar">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Quản lý tài khoản
                    </h2>

                </div>

            </div>


            @if($staff->is_active)

                <div class="admin-staff-account-state active">

                    <i class="bi bi-check-circle"></i>

                    <div>

                        <strong>
                            Đang hoạt động
                        </strong>

                        <span>
                            Nhân viên có thể đăng nhập Staff Panel.
                        </span>

                    </div>

                </div>

            @else

                <div class="admin-staff-account-state inactive">

                    <i class="bi bi-slash-circle"></i>

                    <div>

                        <strong>
                            Đang bị khóa
                        </strong>

                        <span>
                            Nhân viên không thể sử dụng tài khoản.
                        </span>

                    </div>

                </div>

            @endif


            <div class="admin-staff-account-buttons">

                <a
                    href="{{ route(
                        'admin.staff.edit',
                        $staff
                    ) }}"
                    class="admin-btn admin-btn-secondary admin-btn-full"
                >
                    <i class="bi bi-pencil"></i>

                    Chỉnh sửa thông tin
                </a>


                <form
                    action="{{ route(
                        'admin.staff.toggle-active',
                        $staff
                    ) }}"
                    method="POST"
                    onsubmit="
                        return confirm(
                            '{{ $staff->is_active
                                ? 'Bạn có chắc muốn khóa tài khoản nhân viên này?'
                                : 'Bạn có chắc muốn mở khóa tài khoản nhân viên này?' }}'
                        );
                    "
                >

                    @csrf
                    @method('PATCH')


                    @if($staff->is_active)

                        <button
                            type="submit"
                            class="admin-btn admin-btn-danger admin-btn-full"
                        >
                            <i class="bi bi-lock"></i>

                            Khóa tài khoản
                        </button>

                    @else

                        <button
                            type="submit"
                            class="admin-btn admin-btn-primary admin-btn-full"
                        >
                            <i class="bi bi-unlock"></i>

                            Mở khóa tài khoản
                        </button>

                    @endif

                </form>

            </div>

        </section>


        <section class="admin-staff-security-note">

            <i class="bi bi-shield-check"></i>

            <div>

                <strong>
                    Quyền Staff
                </strong>

                <p>
                    Role của tài khoản được quản lý
                    ở Backend và không thể thay đổi
                    từ form này.
                </p>

            </div>

        </section>

    </aside>

</div>

@endsection