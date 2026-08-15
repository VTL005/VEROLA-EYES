@extends('layouts.staff')


@section('title', 'Chỉnh sửa sản phẩm - Staff')

@section('page-title', 'Chỉnh sửa sản phẩm')


@section('content')

@php

    $selectedFaces = old(
        'recommended_face_shapes',
        $product->recommended_face_shapes ?? []
    );

    $selectedStyles = old(
        'style_tags',
        $product->style_tags ?? []
    );

@endphp


<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            SẢN PHẨM
        </span>

        <h1>
            {{ $product->name }}
        </h1>

        <p>
            Cập nhật thông tin và trạng thái sản phẩm.
        </p>

    </div>


    <a
        href="{{ route(
            'staff.products.show',
            $product
        ) }}"
        class="staff-btn staff-btn-secondary"
    >
        ← Chi tiết sản phẩm
    </a>

</div>



<form
    action="{{ route(
        'staff.products.update',
        $product
    ) }}"
    method="POST"
    class="staff-form-layout"
>

    @csrf
    @method('PUT')


    <div class="staff-form-main">


        <div class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thông tin cơ bản
                </h2>

                <p>
                    Slug được tự động cập nhật
                    khi tên sản phẩm thay đổi.
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
                        value="{{ old(
                            'name',
                            $product->name
                        ) }}"
                        class="staff-form-control
                            @error('name') staff-input-error @enderror"
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
                        value="{{ old(
                            'sku',
                            $product->sku
                        ) }}"
                        class="staff-form-control
                            @error('sku') staff-input-error @enderror"
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
                        class="staff-form-control"
                        required
                    >

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                {{
                                    old(
                                        'category_id',
                                        $product->category_id
                                    )
                                    == $category->id
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="staff-form-group staff-product-full">

                    <label>
                        Slug
                    </label>

                    <input
                        type="text"
                        value="{{ $product->slug }}"
                        class="staff-form-control staff-form-readonly"
                        disabled
                    >

                </div>

            </div>

        </div>



        <div class="staff-form-card">

            <div class="staff-form-card-heading">
                <h2>Giá bán</h2>
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
                        value="{{ old(
                            'price',
                            $product->price
                        ) }}"
                        min="1"
                        step="1"
                        class="staff-form-control"
                        required
                    >

                </div>


                <div class="staff-form-group">

                    <label for="sale_price">
                        Giá khuyến mãi
                    </label>

                    <input
                        type="number"
                        id="sale_price"
                        name="sale_price"
                        value="{{ old(
                            'sale_price',
                            $product->sale_price
                        ) }}"
                        min="0"
                        step="1"
                        class="staff-form-control"
                    >

                </div>

            </div>

        </div>



        <div class="staff-form-card">

            <div class="staff-form-card-heading">
                <h2>Thuộc tính kính</h2>
            </div>


            <div class="staff-product-form-grid">

                @php
                    $material = old('material', $product->material);
                    $shape = old('shape', $product->shape);
                    $gender = old('gender', $product->gender);
                @endphp


                <div class="staff-form-group">

                    <label for="material">
                        Chất liệu
                    </label>

                    <select
                        id="material"
                        name="material"
                        class="staff-form-control"
                    >

                        <option value="">-- Chọn --</option>
                        <option value="acetate" {{ $material === 'acetate' ? 'selected' : '' }}>Acetate</option>
                        <option value="tr90" {{ $material === 'tr90' ? 'selected' : '' }}>TR90</option>
                        <option value="metal" {{ $material === 'metal' ? 'selected' : '' }}>Kim loại</option>
                        <option value="titanium" {{ $material === 'titanium' ? 'selected' : '' }}>Titanium</option>

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

                        <option value="">-- Chọn --</option>
                        <option value="round" {{ $shape === 'round' ? 'selected' : '' }}>Tròn</option>
                        <option value="square" {{ $shape === 'square' ? 'selected' : '' }}>Vuông</option>
                        <option value="rectangle" {{ $shape === 'rectangle' ? 'selected' : '' }}>Chữ nhật</option>
                        <option value="oval" {{ $shape === 'oval' ? 'selected' : '' }}>Oval</option>
                        <option value="cat_eye" {{ $shape === 'cat_eye' ? 'selected' : '' }}>Mắt mèo</option>
                        <option value="aviator" {{ $shape === 'aviator' ? 'selected' : '' }}>Aviator</option>
                        <option value="browline" {{ $shape === 'browline' ? 'selected' : '' }}>Browline</option>

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

                        <option value="">-- Chọn --</option>
                        <option value="male" {{ $gender === 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ $gender === 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="unisex" {{ $gender === 'unisex' ? 'selected' : '' }}>Unisex</option>
                        <option value="kids" {{ $gender === 'kids' ? 'selected' : '' }}>Trẻ em</option>

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
                        value="{{ old(
                            'dimensions',
                            $product->dimensions
                        ) }}"
                        class="staff-form-control"
                    >

                </div>

            </div>

        </div>



        <div class="staff-form-card">

            <div class="staff-form-group">

                <label for="description">
                    Mô tả
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="6"
                    class="staff-form-control"
                >{{ old(
                    'description',
                    $product->description
                ) }}</textarea>

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
                >{{ old(
                    'highlights',
                    $product->highlights
                ) }}</textarea>

            </div>

        </div>

    </div>



    <aside class="staff-form-sidebar">


        <div class="staff-form-card">

            <div class="staff-form-card-heading">
                <h2>Khuôn mặt phù hợp</h2>
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

                    <span>{{ $label }}</span>

                </label>

            @endforeach

        </div>



        <div class="staff-form-card">

            <div class="staff-form-card-heading">
                <h2>Phong cách</h2>
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

                    <span>{{ $label }}</span>

                </label>

            @endforeach

        </div>



        <div class="staff-form-card">

            <div class="staff-form-card-heading">
                <h2>Trạng thái bán</h2>
            </div>


            <label class="staff-switch-row">

                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    {{
                        old(
                            'is_active',
                            $product->is_active
                        )
                            ? 'checked'
                            : ''
                    }}
                >

                <span>

                    <strong>
                        Đang kinh doanh
                    </strong>

                    <small>
                        Chỉ có thể bật khi sản phẩm
                        đã có ảnh và biến thể hoạt động.
                    </small>

                </span>

            </label>


            @error('is_active')

                <div class="staff-field-error">
                    {{ $message }}
                </div>

            @enderror

        </div>



        <div class="staff-form-card staff-form-actions-card">

            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Lưu thay đổi
            </button>

            <a
                href="{{ route(
                    'staff.products.show',
                    $product
                ) }}"
                class="staff-btn staff-btn-secondary"
            >
                Hủy
            </a>

        </div>

    </aside>

</form>

@endsection