@extends('layouts.admin')


@section(
    'title',
    $warranty->warranty_code
    . ' - VELORA Eyes'
)


@section(
    'page-title',
    'Chi tiết bảo hành'
)


@section('content')

@php

    $effectiveStatus =
        $warrantyService
            ->effectiveStatus(
                $warranty
            );


    $statusLabel =
        $warrantyService
            ->effectiveStatusLabel(
                $warranty
            );

@endphp



<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            WARRANTY DETAIL
        </span>

        <h1>
            {{ $warranty->warranty_code }}
        </h1>

        <p>
            Bảo hành điện tử VELORA Eyes
        </p>

    </div>


    <div class="admin-warranty-header-actions">

        <a
            href="{{ route(
                'admin.warranties.index'
            ) }}"
            class="admin-btn admin-btn-secondary"
        >
            <i class="bi bi-arrow-left"></i>
            Danh sách
        </a>


        @if(
            $warranty
                ->orderDetail
                ?->order
        )

            <a
                href="{{ route(
                    'admin.orders.show',
                    $warranty
                        ->orderDetail
                        ->order
                ) }}"
                class="admin-btn admin-btn-secondary"
            >
                <i class="bi bi-receipt"></i>
                Đơn hàng
            </a>

        @endif

    </div>

</div>



<div class="admin-warranty-certificate">

    <div class="admin-warranty-certificate-icon">

        <i class="bi bi-shield-check"></i>

    </div>


    <div>

        <span>
            Mã bảo hành
        </span>

        <strong>
            {{ $warranty->warranty_code }}
        </strong>

        <small>
            VELORA EYES ELECTRONIC WARRANTY
        </small>

    </div>


    @if($effectiveStatus === 'active')

        <span class="admin-status success">
            <i class="bi bi-check-circle"></i>
            {{ $statusLabel }}
        </span>

    @elseif($effectiveStatus === 'expired')

        <span class="admin-status warning">
            <i class="bi bi-hourglass-bottom"></i>
            {{ $statusLabel }}
        </span>

    @else

        <span class="admin-status danger">
            <i class="bi bi-x-circle"></i>
            {{ $statusLabel }}
        </span>

    @endif

</div>



<div class="admin-warranty-detail-layout">


    <div class="admin-warranty-detail-main">

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Sản phẩm được bảo hành</h2>
                </div>

            </div>


            <div class="admin-warranty-product-detail">

                <div>

                    <i class="bi bi-eyeglasses"></i>

                </div>


                <span>

                    <strong>

                        {{ $warranty
                            ->product
                            ?->name
                            ?? $warranty
                                ->orderDetail
                                ?->product_name
                            ?? '—' }}

                    </strong>

                    <small>

                        SKU:
                        {{ $warranty
                            ->orderDetail
                            ?->sku
                            ?? '—' }}

                    </small>

                    <small>

                        {{ $warranty
                            ->orderDetail
                            ?->color
                            ?: '—' }}

                        · Size

                        {{ $warranty
                            ->orderDetail
                            ?->size
                            ?: '—' }}

                    </small>

                </span>

            </div>

        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Thời hạn bảo hành</h2>
                </div>

            </div>


            <div class="admin-warranty-period">

                <div>

                    <span>
                        Ngày bắt đầu
                    </span>

                    <strong>
                        {{ $warranty
                            ->start_date
                            ->format('d/m/Y') }}
                    </strong>

                </div>


                <div class="line">

                    <span></span>

                    <i class="bi bi-arrow-right"></i>

                    <span></span>

                </div>


                <div>

                    <span>
                        Ngày hết hạn
                    </span>

                    <strong>
                        {{ $warranty
                            ->end_date
                            ->format('d/m/Y') }}
                    </strong>

                </div>

            </div>

        </section>



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Chính sách bảo hành</h2>
                </div>

            </div>


            <div class="admin-warranty-content">

                {{ $warranty->warranty_content }}

            </div>

        </section>

    </div>



    <aside class="admin-warranty-detail-sidebar">

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
                        {{ $warranty->user?->name ?? '—' }}
                    </strong>

                    <small>
                        {{ $warranty->user?->phone ?? '—' }}
                    </small>

                    <small>
                        {{ $warranty->user?->email ?? '—' }}
                    </small>

                </span>

            </div>

        </section>



        @if(
            $warranty
                ->orderDetail
                ?->order
        )

            <section class="admin-panel">

                <div class="admin-panel-header">
                    <div>
                        <h2>Đơn hàng</h2>
                    </div>
                </div>


                <div class="admin-warranty-meta">

                    <span>

                        Mã đơn

                        <strong>
                            {{ $warranty
                                ->orderDetail
                                ->order
                                ->order_code }}
                        </strong>

                    </span>


                    <span>

                        Giá mua

                        <strong>

                            {{ number_format(
                                (float) $warranty
                                    ->orderDetail
                                    ->unit_price,
                                0,
                                ',',
                                '.'
                            ) }}đ

                        </strong>

                    </span>


                    <span>

                        Số lượng

                        <strong>
                            {{ $warranty
                                ->orderDetail
                                ->quantity }}
                        </strong>

                    </span>

                </div>

            </section>

        @endif



        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Thông tin hệ thống</h2>
                </div>
            </div>


            <div class="admin-warranty-meta">

                <span>

                    Warranty ID

                    <strong>
                        #{{ $warranty->id }}
                    </strong>

                </span>


                <span>

                    Trạng thái DB

                    <strong>
                        {{ $warrantyService
                            ->statusLabel(
                                $warranty->status
                            ) }}
                    </strong>

                </span>


                <span>

                    Trạng thái thực tế

                    <strong>
                        {{ $statusLabel }}
                    </strong>

                </span>


                <span>

                    Ngày cấp

                    <strong>

                        {{ $warranty
                            ->created_at
                            ->format(
                                'H:i d/m/Y'
                            ) }}

                    </strong>

                </span>

            </div>

        </section>



        <a
            href="{{ route(
                'warranties.lookup-form'
            ) }}"
            class="admin-warranty-lookup-link"
            target="_blank"
        >

            <i class="bi bi-search"></i>

            <span>

                <strong>
                    Kiểm tra tra cứu công khai
                </strong>

                <small>
                    Mở trang tra cứu bảo hành của khách hàng.
                </small>

            </span>

        </a>

    </aside>

</div>

@endsection