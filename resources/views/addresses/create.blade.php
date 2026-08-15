@extends('layouts.app')


@section('title', 'Thêm địa chỉ - VELORA Eyes')


@section('content')

<section class="address-form-section">

    <div class="velora-container">

        <div class="address-form-wrapper">


            <div class="address-form-intro">

                <span class="hero-kicker">
                    DELIVERY ADDRESS
                </span>

                <h1>
                    Thêm địa chỉ nhận hàng
                </h1>

                <p>
                    Điền đầy đủ thông tin để VELORA
                    có thể giao đơn hàng chính xác.
                </p>


                <a
                    href="{{ route('addresses.index') }}"
                    class="address-back-link"
                >
                    ← Quay lại danh sách địa chỉ
                </a>

            </div>



            <div class="address-form-card">

                <form
                    action="{{ route('addresses.store') }}"
                    method="POST"
                >

                    @csrf


                    <div class="address-form-grid">


                        {{-- RECIPIENT --}}

                        <div class="form-group">

                            <label
                                for="recipient_name"
                                class="form-label"
                            >
                                Tên người nhận
                            </label>


                            <input
                                type="text"
                                id="recipient_name"
                                name="recipient_name"
                                class="form-control @error('recipient_name') input-error @enderror"
                                value="{{ old('recipient_name') }}"
                                placeholder="Nguyễn Văn A"
                                required
                                autofocus
                            >


                            @error('recipient_name')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- PHONE --}}

                        <div class="form-group">

                            <label
                                for="phone"
                                class="form-label"
                            >
                                Số điện thoại
                            </label>


                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-control @error('phone') input-error @enderror"
                                value="{{ old('phone') }}"
                                placeholder="0912345678"
                                required
                            >


                            @error('phone')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- PROVINCE --}}

                        <div class="form-group">

                            <label
                                for="province"
                                class="form-label"
                            >
                                Tỉnh / Thành phố
                            </label>


                            <input
                                type="text"
                                id="province"
                                name="province"
                                class="form-control @error('province') input-error @enderror"
                                value="{{ old('province') }}"
                                placeholder="Hà Nội"
                                required
                            >


                            @error('province')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- DISTRICT --}}

                        <div class="form-group">

                            <label
                                for="district"
                                class="form-label"
                            >
                                Quận / Huyện
                            </label>


                            <input
                                type="text"
                                id="district"
                                name="district"
                                class="form-control @error('district') input-error @enderror"
                                value="{{ old('district') }}"
                                placeholder="Thanh Xuân"
                                required
                            >


                            @error('district')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- WARD --}}

                        <div class="form-group">

                            <label
                                for="ward"
                                class="form-label"
                            >
                                Phường / Xã
                            </label>


                            <input
                                type="text"
                                id="ward"
                                name="ward"
                                class="form-control @error('ward') input-error @enderror"
                                value="{{ old('ward') }}"
                                placeholder="Khương Trung"
                                required
                            >


                            @error('ward')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- LABEL --}}

                        <div class="form-group">

                            <label
                                for="label"
                                class="form-label"
                            >
                                Tên gợi nhớ
                            </label>


                            <input
                                type="text"
                                id="label"
                                name="label"
                                class="form-control @error('label') input-error @enderror"
                                value="{{ old('label') }}"
                                placeholder="Nhà riêng, Công ty..."
                            >


                            @error('label')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- DETAIL ADDRESS --}}

                        <div class="form-group address-grid-full">

                            <label
                                for="detail_address"
                                class="form-label"
                            >
                                Địa chỉ chi tiết
                            </label>


                            <input
                                type="text"
                                id="detail_address"
                                name="detail_address"
                                class="form-control @error('detail_address') input-error @enderror"
                                value="{{ old('detail_address') }}"
                                placeholder="Số nhà, tên đường..."
                                required
                            >


                            @error('detail_address')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>



                    <label class="address-default-checkbox">

                        <input
                            type="checkbox"
                            name="is_default"
                            value="1"
                            {{ old('is_default') ? 'checked' : '' }}
                        >

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



                    <div class="address-form-actions">

                        <a
                            href="{{ route('addresses.index') }}"
                            class="btn btn-outline"
                        >
                            Hủy
                        </a>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Lưu địa chỉ
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</section>

@endsection