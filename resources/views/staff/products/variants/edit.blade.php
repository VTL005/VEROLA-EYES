@extends('layouts.staff')


@section('title', 'Chỉnh sửa biến thể - Staff')

@section('page-title', 'Chỉnh sửa biến thể')


@section('content')


<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            PRODUCT VARIANT
        </span>

        <h1>
            {{ $variant->sku }}
        </h1>

        <p>
            {{ $product->name }}
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
            Giá sản phẩm
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
            Giá cuối hiện tại
        </span>

        <strong>
            {{ number_format(
                (float) $variant->final_price,
                0,
                ',',
                '.'
            ) }}đ
        </strong>

    </div>

</div>



<form
    action="{{ route(
        'staff.products.variants.update',
        [
            $product,
            $variant
        ]
    ) }}"
    method="POST"
    class="staff-variant-form"
>

    @csrf
    @method('PUT')


    <div class="staff-form-card">

        <div class="staff-form-card-heading">

            <h2>
                Thông tin biến thể
            </h2>

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
                    value="{{ old(
                        'color',
                        $variant->color
                    ) }}"
                    class="staff-form-control
                        @error('color') staff-input-error @enderror"
                    required
                >

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
                    value="{{ old(
                        'size',
                        $variant->size
                    ) }}"
                    class="staff-form-control
                        @error('size') staff-input-error @enderror"
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
                    value="{{ old(
                        'sku',
                        $variant->sku
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
                        $variant->stock_quantity
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
                        $variant->price_adjustment
                    ) }}"
                    step="1"
                    class="staff-form-control
                        @error('price_adjustment') staff-input-error @enderror"
                    required
                >

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
                <h2>Trạng thái</h2>
            </div>


            <label class="staff-switch-row">

                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    {{
                        old(
                            'is_active',
                            $variant->is_active
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
                        Bỏ chọn nếu muốn
                        tạm ngừng bán biến thể.
                    </small>

                </span>

            </label>

        </div>



        <div class="staff-form-card staff-category-meta">

            <span>
                Giá sản phẩm
            </span>

            <strong>
                {{ number_format(
                    (float) $product->current_price,
                    0,
                    ',',
                    '.'
                ) }}đ
            </strong>


            <span>
                Điều chỉnh
            </span>

            <strong>
                {{ number_format(
                    (float) $variant->price_adjustment,
                    0,
                    ',',
                    '.'
                ) }}đ
            </strong>


            <span>
                Giá cuối
            </span>

            <strong>
                {{ number_format(
                    (float) $variant->final_price,
                    0,
                    ',',
                    '.'
                ) }}đ
            </strong>

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