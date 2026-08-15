@extends('layouts.app')


@section('title', 'Chỉnh sửa hồ sơ - VELORA Eyes')


@section('content')

@php

    $initial = strtoupper(
        mb_substr(
            $user->name,
            0,
            1
        )
    );

@endphp


<section class="profile-form-section">

    <div class="velora-container">

        <div class="profile-form-layout">


            {{-- INTRO --}}

            <div class="profile-form-intro">

                <span class="hero-kicker">
                    EDIT PROFILE
                </span>

                <h1>
                    Chỉnh sửa hồ sơ
                </h1>


                <p>
                    Cập nhật thông tin cá nhân
                    và ảnh đại diện của bạn.
                </p>


                <a
                    href="{{ route('profile.show') }}"
                    class="profile-back-link"
                >
                    ← Quay lại tài khoản
                </a>

            </div>



            {{-- FORM --}}

            <div class="profile-form-card">

                <form
                    action="{{ route('profile.update') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    @csrf
                    @method('PATCH')



                    {{-- AVATAR --}}

                    <div class="profile-avatar-editor">

                        <div>

                            @if($user->avatar)

                                <img
                                    src="{{ asset(
                                        'storage/' . $user->avatar
                                    ) }}"
                                    alt="{{ $user->name }}"
                                    class="profile-edit-avatar"
                                >

                            @else

                                <div class="profile-edit-avatar profile-avatar-fallback">
                                    {{ $initial }}
                                </div>

                            @endif

                        </div>


                        <div>

                            <label
                                for="avatar"
                                class="form-label"
                            >
                                Ảnh đại diện
                            </label>


                            <input
                                type="file"
                                id="avatar"
                                name="avatar"
                                class="form-control @error('avatar') input-error @enderror"
                                accept=".jpg,.jpeg,.png,.webp"
                            >


                            <small>
                                JPG, JPEG, PNG hoặc WEBP.
                                Dung lượng tối đa 2MB.
                            </small>


                            @error('avatar')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>



                    <div class="profile-form-grid">


                        {{-- NAME --}}

                        <div class="form-group">

                            <label
                                for="name"
                                class="form-label"
                            >
                                Họ và tên
                            </label>


                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') input-error @enderror"
                                value="{{ old(
                                    'name',
                                    $user->name
                                ) }}"
                                maxlength="50"
                                required
                            >


                            @error('name')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- PHONE --}}

                        <div class="form-group">

                            <label
                                for="phone"
                                class="form-label"
                            >
                                Số điện thoại
                            </label>


                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-control @error('phone') input-error @enderror"
                                value="{{ old(
                                    'phone',
                                    $user->phone
                                ) }}"
                                maxlength="10"
                                placeholder="0912345678"
                                required
                            >


                            @error('phone')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- EMAIL --}}

                        <div class="form-group profile-grid-full">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email
                            </label>


                            <input
                                type="email"
                                id="email"
                                class="form-control profile-readonly-input"
                                value="{{ $user->email }}"
                                disabled
                            >


                            <div class="profile-email-notice">

                                Email là thông tin đăng nhập
                                của tài khoản và không thể
                                thay đổi tại đây.

                            </div>

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
                            Lưu thay đổi
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</section>

@endsection