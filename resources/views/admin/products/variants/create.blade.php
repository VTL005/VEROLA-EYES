@extends('layouts.admin')


@section(
'title',
'Thêm biến thể - VELORA Eyes'
)


@section(
'page-title',
'Thêm biến thể'
)


@section('content')


<div class="admin-page-header">

  <div>

    <span class="admin-page-kicker">
      CREATE VARIANT
    </span>

    <h1>
      Thêm biến thể
    </h1>

    <p>
      {{ $product->name }}
      · {{ $product->sku }}
    </p>

  </div>


  <a href="{{ route(
            'admin.products.show',
            $product
        ) }}" class="admin-btn admin-btn-secondary">
    <i class="bi bi-arrow-left"></i>

    Chi tiết sản phẩm
  </a>

</div>



<div class="admin-variant-product-card">

  <div>

    <i class="bi bi-eyeglasses"></i>

  </div>


  <span>

    <strong>
      {{ $product->name }}
    </strong>

    <small>
      Giá hiện tại:
      {{ number_format(
                (float) $product->current_price,
                0,
                ',',
                '.'
            ) }}đ
    </small>

  </span>

</div>



<form action="{{ route(
        'admin.products.variants.store',
        $product
    ) }}" method="POST" class="admin-variant-form-layout">

  @csrf


  <div class="admin-variant-form-main">

    {{-- =====================================================
            THÔNG TIN BIẾN THỂ
        ====================================================== --}}

    <section class="admin-panel">

      <div class="admin-panel-header">

        <div>

          <h2>
            Thông tin biến thể
          </h2>

          <p>
            Màu sắc, size và mã SKU.
          </p>

        </div>

      </div>


      <div class="admin-form-body">

        <div class="admin-form-grid">


          {{-- COLOR --}}

          <div class="admin-form-group">

            <label for="color">
              Màu sắc
              <span>*</span>
            </label>

            <input type="text" id="color" name="color" value="{{ old('color') }}" maxlength="50" class="admin-form-control
                                @error('color')
                                    admin-input-error
                                @enderror" placeholder="Ví dụ: black" required>

            @error('color')

            <div class="admin-field-error">
              {{ $message }}
            </div>

            @enderror

          </div>



          {{-- SIZE --}}

          <div class="admin-form-group">

            <label for="size">
              Size
              <span>*</span>
            </label>

            <input type="text" id="size" name="size" value="{{ old('size') }}" maxlength="30" class="admin-form-control
                                @error('size')
                                    admin-input-error
                                @enderror" placeholder="Ví dụ: M" required>

            @error('size')

            <div class="admin-field-error">
              {{ $message }}
            </div>

            @enderror

          </div>



          {{-- SKU --}}

          <div class="admin-form-group admin-variant-sku-field">

            <label for="sku">
              SKU biến thể
              <span>*</span>
            </label>

            <input type="text" id="sku" name="sku" value="{{ old('sku') }}" maxlength="120" class="admin-form-control
                                @error('sku')
                                    admin-input-error
                                @enderror" placeholder="VLR-CLASSIC-BLK-M" required>

            @error('sku')

            <div class="admin-field-error">
              {{ $message }}
            </div>

            @enderror

          </div>

        </div>

      </div>

    </section>



    {{-- =====================================================
            KHO VÀ GIÁ
        ====================================================== --}}

    <section class="admin-panel">

      <div class="admin-panel-header">

        <div>

          <h2>
            Kho và giá
          </h2>

          <p>
            Thiết lập tồn kho và chênh lệch giá của biến thể.
          </p>

        </div>

      </div>


      <div class="admin-form-body">

        <div class="admin-form-grid">


          {{-- STOCK --}}

          <div class="admin-form-group">

            <label for="stock_quantity">
              Tồn kho
              <span>*</span>
            </label>

            <input type="number" id="stock_quantity" name="stock_quantity" value="{{ old(
                                'stock_quantity',
                                0
                            ) }}" min="0" step="1" class="admin-form-control
                                @error('stock_quantity')
                                    admin-input-error
                                @enderror" required>

            @error('stock_quantity')

            <div class="admin-field-error">
              {{ $message }}
            </div>

            @enderror

          </div>



          {{-- PRICE ADJUSTMENT --}}

          <div class="admin-form-group">

            <label for="price_adjustment">
              Chênh lệch giá
              <span>*</span>
            </label>


            <div class="admin-product-price-input">

              <input type="number" id="price_adjustment" name="price_adjustment" value="{{ old(
                                    'price_adjustment',
                                    0
                                ) }}" step="1" class="admin-form-control
                                    @error('price_adjustment')
                                        admin-input-error
                                    @enderror" required>

              <span>đ</span>

            </div>


            <small class="admin-variant-help">
              0 = giữ nguyên giá sản phẩm,
              số dương = tăng giá,
              số âm = giảm giá.
            </small>


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



  {{-- =====================================================
        SIDEBAR
    ====================================================== --}}

  <aside class="admin-variant-form-sidebar">


    {{-- STATUS --}}

    <section class="admin-panel">

      <div class="admin-panel-header">

        <div>

          <h2>
            Trạng thái
          </h2>

        </div>

      </div>


      <div class="admin-staff-switch">

        <input type="checkbox" id="is_active" name="is_active" value="1" {{
                        old(
                            'is_active',
                            true
                        )
                            ? 'checked'
                            : ''
                    }}>


        <label for="is_active">

          <span></span>


          <div>

            <strong>
              Đang hoạt động
            </strong>

            <small>
              Biến thể có thể được sử dụng để bán.
            </small>

          </div>

        </label>

      </div>

    </section>



    {{-- PRICE PREVIEW --}}

    <section class="admin-variant-price-preview">

      <i class="bi bi-calculator"></i>


      <div>

        <span>
          Giá cơ sở
        </span>


        <strong>
          {{ number_format(
                        (float) $product->current_price,
                        0,
                        ',',
                        '.'
                    ) }}đ
        </strong>


        <small>
          Giá cuối của biến thể phải lớn hơn 0đ.
        </small>

      </div>

    </section>



    {{-- ACTIONS --}}

    <section class="admin-panel admin-form-actions">

      <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">

        <i class="bi bi-plus-lg"></i>

        Thêm biến thể

      </button>


      <a href="{{ route(
                    'admin.products.show',
                    $product
                ) }}" class="admin-btn admin-btn-secondary admin-btn-full">

        Hủy

      </a>

    </section>

  </aside>

</form>


@endsection