@extends('layouts.admin')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-product-completion.css') }}">
@endpush


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



<div class="product-completion" id="single-page-product-create">


  {{-- =====================================================
        HEADER
    ====================================================== --}}

  <div class="product-completion-hero">

    <div>

      <span class="product-completion-hero-kicker">
        CREATE PRODUCT
      </span>


      <h1>
        Thêm sản phẩm
      </h1>


      <p>
        Tạo và hoàn thiện sản phẩm trên cùng một màn hình.
      </p>

    </div>


    <div class="product-completion-percent">

      <strong id="completion-percent">
        0%
      </strong>


      <span id="completion-text">
        0/4 bước hoàn thành
      </span>

    </div>

  </div>



  {{-- =====================================================
        PROGRESS
    ====================================================== --}}

  <div class="product-completion-progress">


    {{-- STEP 1 --}}

    <div class="product-completion-progress-item current" id="progress-step-1">

      <span class="product-completion-progress-number">
        1
      </span>


      <div class="product-completion-progress-content">

        <strong>
          Thông tin
        </strong>

        <small id="progress-info-text">
          Chưa lưu
        </small>

      </div>

    </div>



    {{-- STEP 2 --}}

    <div class="product-completion-progress-item" id="progress-step-2">

      <span class="product-completion-progress-number">
        2
      </span>


      <div class="product-completion-progress-content">

        <strong>
          Hình ảnh
        </strong>

        <small>
          Không bắt buộc khi tạo
        </small>

      </div>

    </div>



    {{-- STEP 3 --}}

    <div class="product-completion-progress-item" id="progress-step-3">

      <span class="product-completion-progress-number">
        3
      </span>


      <div class="product-completion-progress-content">

        <strong>
          Biến thể & kho
        </strong>

        <small>
          Không bắt buộc khi tạo
        </small>

      </div>

    </div>



    {{-- STEP 4 --}}

    <div class="product-completion-progress-item" id="progress-step-4">

      <span class="product-completion-progress-number">
        4
      </span>


      <div class="product-completion-progress-content">

        <strong>
          Kinh doanh
        </strong>

        <small>
          Chưa thể kích hoạt
        </small>

      </div>

    </div>

  </div>



  {{-- =====================================================
        AJAX MESSAGE
    ====================================================== --}}

  <div id="create-product-message" hidden></div>



  {{-- =====================================================
        STEP 1 - BASIC INFORMATION
    ====================================================== --}}

  <section class="product-completion-step" id="product-basic-information">

    <div class="product-completion-step-head">

      <div class="product-completion-step-title">

        <span class="product-completion-step-number">
          01
        </span>


        <div>

          <h2>
            Thông tin sản phẩm
          </h2>

          <p>
            Lưu thông tin cơ bản trước để hệ thống tạo sản phẩm.
          </p>

        </div>

      </div>


      <span class="product-completion-status warning" id="basic-information-status">

        <i class="bi bi-clock"></i>

        Chưa lưu

      </span>

    </div>



    <form action="{{ route('admin.products.store') }}" method="POST" id="basic-product-form">

      @csrf


      {{-- BASIC --}}

      <div class="admin-form-body">

        <div class="admin-form-grid">


          {{-- NAME --}}

          <div class="admin-form-group">

            <label for="name">

              Tên sản phẩm
              <span>*</span>

            </label>


            <input type="text" id="name" name="name" value="{{ old('name') }}" maxlength="150"
              class="admin-form-control" placeholder="Ví dụ: Gọng kính Velora Classic" required>


            <div class="admin-field-error" data-error-for="name"></div>

          </div>



          {{-- SKU --}}

          <div class="admin-form-group">

            <label for="sku">

              SKU sản phẩm
              <span>*</span>

            </label>


            <input type="text" id="sku" name="sku" value="{{ old('sku') }}" maxlength="100" class="admin-form-control"
              placeholder="VLR-CLASSIC-001" required>


            <div class="admin-field-error" data-error-for="sku"></div>

          </div>



          {{-- CATEGORY --}}

          <div class="admin-form-group">

            <label for="category_id">

              Danh mục
              <span>*</span>

            </label>


            <select id="category_id" name="category_id" class="admin-form-control" required>

              <option value="">
                Chọn danh mục
              </option>


              @foreach(
              $categories
              as $category
              )

              <option value="{{ $category->id }}" {{
                                        (string) old('category_id')
                                        === (string) $category->id
                                            ? 'selected'
                                            : ''
                                    }}>

                {{ $category->name }}

              </option>

              @endforeach

            </select>


            <div class="admin-field-error" data-error-for="category_id"></div>

          </div>



          {{-- DIMENSIONS --}}

          <div class="admin-form-group">

            <label for="dimensions">
              Kích thước
            </label>


            <input type="text" id="dimensions" name="dimensions" value="{{ old('dimensions') }}" maxlength="100"
              class="admin-form-control" placeholder="Ví dụ: 52-18-145 mm">


            <div class="admin-field-error" data-error-for="dimensions"></div>

          </div>



          {{-- PRICE --}}

          <div class="admin-form-group">

            <label for="price">

              Giá niêm yết
              <span>*</span>

            </label>


            <div class="admin-product-price-input">

              <input type="number" id="price" name="price" value="{{ old('price') }}" min="1" step="1"
                class="admin-form-control" placeholder="1500000" required>

              <span>đ</span>

            </div>


            <div class="admin-field-error" data-error-for="price"></div>

          </div>



          {{-- SALE PRICE --}}

          <div class="admin-form-group">

            <label for="sale_price">
              Giá khuyến mãi
            </label>


            <div class="admin-product-price-input">

              <input type="number" id="sale_price" name="sale_price" value="{{ old('sale_price') }}" min="0" step="1"
                class="admin-form-control" placeholder="Để trống nếu không giảm giá">

              <span>đ</span>

            </div>


            <div class="admin-field-error" data-error-for="sale_price"></div>

          </div>

        </div>

      </div>



      {{-- ATTRIBUTES --}}

      <div class="admin-panel">

        <div class="admin-panel-header">

          <div>

            <h2>
              Thuộc tính kính
            </h2>

            <p>
              Có thể để trống nếu chưa có thông tin.
            </p>

          </div>

        </div>


        <div class="admin-form-body">

          <div class="admin-product-attribute-grid">


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
                                            old('material') === $value
                                                ? 'selected'
                                                : ''
                                        }}>

                  {{ $label }}

                </option>

                @endforeach

              </select>

            </div>



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
                                            old('shape') === $value
                                                ? 'selected'
                                                : ''
                                        }}>

                  {{ $label }}

                </option>

                @endforeach

              </select>

            </div>



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
                                            old('gender') === $value
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



      {{-- RECOMMENDATION --}}

      <div class="admin-panel">

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

                <input type="checkbox" name="recommended_face_shapes[]" value="{{ $value }}" {{
                                            in_array(
                                                $value,
                                                old(
                                                    'recommended_face_shapes',
                                                    []
                                                )
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
                                                old(
                                                    'style_tags',
                                                    []
                                                )
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



      {{-- CONTENT --}}

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
              class="admin-form-control admin-product-textarea"
              placeholder="Mô tả chi tiết về sản phẩm...">{{ old('description') }}</textarea>


            <div class="admin-field-error" data-error-for="description"></div>

          </div>



          <div class="admin-form-group admin-product-highlights">

            <label for="highlights">
              Thông tin nổi bật
            </label>


            <textarea id="highlights" name="highlights" rows="5" maxlength="3000"
              class="admin-form-control admin-product-textarea"
              placeholder="Ví dụ: Trọng lượng nhẹ, chống gỉ...">{{ old('highlights') }}</textarea>


            <div class="admin-field-error" data-error-for="highlights"></div>

          </div>

        </div>

      </div>



      {{-- SAVE BASIC --}}

      <div class="product-completion-foot">

        <a href="{{ route('admin.products.index') }}" class="admin-btn admin-btn-secondary">

          <i class="bi bi-arrow-left"></i>

          Hủy

        </a>


        <button type="submit" class="admin-btn admin-btn-primary" id="save-basic-product">

          <i class="bi bi-floppy"></i>

          Lưu thông tin & tiếp tục

        </button>

      </div>

    </form>

  </section>



  {{-- =====================================================
        STEP 2 - IMAGE
    ====================================================== --}}

  <section class="product-completion-step" id="create-product-images">

    <div class="product-completion-step-head">

      <div class="product-completion-step-title">

        <span class="product-completion-step-number">
          02
        </span>


        <div>

          <h2>
            Hình ảnh sản phẩm
          </h2>

          <p>
            Không bắt buộc khi tạo.
            Tối đa 5 ảnh JPG, JPEG, PNG hoặc WEBP.
          </p>

        </div>

      </div>


      <span class="product-completion-status muted" id="create-image-status">

        <i class="bi bi-lock"></i>

        Chờ lưu thông tin

      </span>

    </div>



    <fieldset id="image-step-fields" disabled>

      <div class="product-completion-note">

        <i class="bi bi-info-circle"></i>

        <span>
          Bạn có thể bỏ qua hình ảnh và bổ sung sau.
          Tuy nhiên cần ít nhất 1 ảnh thật để kích hoạt kinh doanh.
        </span>

      </div>



      <div class="product-completion-image-body">


        <div class="product-completion-upload">

          <h3>
            Tải hình ảnh
          </h3>


          <p>
            Có thể chọn nhiều ảnh một lần.
          </p>


          <form id="create-image-form" method="POST" enctype="multipart/form-data">

            @csrf


            <input type="file" id="create-product-image-input" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple
              required>


            <div class="product-completion-error" id="create-image-error"></div>


            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full" id="create-image-upload-button">

              <i class="bi bi-cloud-arrow-up"></i>

              Tải ảnh lên

            </button>

          </form>

        </div>



        <div class="product-completion-gallery" id="create-image-gallery">

          <div class="product-completion-empty">

            <i class="bi bi-images"></i>

            <strong>
              Chưa có hình ảnh
            </strong>

            <span>
              Có thể bổ sung sau.
            </span>

          </div>

        </div>

      </div>

    </fieldset>

  </section>



  {{-- =====================================================
        STEP 3 - VARIANT & STOCK
    ====================================================== --}}

  <section class="product-completion-step" id="create-product-variants">

    <div class="product-completion-step-head">

      <div class="product-completion-step-title">

        <span class="product-completion-step-number">
          03
        </span>


        <div>

          <h2>
            Biến thể & tồn kho
          </h2>

          <p>
            Thêm màu, size, SKU và số lượng tồn kho.
            Có thể bổ sung sau.
          </p>

        </div>

      </div>


      <span class="product-completion-status muted" id="create-variant-status">

        <i class="bi bi-lock"></i>

        Chờ lưu thông tin

      </span>

    </div>



    <fieldset id="variant-step-fields" disabled>

      <div class="product-completion-note">

        <i class="bi bi-info-circle"></i>

        <span>
          Biến thể không bắt buộc để lưu sản phẩm.
          Tuy nhiên cần ít nhất 1 biến thể hoạt động
          trước khi kích hoạt kinh doanh.
        </span>

      </div>



      <div class="product-completion-variant-form">

        <h3>

          <i class="bi bi-plus-circle"></i>

          Thêm biến thể mới

        </h3>


        <form id="create-variant-form" method="POST">

          @csrf


          <div class="product-completion-form-grid">


            {{-- COLOR --}}

            <div class="product-completion-field">

              <label for="variant-color">
                Màu sắc
              </label>


              <input type="text" id="variant-color" name="color" maxlength="50" placeholder="Đen" required>


              <div class="product-completion-error" data-variant-error="color"></div>

            </div>



            {{-- SIZE --}}

            <div class="product-completion-field">

              <label for="variant-size">
                Size
              </label>


              <input type="text" id="variant-size" name="size" maxlength="30" placeholder="M" required>


              <div class="product-completion-error" data-variant-error="size"></div>

            </div>



            {{-- SKU --}}

            <div class="product-completion-field">

              <label for="variant-sku">
                SKU biến thể
              </label>


              <input type="text" id="variant-sku" name="sku" maxlength="120" placeholder="VLR-001-BLK-M" required>


              <div class="product-completion-error" data-variant-error="sku"></div>

            </div>



            {{-- STOCK --}}

            <div class="product-completion-field">

              <label for="variant-stock">
                Tồn kho
              </label>


              <input type="number" id="variant-stock" name="stock_quantity" value="0" min="0" step="1" required>


              <div class="product-completion-error" data-variant-error="stock_quantity"></div>

            </div>



            {{-- PRICE ADJUSTMENT --}}

            <div class="product-completion-field">

              <label for="variant-price-adjustment">
                Chênh lệch giá (nếu có)
              </label>


              <input type="number" id="variant-price-adjustment" name="price_adjustment" value="0" step="1" required>


              <div class="product-completion-error" data-variant-error="price_adjustment"></div>

            </div>

          </div>



          <div class="product-completion-variant-bottom">

            <label class="product-completion-checkbox">

              <input type="checkbox" name="is_active" value="1" checked>

              Biến thể đang hoạt động

            </label>


            <button type="submit" class="admin-btn admin-btn-primary" id="create-variant-button">

              <i class="bi bi-plus-lg"></i>

              Thêm biến thể

            </button>

          </div>

        </form>

      </div>



      {{-- VARIANT TABLE --}}

      <div class="product-completion-table-wrap" id="create-variant-table-wrapper" hidden>

        <table class="admin-table">

          <thead>

            <tr>

              <th>SKU</th>

              <th>Màu</th>

              <th>Size</th>

              <th>Tồn kho</th>

              <th>Chênh lệch giá</th>

              <th>Giá cuối</th>

              <th>Trạng thái</th>

            </tr>

          </thead>


          <tbody id="create-variant-table-body">
          </tbody>

        </table>

      </div>

    </fieldset>

  </section>



  {{-- =====================================================
        STEP 4 - PUBLISH
    ====================================================== --}}

  <section class="product-completion-step" id="create-product-publish">

    <div class="product-completion-step-head">

      <div class="product-completion-step-title">

        <span class="product-completion-step-number">
          04
        </span>


        <div>

          <h2>
            Kinh doanh
          </h2>

          <p>
            Chỉ kích hoạt khi sản phẩm đủ điều kiện.
          </p>

        </div>

      </div>

    </div>



    <div class="product-completion-publish-body">


      <div class="product-completion-checklist">


        {{-- BASIC --}}

        <div class="product-completion-check missing" id="check-basic">

          <i class="bi bi-x-circle-fill"></i>


          <div>

            <strong>
              Thông tin sản phẩm
            </strong>

            <small>
              Chưa lưu.
            </small>

          </div>

        </div>



        {{-- IMAGE --}}

        <div class="product-completion-check missing" id="check-image">

          <i class="bi bi-x-circle-fill"></i>


          <div>

            <strong>
              Hình ảnh
            </strong>

            <small>
              Cần ít nhất 1 ảnh thật.
            </small>

          </div>

        </div>



        {{-- VARIANT --}}

        <div class="product-completion-check missing" id="check-variant">

          <i class="bi bi-x-circle-fill"></i>


          <div>

            <strong>
              Biến thể hoạt động
            </strong>

            <small>
              Cần ít nhất 1 biến thể active.
            </small>

          </div>

        </div>

      </div>



      <div class="product-completion-publish-card" id="create-publish-card">

        <h3 id="create-publish-title">
          Chưa thể kích hoạt
        </h3>


        <p id="publish-message">
          Hãy lưu thông tin sản phẩm trước.
        </p>


        <div id="create-publish-action">
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
    | BASIC ELEMENTS
    |--------------------------------------------------------------------------
    */

    const form =
      document.getElementById(
        'basic-product-form'
      );


    const saveButton =
      document.getElementById(
        'save-basic-product'
      );


    const messageBox =
      document.getElementById(
        'create-product-message'
      );


    /*
    |--------------------------------------------------------------------------
    | IMAGE ELEMENTS
    |--------------------------------------------------------------------------
    */

    const imageForm =
      document.getElementById(
        'create-image-form'
      );


    const imageInput =
      document.getElementById(
        'create-product-image-input'
      );


    const imageButton =
      document.getElementById(
        'create-image-upload-button'
      );


    const imageError =
      document.getElementById(
        'create-image-error'
      );


    const imageGallery =
      document.getElementById(
        'create-image-gallery'
      );


    /*
    |--------------------------------------------------------------------------
    | VARIANT ELEMENTS
    |--------------------------------------------------------------------------
    */

    const variantForm =
      document.getElementById(
        'create-variant-form'
      );


    const variantButton =
      document.getElementById(
        'create-variant-button'
      );


    const variantTableWrapper =
      document.getElementById(
        'create-variant-table-wrapper'
      );


    const variantTableBody =
      document.getElementById(
        'create-variant-table-body'
      );


    /*
    |--------------------------------------------------------------------------
    | PRODUCT STATE
    |--------------------------------------------------------------------------
    */

    let productId =
      null;


    let productUrls =
      null;


    let hasRealImage =
      false;


    let hasActiveVariant =
      false;


    let productIsActive =
      false;



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
        .replaceAll(
          '&',
          '&amp;'
        )
        .replaceAll(
          '<',
          '&lt;'
        )
        .replaceAll(
          '>',
          '&gt;'
        )
        .replaceAll(
          '"',
          '&quot;'
        )
        .replaceAll(
          "'",
          '&#039;'
        );
    }



    /*
    |--------------------------------------------------------------------------
    | CLEAR BASIC VALIDATION
    |--------------------------------------------------------------------------
    */

    function clearErrors() {

      document
        .querySelectorAll(
          '[data-error-for]'
        )
        .forEach(
          function(element) {

            element.textContent =
              '';

          }
        );


      document
        .querySelectorAll(
          '.admin-input-error'
        )
        .forEach(
          function(element) {

            element.classList.remove(
              'admin-input-error'
            );

          }
        );
    }



    /*
    |--------------------------------------------------------------------------
    | SHOW BASIC VALIDATION
    |--------------------------------------------------------------------------
    */

    function showErrors(
      errors
    ) {

      Object.keys(
          errors
        )
        .forEach(
          function(field) {

            const normalizedField =
              field.split('.')[0];


            const errorBox =
              document.querySelector(
                '[data-error-for="' +
                normalizedField +
                '"]'
              );


            const input =
              form.querySelector(
                '[name="' +
                normalizedField +
                '"]'
              );


            if (errorBox) {

              errorBox.textContent =
                errors[field][0];

            }


            if (input) {

              input.classList.add(
                'admin-input-error'
              );

            }

          }
        );
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
    | UPDATE COMPLETION
    |--------------------------------------------------------------------------
    */

    function updateCompletion() {

      if (productIsActive) {

        document.getElementById(
            'completion-percent'
          ).textContent =
          '100%';


        document.getElementById(
            'completion-text'
          ).textContent =
          '4/4 bước hoàn thành';


        return;
      }


      let completed =
        productId ?
        1 :
        0;


      if (hasRealImage) {

        completed++;
      }


      if (hasActiveVariant) {

        completed++;
      }


      const percent =
        completed * 25;


      document.getElementById(
          'completion-percent'
        ).textContent =
        percent + '%';


      document.getElementById(
          'completion-text'
        ).textContent =
        completed +
        '/4 bước hoàn thành';


      updatePublishArea();
    }



    /*
    |--------------------------------------------------------------------------
    | UPDATE PUBLISH AREA
    |--------------------------------------------------------------------------
    */

    function updatePublishArea() {

      const card =
        document.getElementById(
          'create-publish-card'
        );


      const title =
        document.getElementById(
          'create-publish-title'
        );


      const message =
        document.getElementById(
          'publish-message'
        );


      const action =
        document.getElementById(
          'create-publish-action'
        );


      const progress =
        document.getElementById(
          'progress-step-4'
        );


      card.classList.remove(
        'ready',
        'active'
      );


      progress.classList.remove(
        'current'
      );


      action.innerHTML =
        '';


      /*
       * Chưa lưu Product.
       */
      if (!productId) {

        title.textContent =
          'Chưa thể kích hoạt';


        message.textContent =
          'Hãy lưu thông tin sản phẩm trước.';


        return;
      }


      /*
       * Thiếu ảnh + Variant.
       */
      if (
        !hasRealImage &&
        !hasActiveVariant
      ) {

        title.textContent =
          'Chưa thể kích hoạt';


        message.textContent =
          'Sản phẩm còn thiếu hình ảnh và biến thể hoạt động.';


        return;
      }


      /*
       * Thiếu ảnh.
       */
      if (!hasRealImage) {

        title.textContent =
          'Chưa thể kích hoạt';


        message.textContent =
          'Sản phẩm còn thiếu ít nhất một hình ảnh thật.';


        return;
      }


      /*
       * Thiếu Variant.
       */
      if (!hasActiveVariant) {

        title.textContent =
          'Chưa thể kích hoạt';


        message.textContent =
          'Sản phẩm còn thiếu ít nhất một biến thể hoạt động.';


        return;
      }


      /*
       * Đủ điều kiện.
       */
      card.classList.add(
        'ready'
      );


      progress.classList.add(
        'current'
      );


      progress
        .querySelector(
          'small'
        )
        .textContent =
        'Sẵn sàng kích hoạt';


      title.textContent =
        'Sản phẩm đã sẵn sàng';


      message.textContent =
        'Đã có hình ảnh thật và biến thể hoạt động. ' +
        'Bạn có thể kích hoạt sản phẩm.';


      action.innerHTML = `
                <button
                    type="button"
                    class="product-completion-publish-btn"
                    id="create-activate-product"
                >
                    <i class="bi bi-rocket-takeoff"></i>

                    Kích hoạt & đưa lên website
                </button>
            `;


      document
        .getElementById(
          'create-activate-product'
        )
        .addEventListener(
          'click',
          activateProduct
        );
    }



    /*
    |--------------------------------------------------------------------------
    | BASIC PRODUCT COMPLETED
    |--------------------------------------------------------------------------
    */

    function markBasicAsCompleted(
      data
    ) {

      productId =
        data.product.id;


      productUrls =
        data.urls;


      /*
       * Không cho tạo Product lần hai.
       */
      form
        .querySelectorAll(
          'input, select, textarea'
        )
        .forEach(
          function(element) {

            element.disabled =
              true;

          }
        );


      saveButton.disabled =
        true;


      saveButton.innerHTML =
        '<i class="bi bi-check-lg"></i> Đã lưu thông tin';


      /*
       * Step 1.
       */
      const status =
        document.getElementById(
          'basic-information-status'
        );


      status.className =
        'product-completion-status success';


      status.innerHTML =
        '<i class="bi bi-check-circle"></i> Đã lưu';


      const progress =
        document.getElementById(
          'progress-step-1'
        );


      progress.classList.remove(
        'current'
      );


      progress.classList.add(
        'done'
      );


      progress
        .querySelector(
          '.product-completion-progress-number'
        )
        .innerHTML =
        '<i class="bi bi-check-lg"></i>';


      document.getElementById(
          'progress-info-text'
        ).textContent =
        'Đã lưu';


      /*
       * Checklist Basic.
       */
      const checkBasic =
        document.getElementById(
          'check-basic'
        );


      checkBasic.className =
        'product-completion-check complete';


      checkBasic.innerHTML =
        '<i class="bi bi-check-circle-fill"></i>' +
        '<div>' +
        '<strong>Thông tin sản phẩm</strong>' +
        '<small>Đã lưu Product #' +
        productId +
        '.</small>' +
        '</div>';


      /*
      |--------------------------------------------------------------------------
      | MỞ KHÓA IMAGE
      |--------------------------------------------------------------------------
      */

      document.getElementById(
          'image-step-fields'
        ).disabled =
        false;


      imageForm.action =
        productUrls.upload_images;


      const imageStatus =
        document.getElementById(
          'create-image-status'
        );


      imageStatus.className =
        'product-completion-status warning';


      imageStatus.innerHTML =
        '<i class="bi bi-clock"></i> Chưa có ảnh';


      document
        .getElementById(
          'progress-step-2'
        )
        .classList.add(
          'current'
        );


      /*
      |--------------------------------------------------------------------------
      | MỞ KHÓA VARIANT
      |--------------------------------------------------------------------------
      */

      document.getElementById(
          'variant-step-fields'
        ).disabled =
        false;


      variantForm.action =
        productUrls.store_variant;


      const variantStatus =
        document.getElementById(
          'create-variant-status'
        );


      variantStatus.className =
        'product-completion-status warning';


      variantStatus.innerHTML =
        '<i class="bi bi-clock"></i> Chưa có biến thể';


      document
        .getElementById(
          'progress-step-3'
        )
        .classList.add(
          'current'
        );


      /*
       * Cập nhật tiến độ.
       */
      updateCompletion();
    }



    /*
    |--------------------------------------------------------------------------
    | CREATE BASIC PRODUCT
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
      'submit',
      async function(event) {

        event.preventDefault();


        if (productId) {

          return;
        }


        clearErrors();


        saveButton.disabled =
          true;


        saveButton.innerHTML =
          '<i class="bi bi-hourglass-split"></i> Đang lưu...';


        try {

          const response =
            await fetch(
              form.action, {
                method: 'POST',

                body: new FormData(
                  form
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

            showErrors(
              data.errors ?? {}
            );


            showMessage(
              'Thông tin chưa hợp lệ. Vui lòng kiểm tra lại.',
              'error'
            );


            return;
          }


          if (!response.ok) {

            throw new Error(
              data.message ??
              'Không thể tạo sản phẩm.'
            );
          }


          markBasicAsCompleted(
            data
          );


          showMessage(
            'Đã lưu thông tin sản phẩm. Bạn vẫn đang ở cùng màn hình.',
            'success'
          );

        } catch (error) {

          showMessage(
            error.message ??
            'Có lỗi xảy ra khi tạo sản phẩm.',
            'error'
          );

        } finally {

          if (!productId) {

            saveButton.disabled =
              false;


            saveButton.innerHTML =
              '<i class="bi bi-floppy"></i> Lưu thông tin & tiếp tục';

          }

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

      imageGallery.innerHTML =
        '';


      if (
        !images ||
        images.length === 0
      ) {

        imageGallery.innerHTML = `
                    <div class="product-completion-empty">

                        <i class="bi bi-images"></i>

                        <strong>
                            Chưa có hình ảnh
                        </strong>

                        <span>
                            Có thể bổ sung sau.
                        </span>

                    </div>
                `;


        return;
      }


      images.forEach(
        function(image) {

          const card =
            document.createElement(
              'div'
            );


          card.className =
            'product-completion-image-card';


          card.innerHTML = `

                        <div class="product-completion-image-photo">

                            <img
                                src="${escapeHtml(image.image_url)}"
                                alt="${escapeHtml(image.alt_text)}"
                            >

                            ${
                                image.is_primary
                                    ? `
                                        <span>
                                            <i class="bi bi-star-fill"></i>
                                            Ảnh chính
                                        </span>
                                    `
                                    : ''
                            }

                        </div>
                    `;


          imageGallery.appendChild(
            card
          );

        }
      );
    }



    /*
    |--------------------------------------------------------------------------
    | IMAGE COMPLETED
    |--------------------------------------------------------------------------
    */

    function markImageAsCompleted(
      data
    ) {

      hasRealImage =
        data.has_real_image;


      hasActiveVariant =
        data.has_active_variant;


      const imageStatus =
        document.getElementById(
          'create-image-status'
        );


      imageStatus.className =
        'product-completion-status success';


      imageStatus.innerHTML =
        '<i class="bi bi-check-circle"></i> ' +
        data.real_image_count +
        '/5 ảnh';


      /*
       * Progress.
       */
      const progress =
        document.getElementById(
          'progress-step-2'
        );


      progress.classList.remove(
        'current'
      );


      progress.classList.add(
        'done'
      );


      progress
        .querySelector(
          '.product-completion-progress-number'
        )
        .innerHTML =
        '<i class="bi bi-check-lg"></i>';


      progress
        .querySelector(
          'small'
        )
        .textContent =
        data.real_image_count +
        ' ảnh thật';


      /*
       * Checklist.
       */
      const check =
        document.getElementById(
          'check-image'
        );


      check.className =
        'product-completion-check complete';


      check.innerHTML =
        '<i class="bi bi-check-circle-fill"></i>' +
        '<div>' +
        '<strong>Hình ảnh</strong>' +
        '<small>Đã có ' +
        data.real_image_count +
        ' ảnh thật.</small>' +
        '</div>';


      updateCompletion();
    }



    /*
    |--------------------------------------------------------------------------
    | UPLOAD IMAGE
    |--------------------------------------------------------------------------
    */

    imageForm.addEventListener(
      'submit',
      async function(event) {

        event.preventDefault();


        imageError.textContent =
          '';


        if (
          !productId ||
          !productUrls
        ) {

          showMessage(
            'Bạn cần lưu thông tin sản phẩm trước.',
            'error'
          );


          return;
        }


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
              productUrls.upload_images, {
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


            if (errors.images) {

              imageError.textContent =
                errors.images[0];

            } else if (
              errors['images.0']
            ) {

              imageError.textContent =
                errors['images.0'][0];

            } else {

              imageError.textContent =
                data.message ??
                'Hình ảnh không hợp lệ.';
            }


            return;
          }


          if (!response.ok) {

            throw new Error(
              data.message ??
              'Không thể tải hình ảnh.'
            );
          }


          renderImages(
            data.images
          );


          markImageAsCompleted(
            data
          );


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


          const adjustment =
            Number(
              variant.price_adjustment ??
              0
            );


          const finalPrice =
            Number(
              variant.final_price ??
              0
            );


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

                            <strong>
                                ${variant.stock_quantity}
                            </strong>

                        </td>


                        <td>
                            ${adjustment.toLocaleString('vi-VN')}đ
                        </td>


                        <td>

                            <strong class="admin-money">
                                ${finalPrice.toLocaleString('vi-VN')}đ
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
                                            Ngừng hoạt động
                                        </span>
                                    `
                            }

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
    | VARIANT COMPLETED
    |--------------------------------------------------------------------------
    */

    function markVariantAsCompleted(
      data
    ) {

      hasActiveVariant =
        data.has_active_variant;


      hasRealImage =
        data.has_real_image;


      const status =
        document.getElementById(
          'create-variant-status'
        );


      const progress =
        document.getElementById(
          'progress-step-3'
        );


      const check =
        document.getElementById(
          'check-variant'
        );


      if (hasActiveVariant) {

        status.className =
          'product-completion-status success';


        status.innerHTML =
          '<i class="bi bi-check-circle"></i> ' +
          data.active_variant_count +
          ' biến thể · Kho ' +
          data.total_stock;


        progress.classList.remove(
          'current'
        );


        progress.classList.add(
          'done'
        );


        progress
          .querySelector(
            '.product-completion-progress-number'
          )
          .innerHTML =
          '<i class="bi bi-check-lg"></i>';


        progress
          .querySelector(
            'small'
          )
          .textContent =
          data.active_variant_count +
          ' biến thể · Kho ' +
          data.total_stock;


        check.className =
          'product-completion-check complete';


        check.innerHTML =
          '<i class="bi bi-check-circle-fill"></i>' +
          '<div>' +
          '<strong>Biến thể hoạt động</strong>' +
          '<small>Đã có ' +
          data.active_variant_count +
          ' biến thể hoạt động.</small>' +
          '</div>';

      } else {

        status.className =
          'product-completion-status warning';


        status.innerHTML =
          '<i class="bi bi-exclamation-circle"></i> ' +
          'Chưa có biến thể hoạt động';


        progress.classList.add(
          'current'
        );


        check.className =
          'product-completion-check missing';


        check.innerHTML =
          '<i class="bi bi-x-circle-fill"></i>' +
          '<div>' +
          '<strong>Biến thể hoạt động</strong>' +
          '<small>Cần ít nhất 1 biến thể active.</small>' +
          '</div>';
      }


      updateCompletion();
    }



    /*
    |--------------------------------------------------------------------------
    | STORE VARIANT
    |--------------------------------------------------------------------------
    */

    variantForm.addEventListener(
      'submit',
      async function(event) {

        event.preventDefault();


        if (
          !productId ||
          !productUrls
        ) {

          showMessage(
            'Bạn cần lưu thông tin sản phẩm trước.',
            'error'
          );


          return;
        }


        document
          .querySelectorAll(
            '[data-variant-error]'
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
              productUrls.store_variant, {
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

                  const errorBox =
                    document.querySelector(
                      '[data-variant-error="' +
                      field +
                      '"]'
                    );


                  if (errorBox) {

                    errorBox.textContent =
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


          markVariantAsCompleted(
            data
          );


          /*
           * Reset form Variant.
           */
          variantForm.reset();


          document.getElementById(
              'variant-stock'
            ).value =
            0;


          document.getElementById(
              'variant-price-adjustment'
            ).value =
            0;


          variantForm
            .querySelector(
              '[name="is_active"]'
            )
            .checked =
            true;


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



    /*
    |--------------------------------------------------------------------------
    | ACTIVATE PRODUCT
    |--------------------------------------------------------------------------
    */

    async function activateProduct() {

      if (
        !productId ||
        !productUrls ||
        !hasRealImage ||
        !hasActiveVariant
      ) {

        return;
      }


      if (
        !confirm(
          'Kích hoạt sản phẩm và đưa lên website?'
        )
      ) {

        return;
      }


      const button =
        document.getElementById(
          'create-activate-product'
        );


      if (!button) {

        return;
      }


      button.disabled =
        true;


      button.innerHTML =
        '<i class="bi bi-hourglass-split"></i> Đang kích hoạt...';


      try {

        /*
         * Dùng POST + _method=PATCH
         * để Laravel xử lý FormData ổn định.
         */
        const payload =
          new FormData();


        payload.append(
          '_token',
          '{{ csrf_token() }}'
        );


        payload.append(
          '_method',
          'PATCH'
        );


        const response =
          await fetch(
            productUrls.activate, {
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


        if (!response.ok) {

          throw new Error(
            data.message ??
            'Không thể kích hoạt sản phẩm.'
          );
        }


        productIsActive =
          true;


        /*
         * Progress Step 4.
         */
        const progress =
          document.getElementById(
            'progress-step-4'
          );


        progress.classList.remove(
          'current'
        );


        progress.classList.add(
          'done'
        );


        progress
          .querySelector(
            '.product-completion-progress-number'
          )
          .innerHTML =
          '<i class="bi bi-check-lg"></i>';


        progress
          .querySelector(
            'small'
          )
          .textContent =
          'Đang kinh doanh';


        /*
         * 100%.
         */
        updateCompletion();


        /*
         * Publish Card.
         */
        const card =
          document.getElementById(
            'create-publish-card'
          );


        card.classList.remove(
          'ready'
        );


        card.classList.add(
          'active'
        );


        document.getElementById(
            'create-publish-title'
          ).innerHTML =
          '<i class="bi bi-check-circle"></i> ' +
          'Sản phẩm đang kinh doanh';


        document.getElementById(
            'publish-message'
          ).textContent =
          'Sản phẩm đã được kích hoạt và đang hiển thị cho khách hàng.';


        document.getElementById(
          'create-publish-action'
        ).innerHTML = `

                    <a
                        href="${escapeHtml(data.urls.customer_show)}"
                        target="_blank"
                        class="product-completion-publish-btn"
                    >
                        <i class="bi bi-box-arrow-up-right"></i>

                        Xem ngoài website
                    </a>

                    <a
                        href="${escapeHtml(data.urls.index)}"
                        class="admin-btn admin-btn-secondary"
                    >
                        <i class="bi bi-list-ul"></i>

                        Về danh sách sản phẩm
                    </a>
                `;


        showMessage(
          'Kích hoạt sản phẩm thành công.',
          'success'
        );

      } catch (error) {

        showMessage(
          error.message ??
          'Có lỗi xảy ra khi kích hoạt sản phẩm.',
          'error'
        );


        button.disabled =
          false;


        button.innerHTML =
          '<i class="bi bi-rocket-takeoff"></i> ' +
          'Kích hoạt & đưa lên website';

      }

    }

  }
);
</script>


@endsection