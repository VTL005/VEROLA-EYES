@extends('layouts.admin')


@section(
    'title',
    'Báo cáo - VELORA Eyes'
)


@section(
    'page-title',
    'Báo cáo'
)


@section('content')

@php

    $statusLabels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'preparing' => 'Đang chuẩn bị',
        'packed' => 'Đã đóng gói',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];


    $statusIcons = [
        'pending' => 'bi-clock-history',
        'confirmed' => 'bi-check2-circle',
        'preparing' => 'bi-box',
        'packed' => 'bi-box-seam',
        'shipping' => 'bi-truck',
        'completed' => 'bi-check-circle',
        'cancelled' => 'bi-x-circle',
    ];


    $paymentLabels = [
        'cod' => 'COD',
        'qr' => 'QR',
        'vnpay' => 'VNPay',
    ];


    $maxStatusCount =
        max(
            1,
            ...array_values(
                $orderStatusCounts
            )
        );


    $maxPaymentCount =
        max(
            1,
            ...array_values(
                $paymentMethodCounts
            )
        );

@endphp



{{-- =========================================================
    HEADER
========================================================= --}}

<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            BUSINESS REPORT
        </span>

        <h1>
            Báo cáo kinh doanh
        </h1>

        <p>
            Phân tích doanh thu,
            đơn hàng và sản phẩm bán chạy.
        </p>

    </div>

</div>



{{-- =========================================================
    DATE FILTER
========================================================= --}}

<div class="admin-report-filter">

    <form
        action="{{ route(
            'admin.reports.index'
        ) }}"
        method="GET"
        class="admin-report-filter-form"
    >

        <div>

            <label for="from_date">
                Từ ngày
            </label>

            <input
                type="date"
                id="from_date"
                name="from_date"
                value="{{ $fromDate }}"
                class="admin-form-control"
                required
            >

        </div>


        <div>

            <label for="to_date">
                Đến ngày
            </label>

            <input
                type="date"
                id="to_date"
                name="to_date"
                value="{{ $toDate }}"
                class="admin-form-control"
                required
            >

        </div>


        <div class="admin-report-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-bar-chart"></i>

                Xem báo cáo
            </button>

        </div>

    </form>


    <div class="admin-report-presets">

        <span>
            Xem nhanh:
        </span>


        <a
            href="{{ route(
                'admin.reports.index',
                [
                    'from_date' =>
                        now()->format('Y-m-d'),

                    'to_date' =>
                        now()->format('Y-m-d'),
                ]
            ) }}"
        >
            Hôm nay
        </a>


        <a
            href="{{ route(
                'admin.reports.index',
                [
                    'from_date' =>
                        now()
                            ->subDays(6)
                            ->format('Y-m-d'),

                    'to_date' =>
                        now()->format('Y-m-d'),
                ]
            ) }}"
        >
            7 ngày
        </a>


        <a
            href="{{ route(
                'admin.reports.index',
                [
                    'from_date' =>
                        now()
                            ->subDays(29)
                            ->format('Y-m-d'),

                    'to_date' =>
                        now()->format('Y-m-d'),
                ]
            ) }}"
        >
            30 ngày
        </a>


        <a
            href="{{ route(
                'admin.reports.index',
                [
                    'from_date' =>
                        now()
                            ->startOfMonth()
                            ->format('Y-m-d'),

                    'to_date' =>
                        now()->format('Y-m-d'),
                ]
            ) }}"
        >
            Tháng này
        </a>


        <a
            href="{{ route(
                'admin.reports.index',
                [
                    'from_date' =>
                        now()
                            ->startOfYear()
                            ->format('Y-m-d'),

                    'to_date' =>
                        now()->format('Y-m-d'),
                ]
            ) }}"
        >
            Năm nay
        </a>

    </div>

</div>



<div class="admin-report-period">

    <i class="bi bi-calendar-range"></i>

    <span>

        Báo cáo từ

        <strong>
            {{ \Illuminate\Support\Carbon::parse(
                $fromDate
            )->format('d/m/Y') }}
        </strong>

        đến

        <strong>
            {{ \Illuminate\Support\Carbon::parse(
                $toDate
            )->format('d/m/Y') }}
        </strong>

    </span>

</div>



{{-- =========================================================
    KPI
========================================================= --}}

<div class="admin-report-kpis">


    <div class="admin-report-kpi revenue">

        <div class="admin-report-kpi-icon">
            <i class="bi bi-cash-stack"></i>
        </div>


        <div>

            <span>
                Doanh thu
            </span>

            <strong>

                {{ number_format(
                    $revenue,
                    0,
                    ',',
                    '.'
                ) }}đ

            </strong>

            <small>
                Chỉ đơn đã hoàn thành
            </small>

        </div>

    </div>



    <div class="admin-report-kpi">

        <div class="admin-report-kpi-icon">
            <i class="bi bi-receipt"></i>
        </div>


        <div>

            <span>
                Tổng đơn
            </span>

            <strong>
                {{ $totalOrders }}
            </strong>

            <small>
                {{ $completedOrders }}
                đơn hoàn thành
            </small>

        </div>

    </div>



    <div class="admin-report-kpi">

        <div class="admin-report-kpi-icon">
            <i class="bi bi-calculator"></i>
        </div>


        <div>

            <span>
                Giá trị đơn trung bình
            </span>

            <strong>

                {{ number_format(
                    $averageOrderValue,
                    0,
                    ',',
                    '.'
                ) }}đ

            </strong>

            <small>
                Trên đơn hoàn thành
            </small>

        </div>

    </div>



    <div class="admin-report-kpi">

        <div class="admin-report-kpi-icon">
            <i class="bi bi-box-seam"></i>
        </div>


        <div>

            <span>
                Sản phẩm đã bán
            </span>

            <strong>
                {{ $totalItemsSold }}
            </strong>

            <small>
                Tổng số lượng sản phẩm
            </small>

        </div>

    </div>

</div>



{{-- =========================================================
    PERFORMANCE
========================================================= --}}

<div class="admin-report-performance">

    <div>

        <div class="admin-report-performance-head">

            <span>
                Tỷ lệ hoàn thành
            </span>

            <strong>
                {{ $completionRate }}%
            </strong>

        </div>


        <div class="admin-report-progress success">

            <span
                style="
                    width:
                    {{ min(
                        100,
                        $completionRate
                    ) }}%;
                "
            ></span>

        </div>


        <small>

            {{ $completedOrders }}
            /
            {{ $totalOrders }}
            đơn hàng

        </small>

    </div>



    <div>

        <div class="admin-report-performance-head">

            <span>
                Tỷ lệ hủy
            </span>

            <strong>
                {{ $cancellationRate }}%
            </strong>

        </div>


        <div class="admin-report-progress danger">

            <span
                style="
                    width:
                    {{ min(
                        100,
                        $cancellationRate
                    ) }}%;
                "
            ></span>

        </div>


        <small>

            {{ $cancelledOrders }}
            /
            {{ $totalOrders }}
            đơn hàng

        </small>

    </div>

</div>



{{-- =========================================================
    REVENUE TREND
========================================================= --}}

<section class="admin-panel admin-report-section">

    <div class="admin-panel-header">

        <div>

            <h2>
                Doanh thu theo ngày
            </h2>

            <p>
                Chỉ tính đơn hàng hoàn thành
            </p>

        </div>

    </div>


    <div class="admin-report-revenue-chart">

        @foreach(
            $revenueByDate
            as $daily
        )

            @php

                $barWidth =
                    $maxDailyRevenue > 0
                        ? (
                            $daily->revenue
                            / $maxDailyRevenue
                        ) * 100
                        : 0;

            @endphp


            <div class="admin-report-revenue-row">

                <div class="admin-report-revenue-date">

                    <strong>

                        {{ \Illuminate\Support\Carbon::parse(
                            $daily->report_date
                        )->format('d/m') }}

                    </strong>

                    <span>

                        {{ $daily->order_count }}
                        đơn

                    </span>

                </div>


                <div class="admin-report-revenue-track">

                    <span
                        style="
                            width:
                            {{ max(
                                0,
                                $barWidth
                            ) }}%;
                        "
                    ></span>

                </div>


                <strong class="admin-report-revenue-value">

                    {{ number_format(
                        $daily->revenue,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </strong>

            </div>

        @endforeach

    </div>

</section>



{{-- =========================================================
    BREAKDOWNS
========================================================= --}}

<div class="admin-report-two-columns">


    {{-- ORDER STATUS --}}

    <section class="admin-panel">

        <div class="admin-panel-header">

            <div>

                <h2>
                    Trạng thái đơn hàng
                </h2>

                <p>
                    Phân bổ trong kỳ
                </p>

            </div>

        </div>


        <div class="admin-report-breakdown">

            @foreach(
                $orderStatusCounts
                as $status => $count
            )

                @php

                    $percentage =
                        $totalOrders > 0
                            ? round(
                                (
                                    $count
                                    / $totalOrders
                                ) * 100,
                                1
                            )
                            : 0;

                @endphp


                <div class="admin-report-breakdown-row">

                    <div class="admin-report-breakdown-label">

                        <i
                            class="bi {{
                                $statusIcons[$status]
                                ?? 'bi-circle'
                            }}"
                        ></i>


                        <span>

                            {{ $statusLabels[
                                $status
                            ] ?? $status }}

                        </span>

                    </div>


                    <div class="admin-report-breakdown-bar">

                        <span
                            style="
                                width:
                                {{
                                    (
                                        $count
                                        / $maxStatusCount
                                    ) * 100
                                }}%;
                            "
                        ></span>

                    </div>


                    <div class="admin-report-breakdown-value">

                        <strong>
                            {{ $count }}
                        </strong>

                        <small>
                            {{ $percentage }}%
                        </small>

                    </div>

                </div>

            @endforeach

        </div>

    </section>



    {{-- PAYMENT METHOD --}}

    <section class="admin-panel">

        <div class="admin-panel-header">

            <div>

                <h2>
                    Phương thức thanh toán
                </h2>

                <p>
                    Theo số đơn được tạo
                </p>

            </div>

        </div>


        <div class="admin-report-payment-methods">

            @foreach(
                $paymentMethodCounts
                as $method => $count
            )

                @php

                    $percentage =
                        $totalOrders > 0
                            ? round(
                                (
                                    $count
                                    / $totalOrders
                                ) * 100,
                                1
                            )
                            : 0;

                @endphp


                <div>

                    <div class="admin-report-payment-icon">

                        @if($method === 'cod')

                            <i class="bi bi-cash"></i>

                        @elseif($method === 'qr')

                            <i class="bi bi-qr-code"></i>

                        @else

                            <i class="bi bi-credit-card"></i>

                        @endif

                    </div>


                    <span>

                        <strong>

                            {{ $paymentLabels[
                                $method
                            ] ?? strtoupper(
                                $method
                            ) }}

                        </strong>

                        <small>
                            {{ $count }} đơn
                        </small>

                    </span>


                    <em>
                        {{ $percentage }}%
                    </em>

                </div>

            @endforeach

        </div>

    </section>

</div>



{{-- =========================================================
    TOP PRODUCTS
========================================================= --}}

<section class="admin-panel admin-report-section">

    <div class="admin-panel-header">

        <div>

            <h2>
                Top sản phẩm bán chạy
            </h2>

            <p>
                Xếp hạng theo số lượng đã bán
            </p>

        </div>

    </div>


    @if($topProducts->isEmpty())

        <div class="admin-report-empty">

            <i class="bi bi-box"></i>

            <strong>
                Chưa có dữ liệu bán hàng
            </strong>

            <span>
                Không có Order Completed trong khoảng thời gian này.
            </span>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Sản phẩm
                        </th>

                        <th>
                            Số đơn
                        </th>

                        <th>
                            Số lượng bán
                        </th>

                        <th>
                            Doanh thu
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $topProducts
                        as $product
                    )

                        <tr>

                            <td>

                                <div class="admin-report-rank {{
                                    $loop->iteration <= 3
                                        ? 'top'
                                        : ''
                                }}">

                                    {{ $loop->iteration }}

                                </div>

                            </td>


                            <td>

                                <div class="admin-report-product">

                                    <strong>
                                        {{ $product->product_name }}
                                    </strong>

                                    <span>

                                        Product ID:

                                        {{ $product->product_id
                                            ?? 'Snapshot' }}

                                    </span>

                                </div>

                            </td>


                            <td>

                                {{ $product->order_count }}

                            </td>


                            <td>

                                <strong>
                                    {{ $product->quantity_sold }}
                                </strong>

                            </td>


                            <td>

                                <strong class="admin-money">

                                    {{ number_format(
                                        (float) $product->revenue,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif

</section>



{{-- =========================================================
    RECENT COMPLETED ORDERS
========================================================= --}}

<section class="admin-panel admin-report-section">

    <div class="admin-panel-header">

        <div>

            <h2>
                Đơn hoàn thành gần nhất
            </h2>

            <p>
                Trong khoảng báo cáo
            </p>

        </div>

    </div>


    @if(
        $recentCompletedOrders->isEmpty()
    )

        <div class="admin-report-empty">

            <i class="bi bi-receipt"></i>

            <strong>
                Chưa có đơn hoàn thành
            </strong>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>
                            Mã đơn
                        </th>

                        <th>
                            Khách hàng
                        </th>

                        <th>
                            Phương thức
                        </th>

                        <th>
                            Tổng tiền
                        </th>

                        <th>
                            Ngày đặt
                        </th>

                        <th></th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $recentCompletedOrders
                        as $order
                    )

                        <tr>

                            <td>

                                <code class="admin-report-order-code">

                                    {{ $order->order_code }}

                                </code>

                            </td>


                            <td>

                                <div class="admin-table-primary">

                                    <strong>
                                        {{ $order->customer_name }}
                                    </strong>

                                    <span>
                                        {{ $order->phone }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                {{ $paymentLabels[
                                    $order->payment_method
                                ] ?? strtoupper(
                                    $order->payment_method
                                ) }}

                            </td>


                            <td>

                                <strong class="admin-money">

                                    {{ number_format(
                                        (float) $order->total,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </td>


                            <td>

                                {{ $order
                                    ->created_at
                                    ->format(
                                        'd/m/Y H:i'
                                    ) }}

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'admin.orders.show',
                                        $order
                                    ) }}"
                                    class="admin-order-view"
                                    title="Xem đơn hàng"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif

</section>

@endsection