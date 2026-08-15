@extends('layouts.staff')


@section(
    'title',
    'Chỉnh sửa danh mục - Staff'
)


@section(
    'page-title',
    'Chỉnh sửa danh mục'
)


@section('content')


<div class="staff-form-page">


    <div class="staff-page-header">

        <div>

            <span class="staff-page-kicker">
                DANH MỤC
            </span>

            <h1>
                {{ $category->name }}
            </h1>

            <p>
                Cập nhật thông tin danh mục sản phẩm.
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
            'staff.categories.update',
            $category
        ) }}"
        method="POST"
        enctype="multipart/form-data"
        class="staff-form-layout"
    >

        @csrf
        @method('PUT')



        <div class="staff-form-main">


            {{-- INFORMATION --}}

            <div class="staff-form-card">

                <div class="staff-form-card-heading">

                    <h2>
                        Thông tin danh mục
                    </h2>

                    <p>
                        Slug sẽ tự động cập nhật
                        nếu tên danh mục thay đổi.
                    </p>

                </div>



                <div class="staff-form-group">

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
                        class="staff-form-control
                            @error('name')
                                staff-input-error
                            @enderror"
                        required
                    >


                    @error('name')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <div class="staff-form-group">

                    <label>
                        Slug hiện tại
                    </label>


                    <input
                        type="text"
                        value="{{ $category->slug }}"
                        class="staff-form-control staff-form-readonly"
                        disabled
                    >


                    <small class="staff-form-help">
                        Slug được hệ thống tự động sinh.
                    </small>

                </div>



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
                    >{{ old(
                        'description',
                        $category->description
                    ) }}</textarea>


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
                        Hình ảnh
                    </h2>

                    <p>
                        Nếu không chọn ảnh mới,
                        ảnh hiện tại sẽ được giữ nguyên.
                    </p>

                </div>



                @if($category->image)

                    <div class="staff-category-current-image">

                        <img
                            src="{{ asset(
                                $category->image
                            ) }}"
                            alt="{{ $category->name }}"
                        >


                        <div>

                            <span>
                                Ảnh hiện tại
                            </span>

                            <strong>
                                {{ $category->name }}
                            </strong>

                        </div>

                    </div>

                @endif



                <div class="staff-form-group">

                    <label for="image">
                        Thay ảnh mới
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
                        {{
                            old(
                                'is_active',
                                $category->is_active
                            )
                                ? 'checked'
                                : ''
                        }}
                    >


                    <span>

                        <strong>
                            Hiển thị danh mục
                        </strong>

                        <small>
                            Tắt nếu muốn ẩn
                            danh mục khỏi website.
                        </small>

                    </span>

                </label>

            </div>



            <div class="staff-form-card staff-category-meta">

                <span>
                    Số sản phẩm
                </span>

                <strong>
                    {{ $category->products()->count() }}
                </strong>


                <span>
                    Ngày tạo
                </span>

                <strong>

                    {{ $category
                        ->created_at
                        ->format('d/m/Y H:i') }}

                </strong>

            </div>



            <div class="staff-form-card staff-form-actions-card">

                <button
                    type="submit"
                    class="staff-btn staff-btn-primary"
                >
                    Cập nhật
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