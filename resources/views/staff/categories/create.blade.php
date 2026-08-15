@extends('layouts.staff')


@section(
    'title',
    'Thêm danh mục - Staff'
)


@section(
    'page-title',
    'Thêm danh mục'
)


@section('content')


<div class="staff-form-page">


    {{-- HEADER --}}

    <div class="staff-page-header">

        <div>

            <span class="staff-page-kicker">
                DANH MỤC
            </span>

            <h1>
                Thêm danh mục mới
            </h1>

            <p>
                Tạo nhóm sản phẩm mới
                cho cửa hàng VELORA Eyes.
            </p>

        </div>


        <a
            href="{{ route(
                'staff.categories.index'
            ) }}"
            class="staff-btn staff-btn-secondary"
        >
            ← Danh sách
        </a>

    </div>



    <form
        action="{{ route(
            'staff.categories.store'
        ) }}"
        method="POST"
        enctype="multipart/form-data"
        class="staff-form-layout"
    >

        @csrf



        {{-- MAIN --}}

        <div class="staff-form-main">


            <div class="staff-form-card">

                <div class="staff-form-card-heading">

                    <h2>
                        Thông tin danh mục
                    </h2>

                    <p>
                        Nhập thông tin cơ bản
                        của danh mục.
                    </p>

                </div>



                {{-- NAME --}}

                <div class="staff-form-group">

                    <label for="name">
                        Tên danh mục
                        <span>*</span>
                    </label>


                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="staff-form-control
                            @error('name')
                                staff-input-error
                            @enderror"
                        placeholder="Ví dụ: Gọng kính"
                        required
                        autofocus
                    >


                    @error('name')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                {{-- DESCRIPTION --}}

                <div class="staff-form-group">

                    <label for="description">
                        Mô tả
                    </label>


                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        class="staff-form-control
                            @error('description')
                                staff-input-error
                            @enderror"
                        placeholder="Mô tả ngắn về danh mục..."
                    >{{ old('description') }}</textarea>


                    @error('description')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>



            {{-- IMAGE --}}

            <div class="staff-form-card">

                <div class="staff-form-card-heading">

                    <h2>
                        Hình ảnh danh mục
                    </h2>

                    <p>
                        Ảnh được sử dụng để đại diện
                        cho danh mục trên website.
                    </p>

                </div>


                <div class="staff-form-group">

                    <label for="image">
                        Chọn ảnh
                    </label>


                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="staff-form-control
                            @error('image')
                                staff-input-error
                            @enderror"
                    >


                    <small class="staff-form-help">
                        Chọn JPG, JPEG, PNG hoặc WEBP.
                    </small>


                    @error('image')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </div>



        {{-- SIDEBAR --}}

        <aside class="staff-form-sidebar">

            <div class="staff-form-card">

                <div class="staff-form-card-heading">

                    <h2>
                        Trạng thái
                    </h2>

                </div>


                <label class="staff-switch-row">

                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        {{ old(
                            'is_active',
                            true
                        )
                            ? 'checked'
                            : '' }}
                    >


                    <span>

                        <strong>
                            Hiển thị danh mục
                        </strong>

                        <small>
                            Cho phép danh mục
                            xuất hiện trên website.
                        </small>

                    </span>

                </label>

            </div>



            <div class="staff-form-card staff-form-actions-card">

                <button
                    type="submit"
                    class="staff-btn staff-btn-primary"
                >
                    Lưu danh mục
                </button>


                <a
                    href="{{ route(
                        'staff.categories.index'
                    ) }}"
                    class="staff-btn staff-btn-secondary"
                >
                    Hủy
                </a>

            </div>

        </aside>

    </form>

</div>

@endsection