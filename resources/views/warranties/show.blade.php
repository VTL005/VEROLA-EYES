@extends('layouts.app')


@section(
    'title',
    'Bảo hành ' . $warranty->warranty_code . ' - VELORA Eyes'
)


@section('content')

@php

    $productName =
        $warranty->product?->name
        ?? $warranty->orderDetail?->product_name
        ?? 'Sản phẩm VELORA';


    $isCancelled =
        $warranty->status
        === 'cancelled';


    $isExpired =
        !$isCancelled
        && $warranty->isExpired();

@endphp



<section class="warranty-detail-hero">

    <div class="velora-container">

        <a
            href="{{ route(
                'warranties.index'
            ) }}"
            class="warranty-back-link"
        >
            ← Bảo hành của tôi
        </a>


        <div class="warranty-detail-hero-row">

            <div>

                <span class="hero-kicker">
                    WARRANTY CERTIFICATE
                </span>

                <h1>
                    Thẻ bảo hành điện tử
                </h1>

                <p class="text-muted mb-0">
                    {{ $warranty->warranty_code }}
                </p>

            </div>


            @if($isCancelled)

                <span class="warranty-status warranty-cancelled warranty-status-large">
                    Đã hủy
                </span>

            @elseif($isExpired)

                <span class="warranty-status warranty-expired warranty-status-large">
                    Hết hạn
                </span>

            @else

                <span class="warranty-status warranty-active warranty-status-large">
                    Còn hiệu lực
                </span>

            @endif

        </div>

    </div>

</section>



<section class="section">

    <div class="velora-container">

        <div class="warranty-detail-layout">


            <main class="warranty-detail-main">


                {{-- CERTIFICATE --}}

                <div class="warranty-certificate">

                    <div class="warranty-certificate-top">

                        <div>

                            <span>
                                VELORA EYES
                            </span>

                            <h2>
                                Bảo hành điện tử
                            </h2>

                        </div>


                        <div class="warranty-certificate-code">

                            <small>
                                WARRANTY CODE
                            </small>

                            <strong>
                                {{ $warranty->warranty_code }}
                            </strong>

                        </div>

                    </div>



                    <div class="warranty-certificate-product">

                        <span>
                            Sản phẩm được bảo hành
                        </span>

                        <h2>
                            {{ $productName }}
                        </h2>

                    </div>



                    <div class="warranty-certificate-dates">

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

                </div>



                {{-- POLICY --}}

                <div class="warranty-detail-card">

                    <h2>
                        Chính sách bảo hành
                    </h2>


                    <div class="warranty-policy-content">

                        {!! nl2br(
                            e(
                                $warranty->warranty_content
                                ?: 'Áp dụng theo chính sách bảo hành của VELORA Eyes.'
                            )
                        ) !!}

                    </div>

                </div>

            </main>



            <aside class="warranty-detail-sidebar">

                <div class="warranty-detail-card">

                    <h2>
                        Thông tin bảo hành
                    </h2>


                    <div class="warranty-meta-list">

                        <div>

                            <span>
                                Mã bảo hành
                            </span>

                            <strong>
                                {{ $warranty->warranty_code }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Sản phẩm
                            </span>

                            <strong>
                                {{ $productName }}
                            </strong>

                        </div>


                        @if(
                            $warranty
                                ->orderDetail
                                ?->order
                        )

                            <div>

                                <span>
                                    Đơn hàng
                                </span>

                                <a
                                    href="{{ route(
                                        'orders.show',
                                        $warranty
                                            ->orderDetail
                                            ->order
                                    ) }}"
                                >

                                    {{ $warranty
                                        ->orderDetail
                                        ->order
                                        ->order_code }}

                                </a>

                            </div>

                        @endif


                        <div>

                            <span>
                                Ngày kích hoạt
                            </span>

                            <strong>

                                {{ $warranty
                                    ->created_at
                                    ->format('d/m/Y H:i') }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                Trạng thái
                            </span>

                            <strong>

                                @if($isCancelled)

                                    Đã hủy

                                @elseif($isExpired)

                                    Hết hạn

                                @else

                                    Còn hiệu lực

                                @endif

                            </strong>

                        </div>

                    </div>

                </div>


                <a
                    href="{{ route(
                        'warranties.lookup-form'
                    ) }}"
                    class="btn btn-outline"
                    style="width:100%;"
                >
                    Tra cứu mã bảo hành
                </a>


                <a
                    href="{{ route(
                        'warranties.index'
                    ) }}"
                    class="btn btn-outline"
                    style="width:100%;"
                >
                    ← Quay lại danh sách
                </a>

            </aside>

        </div>

    </div>

</section>

@endsection