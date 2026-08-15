@extends('layouts.admin')


@section(
    'title',
    'Thêm Voucher - VELORA Eyes'
)


@section(
    'page-title',
    'Thêm Voucher'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            CREATE VOUCHER
        </span>

        <h1>
            Thêm Voucher
        </h1>

        <p>
            Tạo chương trình giảm giá mới
            cho khách hàng.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.vouchers.index'
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>
        Danh sách
    </a>

</div>



<form
    action="{{ route(
        'admin.vouchers.store'
    ) }}"
    method="POST"
    class="admin-voucher-form-layout"
>

    @csrf


    <div class="admin-voucher-form-main">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin Voucher
                    </h2>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-grid">

                    <div class="admin-form-group">

                        <label for="code">
                            Mã Voucher
                            <span>*</span>
                        </label>

                        <input
                            type="text"
                            id="code"
                            name="code"
                            value="{{ old('code') }}"
                            maxlength="50"
                            class="admin-form-control"
                            placeholder="VELORA10"
                            required
                        >

                        <small class="admin-voucher-help">
                            Chỉ dùng chữ, số và dấu gạch ngang.
                        </small>

                        @error('code')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="discount_type">
                            Loại giảm
                            <span>*</span>
                        </label>

                        <select
                            id="discount_type"
                            name="discount_type"
                            class="admin-form-control"
                            required
                        >

                            <option value="">
                                Chọn loại giảm
                            </option>

                            <option
                                value="percentage"
                                {{
                                    old('discount_type')
                                    === 'percentage'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Giảm theo phần trăm
                            </option>

                            <option
                                value="fixed"
                                {{
                                    old('discount_type')
                                    === 'fixed'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Giảm tiền cố định
                            </option>

                        </select>

                        @error('discount_type')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="discount_value">
                            Giá trị giảm
                            <span>*</span>
                        </label>

                        <input
                            type="number"
                            id="discount_value"
                            name="discount_value"
                            value="{{ old('discount_value') }}"
                            min="0.01"
                            step="0.01"
                            class="admin-form-control"
                            placeholder="10 hoặc 100000"
                            required
                        >

                        @error('discount_value')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="minimum_order_amount">
                            Giá trị đơn tối thiểu
                            <span>*</span>
                        </label>

                        <input
                            type="number"
                            id="minimum_order_amount"
                            name="minimum_order_amount"
                            value="{{ old(
                                'minimum_order_amount',
                                0
                            ) }}"
                            min="0"
                            step="1"
                            class="admin-form-control"
                            required
                        >

                        @error('minimum_order_amount')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Thời gian hiệu lực</h2>
                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-grid">

                    <div class="admin-form-group">

                        <label for="starts_at">
                            Bắt đầu
                            <span>*</span>
                        </label>

                        <input
                            type="datetime-local"
                            id="starts_at"
                            name="starts_at"
                            value="{{ old(
                                'starts_at',
                                now()->format(
                                    'Y-m-d\TH:i'
                                )
                            ) }}"
                            class="admin-form-control"
                            required
                        >

                        @error('starts_at')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="ends_at">
                            Kết thúc
                            <span>*</span>
                        </label>

                        <input
                            type="datetime-local"
                            id="ends_at"
                            name="ends_at"
                            value="{{ old('ends_at') }}"
                            class="admin-form-control"
                            required
                        >

                        @error('ends_at')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Giới hạn sử dụng</h2>
                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-group">

                    <label for="usage_limit">
                        Usage Limit
                    </label>

                    <input
                        type="number"
                        id="usage_limit"
                        name="usage_limit"
                        value="{{ old('usage_limit') }}"
                        min="1"
                        step="1"
                        class="admin-form-control"
                        placeholder="Để trống nếu không giới hạn"
                    >

                    <small class="admin-voucher-help">
                        Usage Count sẽ bắt đầu từ 0
                        và hệ thống tự tăng khi Voucher được sử dụng.
                    </small>

                    @error('usage_limit')
                        <div class="admin-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </section>

    </div>



    <aside class="admin-voucher-form-sidebar">

        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Trạng thái</h2>
                </div>
            </div>


            <div class="admin-staff-switch">

                <input
                    type="checkbox"
                    id="is_active"
                    name="is_active"
                    value="1"
                    {{
                        old(
                            'is_active',
                            true
                        )
                            ? 'checked'
                            : ''
                    }}
                >

                <label for="is_active">

                    <span></span>

                    <div>

                        <strong>
                            Kích hoạt Voucher
                        </strong>

                        <small>
                            Voucher vẫn phải đúng thời gian,
                            còn lượt và đạt điều kiện đơn hàng.
                        </small>

                    </div>

                </label>

            </div>

        </section>


        <section class="admin-voucher-info-box">

            <i class="bi bi-info-circle"></i>

            <div>

                <strong>
                    Điều kiện sử dụng
                </strong>

                <span>
                    Active<br>
                    Đúng thời gian<br>
                    Còn lượt sử dụng<br>
                    Đạt giá trị đơn tối thiểu
                </span>

            </div>

        </section>


        <section class="admin-panel admin-form-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary admin-btn-full"
            >
                <i class="bi bi-plus-lg"></i>
                Tạo Voucher
            </button>


            <a
                href="{{ route(
                    'admin.vouchers.index'
                ) }}"
                class="admin-btn admin-btn-secondary admin-btn-full"
            >
                Hủy
            </a>

        </section>

    </aside>

</form>

@endsection