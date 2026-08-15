@extends('layouts.admin')


@section(
    'title',
    'Sửa ' . $product->name
)


@section(
    'page-title',
    'Sửa sản phẩm'
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


    $selectedFaceShapes =
        old(
            'recommended_face_shapes',
            $product->recommended_face_shapes ?? []
        );


    $selectedStyles =
        old(
            'style_tags',
            $product->style_tags ?? []
        );


    $readyForSale =
        $product->isReadyForSale();

@endphp



<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            EDIT PRODUCT
        </span>

        <h1>
            Chỉnh sửa sản phẩm
        </h1>

        <p>
            {{ $product->name }}
            · {{ $product->sku }}
        </p>

    </div>


    <div class="admin-product-header-actions">

        <a
            href="{{ route(
                'admin.products.show',
                $product
            ) }}"
            class="admin-btn admin-btn-secondary"
        >
            <i class="bi bi-arrow-left"></i>

            Chi tiết
        </a>

    </div>

</div>



@if($readyForSale)

    <div class="admin-product-notice success">

        <i class="bi bi-check-circle"></i>

        <div>

            <strong>
                Sản phẩm đã đủ điều kiện kinh doanh
            </strong>

            <span>
                Đã có ảnh thật và ít nhất một biến thể hoạt động.
            </span>

        </div>

    </div>

@else

    <div class="admin-product-notice warning">

        <i class="bi bi-exclamation-triangle"></i>

        <div>

            <strong>
                Chưa thể kích hoạt sản phẩm
            </strong>

            <span>
                Sản phẩm phải có ít nhất 1 ảnh thật và 1 biến thể hoạt động.
            </span>

        </div>

    </div>

@endif



<form
    action="{{ route(
        'admin.products.update',
        $product
    ) }}"
    method="POST"
    class="admin-product-form-layout"
>

    @csrf
    @method('PUT')


    <div class="admin-product-form-main">


        {{-- BASIC --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Thông tin cơ bản</h2>
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
                            value="{{ old(
                                'name',
                                $product->name
                            ) }}"
                            maxlength="150"
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
                            maxlength="100"
                            class="admin-form-control
                                @error('sku')
                                    admin-input-error
                                @enderror"
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
                            class="admin-form-control"
                            required
                        >

                            @foreach(
                                $categories
                                as $category
                            )

                                <option
                                    value="{{ $category->id }}"
                                    {{
                                        (string) old(
                                            'category_id',
                                            $product->category_id
                                        )
                                        === (string) $category->id
                                            ? 'selected'
                                            : ''
                                    }}
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <div class="admin-form-group">

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
                            maxlength="100"
                            class="admin-form-control"
                            placeholder="52-18-145 mm"
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
                                value="{{ old(
                                    'price',
                                    $product->price
                                ) }}"
                                min="1"
                                step="1"
                                class="admin-form-control"
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
                                value="{{ old(
                                    'sale_price',
                                    $product->sale_price
                                ) }}"
                                min="0"
                                step="1"
                                class="admin-form-control"
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



        {{-- ATTRIBUTE --}}

        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Thuộc tính kính</h2>
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
                                        old(
                                            'material',
                                            $product->material
                                        ) === $value
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
                                        old(
                                            'shape',
                                            $product->shape
                                        ) === $value
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
                                        old(
                                            'gender',
                                            $product->gender
                                        ) === $value
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
                    <h2>Gợi ý lựa chọn</h2>
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
                                            $selectedFaceShapes
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
                                            $selectedStyles
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
                    <h2>Nội dung sản phẩm</h2>
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
                    >{{ old(
                        'description',
                        $product->description
                    ) }}</textarea>

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
                    >{{ old(
                        'highlights',
                        $product->highlights
                    ) }}</textarea>

                </div>

            </div>

        </section>

    </div>



    {{-- SIDEBAR --}}

    <aside class="admin-product-form-sidebar">


        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Trạng thái kinh doanh</h2>
                </div>

            </div>


            <div class="admin-product-sale-status">

                @if($readyForSale)

                    <div class="ready">

                        <i class="bi bi-check-circle"></i>

                        <span>
                            <strong>Đã sẵn sàng</strong>
                            <small>Có thể bật bán sản phẩm.</small>
                        </span>

                    </div>

                @else

                    <div class="not-ready">

                        <i class="bi bi-exclamation-circle"></i>

                        <span>
                            <strong>Chưa sẵn sàng</strong>
                            <small>Hãy kiểm tra ảnh và biến thể.</small>
                        </span>

                    </div>

                @endif


                <div class="admin-staff-switch">

                    <input
                        type="checkbox"
                        id="is_active"
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

                    <label for="is_active">

                        <span></span>

                        <div>

                            <strong>
                                Đang kinh doanh
                            </strong>

                            <small>
                                Hiển thị sản phẩm cho khách hàng.
                            </small>

                        </div>

                    </label>

                </div>


                @error('is_active')

                    <div class="admin-product-active-error">

                        <i class="bi bi-exclamation-triangle"></i>

                        {{ $message }}

                    </div>

                @enderror

            </div>

        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Thông tin hệ thống</h2>
                </div>
            </div>


            <div class="admin-product-edit-meta">

                <span>
                    Product ID
                    <strong>#{{ $product->id }}</strong>
                </span>

                <span>
                    Slug
                    <strong>{{ $product->slug }}</strong>
                </span>

                <span>
                    Ngày tạo
                    <strong>
                        {{ $product
                            ->created_at
                            ->format('d/m/Y H:i') }}
                    </strong>
                </span>

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
                    'admin.products.show',
                    $product
                ) }}"
                class="admin-btn admin-btn-secondary admin-btn-full"
            >
                Hủy
            </a>

        </section>

    </aside>

</form>

@endsection