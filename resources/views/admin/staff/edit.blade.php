@extends('layouts.admin')


@section(
    'title',
    'Sửa ' . $staff->name
)


@section(
    'page-title',
    'Sửa nhân viên'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            EDIT STAFF
        </span>

        <h1>
            Chỉnh sửa nhân viên
        </h1>

        <p>
            Cập nhật thông tin của
            <strong>{{ $staff->name }}</strong>.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.staff.show',
            $staff
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Chi tiết nhân viên
    </a>

</div>



<form
    action="{{ route(
        'admin.staff.update',
        $staff
    ) }}"
    method="POST"
    class="admin-staff-form-layout"
>

    @csrf
    @method('PUT')


    <div class="admin-staff-form-main">


        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin nhân viên
                    </h2>

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
                            value="{{ old(
                                'name',
                                $staff->name
                            ) }}"
                            maxlength="100"
                            class="admin-form-control
                                @error('name')
                                    admin-input-error
                                @enderror"
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
                            value="{{ old(
                                'position',
                                $staff->position
                            ) }}"
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
                            value="{{ old(
                                'email',
                                $staff->email
                            ) }}"
                            maxlength="255"
                            class="admin-form-control
                                @error('email')
                                    admin-input-error
                                @enderror"
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
                            value="{{ old(
                                'phone',
                                $staff->phone
                            ) }}"
                            maxlength="10"
                            inputmode="numeric"
                            class="admin-form-control
                                @error('phone')
                                    admin-input-error
                                @enderror"
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
                        Đổi mật khẩu
                    </h2>

                    <p>
                        Để trống nếu muốn giữ mật khẩu hiện tại.
                    </p>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-grid">

                    <div class="admin-form-group">

                        <label for="password">
                            Mật khẩu mới
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
                            placeholder="Để trống nếu không đổi"
                        >

                        @error('password')

                            <div class="admin-field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="password_confirmation">
                            Xác nhận mật khẩu mới
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            minlength="8"
                            class="admin-form-control"
                            placeholder="Nhập lại mật khẩu mới"
                        >

                    </div>

                </div>

            </div>

        </section>

    </div>



    <aside class="admin-staff-form-sidebar">


        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>
                        Tài khoản
                    </h2>
                </div>

            </div>


            <div class="admin-staff-edit-user">

                <div>

                    {{ strtoupper(
                        mb_substr(
                            $staff->name,
                            0,
                            1
                        )
                    ) }}

                </div>


                <span>

                    <strong>
                        {{ $staff->name }}
                    </strong>

                    <small>
                        Staff #{{ $staff->id }}
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
                            $staff->is_active
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
                            Cho phép Staff đăng nhập.
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
                <i class="bi bi-check-lg"></i>

                Lưu thay đổi
            </button>


            <a
                href="{{ route(
                    'admin.staff.show',
                    $staff
                ) }}"
                class="admin-btn admin-btn-secondary admin-btn-full"
            >
                Hủy
            </a>

        </section>

    </aside>

</form>

@endsection