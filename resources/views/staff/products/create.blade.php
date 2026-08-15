@extends('layouts.staff')


@section('title', 'Thêm sản phẩm - Staff')

@section('page-title', 'Thêm sản phẩm')


@section('content')

@php

    $selectedFaces =
        old('recommended_face_shapes', []);

    $selectedStyles =
        old('style_tags', []);

@endphp


<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            SẢN PHẨM
        </span>

        <h1>
            Thêm sản phẩm mới
        </h1>

        <p>
            Tạo thông tin cơ bản trước,
            sau đó thêm hình ảnh và biến thể.
        </p>

    </div>


    <a
        href="{{ route('staff.products.index') }}"
        class="staff-btn staff-btn-secondary"
    >
        ← Danh sách
    </a>

</div>



<div class="staff-product-notice">

    <strong>
        Quy trình tạo sản phẩm
    </strong>

    <p>
        Sản phẩm mới sẽ ở trạng thái
        <b>ngừng kinh doanh</b>.
        Sau khi tạo, hãy thêm ít nhất
        1 ảnh thật và 1 biến thể hoạt động
        rồi mới kích hoạt.
    </p>

</div>



<form
    action="{{ route('staff.products.store') }}"
    method="POST"
    class="staff-form-layout"
>

    @csrf


    <div class="staff-form-main">


        {{-- BASIC --}}

        <div class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thông tin cơ bản
                </h2>

                <p>
                    Các thông tin nhận diện sản phẩm.
                </p>

            </div>


            <div class="staff-product-form-grid">


                <div class="staff-form-group staff-product-full">

                    <label for="name">
                        Tên sản phẩm
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="staff-form-control
                            @error('name') staff-input-error @enderror"
                        maxlength="150"
                        required
                    >

                    @error('name')
                        <div class="staff-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="staff-form-group">

                    <label for="sku">
                        SKU
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="sku"
                        name="sku"
                        value="{{ old('sku') }}"
                        class="staff-form-control
                            @error('sku') staff-input-error @enderror"
                        placeholder="VLR-001"
                        required
                    >

                    @error('sku')
                        <div class="staff-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="staff-form-group">

                    <label for="category_id">
                        Danh mục
                        <span>*</span>
                    </label>

                    <select
                        id="category_id"
                        name="category_id"
                        class="staff-form-control
                            @error('category_id') staff-input-error @enderror"
                        required
                    >

                        <option value="">
                            -- Chọn danh mục --
                        </option>

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                {{
                                    old('category_id')
                                    == $category->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('category_id')
                        <div class="staff-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>



        {{-- PRICE --}}

        <div class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Giá bán
                </h2>

            </div>


            <div class="staff-product-form-grid">

                <div class="staff-form-group">

                    <label for="price">
                        Giá niêm yết
                        <span>*</span>
                    </label>

                    <input
                        type="number"
                        id="price"
                        name="price"
                        value="{{ old('price') }}"
                        min="1"
                        step="1"
                        class="staff-form-control
                            @error('price') staff-input-error @enderror"
                        required
                    >

                    @error('price')
                        <div class="staff-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="staff-form-group">

                    <label for="sale_price">
                        Giá khuyến mãi
                    </label>

                    <input
                        type="number"
                        id="sale_price"
                        name="sale_price"
                        value="{{ old('sale_price') }}"
                        min="0"
                        step="1"
                        class="staff-form-control
                            @error('sale_price') staff-input-error @enderror"
                    >

                    <small class="staff-form-help">
                        Để trống nếu không giảm giá.
                    </small>

                    @error('sale_price')
                        <div class="staff-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>



        {{-- ATTRIBUTES --}}

        <div class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thuộc tính kính
                </h2>

            </div>


            <div class="staff-product-form-grid">

                <div class="staff-form-group">

                    <label for="material">
                        Chất liệu
                    </label>

                    <select
                        id="material"
                        name="material"
                        class="staff-form-control"
                    >

                        <option value="">
                            -- Chọn chất liệu --
                        </option>

                        <option value="acetate" {{ old('material') === 'acetate' ? 'selected' : '' }}>
                            Acetate
                        </option>

                        <option value="tr90" {{ old('material') === 'tr90' ? 'selected' : '' }}>
                            TR90
                        </option>

                        <option value="metal" {{ old('material') === 'metal' ? 'selected' : '' }}>
                            Kim loại
                        </option>

                        <option value="titanium" {{ old('material') === 'titanium' ? 'selected' : '' }}>
                            Titanium
                        </option>

                    </select>

                </div>


                <div class="staff-form-group">

                    <label for="shape">
                        Kiểu dáng
                    </label>

                    <select
                        id="shape"
                        name="shape"
                        class="staff-form-control"
                    >

                        <option value="">
                            -- Chọn kiểu dáng --
                        </option>

                        <option value="round" {{ old('shape') === 'round' ? 'selected' : '' }}>Tròn</option>
                        <option value="square" {{ old('shape') === 'square' ? 'selected' : '' }}>Vuông</option>
                        <option value="rectangle" {{ old('shape') === 'rectangle' ? 'selected' : '' }}>Chữ nhật</option>
                        <option value="oval" {{ old('shape') === 'oval' ? 'selected' : '' }}>Oval</option>
                        <option value="cat_eye" {{ old('shape') === 'cat_eye' ? 'selected' : '' }}>Mắt mèo</option>
                        <option value="aviator" {{ old('shape') === 'aviator' ? 'selected' : '' }}>Aviator</option>
                        <option value="browline" {{ old('shape') === 'browline' ? 'selected' : '' }}>Browline</option>

                    </select>

                </div>


                <div class="staff-form-group">

                    <label for="gender">
                        Đối tượng
                    </label>

                    <select
                        id="gender"
                        name="gender"
                        class="staff-form-control"
                    >

                        <option value="">
                            -- Chọn đối tượng --
                        </option>

                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="unisex" {{ old('gender') === 'unisex' ? 'selected' : '' }}>Unisex</option>
                        <option value="kids" {{ old('gender') === 'kids' ? 'selected' : '' }}>Trẻ em</option>

                    </select>

                </div>


                <div class="staff-form-group">

                    <label for="dimensions">
                        Kích thước
                    </label>

                    <input
                        type="text"
                        id="dimensions"
                        name="dimensions"
                        value="{{ old('dimensions') }}"
                        class="staff-form-control"
                        placeholder="52-18-145 mm"
                    >

                </div>

            </div>

        </div>



        {{-- CONTENT --}}

        <div class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Nội dung sản phẩm
                </h2>

            </div>


            <div class="staff-form-group">

                <label for="description">
                    Mô tả
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="6"
                    class="staff-form-control"
                >{{ old('description') }}</textarea>

            </div>


            <div class="staff-form-group">

                <label for="highlights">
                    Điểm nổi bật
                </label>

                <textarea
                    id="highlights"
                    name="highlights"
                    rows="5"
                    class="staff-form-control"
                    placeholder="Ví dụ: Nhẹ, bền, phù hợp sử dụng hằng ngày..."
                >{{ old('highlights') }}</textarea>

            </div>

        </div>

    </div>



    {{-- SIDEBAR --}}

    <aside class="staff-form-sidebar">


        <div class="staff-form-card">

            <div class="staff-form-card-heading">
                <h2>
                    Khuôn mặt phù hợp
                </h2>
            </div>


            @foreach([
                'round' => 'Mặt tròn',
                'square' => 'Mặt vuông',
                'oval' => 'Mặt oval',
                'heart' => 'Mặt trái tim',
            ] as $value => $label)

                <label class="staff-check-row">

                    <input
                        type="checkbox"
                        name="recommended_face_shapes[]"
                        value="{{ $value }}"
                        {{
                            in_array(
                                $value,
                                $selectedFaces,
                                true
                            )
                                ? 'checked'
                                : ''
                        }}
                    >

                    <span>
                        {{ $label }}
                    </span>

                </label>

            @endforeach

        </div>



        <div class="staff-form-card">

            <div class="staff-form-card-heading">
                <h2>
                    Phong cách
                </h2>
            </div>


            @foreach([
                'minimal' => 'Tối giản',
                'elegant' => 'Thanh lịch',
                'bold' => 'Cá tính',
                'vintage' => 'Cổ điển',
            ] as $value => $label)

                <label class="staff-check-row">

                    <input
                        type="checkbox"
                        name="style_tags[]"
                        value="{{ $value }}"
                        {{
                            in_array(
                                $value,
                                $selectedStyles,
                                true
                            )
                                ? 'checked'
                                : ''
                        }}
                    >

                    <span>
                        {{ $label }}
                    </span>

                </label>

            @endforeach

        </div>



        <div class="staff-form-card staff-product-create-status">

            <span>
                Trạng thái sau khi tạo
            </span>

            <strong>
                Ngừng kinh doanh
            </strong>

            <small>
                Thêm ảnh và biến thể trước
                khi kích hoạt sản phẩm.
            </small>

        </div>



        <div class="staff-form-card staff-form-actions-card">

            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Tạo sản phẩm
            </button>

            <a
                href="{{ route('staff.products.index') }}"
                class="staff-btn staff-btn-secondary"
            >
                Hủy
            </a>

        </div>

    </aside>

</form>

@endsection