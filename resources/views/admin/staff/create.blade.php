@extends('layouts.admin')


@section(
    'title',
    'Thêm nhân viên - VELORA Eyes'
)


@section(
    'page-title',
    'Thêm nhân viên'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            CREATE STAFF
        </span>

        <h1>
            Thêm nhân viên
        </h1>

        <p>
            Tạo tài khoản Staff mới
            cho hệ thống VELORA Eyes.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.staff.index'
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Danh sách nhân viên
    </a>

</div>



<form
    action="{{ route(
        'admin.staff.store'
    ) }}"
    method="POST"
    class="admin-staff-form-layout"
>

    @csrf


    <div class="admin-staff-form-main">


        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin nhân viên
                    </h2>

                    <p>
                        Các thông tin cơ bản của tài khoản.
                    </p>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-grid">


                    <div class="admin-form-group">

                        <label for="name">
                            Họ và tên
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            maxlength="100"
                            class="admin-form-control
                                @error('name')
                                    admin-input-error
                                @enderror"
                            placeholder="Ví dụ: Nguyễn Minh Anh"
                            required
                        >

                        @error('name')

                            <div class="admin-field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="position">
                            Chức vụ
                        </label>

                        <input
                            type="text"
                            id="position"
                            name="position"
                            value="{{ old('position') }}"
                            maxlength="100"
                            class="admin-form-control
                                @error('position')
                                    admin-input-error
                                @enderror"
                            placeholder="Ví dụ: Tư vấn viên"
                        >

                        @error('position')

                            <div class="admin-field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="email">
                            Email
                            <span>*</span>
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            maxlength="255"
                            class="admin-form-control
                                @error('email')
                                    admin-input-error
                                @enderror"
                            placeholder="nhanvien@velora.vn"
                            required
                        >

                        @error('email')

                            <div class="admin-field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="phone">
                            Số điện thoại
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            maxlength="10"
                            inputmode="numeric"
                            class="admin-form-control
                                @error('phone')
                                    admin-input-error
                                @enderror"
                            placeholder="0912345678"
                            required
                        >

                        @error('phone')

                            <div class="admin-field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>

                </div>

            </div>

        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin đăng nhập
                    </h2>

                    <p>
                        Mật khẩu tối thiểu 8 ký tự.
                    </p>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-grid">

                    <div class="admin-form-group">

                        <label for="password">
                            Mật khẩu
                            <span>*</span>
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            minlength="8"
                            class="admin-form-control
                                @error('password')
                                    admin-input-error
                                @enderror"
                            placeholder="Tối thiểu 8 ký tự"
                            required
                        >

                        @error('password')

                            <div class="admin-field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="password_confirmation">
                            Xác nhận mật khẩu
                            <span>*</span>
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            minlength="8"
                            class="admin-form-control"
                            placeholder="Nhập lại mật khẩu"
                            required
                        >

                    </div>

                </div>

            </div>

        </section>

    </div>



    {{-- SIDEBAR --}}

    <aside class="admin-staff-form-sidebar">


        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>
                        Vai trò
                    </h2>
                </div>

            </div>


            <div class="admin-staff-role-card">

                <div>

                    <i class="bi bi-person-badge"></i>

                </div>


                <span>

                    <strong>
                        Staff
                    </strong>

                    <small>
                        Vai trò được gán tự động bởi hệ thống.
                    </small>

                </span>

            </div>

        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>
                        Trạng thái
                    </h2>
                </div>

            </div>


            <div class="admin-staff-switch">

                <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    value="1"
                    {{
                        old(
                            'is_active',
                            true
                        )
                            ? 'checked'
                            : ''
                    }}
                >

                <label for="is_active">

                    <span></span>

                    <div>

                        <strong>
                            Tài khoản hoạt động
                        </strong>

                        <small>
                            Cho phép Staff đăng nhập hệ thống.
                        </small>

                    </div>

                </label>

            </div>

        </section>



        <section class="admin-panel admin-form-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary admin-btn-full"
            >
                <i class="bi bi-person-plus"></i>

                Tạo nhân viên
            </button>


            <a
                href="{{ route(
                    'admin.staff.index'
                ) }}"
                class="admin-btn admin-btn-secondary admin-btn-full"
            >
                Hủy
            </a>

        </section>

    </aside>

</form>

@endsection