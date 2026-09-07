@extends('layouts.app')

@section('title', 'Chỉnh sửa địa chỉ - VELORA Eyes')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/address-selector.css') }}">
@endpush

@section('content')

<section class="address-form-section" data-ghn-provinces-url="{{ route('ghn.provinces') }}"
  data-ghn-districts-url="{{ route('ghn.districts') }}" data-ghn-wards-url="{{ route('ghn.wards') }}">
  <div class="velora-container">
    <div class="address-form-wrapper">

      <div class="address-form-intro">
        <span class="hero-kicker">
          UPDATE ADDRESS
        </span>

        <h1>Chỉnh sửa địa chỉ</h1>

        <p>
          Chọn đầy đủ Tỉnh/Thành phố, Quận/Huyện và
          Phường/Xã theo dữ liệu GHN.
        </p>

        <a href="{{ route('addresses.index') }}" class="address-back-link">
          ← Quay lại danh sách địa chỉ
        </a>
      </div>

      <div class="address-form-card">
        <form action="{{ route('addresses.update', $address) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="address-form-grid">

            {{-- Tên người nhận --}}
            <div class="form-group">
              <label for="recipient_name" class="form-label">
                Tên người nhận
              </label>

              <input type="text" id="recipient_name" name="recipient_name"
                class="form-control @error('recipient_name') input-error @enderror"
                value="{{ old('recipient_name', $address->recipient_name) }}" placeholder="Nguyễn Văn A" required
                autofocus>

              @error('recipient_name')
              <div class="field-error">{{ $message }}</div>
              @enderror
            </div>

            {{-- Số điện thoại --}}
            <div class="form-group">
              <label for="phone" class="form-label">
                Số điện thoại
              </label>

              <input type="text" id="phone" name="phone" class="form-control @error('phone') input-error @enderror"
                value="{{ old('phone', $address->phone) }}" placeholder="0912345678" inputmode="numeric" maxlength="10"
                required>

              @error('phone')
              <div class="field-error">{{ $message }}</div>
              @enderror
            </div>

            {{-- Tỉnh / Thành phố --}}
            <div class="form-group">
              <label for="province_picker" class="form-label">
                Tỉnh / Thành phố
              </label>

              <input type="hidden" id="province" name="province" value="{{ old('province', $address->province) }}">

              <input type="hidden" id="ghn_province_id" name="ghn_province_id"
                value="{{ old('ghn_province_id', $address->ghn_province_id) }}"
                data-selected-id="{{ old('ghn_province_id', $address->ghn_province_id) }}"
                data-selected-name="{{ old('province', $address->province) }}">

              <div class="address-location-combobox" data-location-combobox="province">
                <input type="text" id="province_picker" class="form-control address-location-picker
                    @error('province') input-error @enderror
                    @error('ghn_province_id') input-error @enderror" value="{{ old('province', $address->province) }}"
                  placeholder="Chọn hoặc nhập Tỉnh/Thành phố" autocomplete="off" role="combobox"
                  aria-autocomplete="list" aria-expanded="false" aria-controls="province_dropdown" required disabled>

                <div id="province_dropdown" class="address-location-dropdown" role="listbox" hidden></div>
              </div>

              <small class="address-location-hint">
                Bấm để xem danh sách hoặc gõ có dấu/không dấu để tìm.
              </small>

              @error('province')
              <div class="field-error">{{ $message }}</div>
              @enderror

              @error('ghn_province_id')
              <div class="field-error">{{ $message }}</div>
              @enderror
            </div>

            {{-- Quận / Huyện --}}
            <div class="form-group">
              <label for="district_picker" class="form-label">
                Quận / Huyện
              </label>

              <input type="hidden" id="district" name="district" value="{{ old('district', $address->district) }}">

              <input type="hidden" id="ghn_district_id" name="ghn_district_id"
                value="{{ old('ghn_district_id', $address->ghn_district_id) }}"
                data-selected-id="{{ old('ghn_district_id', $address->ghn_district_id) }}"
                data-selected-name="{{ old('district', $address->district) }}">

              <div class="address-location-combobox" data-location-combobox="district">
                <input type="text" id="district_picker" class="form-control address-location-picker
                    @error('district') input-error @enderror
                    @error('ghn_district_id') input-error @enderror" value="{{ old('district', $address->district) }}"
                  placeholder="Chọn Tỉnh/Thành phố trước" autocomplete="off" role="combobox" aria-autocomplete="list"
                  aria-expanded="false" aria-controls="district_dropdown" required disabled>

                <div id="district_dropdown" class="address-location-dropdown" role="listbox" hidden></div>
              </div>

              @error('district')
              <div class="field-error">{{ $message }}</div>
              @enderror

              @error('ghn_district_id')
              <div class="field-error">{{ $message }}</div>
              @enderror
            </div>

            {{-- Phường / Xã --}}
            <div class="form-group address-grid-full">
              <label for="ward_picker" class="form-label">
                Phường / Xã
              </label>

              <input type="hidden" id="ward" name="ward" value="{{ old('ward', $address->ward) }}">

              <input type="hidden" id="ghn_ward_code" name="ghn_ward_code"
                value="{{ old('ghn_ward_code', $address->ghn_ward_code) }}"
                data-selected-code="{{ old('ghn_ward_code', $address->ghn_ward_code) }}"
                data-selected-name="{{ old('ward', $address->ward) }}">

              <div class="address-location-combobox" data-location-combobox="ward">
                <input type="text" id="ward_picker" class="form-control address-location-picker
                    @error('ward') input-error @enderror
                    @error('ghn_ward_code') input-error @enderror" value="{{ old('ward', $address->ward) }}"
                  placeholder="Chọn Quận/Huyện trước" autocomplete="off" role="combobox" aria-autocomplete="list"
                  aria-expanded="false" aria-controls="ward_dropdown" required disabled>

                <div id="ward_dropdown" class="address-location-dropdown" role="listbox" hidden></div>
              </div>

              @error('ward')
              <div class="field-error">{{ $message }}</div>
              @enderror

              @error('ghn_ward_code')
              <div class="field-error">{{ $message }}</div>
              @enderror
            </div>

            {{-- Trạng thái GHN --}}
            <div class="form-group address-grid-full">
              <small id="address-location-status" aria-live="polite"></small>
            </div>

            {{-- Địa chỉ chi tiết --}}
            <div class="form-group address-grid-full">
              <label for="detail_address" class="form-label">
                Địa chỉ chi tiết
              </label>

              <input type="text" id="detail_address" name="detail_address"
                class="form-control @error('detail_address') input-error @enderror"
                value="{{ old('detail_address', $address->detail_address) }}"
                placeholder="Số nhà, tên đường, tòa nhà..." required>

              @error('detail_address')
              <div class="field-error">{{ $message }}</div>
              @enderror

              <small>
                Ví dụ: Số 15 đường Trần Thái Tông.
              </small>
            </div>

            {{-- Tên gợi nhớ --}}
            <div class="form-group address-grid-full">
              <label for="label" class="form-label">
                Tên gợi nhớ
              </label>

              <input type="text" id="label" name="label" class="form-control @error('label') input-error @enderror"
                value="{{ old('label', $address->label) }}" placeholder="Nhà riêng, Công ty..." maxlength="50">

              @error('label')
              <div class="field-error">{{ $message }}</div>
              @enderror
            </div>
          </div>

          @if(
          !$address->ghn_province_id
          || !$address->ghn_district_id
          || !$address->ghn_ward_code
          )
          <div class="alert alert-warning">
            Địa chỉ này chưa có đầy đủ mã GHN.
            Vui lòng chọn lại Tỉnh/Thành phố, Quận/Huyện và
            Phường/Xã trước khi cập nhật.
          </div>
          @endif

          <label class="address-default-checkbox">
            <input type="checkbox" name="is_default" value="1"
              {{ old('is_default', $address->is_default) ? 'checked' : '' }}>

            <span>
              <strong>Đặt làm địa chỉ mặc định</strong>

              <small>
                @if($address->is_default)
                Đây hiện là địa chỉ mặc định của bạn.
                @else
                Chọn để ưu tiên địa chỉ này khi thanh toán.
                @endif
              </small>
            </span>
          </label>

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

<script src="{{ asset('js/address-selector.js') }}" defer></script>

@endsection