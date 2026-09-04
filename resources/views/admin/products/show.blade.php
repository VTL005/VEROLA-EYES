@extends('layouts.admin')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin-product-completion.css') }}">
@endpush


@section(
'title',
$product->name . ' - VELORA Eyes'
)


@section(
'page-title',
'Chi tiết sản phẩm'
)


@section('content')

@php

/*
|--------------------------------------------------------------------------
| LABELS
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| FALLBACK DATA
|--------------------------------------------------------------------------
*/

$realImages =
$realImages
?? $product->images->filter(
fn ($image) =>
$image->image_path
!== 'images/no-image.png'
);


$realImageCount =
$realImageCount
?? $realImages->count();


$hasRealImage =
$hasRealImage
?? ($realImageCount > 0);


$activeVariants =
$activeVariants
?? $product->variants->where(
'is_active',
true
);


$activeVariantCount =
$activeVariantCount
?? $activeVariants->count();


$hasActiveVariant =
$hasActiveVariant
?? ($activeVariantCount > 0);


$totalStock =
$totalStock
?? $activeVariants->sum(
'stock_quantity'
);


$isReadyForSale =
$isReadyForSale
?? (
$hasRealImage
&& $hasActiveVariant
);


$completedSteps =
$completedSteps
?? (
1
+ ($hasRealImage ? 1 : 0)
+ ($hasActiveVariant ? 1 : 0)
+ ($product->is_active ? 1 : 0)
);


$completionPercent =
$completionPercent
?? (int) round(
($completedSteps / 4) * 100
);


/*
|--------------------------------------------------------------------------
| PRIMARY IMAGE
|--------------------------------------------------------------------------
*/

$primaryImage =
$realImages->firstWhere(
'is_primary',
true
)
?? $realImages->first();


$editUrl = route(
'admin.products.edit',
$product
);

@endphp



<div class="product-completion">


  {{-- =====================================================
        HEADER
    ====================================================== --}}

  <div class="product-completion-hero">

    <div>

      <span class="product-completion-hero-kicker">
        PRODUCT DETAIL
      </span>


      <h1>
        Chi tiết sản phẩm
      </h1>


      <p>
        {{ $product->name }}
        ·
        {{ $product->sku }}
      </p>

    </div>


    <div class="product-completion-percent">

      <strong>
        {{ $completionPercent }}%
      </strong>


      <span>
        {{ $completedSteps }}/4 bước hoàn thành
      </span>

    </div>

  </div>



  {{-- =====================================================
        PROGRESS
    ====================================================== --}}

  <div class="product-completion-progress">


    {{-- STEP 1 --}}

    <div class="product-completion-progress-item done">

      <span class="product-completion-progress-number">
        <i class="bi bi-check-lg"></i>
      </span>


      <div class="product-completion-progress-content">

        <strong>
          Thông tin
        </strong>


        <small>
          Đã lưu
        </small>

      </div>

    </div>



    {{-- STEP 2 --}}

    <div class="
                product-completion-progress-item
                {{ $hasRealImage ? 'done' : 'current' }}
            ">

      <span class="product-completion-progress-number">

        @if($hasRealImage)

        <i class="bi bi-check-lg"></i>

        @else

        2

        @endif

      </span>


      <div class="product-completion-progress-content">

        <strong>
          Hình ảnh
        </strong>


        <small>

          {{ $hasRealImage
                        ? $realImageCount . ' ảnh thật'
                        : 'Có thể bổ sung sau' }}

        </small>

      </div>

    </div>



    {{-- STEP 3 --}}

    <div class="
                product-completion-progress-item
                {{ $hasActiveVariant ? 'done' : 'current' }}
            ">

      <span class="product-completion-progress-number">

        @if($hasActiveVariant)

        <i class="bi bi-check-lg"></i>

        @else

        3

        @endif

      </span>


      <div class="product-completion-progress-content">

        <strong>
          Biến thể & kho
        </strong>


        <small>

          @if($hasActiveVariant)

          {{ $activeVariantCount }}
          biến thể · Kho {{ $totalStock }}

          @else

          Chưa có biến thể hoạt động

          @endif

        </small>

      </div>

    </div>



    {{-- STEP 4 --}}

    <div class="
                product-completion-progress-item
                {{ $product->is_active ? 'done' : 'current' }}
            ">

      <span class="product-completion-progress-number">

        @if($product->is_active)

        <i class="bi bi-check-lg"></i>

        @else

        4

        @endif

      </span>


      <div class="product-completion-progress-content">

        <strong>
          Kinh doanh
        </strong>


        <small>

          {{ $product->is_active
                        ? 'Đang hiển thị cho khách'
                        : 'Chưa kích hoạt' }}

        </small>

      </div>

    </div>

  </div>



  {{-- =====================================================
        STEP 1 - PRODUCT INFORMATION
    ====================================================== --}}

  <section class="product-completion-step" id="product-info">

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
            Thông tin cơ bản đã được lưu.
          </p>

        </div>

      </div>


      <a href="{{ $editUrl }}#edit-product-information" class="admin-btn admin-btn-secondary">

        <i class="bi bi-pencil"></i>

        Sửa thông tin

      </a>

    </div>



    <div class="product-completion-summary">


      {{-- COVER --}}

      <div class="product-completion-cover">

        @if($primaryImage)

        <img src="{{ asset(
                            $primaryImage->image_path
                        ) }}" alt="{{ $product->name }}">

        @else

        <div class="product-completion-no-image">

          <i class="bi bi-eyeglasses"></i>


          <span>
            Chưa có ảnh
          </span>

        </div>

        @endif

      </div>



      {{-- MAIN INFORMATION --}}

      <div class="product-completion-main">

        <h3>
          {{ $product->name }}
        </h3>


        <code>
                    {{ $product->sku }}
                </code>



        {{-- PRICE --}}

        <div class="product-completion-price">

          @if($product->sale_price !== null)

          <strong>

            {{ number_format(
                                (float) $product->sale_price,
                                0,
                                ',',
                                '.'
                            ) }}đ

          </strong>


          <del>

            {{ number_format(
                                (float) $product->price,
                                0,
                                ',',
                                '.'
                            ) }}đ

          </del>

          @else

          <strong>

            {{ number_format(
                                (float) $product->price,
                                0,
                                ',',
                                '.'
                            ) }}đ

          </strong>

          @endif

        </div>



        {{-- PRODUCT DATA --}}

        <div class="product-completion-info-grid">


          <div>

            <span>
              Danh mục
            </span>


            <strong>

              {{ $product->category?->name
                                ?? 'Chưa phân loại' }}

            </strong>

          </div>



          <div>

            <span>
              Chất liệu
            </span>


            <strong>

              {{ $materials[$product->material]
                                ?? 'Chưa cập nhật' }}

            </strong>

          </div>



          <div>

            <span>
              Kiểu dáng
            </span>


            <strong>

              {{ $shapes[$product->shape]
                                ?? 'Chưa cập nhật' }}

            </strong>

          </div>



          <div>

            <span>
              Đối tượng
            </span>


            <strong>

              {{ $genders[$product->gender]
                                ?? 'Chưa cập nhật' }}

            </strong>

          </div>



          <div>

            <span>
              Kích thước
            </span>


            <strong>
              {{ $product->dimensions ?? '—' }}
            </strong>

          </div>



          <div>

            <span>
              Trạng thái
            </span>


            <strong>

              {{ $product->is_active
                                ? 'Đang kinh doanh'
                                : 'Chưa kinh doanh' }}

            </strong>

          </div>

        </div>

      </div>

    </div>

  </section>



  {{-- =====================================================
        STEP 2 - IMAGES
    ====================================================== --}}

  <section class="product-completion-step" id="product-images">

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
            Xem các hình ảnh hiện có của sản phẩm.
          </p>

        </div>

      </div>


      <div style="display:flex; align-items:center; gap:10px;">

        @if($hasRealImage)

        <span class="product-completion-status success">

          <i class="bi bi-check-circle"></i>

          {{ $realImageCount }}/5 ảnh

        </span>

        @else

        <span class="product-completion-status warning">

          <i class="bi bi-clock"></i>

          Chưa có ảnh

        </span>

        @endif


        <a href="{{ $editUrl }}#edit-product-images" class="admin-btn admin-btn-secondary">

          <i class="bi bi-images"></i>

          Quản lý hình ảnh

        </a>

      </div>

    </div>


    <div class="product-completion-gallery">

      @forelse(
      $realImages->sortBy('sort_order')
      as $image
      )

      <div class="product-completion-image-card">

        <div class="product-completion-image-photo">

          <img src="{{ asset(
                            $image->image_path
                        ) }}" alt="{{ $image->alt_text
                            ?: $product->name }}">

          @if($image->is_primary)

          <span>

            <i class="bi bi-star-fill"></i>

            Ảnh chính

          </span>

          @endif

        </div>

      </div>

      @empty

      <div class="product-completion-empty">

        <i class="bi bi-images"></i>

        <strong>
          Chưa có hình ảnh
        </strong>

        <span>
          Hãy vào trang Sửa sản phẩm để thêm hình ảnh.
        </span>

      </div>

      @endforelse

    </div>

  </section>



  {{-- =====================================================
        STEP 3 - VARIANT & STOCK
    ====================================================== --}}

  <section class="product-completion-step" id="product-variants">

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
            Xem màu sắc, size, SKU, chênh lệch giá và tồn kho.
          </p>

        </div>

      </div>


      <div style="display:flex; align-items:center; gap:10px;">

        @if($hasActiveVariant)

        <span class="product-completion-status success">

          <i class="bi bi-check-circle"></i>

          {{ $activeVariantCount }}
          biến thể · Kho {{ $totalStock }}

        </span>

        @else

        <span class="product-completion-status warning">

          <i class="bi bi-exclamation-circle"></i>

          Chưa có biến thể

        </span>

        @endif


        <a href="{{ $editUrl }}#edit-product-variants" class="admin-btn admin-btn-secondary">

          <i class="bi bi-box-seam"></i>

          Quản lý biến thể

        </a>

      </div>

    </div>


    @if($product->variants->isEmpty())

    <div class="product-completion-empty">

      <i class="bi bi-box-seam"></i>

      <strong>
        Chưa có biến thể
      </strong>

      <span>
        Hãy vào trang Sửa sản phẩm để tạo biến thể đầu tiên.
      </span>

    </div>

    @else

    <div class="product-completion-table-wrap">

      <table class="admin-table">

        <thead>

          <tr>

            <th>SKU</th>
            <th>Màu</th>
            <th>Size</th>
            <th>Tồn kho</th>
            <th>Tình trạng kho</th>
            <th>Chênh lệch giá</th>
            <th>Giá cuối</th>
            <th>Trạng thái</th>

          </tr>

        </thead>


        <tbody>

          @foreach(
          $product->variants->sortBy('sku')
          as $variant
          )

          <tr>

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

              <strong>
                {{ $variant->stock_quantity }}
              </strong>

            </td>


            <td>

              @if(
              $inventoryService
              ->isOutOfStock(
              $variant
              )
              )

              <span class="admin-status danger">
                Hết hàng
              </span>

              @elseif(
              $inventoryService
              ->isLowStock(
              $variant
              )
              )

              <span class="admin-status warning">
                Sắp hết
              </span>

              @else

              <span class="admin-status success">
                Còn hàng
              </span>

              @endif

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

              <strong class="admin-money">

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
                Ngừng hoạt động
              </span>

              @endif

            </td>

          </tr>

          @endforeach

        </tbody>

      </table>

    </div>

    @endif

  </section>



  {{-- =====================================================
        STEP 4 - BUSINESS
    ====================================================== --}}

  <section class="product-completion-step" id="product-publish">

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
            Xem điều kiện sẵn sàng và trạng thái hiển thị sản phẩm.
          </p>

        </div>

      </div>


      <a href="{{ $editUrl }}#edit-product-business" class="admin-btn admin-btn-secondary">

        <i class="bi bi-shop"></i>

        Quản lý kinh doanh

      </a>

    </div>


    <div class="product-completion-publish-body">

      <div class="product-completion-checklist">

        <div class="product-completion-check complete">

          <i class="bi bi-check-circle-fill"></i>

          <div>

            <strong>
              Thông tin sản phẩm
            </strong>

            <small>
              Đã lưu thông tin cơ bản.
            </small>

          </div>

        </div>


        <div class="
                        product-completion-check
                        {{ $hasRealImage
                            ? 'complete'
                            : 'missing' }}
                    ">

          <i class="bi {{
                            $hasRealImage
                                ? 'bi-check-circle-fill'
                                : 'bi-x-circle-fill'
                        }}"></i>

          <div>

            <strong>
              Hình ảnh sản phẩm
            </strong>

            <small>

              @if($hasRealImage)

              Có {{ $realImageCount }} ảnh thật.

              @else

              Cần ít nhất 1 ảnh thật.

              @endif

            </small>

          </div>

        </div>


        <div class="
                        product-completion-check
                        {{ $hasActiveVariant
                            ? 'complete'
                            : 'missing' }}
                    ">

          <i class="bi {{
                            $hasActiveVariant
                                ? 'bi-check-circle-fill'
                                : 'bi-x-circle-fill'
                        }}"></i>

          <div>

            <strong>
              Biến thể hoạt động
            </strong>

            <small>

              @if($hasActiveVariant)

              Có {{ $activeVariantCount }}
              biến thể hoạt động.

              @else

              Cần ít nhất 1 biến thể hoạt động.

              @endif

            </small>

          </div>

        </div>


        <div class="
                        product-completion-check
                        {{ $totalStock > 0
                            ? 'complete'
                            : 'warning' }}
                    ">

          <i class="bi {{
                            $totalStock > 0
                                ? 'bi-check-circle-fill'
                                : 'bi-exclamation-circle-fill'
                        }}"></i>

          <div>

            <strong>
              Tồn kho
            </strong>

            <small>

              Tổng tồn kho:
              {{ $totalStock }}.

              @if($totalStock <= 0) Sản phẩm vẫn có thể kinh doanh nhưng sẽ hiển thị hết hàng. @endif </small>

          </div>

        </div>

      </div>


      @if($product->is_active)

      <div class="
                        product-completion-publish-card
                        active
                    ">

        <h3>

          <i class="bi bi-check-circle"></i>

          Sản phẩm đang kinh doanh

        </h3>

        <p>
          Sản phẩm đang được phép hiển thị cho khách hàng.
        </p>

        <a href="{{ route(
                            'products.show',
                            $product
                        ) }}" target="_blank" class="product-completion-publish-btn">

          <i class="bi bi-box-arrow-up-right"></i>

          Xem ngoài website

        </a>

      </div>

      @elseif($isReadyForSale)

      <div class="
                        product-completion-publish-card
                        ready
                    ">

        <h3>
          Sản phẩm đã sẵn sàng
        </h3>

        <p>
          Đã có ảnh thật và biến thể hoạt động.
          Bạn có thể kích hoạt ở trang Sửa sản phẩm.
        </p>

        <a href="{{ $editUrl }}#edit-product-business" class="product-completion-publish-btn">

          <i class="bi bi-gear"></i>

          Quản lý kinh doanh

        </a>

      </div>

      @else

      <div class="product-completion-publish-card">

        <h3>
          Chưa đủ điều kiện kinh doanh
        </h3>

        <p>

          @if(
          ! $hasRealImage
          && ! $hasActiveVariant
          )

          Sản phẩm còn thiếu hình ảnh thật
          và biến thể hoạt động.

          @elseif(! $hasRealImage)

          Sản phẩm còn thiếu hình ảnh thật.

          @else

          Sản phẩm còn thiếu biến thể hoạt động.

          @endif

        </p>

        <a href="{{ $editUrl }}#edit-product-business" class="product-completion-publish-btn">

          <i class="bi bi-pencil-square"></i>

          Hoàn thiện sản phẩm

        </a>

      </div>

      @endif

    </div>

  </section>



  {{-- =====================================================
        ADDITIONAL PRODUCT INFORMATION
    ====================================================== --}}

  <div class="product-completion-extra-grid">


    {{-- DESCRIPTION --}}

    <section class="admin-panel">

      <div class="admin-panel-header">

        <div>

          <h2>
            Mô tả sản phẩm
          </h2>

        </div>

      </div>


      <div class="admin-product-text-content">

        {{ $product->description
                    ?: 'Chưa có mô tả.' }}

      </div>

    </section>



    {{-- HIGHLIGHTS --}}

    <section class="admin-panel">

      <div class="admin-panel-header">

        <div>

          <h2>
            Thông tin nổi bật
          </h2>

        </div>

      </div>


      <div class="admin-product-text-content">

        {{ $product->highlights
                    ?: 'Chưa có thông tin.' }}

      </div>

    </section>



    {{-- FACE SHAPE --}}

    <section class="admin-panel">

      <div class="admin-panel-header">

        <div>

          <h2>
            Khuôn mặt phù hợp
          </h2>

        </div>

      </div>


      <div class="admin-product-tag-list">

        @forelse(
        $product->recommended_face_shapes
        ?? []
        as $faceShape
        )

        <span>

          {{ $faceShapes[$faceShape]
                            ?? $faceShape }}

        </span>

        @empty

        <small>
          Chưa cấu hình.
        </small>

        @endforelse

      </div>

    </section>



    {{-- STYLE --}}

    <section class="admin-panel">

      <div class="admin-panel-header">

        <div>

          <h2>
            Phong cách
          </h2>

        </div>

      </div>


      <div class="admin-product-tag-list">

        @forelse(
        $product->style_tags
        ?? []
        as $style
        )

        <span>

          {{ $styles[$style]
                            ?? $style }}

        </span>

        @empty

        <small>
          Chưa cấu hình.
        </small>

        @endforelse

      </div>

    </section>

  </div>



  {{-- =====================================================
        BOTTOM ACTIONS
    ====================================================== --}}

  <div class="product-completion-foot">

    <a href="{{ route(
                'admin.products.index'
            ) }}" class="admin-btn admin-btn-secondary">

      <i class="bi bi-arrow-left"></i>

      Về danh sách

    </a>


    <a href="{{ $editUrl }}#edit-product-information" class="admin-btn admin-btn-primary">

      <i class="bi bi-pencil"></i>

      Sửa sản phẩm

    </a>

  </div>



  {{-- =====================================================
        DANGER ZONE
    ====================================================== --}}

  <section class="admin-product-danger-zone">

    <div>

      <i class="bi bi-exclamation-triangle"></i>


      <span>

        <strong>
          Xóa sản phẩm
        </strong>


        <small>

          Sản phẩm đã phát sinh dữ liệu quan trọng
          có thể được chuyển sang trạng thái
          không hoạt động thay vì xóa hoàn toàn.

        </small>

      </span>

    </div>


    <form action="{{ route(
                'admin.products.destroy',
                $product
            ) }}" method="POST" onsubmit="
                return confirm(
                    'Bạn có chắc muốn xóa sản phẩm này?'
                );
            ">

      @csrf
      @method('DELETE')


      <button type="submit" class="admin-btn admin-btn-danger">

        <i class="bi bi-trash"></i>

        Xóa sản phẩm

      </button>

    </form>

  </section>


</div>

@endsection