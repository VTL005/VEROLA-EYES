@extends('layouts.app')


@section('title', 'Thanh toán - VELORA Eyes')


@push('styles')

<link rel="stylesheet" href="{{ asset('css/checkout-voucher.css') }}">

@endpush


@section('content')

<section class="checkout-intro-pearl">
  <div class="velora-container">
    <div class="checkout-intro-inner">
      <div class="checkout-intro-main">
        <nav class="checkout-breadcrumb" aria-label="Breadcrumb">
          <a href="{{ route('home') }}">Trang chủ</a>
          <span class="breadcrumb-separator">›</span>
          <a href="{{ route('cart.index') }}">Giỏ hàng</a>
          <span class="breadcrumb-separator">›</span>
          <span class="breadcrumb-current">Thanh toán</span>
        </nav>
        <h1 class="checkout-pearl-title">Hoàn tất đơn hàng</h1>
        <p class="checkout-pearl-subtitle">Kiểm tra thông tin nhận hàng, chọn phương thức thanh toán và xác nhận đơn hàng của bạn.</p>
      </div>
      <div class="checkout-step-indicator" aria-label="Tiến trình đặt hàng">
        <div class="step-badge is-done">
          <span class="step-num">01</span>
          <span class="step-name">Giỏ hàng</span>
        </div>
        <span class="step-divider"></span>
        <div class="step-badge is-active">
          <span class="step-num">02</span>
          <span class="step-name">Thanh toán</span>
        </div>
        <span class="step-divider"></span>
        <div class="step-badge">
          <span class="step-num">03</span>
          <span class="step-name">Hoàn tất</span>
        </div>
      </div>
    </div>
  </div>
</section>


<section class="section checkout-section-luxury">

  <div class="velora-container">

    <form action="{{ route('checkout.store') }}" method="POST" id="checkoutForm"
      data-shipping-fee-url="{{ route('checkout.shipping-fee') }}">

      @csrf


      <div class="checkout-layout">


        {{-- =============================================
                    LEFT
                ============================================== --}}

        <div class="checkout-main">


          {{-- ADDRESS --}}

          <div class="checkout-card">

            <div class="checkout-card-heading">

              <div class="checkout-step">
                1
              </div>

              <div>

                <h2>
                  Địa chỉ nhận hàng
                </h2>

                <p>
                  Chọn địa chỉ bạn muốn
                  nhận đơn hàng.
                </p>

              </div>

            </div>


            <div class="checkout-address-list">

              @foreach($addresses as $address)

              <label class="checkout-address">

                <input type="radio" name="address_id" value="{{ $address->id }}" {{
                                            (string) $selectedAddressId
                                            === (string) $address->id
                                                ? 'checked'
                                                : ''
                                        }} required>


                <div>

                  <div class="checkout-address-title">

                    <strong>
                      {{ $address->recipient_name }}
                    </strong>


                    @if($address->is_default)

                    <span class="badge badge-success">
                      Mặc định
                    </span>

                    @endif


                    @if($address->label)

                    <span class="badge">
                      {{ $address->label }}
                    </span>

                    @endif

                  </div>


                  <p class="mb-1">
                    {{ $address->phone }}
                  </p>


                  <p class="text-muted mb-0">

                    {{ $address->detail_address }},
                    {{ $address->ward }},
                    {{ $address->district }},
                    {{ $address->province }}

                  </p>

                </div>

              </label>

              @endforeach

            </div>


            <div class="checkout-address-actions">

              <a href="{{ route('addresses.create') }}" class="btn btn-outline btn-sm">
                + Thêm địa chỉ mới
              </a>


              <a href="{{ route('addresses.index') }}" class="btn btn-outline btn-sm">
                Quản lý địa chỉ
              </a>

            </div>

          </div>



          {{-- PRODUCTS --}}

          <div class="checkout-card">

            <div class="checkout-card-heading">

              <div class="checkout-step">
                2
              </div>

              <div>

                <h2>
                  Sản phẩm thanh toán
                </h2>

                <p>
                  @if($isBuyNow ?? false)
                  Sản phẩm bạn đang chọn Mua ngay.
                  @else
                  Chỉ những sản phẩm bạn
                  đã chọn từ giỏ hàng.
                  @endif
                </p>

              </div>

            </div>


            <div class="checkout-product-list">

              @foreach($cart->items as $item)

              @php

              $variant =
              $item->variant;

              $product =
              $variant
              ? $variant->product
              : null;

              @endphp


              <div class="checkout-product">

                <div class="checkout-product-image">

                  @if(
                  $product
                  && $product->primaryImage
                  )

                  <img src="{{ asset(
                                                    $product
                                                        ->primaryImage
                                                        ->image_path
                                                ) }}" alt="{{ $product->name }}">

                  @else

                  <span>
                    VELORA
                  </span>

                  @endif

                </div>


                <div class="checkout-product-info">

                  <strong>
                    {{ $product?->name
                                                ?? 'Sản phẩm' }}
                  </strong>


                  @if($variant)

                  <span>
                    {{ $variant->color }}
                    /
                    {{ $variant->size }}
                  </span>


                  <span>
                    SKU:
                    {{ $variant->sku }}
                  </span>

                  @endif

                </div>


                <div class="checkout-product-quantity">

                  × {{ $item->quantity }}

                </div>


                <div class="checkout-product-price">

                  {{ number_format(
                                            (float) $item->subtotal,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                </div>

              </div>

              @endforeach

            </div>


            @if($isBuyNow ?? false)

            <a href="{{ route('products.index') }}" class="checkout-edit-cart">
              ← Tiếp tục xem sản phẩm
            </a>

            @else

            <a href="{{ route('cart.index') }}" class="checkout-edit-cart">
              ← Thay đổi sản phẩm đã chọn
            </a>

            @endif

          </div>



          {{-- PAYMENT METHOD --}}

          <div class="checkout-card">

            <div class="checkout-card-heading">

              <div class="checkout-step">
                3
              </div>

              <div>

                <h2>
                  Phương thức thanh toán
                </h2>

                <p>
                  Chọn cách bạn muốn
                  thanh toán đơn hàng.
                </p>

              </div>

            </div>


            <div class="payment-method-grid">


              {{-- COD --}}

              <label class="payment-method-card">

                <input type="radio" name="payment_method" value="cod" {{
                                        old(
                                            'payment_method',
                                            'cod'
                                        ) === 'cod'
                                            ? 'checked'
                                            : ''
                                    }}>

                <div class="payment-method-icon">
                  COD
                </div>


                <div>

                  <strong>
                    Thanh toán khi nhận hàng
                  </strong>

                  <span>
                    Thanh toán trực tiếp
                    khi đơn hàng được giao.
                  </span>

                </div>

              </label>



              {{-- QR --}}

              <label class="payment-method-card">

                <input type="radio" name="payment_method" value="qr" {{
                                        old('payment_method')
                                        === 'qr'
                                            ? 'checked'
                                            : ''
                                    }}>

                <div class="payment-method-icon">
                  QR
                </div>


                <div>

                  <strong>
                    Chuyển khoản QR
                  </strong>

                  <span>
                    Thanh toán an toàn bằng
                    mã QR qua payOS.
                  </span>

                </div>

              </label>



              {{-- ONEPAY --}}

              <label class="payment-method-card">

                <input type="radio" name="payment_method" value="onepay" {{
                                        old('payment_method')
                                        === 'onepay'
                                            ? 'checked'
                                            : ''
                                    }}>

                <div class="payment-method-icon">
                  ONE
                </div>


                <div>

                  <strong>
                    OnePAY Sandbox
                  </strong>

                  <span>
                    Thanh toán qua
                    cổng thẻ quốc tế OnePAY.
                  </span>

                </div>

              </label>

            </div>

          </div>



          {{-- NOTE --}}

          <div class="checkout-card">

            <div class="checkout-card-heading">

              <div class="checkout-step">
                4
              </div>

              <div>

                <h2>
                  Ghi chú
                </h2>

                <p>
                  Thông tin thêm cho đơn hàng.
                </p>

              </div>

            </div>


            <textarea name="note" class="form-control" rows="4" maxlength="500"
              placeholder="Ví dụ: Giao hàng giờ hành chính...">{{ old('note') }}</textarea>

          </div>

        </div>



        {{-- =============================================
                    ORDER SUMMARY
                ============================================== --}}

        <aside class="checkout-summary">

          <h2>
            Tóm tắt thanh toán
          </h2>


          <div class="checkout-summary-row">

            <span>
              Tạm tính
            </span>

            <strong>

              {{ number_format(
                                (float) $subtotal,
                                0,
                                ',',
                                '.'
                            ) }}đ

            </strong>

          </div>


          <section id="checkout-voucher" class="checkout-voucher-selector">

            <div class="checkout-voucher-heading">

              <div>

                <span class="checkout-voucher-eyebrow">
                  VELORA VOUCHER
                </span>

                <h3>
                  Voucher
                </h3>

              </div>

              <a href="{{ route('vouchers.index') }}">
                Xem kho
              </a>

            </div>


            @if(session('voucher_success'))

            <div class="checkout-voucher-message is-success">
              {{ session('voucher_success') }}
            </div>

            @endif


            @if($voucherError)

            <div class="checkout-voucher-message is-error">
              {{ $voucherError }}
            </div>

            @endif


            @error('voucher_code')

            <div class="checkout-voucher-message is-error">
              {{ $message }}
            </div>

            @enderror


            <button type="button" id="checkoutVoucherOpen" class="checkout-voucher-trigger" aria-haspopup="dialog"
              aria-controls="checkoutVoucherModal">

              <span class="checkout-voucher-trigger-label">
                <span class="checkout-voucher-trigger-icon">
                  ◆
                </span>

                Chọn voucher
              </span>

              <span class="checkout-voucher-trigger-meta">

                <span>
                  {{ count($availableVouchers) }} mã
                </span>

                <strong>
                  ›
                </strong>

              </span>

            </button>


            @if($appliedVoucher)

            <div class="checkout-voucher-applied">

              <div>

                <span>
                  Đang áp dụng
                </span>

                <strong>
                  {{ $appliedVoucher->code }}
                </strong>

                <small>
                  Tiết kiệm
                  {{ number_format(
                                            (float) $discountAmount,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ
                </small>

              </div>

              <button type="submit" class="checkout-voucher-remove" formaction="{{ route('checkout.voucher.remove') }}"
                formmethod="POST" name="_method" value="DELETE" formnovalidate>
                Bỏ
              </button>

            </div>

            @endif


            <div id="checkoutVoucherModal" class="checkout-voucher-modal" data-open-on-error="{{
                $voucherError
                || $errors->has('voucher_code')
                    ? '1'
                    : '0'
              }}" hidden>

              <button type="button" class="checkout-voucher-modal-backdrop" data-voucher-close
                aria-label="Đóng cửa sổ chọn voucher"></button>

              <div class="checkout-voucher-dialog" role="dialog" aria-modal="true"
                aria-labelledby="checkoutVoucherDialogTitle">

                <div class="checkout-voucher-dialog-header">

                  <div>

                    <span class="checkout-voucher-eyebrow">
                      KHO VOUCHER CỦA BẠN
                    </span>

                    <h3 id="checkoutVoucherDialogTitle">
                      Chọn voucher
                    </h3>

                  </div>

                  <button type="button" class="checkout-voucher-dialog-close" data-voucher-close aria-label="Đóng">
                    ×
                  </button>

                </div>

                <div class="checkout-voucher-dialog-body">

                  <div class="checkout-voucher-code-form">

                    <input id="checkoutVoucherCode" type="text" name="voucher_code" value="{{ old('voucher_code') }}"
                      maxlength="50" placeholder="Nhập mã voucher" autocomplete="off">

                    <button type="submit" class="btn btn-outline" formaction="{{ route('checkout.voucher.apply') }}"
                      formmethod="POST" formnovalidate>
                      Áp dụng
                    </button>

                  </div>


                  @if(! empty($availableVouchers))

                  <div class="checkout-voucher-list">

                    @foreach($availableVouchers as $option)

                    @php

                    $voucher = $option['voucher'];

                    $isApplied = $appliedVoucher
                    && $appliedVoucher->id === $voucher->id;

                    @endphp

                    <div class="checkout-voucher-option {{ $isApplied ? 'is-selected' : '' }}">

                      <div class="checkout-voucher-option-icon">
                        %
                      </div>

                      <div class="checkout-voucher-option-content">

                        <strong>
                          {{ $voucher->code }}
                        </strong>

                        <span>
                          @if($voucher->discount_type === 'percentage')

                          Giảm
                          {{ number_format(
                                                (float) $voucher->discount_value,
                                                0,
                                                ',',
                                                '.'
                                            ) }}%

                          @else

                          Giảm
                          {{ number_format(
                                                (float) $voucher->discount_value,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                          @endif
                        </span>

                        <small>
                          Đơn tối thiểu
                          {{ number_format(
                                            (float) $voucher->minimum_order_amount,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ
                        </small>

                      </div>

                      <button type="submit" class="checkout-voucher-use"
                        formaction="{{ route('checkout.voucher.apply') }}" formmethod="POST" name="voucher_code"
                        value="{{ $voucher->code }}" formnovalidate {{ $isApplied ? 'disabled' : '' }}>
                        {{ $isApplied ? 'Đang dùng' : 'Chọn' }}
                      </button>

                    </div>

                    @endforeach

                  </div>

                  @endif


                  @if(! empty($lockedVouchers))

                  <details class="checkout-voucher-locked">

                    <summary>
                      Voucher chưa đủ điều kiện
                      ({{ count($lockedVouchers) }})
                    </summary>

                    <div class="checkout-voucher-list">

                      @foreach($lockedVouchers as $option)

                      @php

                      $voucher = $option['voucher'];

                      @endphp

                      <div class="checkout-voucher-option is-locked">

                        <div class="checkout-voucher-option-icon">
                          %
                        </div>

                        <div class="checkout-voucher-option-content">

                          <strong>
                            {{ $voucher->code }}
                          </strong>

                          <small>
                            Mua thêm
                            {{ number_format(
                                                (float) $option['amount_missing'],
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ
                            để sử dụng
                          </small>

                        </div>

                        <button type="button" disabled>
                          Chưa đủ
                        </button>

                      </div>

                      @endforeach

                    </div>

                  </details>

                  @endif


                  @if(
                  empty($availableVouchers)
                  && empty($lockedVouchers)
                  )

                  <p class="checkout-voucher-empty">
                    Bạn chưa có voucher nào trong kho.
                    <a href="{{ route('vouchers.index') }}">
                      Đến kho voucher
                    </a>
                  </p>

                  @endif


                </div>

              </div>

            </div>

          </section>


          @if($appliedVoucher)

          <div class="checkout-summary-row">

            <span>
              Giảm giá
            </span>

            <strong class="checkout-discount">

              -
              {{ number_format(
                                    (float) $discountAmount,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

            </strong>

          </div>

          @endif


          <div class="checkout-summary-row">

            <span>
              Phí vận chuyển
            </span>

            <strong id="checkoutShippingFee">

              @if((float) $shippingFee === 0.0)

              Miễn phí

              @else

              {{ number_format(
                                    (float) $shippingFee,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ

              @endif

            </strong>

          </div>


          <p id="checkoutShippingMessage" class="{{ $shippingError ? 'field-error' : 'text-muted' }}"
            aria-live="polite">
            @if($shippingError)

            {{ $shippingError }}

            @elseif($isFreeShipping)

            Đơn hàng đủ điều kiện
            miễn phí vận chuyển.

            @else

            Phí vận chuyển được tính
            theo địa chỉ GHN đã chọn.

            @endif
          </p>


          <div class="checkout-summary-total">

            <span>
              Tổng thanh toán
            </span>

            <strong id="checkoutGrandTotal">

              {{ number_format(
                                (float) $total,
                                0,
                                ',',
                                '.'
                            ) }}đ

            </strong>

          </div>


          <button type="submit" id="checkoutSubmitButton" class="btn btn-primary checkout-submit"
            {{ $shippingError ? 'disabled' : '' }} onclick="
                            return confirm(
                                'Bạn xác nhận đặt đơn hàng này?'
                            );
                        ">
            Đặt hàng
          </button>


          <p class="checkout-security-note">

            Bằng việc đặt hàng,
            bạn xác nhận thông tin
            trên là chính xác.

          </p>


          <a href="{{ route('cart.index') }}" class="checkout-back-cart">
            ← Quay lại giỏ hàng
          </a>

        </aside>

      </div>

    </form>

  </div>

</section>


<script>
document.addEventListener('DOMContentLoaded', function() {
  const checkoutForm = document.getElementById(
    'checkoutForm'
  );

  if (!checkoutForm) {
    return;
  }

  const addressInputs = Array.from(
    checkoutForm.querySelectorAll(
      'input[name="address_id"]'
    )
  );

  const shippingFeeElement = document.getElementById(
    'checkoutShippingFee'
  );

  const grandTotalElement = document.getElementById(
    'checkoutGrandTotal'
  );

  const shippingMessageElement = document.getElementById(
    'checkoutShippingMessage'
  );

  const submitButton = document.getElementById(
    'checkoutSubmitButton'
  );

  const shippingFeeUrl =
    checkoutForm.dataset.shippingFeeUrl;

  let activeRequest = null;

  function formatMoney(value) {
    return new Intl.NumberFormat('vi-VN')
      .format(Number(value) || 0) + 'đ';
  }

  function setMessage(message, state = '') {
    if (!shippingMessageElement) {
      return;
    }

    shippingMessageElement.textContent = message;

    shippingMessageElement.classList.remove(
      'field-error',
      'text-muted'
    );

    shippingMessageElement.classList.add(
      state === 'error' ?
      'field-error' :
      'text-muted'
    );
  }

  async function updateShippingFee(addressId) {
    if (activeRequest) {
      activeRequest.abort();
    }

    activeRequest = new AbortController();

    if (submitButton) {
      submitButton.disabled = true;
    }

    if (shippingFeeElement) {
      shippingFeeElement.textContent = 'Đang tính...';
    }

    setMessage(
      'Đang lấy phí vận chuyển từ GHN...'
    );

    try {
      const url = new URL(
        shippingFeeUrl,
        window.location.origin
      );

      url.searchParams.set(
        'address_id',
        addressId
      );

      const response = await fetch(url.toString(), {
        method: 'GET',
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        signal: activeRequest.signal,
      });

      const payload = await response.json().catch(
        () => null
      );

      if (!response.ok) {
        throw new Error(
          payload?.message ||
          'Không thể tính phí vận chuyển.'
        );
      }

      const data = payload?.data;

      if (!data) {
        throw new Error(
          'Dữ liệu phí vận chuyển không hợp lệ.'
        );
      }

      if (shippingFeeElement) {
        shippingFeeElement.textContent = data.is_free ?
          'Miễn phí' :
          formatMoney(data.shipping_fee);
      }

      if (grandTotalElement) {
        grandTotalElement.textContent =
          formatMoney(data.total);
      }

      setMessage(
        data.is_free ?
        'Đơn hàng đủ điều kiện miễn phí vận chuyển.' :
        'Phí vận chuyển đã được cập nhật theo địa chỉ GHN.'
      );

      if (submitButton) {
        submitButton.disabled = false;
      }
    } catch (error) {
      if (error.name === 'AbortError') {
        return;
      }

      if (shippingFeeElement) {
        shippingFeeElement.textContent =
          'Không thể tính';
      }

      setMessage(error.message, 'error');

      if (submitButton) {
        submitButton.disabled = true;
      }
    }
  }

  addressInputs.forEach(input => {
    input.addEventListener('change', function() {
      if (this.checked) {
        updateShippingFee(this.value);
      }
    });
  });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById(
    'checkoutVoucherModal'
  );

  const openButton = document.getElementById(
    'checkoutVoucherOpen'
  );

  if (!modal || !openButton) {
    return;
  }

  const closeButtons = modal.querySelectorAll(
    '[data-voucher-close]'
  );

  function openVoucherModal() {
    modal.hidden = false;
    document.body.classList.add(
      'checkout-voucher-modal-open'
    );

    const closeButton = modal.querySelector(
      '.checkout-voucher-dialog-close'
    );

    closeButton?.focus();
  }

  function closeVoucherModal() {
    modal.hidden = true;
    document.body.classList.remove(
      'checkout-voucher-modal-open'
    );
    openButton.focus();
  }

  openButton.addEventListener(
    'click',
    openVoucherModal
  );

  closeButtons.forEach(button => {
    button.addEventListener(
      'click',
      closeVoucherModal
    );
  });

  document.addEventListener('keydown', function(event) {
    if (
      event.key === 'Escape' &&
      !modal.hidden
    ) {
      closeVoucherModal();
    }
  });

  const shouldOpenAfterError =
    modal.dataset.openOnError === '1';

  if (shouldOpenAfterError) {
    openVoucherModal();
  }
});
</script>

@endsection