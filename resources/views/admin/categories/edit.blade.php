@extends('layouts.admin')


@section(
    'title',
    'Sửa ' . $category->name
)


@section(
    'page-title',
    'Sửa danh mục'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            EDIT CATEGORY
        </span>

        <h1>
            Chỉnh sửa danh mục
        </h1>

        <p>
            {{ $category->name }}
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
        'admin.categories.update',
        $category
    ) }}"
    method="POST"
    enctype="multipart/form-data"
    class="admin-category-form-layout"
>

    @csrf
    @method('PUT')


    <div class="admin-category-form-main">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin danh mục
                    </h2>

                    <p>
                        Slug hiện tại:
                        {{ $category->slug }}
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
                        value="{{ old(
                            'name',
                            $category->name
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
                    >{{ old(
                        'description',
                        $category->description
                    ) }}</textarea>

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
                    <h2>Hình ảnh</h2>
                </div>

            </div>


            <div class="admin-category-current-image">

                @if($category->image)

                    <img
                        src="{{ asset(
                            $category->image
                        ) }}"
                        alt="{{ $category->name }}"
                    >

                @else

                    <div>
                        <i class="bi bi-image"></i>
                    </div>

                @endif

            </div>


            <div class="admin-category-replace-image">

                <label for="image">
                    Thay ảnh mới
                </label>

                <input
                    type="file"
                    id="image"
                    name="image"
                    accept=".jpg,.jpeg,.png,.webp"
                >

                <small>
                    Để trống nếu giữ ảnh hiện tại.
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
                            $category->is_active
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
                            Danh mục có thể được sử dụng.
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