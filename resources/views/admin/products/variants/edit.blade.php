@extends('layouts.admin')


@section(
    'title',
    'Sửa biến thể - VELORA Eyes'
)


@section(
    'page-title',
    'Sửa biến thể'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            EDIT VARIANT
        </span>

        <h1>
            Chỉnh sửa biến thể
        </h1>

        <p>
            {{ $product->name }}
            · {{ $variant->sku }}
        </p>

    </div>


    <a
        href="{{ route(
            'admin.products.show',
            $product
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Chi tiết sản phẩm
    </a>

</div>



<div class="admin-variant-product-card">

    <div>

        <i class="bi bi-box-seam"></i>

    </div>


    <span>

        <strong>
            {{ $variant->sku }}
        </strong>

        <small>

            {{ ucfirst($variant->color) }}
            · Size {{ $variant->size }}
            · Tồn {{ $variant->stock_quantity }}

        </small>

    </span>


    @if($variant->is_active)

        <span class="admin-status success">
            Đang bán
        </span>

    @else

        <span class="admin-status muted">
            Ngừng bán
        </span>

    @endif

</div>



<form
    action="{{ route(
        'admin.products.variants.update',
        [
            $product,
            $variant,
        ]
    ) }}"
    method="POST"
    class="admin-variant-form-layout"
>

    @csrf
    @method('PUT')


    <div class="admin-variant-form-main">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Thông tin biến thể</h2>
                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-grid">


                    <div class="admin-form-group">

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
                            maxlength="50"
                            class="admin-form-control
                                @error('color')
                                    admin-input-error
                                @enderror"
                            required
                        >

                        @error('color')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

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
                            maxlength="30"
                            class="admin-form-control
                                @error('size')
                                    admin-input-error
                                @enderror"
                            required
                        >

                        @error('size')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group admin-variant-sku-field">

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
                            maxlength="120"
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

                </div>

            </div>

        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Kho và giá</h2>
                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-grid">


                    <div class="admin-form-group">

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
                            class="admin-form-control
                                @error('stock_quantity')
                                    admin-input-error
                                @enderror"
                            required
                        >

                        @error('stock_quantity')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="price_adjustment">
                            Điều chỉnh giá
                            <span>*</span>
                        </label>

                        <div class="admin-product-price-input">

                            <input
                                type="number"
                                id="price_adjustment"
                                name="price_adjustment"
                                value="{{ old(
                                    'price_adjustment',
                                    $variant->price_adjustment
                                ) }}"
                                step="1"
                                class="admin-form-control
                                    @error('price_adjustment')
                                        admin-input-error
                                    @enderror"
                                required
                            >

                            <span>đ</span>

                        </div>

                        @error('price_adjustment')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </section>

    </div>



    <aside class="admin-variant-form-sidebar">


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
                            $variant->is_active
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
                            Cho phép khách mua biến thể này.
                        </small>

                    </div>

                </label>

            </div>

        </section>



        <section class="admin-variant-price-preview">

            <i class="bi bi-cash-stack"></i>

            <div>

                <span>
                    Giá hiện tại của biến thể
                </span>

                <strong>

                    {{ number_format(
                        (float) $variant->final_price,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </strong>

                <small>

                    Giá Product:
                    {{ number_format(
                        (float) $product->current_price,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </small>

            </div>

        </section>



        @if(
            $variant->is_active
            && $product->is_active
            && $product->variants()
                ->where('is_active', true)
                ->count() === 1
        )

            <section class="admin-variant-warning">

                <i class="bi bi-exclamation-triangle"></i>

                <div>

                    <strong>
                        Đây là biến thể hoạt động cuối cùng
                    </strong>

                    <p>
                        Nếu bạn tắt biến thể này,
                        sản phẩm cũng sẽ tự chuyển sang
                        trạng thái chưa bán.
                    </p>

                </div>

            </section>

        @endif



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