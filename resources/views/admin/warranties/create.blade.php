@extends('layouts.admin')


@section(
    'title',
    'Cấp bảo hành - VELORA Eyes'
)


@section(
    'page-title',
    'Cấp bảo hành'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            CREATE WARRANTY
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
            'admin.orders.show',
            $orderDetail->order
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Đơn hàng
    </a>

</div>



<div class="admin-warranty-create-notice">

    <i class="bi bi-shield-check"></i>

    <div>

        <strong>
            Thời hạn bảo hành: 12 tháng
        </strong>

        <span>
            Ngày bắt đầu được tính từ ngày cấp bảo hành.
            Mã bảo hành được hệ thống sinh tự động.
        </span>

    </div>

</div>



<form
    action="{{ route(
        'admin.warranties.store',
        $orderDetail
    ) }}"
    method="POST"
    class="admin-warranty-form-layout"
>

    @csrf


    <div class="admin-warranty-form-main">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Sản phẩm bảo hành</h2>
                </div>

            </div>


            <div class="admin-warranty-item">

                <div class="admin-warranty-item-icon">
                    <i class="bi bi-eyeglasses"></i>
                </div>


                <div class="admin-warranty-item-main">

                    <strong>
                        {{ $orderDetail->product_name }}
                    </strong>

                    <span>
                        SKU: {{ $orderDetail->sku }}
                    </span>

                    <small>

                        {{ $orderDetail->color ?: '—' }}
                        ·
                        Size {{ $orderDetail->size ?: '—' }}

                    </small>

                </div>


                <div class="admin-warranty-item-price">

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



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Chính sách bảo hành
                    </h2>

                    <p>
                        Có thể để trống để dùng chính sách mặc định.
                    </p>

                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-group">

                    <label for="warranty_content">
                        Nội dung bảo hành
                    </label>


                    <textarea
                        id="warranty_content"
                        name="warranty_content"
                        rows="9"
                        maxlength="3000"
                        class="admin-form-control"
                        placeholder="Để trống để sử dụng chính sách bảo hành mặc định của VELORA Eyes."
                    >{{ old('warranty_content') }}</textarea>


                    @error('warranty_content')

                        <div class="admin-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                @error('warranty')

                    <div class="admin-field-error">
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </section>

    </div>



    <aside class="admin-warranty-form-sidebar">

        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Khách hàng</h2>
                </div>
            </div>


            <div class="admin-warranty-person">

                <div>
                    <i class="bi bi-person"></i>
                </div>

                <span>

                    <strong>
                        {{ $orderDetail
                            ->order
                            ->customer_name }}
                    </strong>

                    <small>
                        {{ $orderDetail
                            ->order
                            ->phone }}
                    </small>

                    <small>
                        {{ $orderDetail
                            ->order
                            ->email }}
                    </small>

                </span>

            </div>

        </section>


        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Thông tin đơn hàng</h2>
                </div>
            </div>


            <div class="admin-warranty-meta">

                <span>
                    Mã đơn

                    <strong>
                        {{ $orderDetail
                            ->order
                            ->order_code }}
                    </strong>
                </span>


                <span>
                    Trạng thái

                    <strong>
                        Hoàn thành
                    </strong>
                </span>


                <span>
                    Ngày đặt

                    <strong>

                        {{ $orderDetail
                            ->order
                            ->created_at
                            ->format(
                                'd/m/Y H:i'
                            ) }}

                    </strong>
                </span>

            </div>

        </section>


        <section class="admin-panel admin-form-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary admin-btn-full"
                onclick="
                    return confirm(
                        'Xác nhận cấp bảo hành điện tử cho sản phẩm này?'
                    );
                "
            >
                <i class="bi bi-shield-check"></i>

                Cấp bảo hành
            </button>


            <a
                href="{{ route(
                    'admin.orders.show',
                    $orderDetail->order
                ) }}"
                class="admin-btn admin-btn-secondary admin-btn-full"
            >
                Hủy
            </a>

        </section>

    </aside>

</form>

@endsection