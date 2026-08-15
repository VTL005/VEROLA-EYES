@extends('layouts.staff')


@section(
    'title',
    $warranty->warranty_code . ' - Staff'
)


@section(
    'page-title',
    'Bảo hành điện tử'
)


@section('content')

@php

    $effectiveStatus =
        $warrantyService
            ->effectiveStatusLabel(
                $warranty
            );

@endphp



{{-- HEADER --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            ELECTRONIC WARRANTY
        </span>

        <h1>
            {{ $warranty->warranty_code }}
        </h1>

        <p>
            Hồ sơ bảo hành điện tử
            của VELORA Eyes.
        </p>

    </div>


    <a
        href="{{ route(
            'staff.orders.show',
            $warranty
                ->orderDetail
                ->order
        ) }}"
        class="staff-btn staff-btn-secondary"
    >
        ← Đơn hàng
    </a>

</div>



{{-- STATUS CARD --}}

<div class="staff-warranty-status-card">

    <div class="staff-warranty-code-icon">
        V
    </div>


    <div class="staff-warranty-status-main">

        <span>
            Mã bảo hành
        </span>

        <strong>
            {{ $warranty->warranty_code }}
        </strong>

    </div>


    <div>

        @if(
            $effectiveStatus
            === 'Đang hiệu lực'
        )

            <span class="staff-status staff-status-success">
                Đang hiệu lực
            </span>

        @elseif(
            $effectiveStatus
            === 'Đã hết hạn'
        )

            <span class="staff-status staff-status-warning">
                Đã hết hạn
            </span>

        @else

            <span class="staff-status staff-status-danger">
                {{ $effectiveStatus }}
            </span>

        @endif

    </div>

</div>



<div class="staff-warranty-detail-layout">


    {{-- MAIN --}}

    <div class="staff-warranty-detail-main">


        {{-- PRODUCT --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Sản phẩm bảo hành
                </h2>

            </div>


            <div class="staff-warranty-product">

                <div class="staff-warranty-product-icon">
                    V
                </div>


                <div class="staff-warranty-product-info">

                    <strong>

                        {{ $warranty
                            ->orderDetail
                            ->product_name }}

                    </strong>


                    <span>

                        SKU:
                        {{ $warranty
                            ->orderDetail
                            ->sku }}

                    </span>


                    <small>

                        {{ $warranty
                            ->orderDetail
                            ->color
                            ?: 'Không màu' }}

                        · Size:

                        {{ $warranty
                            ->orderDetail
                            ->size
                            ?: '—' }}

                    </small>

                </div>

            </div>

        </section>



        {{-- CUSTOMER --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Chủ sở hữu bảo hành
                </h2>

            </div>


            <div class="staff-warranty-info-grid">

                <div>

                    <span>
                        Khách hàng
                    </span>

                    <strong>

                        {{ $warranty
                            ->user
                            ?->name
                            ?? $warranty
                                ->orderDetail
                                ->order
                                ->customer_name }}

                    </strong>

                </div>


                <div>

                    <span>
                        Email
                    </span>

                    <strong>

                        {{ $warranty
                            ->user
                            ?->email
                            ?? $warranty
                                ->orderDetail
                                ->order
                                ->email
                            ?? '—' }}

                    </strong>

                </div>


                <div>

                    <span>
                        Số điện thoại
                    </span>

                    <strong>

                        {{ $warranty
                            ->orderDetail
                            ->order
                            ->phone }}

                    </strong>

                </div>


                <div>

                    <span>
                        Đơn hàng
                    </span>

                    <strong>

                        {{ $warranty
                            ->orderDetail
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

            </div>


            @if($warranty->warranty_content)

                <div class="staff-warranty-content">

                    {{ $warranty->warranty_content }}

                </div>

            @else

                <div class="staff-warranty-default-policy">

                    <strong>
                        Chính sách mặc định
                    </strong>

                    <p>
                        Bảo hành này áp dụng chính sách
                        bảo hành mặc định của VELORA Eyes.
                    </p>

                </div>

            @endif

        </section>

    </div>



    {{-- SIDEBAR --}}

    <aside class="staff-warranty-sidebar">


        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thời hạn bảo hành
                </h2>

            </div>


            <div class="staff-warranty-date">

                <div class="staff-warranty-date-point active">

                    <span></span>

                    <div>

                        <small>
                            Bắt đầu
                        </small>

                        <strong>

                            {{ $warranty
                                ->start_date
                                ->format(
                                    'd/m/Y'
                                ) }}

                        </strong>

                    </div>

                </div>


                <div class="staff-warranty-date-line">
                </div>


                <div class="staff-warranty-date-point">

                    <span></span>

                    <div>

                        <small>
                            Hết hạn
                        </small>

                        <strong>

                            {{ $warranty
                                ->end_date
                                ->format(
                                    'd/m/Y'
                                ) }}

                        </strong>

                    </div>

                </div>

            </div>

        </section>



        <section class="staff-form-card staff-warranty-meta">

            <span>
                Mã bảo hành
            </span>

            <strong>
                {{ $warranty->warranty_code }}
            </strong>


            <span>
                Trạng thái
            </span>

            <strong>
                {{ $effectiveStatus }}
            </strong>


            <span>
                Order Detail
            </span>

            <strong>
                #{{ $warranty->order_detail_id }}
            </strong>


            <span>
                Ngày cấp
            </span>

            <strong>

                {{ $warranty
                    ->created_at
                    ->format(
                        'd/m/Y H:i'
                    ) }}

            </strong>

        </section>



        <section class="staff-form-card">

            <a
                href="{{ route(
                    'staff.orders.show',
                    $warranty
                        ->orderDetail
                        ->order
                ) }}"
                class="staff-btn staff-btn-primary staff-product-full-button"
            >
                Xem đơn hàng
            </a>

        </section>

    </aside>

</div>



<div class="staff-warranty-readonly">

    <strong>
        Hồ sơ điện tử
    </strong>

    <span>
        Khách hàng có thể xem thông tin bảo hành
        này trong tài khoản hoặc tra cứu bằng mã bảo hành.
    </span>

</div>

@endsection