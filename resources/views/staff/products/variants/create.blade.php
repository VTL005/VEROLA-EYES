@extends('layouts.staff')


@section('title', 'Thêm biến thể - Staff')

@section('page-title', 'Thêm biến thể')


@section('content')


<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            PRODUCT VARIANT
        </span>

        <h1>
            Thêm biến thể
        </h1>

        <p>
            {{ $product->name }}
            · {{ $product->sku }}
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



<div class="staff-variant-summary">

    <div>

        <span>
            Sản phẩm
        </span>

        <strong>
            {{ $product->name }}
        </strong>

    </div>


    <div>

        <span>
            Giá hiện tại
        </span>

        <strong>
            {{ number_format(
                (float) $product->current_price,
                0,
                ',',
                '.'
            ) }}đ
        </strong>

    </div>


    <div>

        <span>
            SKU sản phẩm
        </span>

        <strong>
            {{ $product->sku }}
        </strong>

    </div>

</div>



<form
    action="{{ route(
        'staff.products.variants.store',
        $product
    ) }}"
    method="POST"
    class="staff-variant-form"
>

    @csrf


    <div class="staff-form-card">

        <div class="staff-form-card-heading">

            <h2>
                Thông tin biến thể
            </h2>

            <p>
                Mỗi tổ hợp màu + size chỉ được tồn tại một lần.
            </p>

        </div>


        <div class="staff-product-form-grid">


            <div class="staff-form-group">

                <label for="color">
                    Màu sắc
                    <span>*</span>
                </label>

                <input
                    type="text"
                    id="color"
                    name="color"
                    value="{{ old('color') }}"
                    class="staff-form-control
                        @error('color') staff-input-error @enderror"
                    placeholder="Ví dụ: black"
                    required
                >

                <small class="staff-form-help">
                    Hệ thống tự chuyển về chữ thường.
                </small>

                @error('color')
                    <div class="staff-field-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>



            <div class="staff-form-group">

                <label for="size">
                    Size
                    <span>*</span>
                </label>

                <input
                    type="text"
                    id="size"
                    name="size"
                    value="{{ old('size') }}"
                    class="staff-form-control
                        @error('size') staff-input-error @enderror"
                    placeholder="Ví dụ: M"
                    required
                >

                @error('size')
                    <div class="staff-field-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>



            <div class="staff-form-group">

                <label for="sku">
                    SKU biến thể
                    <span>*</span>
                </label>

                <input
                    type="text"
                    id="sku"
                    name="sku"
                    value="{{ old('sku') }}"
                    class="staff-form-control
                        @error('sku') staff-input-error @enderror"
                    placeholder="VLR-001-BLK-M"
                    required
                >

                @error('sku')
                    <div class="staff-field-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>



            <div class="staff-form-group">

                <label for="stock_quantity">
                    Tồn kho
                    <span>*</span>
                </label>

                <input
                    type="number"
                    id="stock_quantity"
                    name="stock_quantity"
                    value="{{ old(
                        'stock_quantity',
                        0
                    ) }}"
                    min="0"
                    step="1"
                    class="staff-form-control
                        @error('stock_quantity') staff-input-error @enderror"
                    required
                >

                @error('stock_quantity')
                    <div class="staff-field-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>



            <div class="staff-form-group staff-product-full">

                <label for="price_adjustment">
                    Điều chỉnh giá
                    <span>*</span>
                </label>

                <input
                    type="number"
                    id="price_adjustment"
                    name="price_adjustment"
                    value="{{ old(
                        'price_adjustment',
                        0
                    ) }}"
                    step="1"
                    class="staff-form-control
                        @error('price_adjustment') staff-input-error @enderror"
                    required
                >

                <div class="staff-price-adjustment-help">

                    <span>
                        <b>50.000</b> → cộng 50.000đ
                    </span>

                    <span>
                        <b>0</b> → giữ nguyên giá
                    </span>

                    <span>
                        <b>-50.000</b> → giảm 50.000đ
                    </span>

                </div>

                @error('price_adjustment')
                    <div class="staff-field-error">
                        {{ $message }}
                    </div>
                @enderror

            </div>

        </div>

    </div>



    <aside class="staff-variant-sidebar">

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
                            true
                        )
                            ? 'checked'
                            : ''
                    }}
                >

                <span>

                    <strong>
                        Đang hoạt động
                    </strong>

                    <small>
                        Cho phép khách hàng
                        lựa chọn biến thể này.
                    </small>

                </span>

            </label>

        </div>


        <div class="staff-form-card staff-form-actions-card">

            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Thêm biến thể
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