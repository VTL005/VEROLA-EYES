@extends('layouts.admin')


@section(
    'title',
    'Sửa ' . $voucher->code
)


@section(
    'page-title',
    'Sửa Voucher'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            EDIT VOUCHER
        </span>

        <h1>
            {{ $voucher->code }}
        </h1>

        <p>
            Cập nhật điều kiện chương trình giảm giá.
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



<div class="admin-voucher-edit-summary">

    <div>
        <span>Đã sử dụng</span>

        <strong>
            {{ $voucher->usage_count }}
        </strong>
    </div>


    <div>
        <span>Giới hạn</span>

        <strong>
            {{ $voucher->usage_limit ?? 'Không giới hạn' }}
        </strong>
    </div>


    <div>
        <span>Ngày tạo</span>

        <strong>
            {{ $voucher
                ->created_at
                ->format('d/m/Y') }}
        </strong>
    </div>

</div>



<form
    action="{{ route(
        'admin.vouchers.update',
        $voucher
    ) }}"
    method="POST"
    class="admin-voucher-form-layout"
>

    @csrf
    @method('PUT')


    <div class="admin-voucher-form-main">

        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Thông tin Voucher</h2>
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
                            value="{{ old(
                                'code',
                                $voucher->code
                            ) }}"
                            maxlength="50"
                            class="admin-form-control"
                            required
                        >

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

                            <option
                                value="percentage"
                                {{
                                    old(
                                        'discount_type',
                                        $voucher->discount_type
                                    ) === 'percentage'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Giảm theo phần trăm
                            </option>

                            <option
                                value="fixed"
                                {{
                                    old(
                                        'discount_type',
                                        $voucher->discount_type
                                    ) === 'fixed'
                                        ? 'selected'
                                        : ''
                                }}
                            >
                                Giảm tiền cố định
                            </option>

                        </select>

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
                            value="{{ old(
                                'discount_value',
                                $voucher->discount_value
                            ) }}"
                            min="0.01"
                            step="0.01"
                            class="admin-form-control"
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
                                $voucher->minimum_order_amount
                            ) }}"
                            min="0"
                            step="1"
                            class="admin-form-control"
                            required
                        >

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
                        </label>

                        <input
                            type="datetime-local"
                            id="starts_at"
                            name="starts_at"
                            value="{{ old(
                                'starts_at',
                                $voucher
                                    ->starts_at
                                    ->format(
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
                        </label>

                        <input
                            type="datetime-local"
                            id="ends_at"
                            name="ends_at"
                            value="{{ old(
                                'ends_at',
                                $voucher
                                    ->ends_at
                                    ->format(
                                        'Y-m-d\TH:i'
                                    )
                            ) }}"
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
                    <h2>Lượt sử dụng</h2>
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
                        value="{{ old(
                            'usage_limit',
                            $voucher->usage_limit
                        ) }}"
                        min="1"
                        step="1"
                        class="admin-form-control"
                        placeholder="Không giới hạn"
                    >

                    <div class="admin-voucher-usage-warning">

                        <i class="bi bi-shield-lock"></i>

                        <span>
                            Usage Count hiện tại:
                            <strong>
                                {{ $voucher->usage_count }}
                            </strong>.
                            Giá trị này không được sửa thủ công.
                        </span>

                    </div>

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
                            $voucher->is_active
                        )
                            ? 'checked'
                            : ''
                    }}
                >

                <label for="is_active">

                    <span></span>

                    <div>

                        <strong>
                            Voucher đang bật
                        </strong>

                        <small>
                            Bỏ chọn để khóa Voucher.
                        </small>

                    </div>

                </label>

            </div>

        </section>


        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Thông tin hệ thống</h2>
                </div>
            </div>


            <div class="admin-voucher-meta">

                <span>
                    Voucher ID
                    <strong>#{{ $voucher->id }}</strong>
                </span>

                <span>
                    Usage Count
                    <strong>{{ $voucher->usage_count }}</strong>
                </span>

                <span>
                    Ngày tạo
                    <strong>
                        {{ $voucher
                            ->created_at
                            ->format(
                                'H:i d/m/Y'
                            ) }}
                    </strong>
                </span>

            </div>

        </section>


        <section class="admin-panel admin-form-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary admin-btn-full"
            >
                <i class="bi bi-check-lg"></i>
                Lưu thay đổi
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