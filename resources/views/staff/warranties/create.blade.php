@extends('layouts.staff')


@section(
    'title',
    'Cấp bảo hành - Staff'
)


@section(
    'page-title',
    'Cấp bảo hành'
)


@section('content')


<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            ELECTRONIC WARRANTY
        </span>

        <h1>
            Cấp bảo hành điện tử
        </h1>

        <p>
            Tạo bảo hành cho sản phẩm
            thuộc đơn hàng đã hoàn thành.
        </p>

    </div>


    <a
        href="{{ route(
            'staff.orders.show',
            $orderDetail->order
        ) }}"
        class="staff-btn staff-btn-secondary"
    >
        ← Chi tiết đơn hàng
    </a>

</div>



@if($errors->has('warranty'))

    <div class="staff-warranty-error">

        {{ $errors->first('warranty') }}

    </div>

@endif



<div class="staff-warranty-create-layout">


    {{-- MAIN --}}

    <div class="staff-warranty-create-main">


        {{-- PRODUCT --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Sản phẩm được bảo hành
                </h2>

                <p>
                    Thông tin được lấy từ
                    Order Detail tại thời điểm mua.
                </p>

            </div>


            <div class="staff-warranty-product">

                <div class="staff-warranty-product-icon">
                    V
                </div>


                <div class="staff-warranty-product-info">

                    <strong>
                        {{ $orderDetail->product_name }}
                    </strong>

                    <span>
                        SKU: {{ $orderDetail->sku }}
                    </span>

                    <small>

                        {{ $orderDetail->color ?: 'Không màu' }}

                        ·

                        Size:
                        {{ $orderDetail->size ?: '—' }}

                        ·

                        SL:
                        {{ $orderDetail->quantity }}

                    </small>

                </div>


                <div class="staff-warranty-product-price">

                    <span>
                        Giá mua
                    </span>

                    <strong>

                        {{ number_format(
                            (float) $orderDetail->unit_price,
                            0,
                            ',',
                            '.'
                        ) }}đ

                    </strong>

                </div>

            </div>

        </section>



        {{-- CUSTOMER --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Khách hàng
                </h2>

            </div>


            <div class="staff-warranty-info-grid">

                <div>

                    <span>
                        Họ tên
                    </span>

                    <strong>
                        {{ $orderDetail
                            ->order
                            ->customer_name }}
                    </strong>

                </div>


                <div>

                    <span>
                        Số điện thoại
                    </span>

                    <strong>
                        {{ $orderDetail
                            ->order
                            ->phone }}
                    </strong>

                </div>


                <div>

                    <span>
                        Email
                    </span>

                    <strong>
                        {{ $orderDetail
                            ->order
                            ->email
                            ?: 'Chưa cung cấp' }}
                    </strong>

                </div>


                <div>

                    <span>
                        Mã đơn hàng
                    </span>

                    <strong>
                        {{ $orderDetail
                            ->order
                            ->order_code }}
                    </strong>

                </div>

            </div>

        </section>



        {{-- WARRANTY CONTENT --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Nội dung bảo hành
                </h2>

                <p>
                    Có thể nhập điều khoản riêng
                    cho sản phẩm này.
                </p>

            </div>


            <div class="staff-form-group">

                <label for="warranty_content">
                    Nội dung / ghi chú bảo hành
                </label>


                <textarea
                    id="warranty_content"
                    name="warranty_content"
                    form="staff-warranty-form"
                    rows="9"
                    maxlength="3000"
                    class="staff-form-control
                        @error('warranty_content')
                            staff-input-error
                        @enderror"
                    placeholder="Để trống để áp dụng chính sách bảo hành mặc định của VELORA Eyes."
                >{{ old('warranty_content') }}</textarea>


                <small class="staff-form-help">
                    Tối đa 3000 ký tự.
                </small>


                @error('warranty_content')

                    <div class="staff-field-error">
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </section>

    </div>



    {{-- SIDEBAR --}}

    <aside class="staff-warranty-sidebar">


        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thời hạn
                </h2>

            </div>


            <div class="staff-warranty-duration">

                <div>

                    <span>
                        Ngày bắt đầu
                    </span>

                    <strong>
                        Ngày cấp bảo hành
                    </strong>

                </div>


                <div class="staff-warranty-duration-arrow">
                    ↓
                </div>


                <div>

                    <span>
                        Thời hạn
                    </span>

                    <strong>
                        12 tháng
                    </strong>

                </div>

            </div>

        </section>



        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Trạng thái
                </h2>

            </div>


            <div class="staff-warranty-active-preview">

                <span></span>

                <div>

                    <strong>
                        Đang hiệu lực
                    </strong>

                    <small>
                        Bắt đầu ngay sau khi cấp.
                    </small>

                </div>

            </div>

        </section>



        <section class="staff-warranty-guide">

            <strong>
                Lưu ý
            </strong>

            <p>
                Mỗi sản phẩm trong đơn chỉ
                được cấp một bảo hành điện tử.
            </p>

            <p>
                Sau khi tạo, hệ thống sẽ sinh
                mã bảo hành riêng.
            </p>

        </section>



        <form
            id="staff-warranty-form"
            action="{{ route(
                'staff.warranties.store',
                $orderDetail
            ) }}"
            method="POST"
            class="staff-form-card staff-form-actions-card"
            onsubmit="
                return confirm(
                    'Xác nhận cấp bảo hành điện tử cho sản phẩm này?'
                );
            "
        >

            @csrf


            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Cấp bảo hành
            </button>


            <a
                href="{{ route(
                    'staff.orders.show',
                    $orderDetail->order
                ) }}"
                class="staff-btn staff-btn-secondary"
            >
                Hủy
            </a>

        </form>

    </aside>

</div>

@endsection