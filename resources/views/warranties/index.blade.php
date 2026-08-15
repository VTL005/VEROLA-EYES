@extends('layouts.app')


@section('title', 'Bảo hành của tôi - VELORA Eyes')


@section('content')


<section class="warranty-hero">

    <div class="velora-container">

        <span class="hero-kicker">
            ELECTRONIC WARRANTY
        </span>

        <h1>
            Bảo hành của tôi
        </h1>

        <p class="text-muted mb-0">
            Quản lý và theo dõi các bảo hành
            điện tử của sản phẩm bạn đã mua.
        </p>

    </div>

</section>



<section class="section">

    <div class="velora-container">


        {{-- =================================================
            HEADER
        ================================================== --}}

        <div class="warranty-page-heading">

            <div>

                <strong>
                    {{ $warranties->total() }}
                </strong>

                <span class="text-muted">
                    bảo hành
                </span>

            </div>


            <a
                href="{{ route(
                    'warranties.lookup-form'
                ) }}"
                class="btn btn-outline"
            >
                Tra cứu mã bảo hành
            </a>

        </div>



        {{-- =================================================
            SEARCH / FILTER
        ================================================== --}}

        <div class="warranty-filter-card">

            <form
                action="{{ route(
                    'warranties.index'
                ) }}"
                method="GET"
                class="warranty-filter-form"
            >

                <div class="warranty-search-field">

                    <label
                        for="keyword"
                        class="form-label"
                    >
                        Mã bảo hành
                    </label>


                    <input
                        type="text"
                        id="keyword"
                        name="keyword"
                        class="form-control"
                        value="{{ $keyword }}"
                        placeholder="Ví dụ: BH-VLR-000001"
                    >

                </div>


                <div class="warranty-status-field">

                    <label
                        for="status"
                        class="form-label"
                    >
                        Trạng thái
                    </label>


                    <select
                        id="status"
                        name="status"
                        class="form-control"
                    >

                        <option value="">
                            Tất cả
                        </option>


                        <option
                            value="active"
                            {{ $status === 'active'
                                ? 'selected'
                                : '' }}
                        >
                            Còn hiệu lực
                        </option>


                        <option
                            value="expired"
                            {{ $status === 'expired'
                                ? 'selected'
                                : '' }}
                        >
                            Hết hạn
                        </option>


                        <option
                            value="cancelled"
                            {{ $status === 'cancelled'
                                ? 'selected'
                                : '' }}
                        >
                            Đã hủy
                        </option>

                    </select>

                </div>


                <div class="warranty-filter-actions">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Tìm kiếm
                    </button>


                    @if(
                        $keyword !== ''
                        || $status
                    )

                        <a
                            href="{{ route(
                                'warranties.index'
                            ) }}"
                            class="btn btn-outline"
                        >
                            Đặt lại
                        </a>

                    @endif

                </div>

            </form>

        </div>



        {{-- =================================================
            EMPTY
        ================================================== --}}

        @if($warranties->isEmpty())

            <div class="warranty-empty">

                <div class="warranty-empty-icon">
                    ◇
                </div>


                @if(
                    $keyword !== ''
                    || $status
                )

                    <h2>
                        Không tìm thấy bảo hành
                    </h2>

                    <p>
                        Không có bảo hành nào phù hợp
                        với điều kiện tìm kiếm.
                    </p>


                    <a
                        href="{{ route(
                            'warranties.index'
                        ) }}"
                        class="btn btn-outline"
                    >
                        Xem tất cả bảo hành
                    </a>

                @else

                    <h2>
                        Bạn chưa có bảo hành điện tử
                    </h2>

                    <p>
                        Bảo hành sẽ được cấp cho
                        sản phẩm đủ điều kiện sau
                        khi đơn hàng hoàn thành.
                    </p>


                    <a
                        href="{{ route(
                            'orders.index'
                        ) }}"
                        class="btn btn-primary"
                    >
                        Xem đơn hàng
                    </a>

                @endif

            </div>

        @else


            {{-- =================================================
                CARDS
            ================================================== --}}

            <div class="warranty-list">

                @foreach($warranties as $warranty)

                    @php

                        $productName =
                            $warranty->product?->name
                            ?? $warranty
                                ->orderDetail
                                ?->product_name
                            ?? 'Sản phẩm VELORA';


                        $isCancelled =
                            $warranty->status
                            === 'cancelled';


                        $isExpired =
                            !$isCancelled
                            && $warranty->isExpired();

                    @endphp


                    <article class="warranty-card">


                        <div class="warranty-card-header">

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



                        <div class="warranty-card-product">

                            <div class="warranty-product-icon">
                                V
                            </div>


                            <div>

                                <span>
                                    Sản phẩm
                                </span>

                                <h3>
                                    {{ $productName }}
                                </h3>


                                @if(
                                    $warranty
                                        ->orderDetail
                                        ?->order
                                )

                                    <small>

                                        Đơn hàng:
                                        {{ $warranty
                                            ->orderDetail
                                            ->order
                                            ->order_code }}

                                    </small>

                                @endif

                            </div>

                        </div>



                        <div class="warranty-date-grid">

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



                        <div class="warranty-card-footer">

                            <a
                                href="{{ route(
                                    'warranties.show',
                                    $warranty
                                ) }}"
                                class="btn btn-primary btn-sm"
                            >
                                Xem chi tiết
                            </a>

                        </div>

                    </article>

                @endforeach

            </div>


            <div class="warranty-pagination">

                {{ $warranties->links() }}

            </div>

        @endif

    </div>

</section>

@endsection