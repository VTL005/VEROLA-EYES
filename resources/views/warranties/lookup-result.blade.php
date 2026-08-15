@extends('layouts.app')


@section('title', 'Kết quả tra cứu bảo hành - VELORA Eyes')


@section('content')

@php

    $productName =
        $warranty->product?->name
        ?? 'Sản phẩm VELORA';


    $isCancelled =
        $warranty->status
        === 'cancelled';


    $isExpired =
        !$isCancelled
        && $warranty->isExpired();

@endphp


<section class="warranty-lookup-result-section">

    <div class="velora-container">

        <div class="warranty-result-wrapper">


            <div class="warranty-result-success">

                <div class="warranty-result-check">
                    ✓
                </div>


                <span class="hero-kicker">
                    WARRANTY FOUND
                </span>


                <h1>
                    Đã tìm thấy bảo hành
                </h1>


                <p>
                    Thông tin dưới đây được xác nhận
                    từ hệ thống VELORA Eyes.
                </p>

            </div>



            <div class="warranty-public-card">

                <div class="warranty-public-header">

                    <div>

                        <span>
                            Mã bảo hành
                        </span>

                        <strong>
                            {{ $warranty->warranty_code }}
                        </strong>

                    </div>


                    @if($isCancelled)

                        <span class="warranty-status warranty-cancelled">
                            Đã hủy
                        </span>

                    @elseif($isExpired)

                        <span class="warranty-status warranty-expired">
                            Hết hạn
                        </span>

                    @else

                        <span class="warranty-status warranty-active">
                            Còn hiệu lực
                        </span>

                    @endif

                </div>



                <div class="warranty-public-product">

                    <span>
                        Sản phẩm
                    </span>

                    <h2>
                        {{ $productName }}
                    </h2>

                </div>



                <div class="warranty-public-dates">

                    <div>

                        <span>
                            Bắt đầu
                        </span>

                        <strong>

                            {{ $warranty
                                ->start_date
                                ->format('d/m/Y') }}

                        </strong>

                    </div>


                    <div>

                        <span>
                            Hết hạn
                        </span>

                        <strong>

                            {{ $warranty
                                ->end_date
                                ->format('d/m/Y') }}

                        </strong>

                    </div>

                </div>



                <div class="warranty-public-policy">

                    <span>
                        Nội dung bảo hành
                    </span>


                    <p>

                        {{ $warranty->warranty_content
                            ?: 'Áp dụng theo chính sách bảo hành của VELORA Eyes.' }}

                    </p>

                </div>

            </div>



            <div class="warranty-result-actions">

                <a
                    href="{{ route(
                        'warranties.lookup-form'
                    ) }}"
                    class="btn btn-outline"
                >
                    Tra cứu mã khác
                </a>


                <a
                    href="{{ route('home') }}"
                    class="btn btn-primary"
                >
                    Trang chủ
                </a>

            </div>

        </div>

    </div>

</section>

@endsection