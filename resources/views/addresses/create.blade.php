@extends('layouts.app')


@section('title', 'Thêm địa chỉ - VELORA Eyes')


@section('content')

<section class="address-form-section">

  <div class="velora-container">

    <div class="address-form-wrapper">


      {{-- =====================================================
                INTRO
            ====================================================== --}}

      <div class="address-form-intro">

        <span class="hero-kicker">
          DELIVERY ADDRESS
        </span>

        <h1>
          Thêm địa chỉ nhận hàng
        </h1>

        <p>
          Chọn địa chỉ hành chính chính xác để
          VELORA có thể giao đơn hàng đến đúng nơi.
        </p>


        <a href="{{ route('addresses.index') }}" class="address-back-link">
          ← Quay lại danh sách địa chỉ
        </a>

      </div>



      {{-- =====================================================
                FORM
            ====================================================== --}}

      <div class="address-form-card">

        <form action="{{ route('addresses.store') }}" method="POST">

          @csrf


          <div class="address-form-grid">


            {{-- =================================================
                            RECIPIENT
                        ================================================== --}}

            <div class="form-group">

              <label for="recipient_name" class="form-label">
                Tên người nhận
              </label>


              <input type="text" id="recipient_name" name="recipient_name"
                class="form-control @error('recipient_name') input-error @enderror" value="{{ old('recipient_name') }}"
                placeholder="Nguyễn Văn A" required autofocus>


              @error('recipient_name')

              <div class="field-error">
                {{ $message }}
              </div>

              @enderror

            </div>



            {{-- =================================================
                            PHONE
                        ================================================== --}}

            <div class="form-group">

              <label for="phone" class="form-label">
                Số điện thoại
              </label>


              <input type="text" id="phone" name="phone" class="form-control @error('phone') input-error @enderror"
                value="{{ old('phone') }}" placeholder="0912345678" inputmode="numeric" maxlength="10" required>


              @error('phone')

              <div class="field-error">
                {{ $message }}
              </div>

              @enderror

            </div>



            {{-- =================================================
                            PROVINCE
                        ================================================== --}}

            <div class="form-group">

              <label for="province_code" class="form-label">
                Tỉnh / Thành phố
              </label>


              {{--
                                province:
                                lưu tên Tỉnh/Thành phố.

                                province_code:
                                lưu mã hành chính.
                            --}}

              <input type="hidden" id="province" name="province" value="{{ old('province') }}">


              <select id="province_code" name="province_code"
                class="form-control @error('province') input-error @enderror @error('province_code') input-error @enderror"
                data-selected-code="{{ old('province_code') }}" required disabled>

                <option value="">
                  Đang tải Tỉnh/Thành phố...
                </option>

              </select>


              @error('province')

              <div class="field-error">
                {{ $message }}
              </div>

              @enderror


              @error('province_code')

              <div class="field-error">
                {{ $message }}
              </div>

              @enderror

            </div>



            {{-- =================================================
                            WARD
                        ================================================== --}}

            <div class="form-group">

              <label for="ward_code" class="form-label">
                Phường / Xã / Đặc khu
              </label>


              {{--
                                ward:
                                lưu tên đơn vị hành chính.

                                ward_code:
                                lưu mã hành chính.
                            --}}

              <input type="hidden" id="ward" name="ward" value="{{ old('ward') }}">


              <select id="ward_code" name="ward_code"
                class="form-control @error('ward') input-error @enderror @error('ward_code') input-error @enderror"
                data-selected-code="{{ old('ward_code') }}" required disabled>

                <option value="">
                  Chọn Tỉnh/Thành phố trước
                </option>

              </select>


              @error('ward')

              <div class="field-error">
                {{ $message }}
              </div>

              @enderror


              @error('ward_code')

              <div class="field-error">
                {{ $message }}
              </div>

              @enderror

            </div>



            {{-- =================================================
                            API STATUS
                        ================================================== --}}

            <div class="form-group address-grid-full">

              <small id="address-location-status" aria-live="polite"></small>

            </div>



            {{-- =================================================
                            DETAIL ADDRESS
                        ================================================== --}}

            <div class="form-group address-grid-full">

              <label for="detail_address" class="form-label">
                Địa chỉ chi tiết
              </label>


              <input type="text" id="detail_address" name="detail_address"
                class="form-control @error('detail_address') input-error @enderror" value="{{ old('detail_address') }}"
                placeholder="Số nhà, tên đường, tòa nhà..." required>


              @error('detail_address')

              <div class="field-error">
                {{ $message }}
              </div>

              @enderror


              <small>
                Ví dụ: Số 15 đường Trần Thái Tông.
              </small>

            </div>



            {{-- =================================================
                            LABEL
                        ================================================== --}}

            <div class="form-group address-grid-full">

              <label for="label" class="form-label">
                Tên gợi nhớ
              </label>


              <input type="text" id="label" name="label" class="form-control @error('label') input-error @enderror"
                value="{{ old('label') }}" placeholder="Nhà riêng, Công ty..." maxlength="50">


              @error('label')

              <div class="field-error">
                {{ $message }}
              </div>

              @enderror

            </div>

          </div>



          {{-- =====================================================
                        DEFAULT ADDRESS
                    ====================================================== --}}

          <label class="address-default-checkbox">

            <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}>


            <span>

              <strong>
                Đặt làm địa chỉ mặc định
              </strong>

              <small>
                Địa chỉ này sẽ được ưu tiên
                khi bạn thanh toán.
              </small>

            </span>

          </label>



          {{-- =====================================================
                        ACTIONS
                    ====================================================== --}}

          <div class="address-form-actions">

            <a href="{{ route('addresses.index') }}" class="btn btn-outline">
              Hủy
            </a>


            <button type="submit" class="btn btn-primary">
              Lưu địa chỉ
            </button>

          </div>

        </form>

      </div>

    </div>

  </div>

</section>


{{-- =========================================================
    ADDRESS SELECTOR
========================================================= --}}

<script src="{{ asset('js/address-selector.js') }}" defer></script>

@endsection