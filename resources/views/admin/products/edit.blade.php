@extends('layouts.admin')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-product-edit.css') }}">
@endpush


@section(
'title',
'Sửa ' . $product->name . ' - VELORA Eyes'
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

@endphp



<div class="admin-product-edit" id="admin-product-edit" data-product-id="{{ $product->id }}">


  {{-- =====================================================
        HERO
    ====================================================== --}}

  <div class="admin-product-edit-hero">

    <div class="admin-product-edit-hero-left">

      <span class="admin-product-edit-kicker">
        EDIT PRODUCT
      </span>


      <h1 id="edit-product-title">
        {{ $product->name }}
      </h1>


      <p>

        Product #{{ $product->id }}

        ·

        <span id="edit-product-sku-text">
          {{ $product->sku }}
        </span>

      </p>

    </div>


    <div class="admin-product-edit-hero-actions">

      <a href="{{ route(
                    'admin.products.show',
                    $product
                ) }}" class="admin-btn admin-btn-secondary">
        <i class="bi bi-eye"></i>

        Chi tiết
      </a>


      @if($product->is_active)

      <a href="{{ route(
                        'products.show',
                        $product
                    ) }}" target="_blank" class="admin-btn admin-btn-primary">
        <i class="bi bi-box-arrow-up-right"></i>

        Xem ngoài website
      </a>

      @endif

    </div>

  </div>



  {{-- =====================================================
        SUMMARY
    ====================================================== --}}

  <div class="admin-product-edit-summary">


    {{-- STATUS --}}

    <div class="
                admin-product-edit-summary-card
                {{ $product->is_active ? 'success' : 'warning' }}
            " id="summary-business-card">

      <div class="admin-product-edit-summary-icon">

        <i class="
                        bi
                        {{ $product->is_active
                            ? 'bi-shop'
                            : 'bi-pause-circle'
                        }}
                    " id="summary-business-icon"></i>

      </div>


      <div class="admin-product-edit-summary-content">

        <span>
          Kinh doanh
        </span>

        <strong id="summary-business-text">

          {{ $product->is_active
                        ? 'Đang kinh doanh'
                        : 'Chưa kinh doanh'
                    }}

        </strong>

      </div>

    </div>



    {{-- IMAGE --}}

    <div class="
                admin-product-edit-summary-card
                {{ $hasRealImage ? 'success' : 'warning' }}
            " id="summary-image-card">

      <div class="admin-product-edit-summary-icon">

        <i class="bi bi-images"></i>

      </div>


      <div class="admin-product-edit-summary-content">

        <span>
          Hình ảnh
        </span>

        <strong id="summary-image-text">
          {{ $realImageCount }}/5 ảnh
        </strong>

      </div>

    </div>



    {{-- VARIANT --}}

    <div class="
                admin-product-edit-summary-card
                {{ $hasActiveVariant ? 'success' : 'warning' }}
            " id="summary-variant-card">

      <div class="admin-product-edit-summary-icon">

        <i class="bi bi-box-seam"></i>

      </div>


      <div class="admin-product-edit-summary-content">

        <span>
          Biến thể hoạt động
        </span>

        <strong id="summary-variant-text">
          {{ $activeVariantCount }} biến thể
        </strong>

      </div>

    </div>



    {{-- STOCK --}}

    <div class="admin-product-edit-summary-card">

      <div class="admin-product-edit-summary-icon">

        <i class="bi bi-boxes"></i>

      </div>


      <div class="admin-product-edit-summary-content">

        <span>
          Tổng tồn kho
        </span>

        <strong id="summary-stock-text">
          {{ $totalStock }}
        </strong>

      </div>

    </div>

  </div>



  {{-- =====================================================
        QUICK NAV
    ====================================================== --}}

  <nav class="admin-product-edit-nav">

    <a href="#edit-product-information">

      <i class="bi bi-pencil-square"></i>

      Thông tin

    </a>


    <a href="#edit-product-images">

      <i class="bi bi-images"></i>

      Hình ảnh

    </a>


    <a href="#edit-product-variants">

      <i class="bi bi-box-seam"></i>

      Biến thể & kho

    </a>


    <a href="#edit-product-business">

      <i class="bi bi-shop"></i>

      Kinh doanh

    </a>

  </nav>



  {{-- =====================================================
        AJAX MESSAGE
    ====================================================== --}}

  <div id="edit-product-message" hidden></div>



  {{-- =====================================================
        STEP 01
        INFORMATION
    ====================================================== --}}

  <section class="admin-product-edit-section" id="edit-product-information">

    <div class="admin-product-edit-section-head">

      <div class="admin-product-edit-section-title">

        <span class="admin-product-edit-section-number">
          01
        </span>


        <div>

          <h2>
            Thông tin sản phẩm
          </h2>

          <p>
            Chỉnh sửa thông tin chung, giá, thuộc tính và nội dung sản phẩm.
          </p>

        </div>

      </div>


      <span class="admin-product-edit-status success">

        <i class="bi bi-check-circle"></i>

        Đã có dữ liệu

      </span>

    </div>



    <div class="admin-product-edit-section-body">

      <form action="{{ route(
                    'admin.products.update',
                    $product
                ) }}" method="POST" id="edit-product-form">

        @csrf
        @method('PUT')


        {{-- =================================================
                    BASIC
                ================================================== --}}

        <div class="admin-panel">

          <div class="admin-panel-header">

            <div>

              <h2>
                Thông tin cơ bản
              </h2>

            </div>

          </div>


          <div class="admin-form-body">

            <div class="admin-form-grid">


              {{-- NAME --}}

              <div class="admin-form-group">

                <label for="name">

                  Tên sản phẩm
                  <span>*</span>

                </label>


                <input type="text" id="name" name="name" value="{{ old(
                                        'name',
                                        $product->name
                                    ) }}" maxlength="150" class="admin-form-control" required>


                <div class="admin-field-error" data-edit-error="name"></div>

              </div>



              {{-- SKU --}}

              <div class="admin-form-group">

                <label for="sku">

                  SKU sản phẩm
                  <span>*</span>

                </label>


                <input type="text" id="sku" name="sku" value="{{ old(
                                        'sku',
                                        $product->sku
                                    ) }}" maxlength="100" class="admin-form-control" required>


                <div class="admin-field-error" data-edit-error="sku"></div>

              </div>



              {{-- CATEGORY --}}

              <div class="admin-form-group">

                <label for="category_id">

                  Danh mục
                  <span>*</span>

                </label>


                <select id="category_id" name="category_id" class="admin-form-control" required>

                  @foreach(
                  $categories
                  as $category
                  )

                  <option value="{{ $category->id }}" {{
                                                (string) old(
                                                    'category_id',
                                                    $product->category_id
                                                )
                                                === (string) $category->id
                                                    ? 'selected'
                                                    : ''
                                            }}>

                    {{ $category->name }}

                  </option>

                  @endforeach

                </select>


                <div class="admin-field-error" data-edit-error="category_id"></div>

              </div>



              {{-- DIMENSIONS --}}

              <div class="admin-form-group">

                <label for="dimensions">
                  Kích thước
                </label>


                <input type="text" id="dimensions" name="dimensions" value="{{ old(
                                        'dimensions',
                                        $product->dimensions
                                    ) }}" maxlength="100" class="admin-form-control" placeholder="52-18-145 mm">


                <div class="admin-field-error" data-edit-error="dimensions"></div>

              </div>



              {{-- PRICE --}}

              <div class="admin-form-group">

                <label for="price">

                  Giá niêm yết
                  <span>*</span>

                </label>


                <div class="admin-product-price-input">

                  <input type="number" id="price" name="price" value="{{ old(
                                            'price',
                                            $product->price
                                        ) }}" min="1" step="1" class="admin-form-control" required>

                  <span>
                    đ
                  </span>

                </div>


                <div class="admin-field-error" data-edit-error="price"></div>

              </div>



              {{-- SALE PRICE --}}

              <div class="admin-form-group">

                <label for="sale_price">
                  Giá khuyến mãi
                </label>


                <div class="admin-product-price-input">

                  <input type="number" id="sale_price" name="sale_price" value="{{ old(
                                            'sale_price',
                                            $product->sale_price
                                        ) }}" min="0" step="1" class="admin-form-control"
                    placeholder="0 hoặc để trống = không giảm giá">

                  <span>
                    đ
                  </span>

                </div>


                <div class="admin-field-error" data-edit-error="sale_price"></div>

              </div>

            </div>

          </div>

        </div>



        {{-- =================================================
                    ATTRIBUTES
                ================================================== --}}

        <div class="admin-panel">

          <div class="admin-panel-header">

            <div>

              <h2>
                Thuộc tính kính
              </h2>

            </div>

          </div>


          <div class="admin-form-body">

            <div class="admin-product-attribute-grid">


              {{-- MATERIAL --}}

              <div class="admin-form-group">

                <label for="material">
                  Chất liệu
                </label>


                <select id="material" name="material" class="admin-form-control">

                  <option value="">
                    Chưa xác định
                  </option>


                  @foreach(
                  $materials
                  as $value => $label
                  )

                  <option value="{{ $value }}" {{
                                                old(
                                                    'material',
                                                    $product->material
                                                ) === $value
                                                    ? 'selected'
                                                    : ''
                                            }}>

                    {{ $label }}

                  </option>

                  @endforeach

                </select>

              </div>



              {{-- SHAPE --}}

              <div class="admin-form-group">

                <label for="shape">
                  Kiểu dáng
                </label>


                <select id="shape" name="shape" class="admin-form-control">

                  <option value="">
                    Chưa xác định
                  </option>


                  @foreach(
                  $shapes
                  as $value => $label
                  )

                  <option value="{{ $value }}" {{
                                                old(
                                                    'shape',
                                                    $product->shape
                                                ) === $value
                                                    ? 'selected'
                                                    : ''
                                            }}>

                    {{ $label }}

                  </option>

                  @endforeach

                </select>

              </div>



              {{-- GENDER --}}

              <div class="admin-form-group">

                <label for="gender">
                  Đối tượng
                </label>


                <select id="gender" name="gender" class="admin-form-control">

                  <option value="">
                    Chưa xác định
                  </option>


                  @foreach(
                  $genders
                  as $value => $label
                  )

                  <option value="{{ $value }}" {{
                                                old(
                                                    'gender',
                                                    $product->gender
                                                ) === $value
                                                    ? 'selected'
                                                    : ''
                                            }}>

                    {{ $label }}

                  </option>

                  @endforeach

                </select>

              </div>

            </div>

          </div>

        </div>



        {{-- =================================================
                    RECOMMENDATIONS
                ================================================== --}}

        <div class="admin-panel">

          <div class="admin-panel-header">

            <div>

              <h2>
                Gợi ý lựa chọn
              </h2>

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

                  <input type="checkbox" name="recommended_face_shapes[]" value="{{ $value }}" {{
                                                in_array(
                                                    $value,
                                                    $selectedFaceShapes
                                                )
                                                    ? 'checked'
                                                    : ''
                                            }}>

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

                  <input type="checkbox" name="style_tags[]" value="{{ $value }}" {{
                                                in_array(
                                                    $value,
                                                    $selectedStyles
                                                )
                                                    ? 'checked'
                                                    : ''
                                            }}>

                  <span>
                    <i class="bi bi-check-lg"></i>
                  </span>

                  {{ $label }}

                </label>

                @endforeach

              </div>

            </div>

          </div>

        </div>



        {{-- =================================================
                    CONTENT
                ================================================== --}}

        <div class="admin-panel">

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


              <textarea id="description" name="description" rows="7" maxlength="5000"
                class="admin-form-control admin-product-textarea">{{ old(
                                'description',
                                $product->description
                            ) }}</textarea>


              <div class="admin-field-error" data-edit-error="description"></div>

            </div>



            <div class="admin-form-group admin-product-highlights">

              <label for="highlights">
                Thông tin nổi bật
              </label>


              <textarea id="highlights" name="highlights" rows="5" maxlength="3000"
                class="admin-form-control admin-product-textarea">{{ old(
                                'highlights',
                                $product->highlights
                            ) }}</textarea>


              <div class="admin-field-error" data-edit-error="highlights"></div>

            </div>

          </div>

        </div>



        {{-- =================================================
                    SAVE INFORMATION
                ================================================== --}}

        <div class="admin-product-edit-save-bar">

          <button type="submit" class="admin-btn admin-btn-primary" id="save-edit-product">

            <i class="bi bi-check-lg"></i>

            Lưu thông tin

          </button>

        </div>

      </form>

    </div>

  </section>



  {{-- =====================================================
        STEP 02
        IMAGES
    ====================================================== --}}

  <section class="admin-product-edit-section" id="edit-product-images">

    <div class="admin-product-edit-section-head">

      <div class="admin-product-edit-section-title">

        <span class="admin-product-edit-section-number">
          02
        </span>


        <div>

          <h2>
            Hình ảnh sản phẩm
          </h2>

          <p>
            Upload, đặt ảnh chính hoặc xóa hình ảnh.
          </p>

        </div>

      </div>


      <span class="
                    admin-product-edit-status
                    {{ $hasRealImage ? 'success' : 'warning' }}
                " id="edit-image-status">

        <i class="
                        bi
                        {{ $hasRealImage
                            ? 'bi-check-circle'
                            : 'bi-exclamation-circle'
                        }}
                    "></i>

        <span>
          {{ $realImageCount }}/5 ảnh
        </span>

      </span>

    </div>



    <div class="admin-product-edit-section-body">

      <div class="admin-product-edit-image-layout">


        {{-- UPLOAD --}}

        <div class="admin-product-edit-upload">

          <h3>
            Thêm hình ảnh
          </h3>


          <p>
            JPG, JPEG, PNG hoặc WEBP.
            Tối đa 5 ảnh thật cho mỗi sản phẩm.
          </p>


          <form action="{{ route(
                            'admin.products.images.store',
                            $product
                        ) }}" method="POST" enctype="multipart/form-data" id="edit-image-upload-form">

            @csrf


            <input type="file" name="images[]" id="edit-image-input" accept=".jpg,.jpeg,.png,.webp" multiple required>


            <div class="admin-field-error" id="edit-image-error"></div>


            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full" id="edit-image-upload-button">

              <i class="bi bi-cloud-arrow-up"></i>

              Tải ảnh lên

            </button>

          </form>

        </div>



        {{-- IMAGE GRID --}}

        <div class="admin-product-edit-image-grid" id="edit-image-grid">

          @forelse(
          $realImages
          as $image
          )

          <article class="admin-product-edit-image-card" data-image-id="{{ $image->id }}">

            <div class="admin-product-edit-image">

              <img src="{{ asset(
                                        $image->image_path
                                    ) }}" alt="{{ $image->alt_text ?? $product->name }}">


              @if($image->is_primary)

              <span class="admin-product-edit-primary">

                <i class="bi bi-star-fill"></i>

                Ảnh chính

              </span>

              @endif

            </div>



            <div class="admin-product-edit-image-actions">


              @if(! $image->is_primary)

              <form action="{{ route(
                                            'admin.products.images.primary',
                                            [
                                                $product,
                                                $image,
                                            ]
                                        ) }}" method="POST">

                @csrf
                @method('PATCH')


                <button type="submit" class="admin-btn admin-btn-secondary" title="Đặt làm ảnh chính">

                  <i class="bi bi-star"></i>

                  Ảnh chính

                </button>

              </form>

              @endif



              <form action="{{ route(
                                        'admin.products.images.destroy',
                                        [
                                            $product,
                                            $image,
                                        ]
                                    ) }}" method="POST" onsubmit="return confirm('Xóa hình ảnh này?')">

                @csrf
                @method('DELETE')


                <button type="submit" class="admin-btn admin-btn-danger" title="Xóa ảnh">

                  <i class="bi bi-trash"></i>

                  Xóa

                </button>

              </form>

            </div>

          </article>

          @empty

          <div class="admin-product-edit-empty" id="edit-image-empty">

            <i class="bi bi-images"></i>

            <strong>
              Chưa có hình ảnh thật
            </strong>

            <span>
              Hãy tải ít nhất một ảnh để sản phẩm đủ điều kiện kinh doanh.
            </span>

          </div>

          @endforelse

        </div>

      </div>

    </div>

  </section>



  {{-- =====================================================
        STEP 03
        VARIANTS & STOCK
    ====================================================== --}}

  <section class="admin-product-edit-section" id="edit-product-variants">

    <div class="admin-product-edit-section-head">

      <div class="admin-product-edit-section-title">

        <span class="admin-product-edit-section-number">
          03
        </span>


        <div>

          <h2>
            Biến thể & tồn kho
          </h2>

          <p>
            Quản lý màu sắc, size, SKU, giá và số lượng tồn kho.
          </p>

        </div>

      </div>


      <span class="
                    admin-product-edit-status
                    {{ $hasActiveVariant ? 'success' : 'warning' }}
                " id="edit-variant-status">

        <i class="
                        bi
                        {{ $hasActiveVariant
                            ? 'bi-check-circle'
                            : 'bi-exclamation-circle'
                        }}
                    "></i>

        <span>
          {{ $activeVariantCount }} hoạt động
        </span>

      </span>

    </div>



    <div class="admin-product-edit-section-body">


      <div class="admin-product-edit-variant-top">

        <div class="admin-product-edit-variant-stats">

          <span class="admin-product-edit-stat-pill">

            <i class="bi bi-box-seam"></i>

            Tổng:
            <strong id="edit-total-variants">
              {{ $product->variants->count() }}
            </strong>

          </span>


          <span class="admin-product-edit-stat-pill">

            <i class="bi bi-check-circle"></i>

            Hoạt động:
            <strong id="edit-active-variants">
              {{ $activeVariantCount }}
            </strong>

          </span>


          <span class="admin-product-edit-stat-pill">

            <i class="bi bi-boxes"></i>

            Tồn kho:
            <strong id="edit-total-stock">
              {{ $totalStock }}
            </strong>

          </span>

        </div>


        <a href="#edit-add-variant" class="admin-btn admin-btn-primary">

          <i class="bi bi-plus-lg"></i>

          Thêm biến thể

        </a>

      </div>



      {{-- VARIANT TABLE --}}

      <div class="admin-product-edit-table-wrap" id="edit-variant-table-wrapper" @if($product->variants->isEmpty())
        hidden
        @endif
        >

        <table class="admin-product-edit-table">

          <thead>

            <tr>

              <th>SKU</th>

              <th>Màu</th>

              <th>Size</th>

              <th>Tồn kho</th>

              <th>Chênh lệch giá</th>

              <th>Giá cuối</th>

              <th>Trạng thái</th>

              <th>Thao tác</th>

            </tr>

          </thead>


          <tbody id="edit-variant-table-body">

            @foreach(
            $product->variants
            as $variant
            )

            <tr data-variant-id="{{ $variant->id }}">

              <td>

                <code class="admin-product-sku">
                                        {{ $variant->sku }}
                                    </code>

              </td>


              <td>
                {{ ucfirst($variant->color) }}
              </td>


              <td>
                <strong>
                  {{ $variant->size }}
                </strong>
              </td>


              <td>
                {{ $variant->stock_quantity }}
              </td>


              <td>

                {{ number_format(
                                        (float) $variant->price_adjustment,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

              </td>


              <td>

                <strong>

                  {{ number_format(
                                            (float) $variant->final_price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                </strong>

              </td>


              <td>

                @if($variant->is_active)

                <span class="admin-status success">
                  Hoạt động
                </span>

                @else

                <span class="admin-status muted">
                  Ngừng bán
                </span>

                @endif

              </td>


              <td>

                <div class="admin-product-edit-variant-actions">

                  <a href="{{ route(
                                                'admin.products.variants.edit',
                                                [
                                                    $product,
                                                    $variant,
                                                ]
                                            ) }}" class="admin-btn admin-btn-secondary">

                    <i class="bi bi-pencil"></i>

                    Sửa

                  </a>


                  <form action="{{ route(
                                                'admin.products.variants.destroy',
                                                [
                                                    $product,
                                                    $variant,
                                                ]
                                            ) }}" method="POST" onsubmit="return confirm('Xóa biến thể này?')">

                    @csrf
                    @method('DELETE')


                    <button type="submit" class="admin-btn admin-btn-danger">

                      <i class="bi bi-trash"></i>

                      Xóa

                    </button>

                  </form>

                </div>

              </td>

            </tr>

            @endforeach

          </tbody>

        </table>

      </div>



      @if($product->variants->isEmpty())

      <div class="admin-product-edit-empty" id="edit-variant-empty">

        <i class="bi bi-box-seam"></i>

        <strong>
          Chưa có biến thể
        </strong>

        <span>
          Hãy thêm ít nhất một biến thể hoạt động.
        </span>

      </div>

      @endif



      {{-- =================================================
                ADD VARIANT
            ================================================== --}}

      <div class="admin-panel" id="edit-add-variant">

        <div class="admin-panel-header">

          <div>

            <h2>
              Thêm biến thể mới
            </h2>

            <p>
              0 ở Chênh lệch giá = giữ nguyên giá sản phẩm.
            </p>

          </div>

        </div>


        <div class="admin-form-body">

          <form action="{{ route(
                            'admin.products.variants.store',
                            $product
                        ) }}" method="POST" id="edit-variant-form">

            @csrf


            <div class="admin-form-grid">


              {{-- COLOR --}}

              <div class="admin-form-group">

                <label for="edit-variant-color">
                  Màu sắc
                </label>


                <input type="text" id="edit-variant-color" name="color" maxlength="50" class="admin-form-control"
                  placeholder="Đen" required>


                <div class="admin-field-error" data-edit-variant-error="color"></div>

              </div>



              {{-- SIZE --}}

              <div class="admin-form-group">

                <label for="edit-variant-size">
                  Size
                </label>


                <input type="text" id="edit-variant-size" name="size" maxlength="30" class="admin-form-control"
                  placeholder="M" required>


                <div class="admin-field-error" data-edit-variant-error="size"></div>

              </div>



              {{-- SKU --}}

              <div class="admin-form-group">

                <label for="edit-variant-sku">
                  SKU biến thể
                </label>


                <input type="text" id="edit-variant-sku" name="sku" maxlength="120" class="admin-form-control"
                  placeholder="VLR-001-BLK-M" required>


                <div class="admin-field-error" data-edit-variant-error="sku"></div>

              </div>



              {{-- STOCK --}}

              <div class="admin-form-group">

                <label for="edit-variant-stock">
                  Tồn kho
                </label>


                <input type="number" id="edit-variant-stock" name="stock_quantity" value="0" min="0" step="1"
                  class="admin-form-control" required>


                <div class="admin-field-error" data-edit-variant-error="stock_quantity"></div>

              </div>



              {{-- PRICE ADJUSTMENT --}}

              <div class="admin-form-group">

                <label for="edit-variant-price-adjustment">
                  Chênh lệch giá
                </label>


                <input type="number" id="edit-variant-price-adjustment" name="price_adjustment" value="0" step="1"
                  class="admin-form-control" required>


                <small class="admin-variant-help">
                  0 = giữ nguyên giá sản phẩm.
                </small>


                <div class="admin-field-error" data-edit-variant-error="price_adjustment"></div>

              </div>

            </div>



            <div class="admin-product-edit-save-bar">

              <label class="admin-product-check">

                <input type="checkbox" name="is_active" value="1" checked>

                <span>
                  <i class="bi bi-check-lg"></i>
                </span>

                Biến thể đang hoạt động

              </label>


              <button type="submit" class="admin-btn admin-btn-primary" id="edit-add-variant-button">

                <i class="bi bi-plus-lg"></i>

                Thêm biến thể

              </button>

            </div>

          </form>

        </div>

      </div>

    </div>

  </section>



  {{-- =====================================================
        STEP 04
        BUSINESS
    ====================================================== --}}

  <section class="admin-product-edit-section" id="edit-product-business">

    <div class="admin-product-edit-section-head">

      <div class="admin-product-edit-section-title">

        <span class="admin-product-edit-section-number">
          04
        </span>


        <div>

          <h2>
            Kinh doanh
          </h2>

          <p>
            Kiểm tra điều kiện và quản lý trạng thái hiển thị cho khách hàng.
          </p>

        </div>

      </div>


      <span class="
                    admin-product-edit-status
                    {{ $product->is_active ? 'success' : 'muted' }}
                " id="edit-business-status">

        <i class="
                        bi
                        {{ $product->is_active
                            ? 'bi-check-circle'
                            : 'bi-pause-circle'
                        }}
                    "></i>

        <span>

          {{ $product->is_active
                        ? 'Đang kinh doanh'
                        : 'Chưa kinh doanh'
                    }}

        </span>

      </span>

    </div>



    <div class="admin-product-edit-section-body">

      <div class="admin-product-edit-business-grid">


        {{-- CHECKLIST --}}

        <div class="admin-product-edit-checklist">


          <div class="admin-product-edit-check complete">

            <i class="bi bi-check-circle-fill"></i>


            <span>

              <strong>
                Thông tin sản phẩm
              </strong>

              <small>
                Product #{{ $product->id }} đã tồn tại.
              </small>

            </span>

          </div>



          <div class="
                            admin-product-edit-check
                            {{ $hasRealImage ? 'complete' : 'missing' }}
                        " id="business-check-image">

            <i class="
                                bi
                                {{ $hasRealImage
                                    ? 'bi-check-circle-fill'
                                    : 'bi-x-circle-fill'
                                }}
                            "></i>


            <span>

              <strong>
                Hình ảnh
              </strong>

              <small id="business-check-image-text">

                @if($hasRealImage)

                Đã có {{ $realImageCount }} ảnh thật.

                @else

                Cần ít nhất một ảnh thật.

                @endif

              </small>

            </span>

          </div>



          <div class="
                            admin-product-edit-check
                            {{ $hasActiveVariant ? 'complete' : 'missing' }}
                        " id="business-check-variant">

            <i class="
                                bi
                                {{ $hasActiveVariant
                                    ? 'bi-check-circle-fill'
                                    : 'bi-x-circle-fill'
                                }}
                            "></i>


            <span>

              <strong>
                Biến thể hoạt động
              </strong>

              <small id="business-check-variant-text">

                @if($hasActiveVariant)

                Đã có {{ $activeVariantCount }} biến thể hoạt động.

                @else

                Cần ít nhất một biến thể hoạt động.

                @endif

              </small>

            </span>

          </div>

        </div>



        {{-- BUSINESS CARD --}}

        <div class="
                        admin-product-edit-business-card
                        {{ $readyForSale ? 'ready' : 'warning' }}
                    " id="edit-business-card">

          <h3 id="edit-business-title">

            @if($readyForSale)

            Sản phẩm đủ điều kiện kinh doanh

            @else

            Sản phẩm chưa đủ điều kiện

            @endif

          </h3>


          <p id="edit-business-message">

            @if($readyForSale)

            Bạn có thể bật hoặc tắt trạng thái kinh doanh của sản phẩm.

            @else

            Cần bổ sung hình ảnh thật và biến thể hoạt động trước khi bật kinh doanh.

            @endif

          </p>



          <div class="admin-staff-switch">

            <input type="checkbox" id="edit-product-is-active" name="is_active" value="1" form="edit-product-form" {{
                                $product->is_active
                                    ? 'checked'
                                    : ''
                            }} {{
                                ! $readyForSale
                                && ! $product->is_active
                                    ? 'disabled'
                                    : ''
                            }}>


            <label for="edit-product-is-active">

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



          <div class="admin-field-error" id="edit-business-error"></div>



          <div class="admin-product-edit-save-bar">

            <button type="submit" form="edit-product-form" class="admin-btn admin-btn-primary"
              id="save-business-button">

              <i class="bi bi-check-lg"></i>

              Lưu trạng thái

            </button>

          </div>

        </div>

      </div>

    </div>

  </section>


</div>



{{-- =========================================================
    JAVASCRIPT
========================================================= --}}

<script>
document.addEventListener(
  'DOMContentLoaded',
  function() {

    /*
    |--------------------------------------------------------------------------
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const productForm =
      document.getElementById(
        'edit-product-form'
      );


    const saveProductButton =
      document.getElementById(
        'save-edit-product'
      );


    const saveBusinessButton =
      document.getElementById(
        'save-business-button'
      );


    const businessCheckbox =
      document.getElementById(
        'edit-product-is-active'
      );


    const messageBox =
      document.getElementById(
        'edit-product-message'
      );


    const imageForm =
      document.getElementById(
        'edit-image-upload-form'
      );


    const imageInput =
      document.getElementById(
        'edit-image-input'
      );


    const imageButton =
      document.getElementById(
        'edit-image-upload-button'
      );


    const imageError =
      document.getElementById(
        'edit-image-error'
      );


    const imageGrid =
      document.getElementById(
        'edit-image-grid'
      );


    const variantForm =
      document.getElementById(
        'edit-variant-form'
      );


    const variantButton =
      document.getElementById(
        'edit-add-variant-button'
      );


    const variantTableWrapper =
      document.getElementById(
        'edit-variant-table-wrapper'
      );


    const variantTableBody =
      document.getElementById(
        'edit-variant-table-body'
      );


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    let hasRealImage = {
      {
        $hasRealImage ? 'true' : 'false'
      }
    };


    let realImageCount = {
      {
        $realImageCount
      }
    };


    let hasActiveVariant = {
      {
        $hasActiveVariant ? 'true' : 'false'
      }
    };


    let activeVariantCount = {
      {
        $activeVariantCount
      }
    };


    let totalStock = {
      {
        $totalStock
      }
    };


    let productIsActive = {
      {
        $product - > is_active ? 'true' : 'false'
      }
    };


    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */

    const productId = {
      {
        $product - > id
      }
    };


    const imageBaseUrl =
      "{{ route('admin.products.images.store', $product) }}";


    const variantBaseUrl =
      "{{ route('admin.products.variants.store', $product) }}";


    /*
    |--------------------------------------------------------------------------
    | ESCAPE HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(
      value
    ) {

      return String(
          value ?? ''
        )
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
    }



    /*
    |--------------------------------------------------------------------------
    | MESSAGE
    |--------------------------------------------------------------------------
    */

    function showMessage(
      message,
      type
    ) {

      messageBox.hidden =
        false;


      messageBox.className =
        type === 'success' ?
        'admin-alert admin-alert-success' :
        'admin-alert admin-alert-danger';


      messageBox.textContent =
        message;


      messageBox.scrollIntoView({
        behavior: 'smooth',
        block: 'center',
      });
    }



    /*
    |--------------------------------------------------------------------------
    | CLEAR PRODUCT ERRORS
    |--------------------------------------------------------------------------
    */

    function clearProductErrors() {

      document
        .querySelectorAll(
          '[data-edit-error]'
        )
        .forEach(
          function(element) {

            element.textContent =
              '';

          }
        );


      document.getElementById(
          'edit-business-error'
        ).textContent =
        '';
    }



    /*
    |--------------------------------------------------------------------------
    | SHOW PRODUCT ERRORS
    |--------------------------------------------------------------------------
    */

    function showProductErrors(
      errors
    ) {

      Object.keys(
          errors
        )
        .forEach(
          function(field) {

            if (
              field ===
              'is_active'
            ) {

              document.getElementById(
                  'edit-business-error'
                ).textContent =
                errors[field][0];


              return;
            }


            const normalized =
              field.split('.')[0];


            const box =
              document.querySelector(
                '[data-edit-error="' +
                normalized +
                '"]'
              );


            if (box) {

              box.textContent =
                errors[field][0];
            }

          }
        );
    }



    /*
    |--------------------------------------------------------------------------
    | UPDATE BUSINESS UI
    |--------------------------------------------------------------------------
    */

    function updateBusinessUI() {

      const ready =
        hasRealImage &&
        hasActiveVariant;


      /*
       * Summary image.
       */
      document.getElementById(
          'summary-image-text'
        ).textContent =
        realImageCount +
        '/5 ảnh';


      /*
       * Summary variant.
       */
      document.getElementById(
          'summary-variant-text'
        ).textContent =
        activeVariantCount +
        ' biến thể';


      document.getElementById(
          'summary-stock-text'
        ).textContent =
        totalStock;


      /*
       * Business image checklist.
       */
      const checkImage =
        document.getElementById(
          'business-check-image'
        );


      checkImage.className =
        'admin-product-edit-check ' +
        (
          hasRealImage ?
          'complete' :
          'missing'
        );


      checkImage
        .querySelector('i')
        .className =
        'bi ' +
        (
          hasRealImage ?
          'bi-check-circle-fill' :
          'bi-x-circle-fill'
        );


      document.getElementById(
          'business-check-image-text'
        ).textContent =
        hasRealImage ?
        'Đã có ' +
        realImageCount +
        ' ảnh thật.' :
        'Cần ít nhất một ảnh thật.';


      /*
       * Business Variant checklist.
       */
      const checkVariant =
        document.getElementById(
          'business-check-variant'
        );


      checkVariant.className =
        'admin-product-edit-check ' +
        (
          hasActiveVariant ?
          'complete' :
          'missing'
        );


      checkVariant
        .querySelector('i')
        .className =
        'bi ' +
        (
          hasActiveVariant ?
          'bi-check-circle-fill' :
          'bi-x-circle-fill'
        );


      document.getElementById(
          'business-check-variant-text'
        ).textContent =
        hasActiveVariant ?
        'Đã có ' +
        activeVariantCount +
        ' biến thể hoạt động.' :
        'Cần ít nhất một biến thể hoạt động.';


      /*
       * Business card.
       */
      const card =
        document.getElementById(
          'edit-business-card'
        );


      const title =
        document.getElementById(
          'edit-business-title'
        );


      const message =
        document.getElementById(
          'edit-business-message'
        );


      card.className =
        'admin-product-edit-business-card ' +
        (
          ready ?
          'ready' :
          'warning'
        );


      if (ready) {

        title.textContent =
          'Sản phẩm đủ điều kiện kinh doanh';


        message.textContent =
          'Bạn có thể bật hoặc tắt trạng thái kinh doanh của sản phẩm.';

      } else {

        title.textContent =
          'Sản phẩm chưa đủ điều kiện';


        message.textContent =
          'Cần ít nhất một ảnh thật và một biến thể hoạt động trước khi bật kinh doanh.';
      }


      /*
       * Nếu Product chưa active
       * và chưa đủ điều kiện:
       * khóa switch.
       *
       * Nếu đang active thì vẫn cho
       * tắt kinh doanh.
       */
      businessCheckbox.disabled = !ready &&
        !productIsActive;
    }



    /*
    |--------------------------------------------------------------------------
    | PRODUCT UPDATE AJAX
    |--------------------------------------------------------------------------
    */

    productForm.addEventListener(
      'submit',
      async function(event) {

        event.preventDefault();


        clearProductErrors();


        saveProductButton.disabled =
          true;


        saveBusinessButton.disabled =
          true;


        saveProductButton.innerHTML =
          '<i class="bi bi-hourglass-split"></i> Đang lưu...';


        try {

          const payload =
            new FormData(
              productForm
            );


          const response =
            await fetch(
              productForm.action, {
                method: 'POST',

                body: payload,

                headers: {

                  'Accept': 'application/json',

                  'X-Requested-With': 'XMLHttpRequest',
                },
              }
            );


          const data =
            await response.json();


          if (
            response.status ===
            422
          ) {

            showProductErrors(
              data.errors ?? {}
            );


            showMessage(
              data.message ??
              'Thông tin sản phẩm chưa hợp lệ.',
              'error'
            );


            return;
          }


          if (!response.ok) {

            throw new Error(
              data.message ??
              'Không thể cập nhật sản phẩm.'
            );
          }


          /*
           * Update state.
           */
          productIsActive =
            data.product.is_active;


          hasRealImage =
            data.status.has_real_image;


          hasActiveVariant =
            data.status.has_active_variant;


          activeVariantCount =
            data.status.active_variant_count;


          totalStock =
            data.status.total_stock;


          /*
           * Header.
           */
          document.getElementById(
              'edit-product-title'
            ).textContent =
            data.product.name;


          document.getElementById(
              'edit-product-sku-text'
            ).textContent =
            data.product.sku;


          /*
           * Business summary.
           */
          document.getElementById(
              'summary-business-text'
            ).textContent =
            productIsActive ?
            'Đang kinh doanh' :
            'Chưa kinh doanh';


          const businessCard =
            document.getElementById(
              'summary-business-card'
            );


          businessCard.classList.remove(
            'success',
            'warning'
          );


          businessCard.classList.add(
            productIsActive ?
            'success' :
            'warning'
          );


          /*
           * Business status.
           */
          const status =
            document.getElementById(
              'edit-business-status'
            );


          status.className =
            'admin-product-edit-status ' +
            (
              productIsActive ?
              'success' :
              'muted'
            );


          status.innerHTML =
            productIsActive ?
            '<i class="bi bi-check-circle"></i><span>Đang kinh doanh</span>' :
            '<i class="bi bi-pause-circle"></i><span>Chưa kinh doanh</span>';


          updateBusinessUI();


          showMessage(
            'Cập nhật sản phẩm thành công. Bạn vẫn đang ở cùng màn hình.',
            'success'
          );

        } catch (error) {

          showMessage(
            error.message ??
            'Có lỗi xảy ra khi cập nhật sản phẩm.',
            'error'
          );

        } finally {

          saveProductButton.disabled =
            false;


          saveBusinessButton.disabled =
            false;


          saveProductButton.innerHTML =
            '<i class="bi bi-check-lg"></i> Lưu thông tin';

        }

      }
    );



    /*
    |--------------------------------------------------------------------------
    | RENDER IMAGES
    |--------------------------------------------------------------------------
    */

    function renderImages(
      images
    ) {

      imageGrid.innerHTML =
        '';


      if (
        !images ||
        images.length === 0
      ) {

        imageGrid.innerHTML = `
                    <div class="admin-product-edit-empty">

                        <i class="bi bi-images"></i>

                        <strong>
                            Chưa có hình ảnh thật
                        </strong>

                        <span>
                            Hãy tải ít nhất một ảnh để sản phẩm đủ điều kiện kinh doanh.
                        </span>

                    </div>
                `;


        return;
      }


      images.forEach(
        function(image) {

          const card =
            document.createElement(
              'article'
            );


          card.className =
            'admin-product-edit-image-card';


          const primaryButton =
            image.is_primary ?
            '' :
            `
                                <form
                                    action="${imageBaseUrl}/${image.id}/primary"
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="_token"
                                        value="{{ csrf_token() }}"
                                    >

                                    <input
                                        type="hidden"
                                        name="_method"
                                        value="PATCH"
                                    >

                                    <button
                                        type="submit"
                                        class="admin-btn admin-btn-secondary"
                                    >
                                        <i class="bi bi-star"></i>

                                        Ảnh chính
                                    </button>

                                </form>
                            `;


          card.innerHTML = `

                        <div class="admin-product-edit-image">

                            <img
                                src="${escapeHtml(image.image_url)}"
                                alt="${escapeHtml(image.alt_text)}"
                            >

                            ${
                                image.is_primary
                                    ? `
                                        <span class="admin-product-edit-primary">

                                            <i class="bi bi-star-fill"></i>

                                            Ảnh chính

                                        </span>
                                    `
                                    : ''
                            }

                        </div>


                        <div class="admin-product-edit-image-actions">

                            ${primaryButton}


                            <form
                                action="${imageBaseUrl}/${image.id}"
                                method="POST"
                                onsubmit="return confirm('Xóa hình ảnh này?')"
                            >

                                <input
                                    type="hidden"
                                    name="_token"
                                    value="{{ csrf_token() }}"
                                >

                                <input
                                    type="hidden"
                                    name="_method"
                                    value="DELETE"
                                >

                                <button
                                    type="submit"
                                    class="admin-btn admin-btn-danger"
                                >

                                    <i class="bi bi-trash"></i>

                                    Xóa

                                </button>

                            </form>

                        </div>
                    `;


          imageGrid.appendChild(
            card
          );

        }
      );
    }



    /*
    |--------------------------------------------------------------------------
    | IMAGE UPLOAD AJAX
    |--------------------------------------------------------------------------
    */

    imageForm.addEventListener(
      'submit',
      async function(event) {

        event.preventDefault();


        imageError.textContent =
          '';


        if (
          !imageInput.files ||
          imageInput.files.length === 0
        ) {

          imageError.textContent =
            'Vui lòng chọn ít nhất một hình ảnh.';


          return;
        }


        imageButton.disabled =
          true;


        imageButton.innerHTML =
          '<i class="bi bi-hourglass-split"></i> Đang tải...';


        try {

          const response =
            await fetch(
              imageForm.action, {
                method: 'POST',

                body: new FormData(
                  imageForm
                ),

                headers: {

                  'Accept': 'application/json',

                  'X-Requested-With': 'XMLHttpRequest',
                },
              }
            );


          const data =
            await response.json();


          if (
            response.status ===
            422
          ) {

            const errors =
              data.errors ?? {};


            imageError.textContent =
              errors.images?. [0] ??
              errors['images.0']?. [0] ??
              data.message ??
              'Hình ảnh không hợp lệ.';


            return;
          }


          if (!response.ok) {

            throw new Error(
              data.message ??
              'Không thể tải hình ảnh.'
            );
          }


          realImageCount =
            data.real_image_count;


          hasRealImage =
            data.has_real_image;


          hasActiveVariant =
            data.has_active_variant;


          renderImages(
            data.images
          );


          /*
           * Image section status.
           */
          const status =
            document.getElementById(
              'edit-image-status'
            );


          status.className =
            'admin-product-edit-status success';


          status.innerHTML =
            '<i class="bi bi-check-circle"></i>' +
            '<span>' +
            realImageCount +
            '/5 ảnh</span>';


          updateBusinessUI();


          imageInput.value =
            '';


          showMessage(
            'Tải hình ảnh thành công.',
            'success'
          );

        } catch (error) {

          showMessage(
            error.message ??
            'Có lỗi xảy ra khi tải hình ảnh.',
            'error'
          );

        } finally {

          imageButton.disabled =
            false;


          imageButton.innerHTML =
            '<i class="bi bi-cloud-arrow-up"></i> Tải ảnh lên';

        }

      }
    );



    /*
    |--------------------------------------------------------------------------
    | RENDER VARIANTS
    |--------------------------------------------------------------------------
    */

    function renderVariants(
      variants
    ) {

      variantTableBody.innerHTML =
        '';


      if (
        !variants ||
        variants.length === 0
      ) {

        variantTableWrapper.hidden =
          true;


        return;
      }


      variantTableWrapper.hidden =
        false;


      variants.forEach(
        function(variant) {

          const row =
            document.createElement(
              'tr'
            );


          row.dataset.variantId =
            variant.id;


          row.innerHTML = `

                        <td>

                            <code class="admin-product-sku">
                                ${escapeHtml(variant.sku)}
                            </code>

                        </td>


                        <td>
                            ${escapeHtml(variant.color)}
                        </td>


                        <td>

                            <strong>
                                ${escapeHtml(variant.size)}
                            </strong>

                        </td>


                        <td>
                            ${variant.stock_quantity}
                        </td>


                        <td>
                            ${Number(
                                variant.price_adjustment
                            ).toLocaleString('vi-VN')}đ
                        </td>


                        <td>

                            <strong>

                                ${Number(
                                    variant.final_price
                                ).toLocaleString('vi-VN')}đ

                            </strong>

                        </td>


                        <td>

                            ${
                                variant.is_active
                                    ? `
                                        <span class="admin-status success">
                                            Hoạt động
                                        </span>
                                    `
                                    : `
                                        <span class="admin-status muted">
                                            Ngừng bán
                                        </span>
                                    `
                            }

                        </td>


                        <td>

                            <div class="admin-product-edit-variant-actions">

                                <a
                                    href="${variantBaseUrl}/${variant.id}/edit"
                                    class="admin-btn admin-btn-secondary"
                                >
                                    <i class="bi bi-pencil"></i>

                                    Sửa
                                </a>


                                <form
                                    action="${variantBaseUrl}/${variant.id}"
                                    method="POST"
                                    onsubmit="return confirm('Xóa biến thể này?')"
                                >

                                    <input
                                        type="hidden"
                                        name="_token"
                                        value="{{ csrf_token() }}"
                                    >

                                    <input
                                        type="hidden"
                                        name="_method"
                                        value="DELETE"
                                    >

                                    <button
                                        type="submit"
                                        class="admin-btn admin-btn-danger"
                                    >

                                        <i class="bi bi-trash"></i>

                                        Xóa

                                    </button>

                                </form>

                            </div>

                        </td>
                    `;


          variantTableBody.appendChild(
            row
          );

        }
      );
    }



    /*
    |--------------------------------------------------------------------------
    | ADD VARIANT AJAX
    |--------------------------------------------------------------------------
    */

    variantForm.addEventListener(
      'submit',
      async function(event) {

        event.preventDefault();


        document
          .querySelectorAll(
            '[data-edit-variant-error]'
          )
          .forEach(
            function(element) {

              element.textContent =
                '';

            }
          );


        variantButton.disabled =
          true;


        variantButton.innerHTML =
          '<i class="bi bi-hourglass-split"></i> Đang lưu...';


        try {

          const response =
            await fetch(
              variantForm.action, {
                method: 'POST',

                body: new FormData(
                  variantForm
                ),

                headers: {

                  'Accept': 'application/json',

                  'X-Requested-With': 'XMLHttpRequest',
                },
              }
            );


          const data =
            await response.json();


          if (
            response.status ===
            422
          ) {

            const errors =
              data.errors ?? {};


            Object.keys(
                errors
              )
              .forEach(
                function(field) {

                  const box =
                    document.querySelector(
                      '[data-edit-variant-error="' +
                      field +
                      '"]'
                    );


                  if (box) {

                    box.textContent =
                      errors[field][0];
                  }

                }
              );


            showMessage(
              data.message ??
              'Thông tin biến thể chưa hợp lệ.',
              'error'
            );


            return;
          }


          if (!response.ok) {

            throw new Error(
              data.message ??
              'Không thể thêm biến thể.'
            );
          }


          renderVariants(
            data.variants
          );


          activeVariantCount =
            data.active_variant_count;


          hasActiveVariant =
            data.has_active_variant;


          hasRealImage =
            data.has_real_image;


          totalStock =
            data.total_stock;


          document.getElementById(
              'edit-total-variants'
            ).textContent =
            data.variants.length;


          document.getElementById(
              'edit-active-variants'
            ).textContent =
            activeVariantCount;


          document.getElementById(
              'edit-total-stock'
            ).textContent =
            totalStock;


          /*
           * Variant status.
           */
          const status =
            document.getElementById(
              'edit-variant-status'
            );


          status.className =
            'admin-product-edit-status ' +
            (
              hasActiveVariant ?
              'success' :
              'warning'
            );


          status.innerHTML =
            hasActiveVariant ?
            '<i class="bi bi-check-circle"></i><span>' +
            activeVariantCount +
            ' hoạt động</span>' :
            '<i class="bi bi-exclamation-circle"></i><span>0 hoạt động</span>';


          variantForm.reset();


          document.getElementById(
              'edit-variant-stock'
            ).value =
            0;


          document.getElementById(
              'edit-variant-price-adjustment'
            ).value =
            0;


          variantForm
            .querySelector(
              '[name="is_active"]'
            )
            .checked =
            true;


          updateBusinessUI();


          showMessage(
            'Thêm biến thể thành công.',
            'success'
          );

        } catch (error) {

          showMessage(
            error.message ??
            'Có lỗi xảy ra khi thêm biến thể.',
            'error'
          );

        } finally {

          variantButton.disabled =
            false;


          variantButton.innerHTML =
            '<i class="bi bi-plus-lg"></i> Thêm biến thể';

        }

      }
    );



    updateBusinessUI();

  }
);
</script>


@endsection