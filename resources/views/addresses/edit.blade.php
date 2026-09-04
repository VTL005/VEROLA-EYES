@extends('layouts.app')


@section('title', 'Chỉnh sửa địa chỉ - VELORA Eyes')


@section('content')

<section class="address-form-section">

  <div class="velora-container">

    <div class="address-form-wrapper">


      {{-- =====================================================
                INTRO
            ====================================================== --}}

      <div class="address-form-intro">

        <span class="hero-kicker">
          UPDATE ADDRESS
        </span>

        <h1>
          Chỉnh sửa địa chỉ
        </h1>

        <p>
          Cập nhật thông tin nhận hàng
          của bạn.
        </p>


        <a href="{{ route('addresses.index') }}" class="address-back-link">
          ← Quay lại danh sách địa chỉ
        </a>

      </div>



      {{-- =====================================================
                FORM
            ====================================================== --}}

      <div class="address-form-card">

        <form action="{{ route(
                        'addresses.update',
                        $address
                    ) }}" method="POST">

          @csrf
          @method('PUT')


          <div class="address-form-grid">


            {{-- =================================================
                            RECIPIENT
                        ================================================== --}}

            <div class="form-group">

              <label for="recipient_name" class="form-label">
                Tên người nhận
              </label>


              <input type="text" id="recipient_name" name="recipient_name"
                class="form-control @error('recipient_name') input-error @enderror" value="{{ old(
                                    'recipient_name',
                                    $address->recipient_name
                                ) }}" placeholder="Nguyễn Văn A" required autofocus>


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
                value="{{ old(
                                    'phone',
                                    $address->phone
                                ) }}" placeholder="0912345678" inputmode="numeric" maxlength="10" required>


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
                                Lưu tên Tỉnh/Thành phố.

                                Với địa chỉ mới:
                                province_code sẽ có mã.

                                Với địa chỉ cũ:
                                JavaScript sẽ thử tìm lại
                                Tỉnh/Thành theo tên.
                            --}}

              <input type="hidden" id="province" name="province" value="{{ old(
                                    'province',
                                    $address->province
                                ) }}">


              <select id="province_code" name="province_code"
                class="form-control @error('province') input-error @enderror @error('province_code') input-error @enderror"
                data-selected-code="{{ old(
                                    'province_code',
                                    $address->province_code
                                ) }}" data-selected-name="{{ old(
                                    'province',
                                    $address->province
                                ) }}" required disabled>

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


              <input type="hidden" id="ward" name="ward" value="{{ old(
                                    'ward',
                                    $address->ward
                                ) }}">


              <select id="ward_code" name="ward_code"
                class="form-control @error('ward') input-error @enderror @error('ward_code') input-error @enderror"
                data-selected-code="{{ old(
                                    'ward_code',
                                    $address->ward_code
                                ) }}" data-selected-name="{{ old(
                                    'ward',
                                    $address->ward
                                ) }}" required disabled>

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
                class="form-control @error('detail_address') input-error @enderror" value="{{ old(
                                    'detail_address',
                                    $address->detail_address
                                ) }}" placeholder="Số nhà, tên đường, tòa nhà..." required>


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
                value="{{ old(
                                    'label',
                                    $address->label
                                ) }}" placeholder="Nhà riêng, Công ty..." maxlength="50">


              @error('label')

              <div class="field-error">
                {{ $message }}
              </div>

              @enderror

            </div>

          </div>



          {{-- =====================================================
                        LEGACY ADDRESS NOTICE
                    ====================================================== --}}

          @if(
          !$address->province_code
          || !$address->ward_code
          )

          <div class="alert alert-warning">

            Địa chỉ này được lưu theo dữ liệu hành chính cũ.
            Vui lòng kiểm tra và chọn lại
            Tỉnh/Thành phố và Phường/Xã/Đặc khu
            trước khi cập nhật.

          </div>

          @endif



          {{-- =====================================================
                        DEFAULT ADDRESS
                    ====================================================== --}}

          <label class="address-default-checkbox">

            <input type="checkbox" name="is_default" value="1" {{
                                old(
                                    'is_default',
                                    $address->is_default
                                )
                                    ? 'checked'
                                    : ''
                            }}>


            <span>

              <strong>
                Đặt làm địa chỉ mặc định
              </strong>

              <small>

                @if($address->is_default)

                Đây hiện là địa chỉ mặc định
                của bạn.

                @else

                Chọn để ưu tiên địa chỉ này
                khi thanh toán.

                @endif

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
              Cập nhật địa chỉ
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