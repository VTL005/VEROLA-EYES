@extends('layouts.admin')


@section(
    'title',
    'Thêm sản phẩm - VELORA Eyes'
)


@section(
    'page-title',
    'Thêm sản phẩm'
)


@section('content')

@php

    $materials = [
        'acetate' => 'Acetate',
        'tr90' => 'TR90',
        'metal' => 'Kim loại',
        'titanium' => 'Titanium',
    ];


    $shapes = [
        'round' => 'Tròn',
        'square' => 'Vuông',
        'rectangle' => 'Chữ nhật',
        'oval' => 'Oval',
        'cat_eye' => 'Mắt mèo',
        'aviator' => 'Aviator',
        'browline' => 'Browline',
    ];


    $genders = [
        'male' => 'Nam',
        'female' => 'Nữ',
        'unisex' => 'Unisex',
        'kids' => 'Trẻ em',
    ];


    $faceShapes = [
        'round' => 'Mặt tròn',
        'square' => 'Mặt vuông',
        'oval' => 'Mặt oval',
        'heart' => 'Mặt trái tim',
    ];


    $styles = [
        'minimal' => 'Tối giản',
        'elegant' => 'Thanh lịch',
        'bold' => 'Cá tính',
        'vintage' => 'Vintage',
    ];

@endphp



<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            CREATE PRODUCT
        </span>

        <h1>
            Thêm sản phẩm
        </h1>

        <p>
            Tạo thông tin cơ bản cho sản phẩm kính mới.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.products.index'
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Danh sách sản phẩm
    </a>

</div>



<div class="admin-product-notice">

    <i class="bi bi-info-circle"></i>

    <div>

        <strong>
            Sản phẩm mới sẽ ở trạng thái chưa bán
        </strong>

        <span>
            Sau khi tạo, hãy thêm ít nhất một ảnh thật và một biến thể hoạt động trước khi kích hoạt sản phẩm.
        </span>

    </div>

</div>



<form
    action="{{ route(
        'admin.products.store'
    ) }}"
    method="POST"
    class="admin-product-form-layout"
>

    @csrf


    <div class="admin-product-form-main">


        {{-- BASIC --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin cơ bản
                    </h2>

                    <p>
                        Tên, SKU, danh mục và giá bán.
                    </p>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-grid">

                    <div class="admin-form-group">

                        <label for="name">
                            Tên sản phẩm
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            maxlength="150"
                            class="admin-form-control
                                @error('name')
                                    admin-input-error
                                @enderror"
                            placeholder="Ví dụ: Gọng kính Velora Classic"
                            required
                        >

                        @error('name')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="sku">
                            SKU sản phẩm
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="sku"
                            name="sku"
                            value="{{ old('sku') }}"
                            maxlength="100"
                            class="admin-form-control
                                @error('sku')
                                    admin-input-error
                                @enderror"
                            placeholder="VLR-CLASSIC-001"
                            required
                        >

                        @error('sku')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="category_id">
                            Danh mục
                            <span>*</span>
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            class="admin-form-control
                                @error('category_id')
                                    admin-input-error
                                @enderror"
                            required
                        >

                            <option value="">
                                Chọn danh mục
                            </option>

                            @foreach(
                                $categories
                                as $category
                            )

                                <option
                                    value="{{ $category->id }}"
                                    {{
                                        (string) old('category_id')
                                        === (string) $category->id
                                            ? 'selected'
                                            : ''
                                    }}
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('category_id')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="dimensions">
                            Kích thước
                        </label>

                        <input
                            type="text"
                            id="dimensions"
                            name="dimensions"
                            value="{{ old('dimensions') }}"
                            maxlength="100"
                            class="admin-form-control"
                            placeholder="Ví dụ: 52-18-145 mm"
                        >

                    </div>


                    <div class="admin-form-group">

                        <label for="price">
                            Giá niêm yết
                            <span>*</span>
                        </label>

                        <div class="admin-product-price-input">

                            <input
                                type="number"
                                id="price"
                                name="price"
                                value="{{ old('price') }}"
                                min="1"
                                step="1"
                                class="admin-form-control
                                    @error('price')
                                        admin-input-error
                                    @enderror"
                                placeholder="1500000"
                                required
                            >

                            <span>đ</span>

                        </div>

                        @error('price')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="sale_price">
                            Giá khuyến mãi
                        </label>

                        <div class="admin-product-price-input">

                            <input
                                type="number"
                                id="sale_price"
                                name="sale_price"
                                value="{{ old('sale_price') }}"
                                min="0"
                                step="1"
                                class="admin-form-control
                                    @error('sale_price')
                                        admin-input-error
                                    @enderror"
                                placeholder="Để trống nếu không giảm giá"
                            >

                            <span>đ</span>

                        </div>

                        @error('sale_price')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </section>



        {{-- ATTRIBUTES --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thuộc tính kính
                    </h2>

                    <p>
                        Chất liệu, kiểu dáng và đối tượng sử dụng.
                    </p>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-product-attribute-grid">

                    <div class="admin-form-group">

                        <label for="material">
                            Chất liệu
                        </label>

                        <select
                            id="material"
                            name="material"
                            class="admin-form-control"
                        >

                            <option value="">
                                Chưa xác định
                            </option>

                            @foreach(
                                $materials
                                as $value => $label
                            )

                                <option
                                    value="{{ $value }}"
                                    {{
                                        old('material') === $value
                                            ? 'selected'
                                            : ''
                                    }}
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="admin-form-group">

                        <label for="shape">
                            Kiểu dáng
                        </label>

                        <select
                            id="shape"
                            name="shape"
                            class="admin-form-control"
                        >

                            <option value="">
                                Chưa xác định
                            </option>

                            @foreach(
                                $shapes
                                as $value => $label
                            )

                                <option
                                    value="{{ $value }}"
                                    {{
                                        old('shape') === $value
                                            ? 'selected'
                                            : ''
                                    }}
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="admin-form-group">

                        <label for="gender">
                            Đối tượng
                        </label>

                        <select
                            id="gender"
                            name="gender"
                            class="admin-form-control"
                        >

                            <option value="">
                                Chưa xác định
                            </option>

                            @foreach(
                                $genders
                                as $value => $label
                            )

                                <option
                                    value="{{ $value }}"
                                    {{
                                        old('gender') === $value
                                            ? 'selected'
                                            : ''
                                    }}
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

        </section>



        {{-- RECOMMENDATION --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Gợi ý lựa chọn
                    </h2>

                    <p>
                        Dùng cho hệ thống đề xuất sản phẩm.
                    </p>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-product-option-section">

                    <label>
                        Khuôn mặt phù hợp
                    </label>

                    <div class="admin-product-checkbox-grid">

                        @foreach(
                            $faceShapes
                            as $value => $label
                        )

                            <label class="admin-product-check">

                                <input
                                    type="checkbox"
                                    name="recommended_face_shapes[]"
                                    value="{{ $value }}"
                                    {{
                                        in_array(
                                            $value,
                                            old(
                                                'recommended_face_shapes',
                                                []
                                            )
                                        )
                                            ? 'checked'
                                            : ''
                                    }}
                                >

                                <span>
                                    <i class="bi bi-check-lg"></i>
                                </span>

                                {{ $label }}

                            </label>

                        @endforeach

                    </div>

                </div>


                <div class="admin-product-option-section">

                    <label>
                        Phong cách
                    </label>

                    <div class="admin-product-checkbox-grid">

                        @foreach(
                            $styles
                            as $value => $label
                        )

                            <label class="admin-product-check">

                                <input
                                    type="checkbox"
                                    name="style_tags[]"
                                    value="{{ $value }}"
                                    {{
                                        in_array(
                                            $value,
                                            old(
                                                'style_tags',
                                                []
                                            )
                                        )
                                            ? 'checked'
                                            : ''
                                    }}
                                >

                                <span>
                                    <i class="bi bi-check-lg"></i>
                                </span>

                                {{ $label }}

                            </label>

                        @endforeach

                    </div>

                </div>

            </div>

        </section>



        {{-- CONTENT --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Nội dung sản phẩm
                    </h2>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-group">

                    <label for="description">
                        Mô tả sản phẩm
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="7"
                        maxlength="5000"
                        class="admin-form-control admin-product-textarea"
                        placeholder="Mô tả chi tiết về sản phẩm..."
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <div class="admin-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="admin-form-group admin-product-highlights">

                    <label for="highlights">
                        Thông tin nổi bật
                    </label>

                    <textarea
                        id="highlights"
                        name="highlights"
                        rows="5"
                        maxlength="3000"
                        class="admin-form-control admin-product-textarea"
                        placeholder="Ví dụ: Trọng lượng nhẹ, chống gỉ, phù hợp đeo lâu..."
                    >{{ old('highlights') }}</textarea>

                    @error('highlights')
                        <div class="admin-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </section>

    </div>



    {{-- =====================================================
        SIDEBAR
    ====================================================== --}}

    <aside class="admin-product-form-sidebar">

        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Quy trình sản phẩm</h2>
                </div>
            </div>


            <div class="admin-product-create-steps">

                <div class="current">

                    <span>1</span>

                    <div>
                        <strong>Tạo sản phẩm</strong>
                        <small>Thông tin cơ bản</small>
                    </div>

                </div>


                <div>

                    <span>2</span>

                    <div>
                        <strong>Thêm hình ảnh</strong>
                        <small>Tối đa 5 ảnh thật</small>
                    </div>

                </div>


                <div>

                    <span>3</span>

                    <div>
                        <strong>Thêm biến thể</strong>
                        <small>Màu, size, tồn kho</small>
                    </div>

                </div>


                <div>

                    <span>4</span>

                    <div>
                        <strong>Kích hoạt</strong>
                        <small>Bắt đầu kinh doanh</small>
                    </div>

                </div>

            </div>

        </section>


        <section class="admin-panel admin-form-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary admin-btn-full"
            >
                <i class="bi bi-plus-lg"></i>

                Tạo sản phẩm
            </button>


            <a
                href="{{ route(
                    'admin.products.index'
                ) }}"
                class="admin-btn admin-btn-secondary admin-btn-full"
            >
                Hủy
            </a>

        </section>

    </aside>

</form>

@endsection