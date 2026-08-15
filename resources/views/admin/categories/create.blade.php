@extends('layouts.admin')


@section(
    'title',
    'Thêm danh mục - VELORA Eyes'
)


@section(
    'page-title',
    'Thêm danh mục'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            CREATE CATEGORY
        </span>

        <h1>
            Thêm danh mục
        </h1>

        <p>
            Tạo nhóm sản phẩm mới
            cho VELORA Eyes.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.categories.index'
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Danh sách
    </a>

</div>



<form
    action="{{ route(
        'admin.categories.store'
    ) }}"
    method="POST"
    enctype="multipart/form-data"
    class="admin-category-form-layout"
>

    @csrf


    <div class="admin-category-form-main">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin danh mục
                    </h2>

                    <p>
                        Slug sẽ được sinh tự động.
                    </p>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-group">

                    <label for="name">
                        Tên danh mục
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
                        placeholder="Ví dụ: Kính cận"
                        required
                    >

                    @error('name')
                        <div class="admin-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="admin-form-group admin-category-description">

                    <label for="description">
                        Mô tả
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        maxlength="1000"
                        class="admin-form-control
                            @error('description')
                                admin-input-error
                            @enderror"
                        placeholder="Mô tả ngắn về danh mục..."
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <div class="admin-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </section>

    </div>



    <aside class="admin-category-form-sidebar">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Ảnh danh mục</h2>
                </div>

            </div>


            <div class="admin-category-upload">

                <div>
                    <i class="bi bi-image"></i>
                </div>

                <label for="image">
                    Chọn hình ảnh
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <small>
                    JPG, PNG, WEBP. Tối đa 2MB.
                </small>

                @error('image')

                    <div class="admin-field-error">
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Trạng thái</h2>
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
                            Đang hoạt động
                        </strong>

                        <small>
                            Cho phép sử dụng danh mục này.
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
                <i class="bi bi-plus-lg"></i>

                Thêm danh mục
            </button>


            <a
                href="{{ route(
                    'admin.categories.index'
                ) }}"
                class="admin-btn admin-btn-secondary admin-btn-full"
            >
                Hủy
            </a>

        </section>

    </aside>

</form>

@endsection