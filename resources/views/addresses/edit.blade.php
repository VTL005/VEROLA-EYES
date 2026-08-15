@extends('layouts.app')


@section('title', 'Chỉnh sửa địa chỉ - VELORA Eyes')


@section('content')

<section class="address-form-section">

    <div class="velora-container">

        <div class="address-form-wrapper">


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


                <a
                    href="{{ route('addresses.index') }}"
                    class="address-back-link"
                >
                    ← Quay lại danh sách địa chỉ
                </a>

            </div>



            <div class="address-form-card">

                <form
                    action="{{ route(
                        'addresses.update',
                        $address
                    ) }}"
                    method="POST"
                >

                    @csrf
                    @method('PUT')


                    <div class="address-form-grid">


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
                                value="{{ old(
                                    'recipient_name',
                                    $address->recipient_name
                                ) }}"
                                required
                            >


                            @error('recipient_name')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



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
                                value="{{ old(
                                    'phone',
                                    $address->phone
                                ) }}"
                                required
                            >


                            @error('phone')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



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
                                value="{{ old(
                                    'province',
                                    $address->province
                                ) }}"
                                required
                            >


                            @error('province')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



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
                                value="{{ old(
                                    'district',
                                    $address->district
                                ) }}"
                                required
                            >


                            @error('district')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



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
                                value="{{ old(
                                    'ward',
                                    $address->ward
                                ) }}"
                                required
                            >


                            @error('ward')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



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
                                value="{{ old(
                                    'label',
                                    $address->label
                                ) }}"
                                placeholder="Nhà riêng, Công ty..."
                            >


                            @error('label')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



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
                                value="{{ old(
                                    'detail_address',
                                    $address->detail_address
                                ) }}"
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
                            {{
                                old(
                                    'is_default',
                                    $address->is_default
                                )
                                    ? 'checked'
                                    : ''
                            }}
                        >


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
                            Cập nhật địa chỉ
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</section>

@endsection