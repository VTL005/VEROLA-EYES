@extends('layouts.staff')


@section(
    'title',
    'Đơn hàng ' . $order->order_code
)


@section(
    'page-title',
    'Chi tiết đơn hàng'
)


@section('content')

@php

    $paymentMethodLabels = [
        'cod' => 'Thanh toán khi nhận hàng (COD)',
        'qr' => 'Chuyển khoản QR',
        'vnpay' => 'VNPay',
    ];

    $paymentStatusLabels = [
        'unpaid' => 'Chưa thanh toán',
        'pending' => 'Đang thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thanh toán thất bại',
        'refunded' => 'Đã hoàn tiền',
    ];

@endphp



{{-- =========================================================
    HEADER
========================================================= --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            ORDER
        </span>

        <h1>
            {{ $order->order_code }}
        </h1>

        <p>
            Đặt lúc
            {{ $order->created_at->format(
                'H:i - d/m/Y'
            ) }}
        </p>

    </div>


    <a
        href="{{ route(
            'staff.orders.index'
        ) }}"
        class="staff-btn staff-btn-secondary"
    >
        ← Danh sách đơn
    </a>

</div>



{{-- =========================================================
    STATUS BAR
========================================================= --}}

<div class="staff-order-status-bar">

    <div>

        <span>
            Trạng thái đơn
        </span>

        <strong>
            {{ $orderStatusService
                ->statusLabel(
                    $order->order_status
                ) }}
        </strong>

    </div>


    <div>

        <span>
            Thanh toán
        </span>

        <strong>
            {{ $paymentStatusLabels[
                $order->payment_status
            ] ?? $order->payment_status }}
        </strong>

    </div>


    <div>

        <span>
            Tổng thanh toán
        </span>

        <strong class="staff-order-status-total">

            {{ number_format(
                (float) $order->total,
                0,
                ',',
                '.'
            ) }}đ

        </strong>

    </div>

</div>



<div class="staff-order-detail-layout">


    {{-- =====================================================
        MAIN
    ====================================================== --}}

    <div class="staff-order-detail-main">


        {{-- CUSTOMER --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thông tin khách hàng
                </h2>

            </div>


            <div class="staff-order-info-grid">

                <div>

                    <span>
                        Khách hàng
                    </span>

                    <strong>
                        {{ $order->customer_name }}
                    </strong>

                </div>


                <div>

                    <span>
                        Số điện thoại
                    </span>

                    <strong>
                        {{ $order->phone }}
                    </strong>

                </div>


                <div>

                    <span>
                        Email
                    </span>

                    <strong>
                        {{ $order->email
                            ?: 'Chưa cung cấp' }}
                    </strong>

                </div>


                <div class="staff-order-info-full">

                    <span>
                        Địa chỉ giao hàng
                    </span>

                    <strong>
                        {{ $order->address }}
                    </strong>

                </div>

            </div>


            @if($order->note)

                <div class="staff-order-note">

                    <span>
                        Ghi chú của khách hàng
                    </span>

                    <p>
                        {{ $order->note }}
                    </p>

                </div>

            @endif

        </section>



        {{-- PRODUCTS --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Sản phẩm trong đơn
                </h2>

                <p>
                    {{ $order->details->count() }}
                    dòng sản phẩm
                </p>

            </div>


            @if($order->details->isEmpty())

                <div class="staff-empty-small">

                    Đơn hàng chưa có sản phẩm.

                </div>

            @else

                <div class="staff-table-responsive">

                    <table class="staff-table">

                        <thead>

                            <tr>

                                <th>
                                    Sản phẩm
                                </th>

                                <th>
                                    SKU
                                </th>

                                <th>
                                    Màu / Size
                                </th>

                                <th>
                                    Đơn giá
                                </th>

                                <th>
                                    SL
                                </th>

                                <th>
                                    Thành tiền
                                </th>

                                <th>
                                    Bảo hành
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach(
                                $order->details
                                as $detail
                            )

                                <tr>

                                    <td>

                                        <strong>
                                            {{ $detail->product_name }}
                                        </strong>

                                    </td>


                                    <td>

                                        <code class="staff-slug">
                                            {{ $detail->sku }}
                                        </code>

                                    </td>


                                    <td>

                                        <div class="staff-order-variant-info">

                                            <span>
                                                {{ $detail->color
                                                    ?: '—' }}
                                            </span>

                                            <strong>
                                                {{ $detail->size
                                                    ?: '—' }}
                                            </strong>

                                        </div>

                                    </td>


                                    <td>

                                        {{ number_format(
                                            (float) $detail->unit_price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                                    </td>


                                    <td>

                                        {{ $detail->quantity }}

                                    </td>


                                    <td>

                                        <strong>

                                            {{ number_format(
                                                (float) $detail->subtotal,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                                        </strong>

                                    </td>


                                    <td>

    @if(
        $order->order_status
        === 'completed'
    )

        @if($detail->warranty)

            <a
                href="{{ route(
                    'staff.warranties.show',
                    $detail->warranty
                ) }}"
                class="staff-action-button"
            >
                Xem bảo hành
            </a>

        @else

            <a
                href="{{ route(
                    'staff.warranties.create',
                    $detail
                ) }}"
                class="staff-action-button"
            >
                Cấp bảo hành
            </a>

        @endif

    @else

        <span class="staff-table-muted">
            Sau hoàn thành
        </span>

    @endif

</td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </section>



        {{-- TIMELINE --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Hành trình đơn hàng
                </h2>

                <p>
                    Lịch sử cập nhật trạng thái.
                </p>

            </div>


            @if(
                $order
                    ->statusHistories
                    ->isEmpty()
            )

                <div class="staff-empty-small">

                    Chưa có lịch sử trạng thái.

                </div>

            @else

                <div class="staff-order-timeline">

                    @foreach(
                        $order->statusHistories
                        as $history
                    )

                        <div class="staff-order-timeline-item">

                            <div class="staff-order-timeline-marker">
                            </div>


                            <div class="staff-order-timeline-content">

                                <div class="staff-order-timeline-head">

                                    <strong>

                                        {{ $orderStatusService
                                            ->statusLabel(
                                                $history->status
                                            ) }}

                                    </strong>


                                    <span>

                                        {{ $history
                                            ->created_at
                                            ->format(
                                                'H:i d/m/Y'
                                            ) }}

                                    </span>

                                </div>


                                @if(
                                    $history->description
                                )

                                    <p>
                                        {{ $history->description }}
                                    </p>

                                @endif


                                <small>

                                    Cập nhật bởi:

                                    <strong>

                                        {{ $history
                                            ->updater
                                            ?->name
                                            ?? 'Hệ thống' }}

                                    </strong>

                                </small>

                            </div>

                        </div>

                    @endforeach

                </div>

            @endif

        </section>

    </div>



    {{-- =====================================================
        SIDEBAR
    ====================================================== --}}

    <aside class="staff-order-detail-sidebar">


        {{-- STATUS UPDATE --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Xử lý đơn hàng
                </h2>

            </div>


            @if(
                !empty($nextStatuses)
            )

                <form
                    action="{{ route(
                        'staff.orders.update-status',
                        $order
                    ) }}"
                    method="POST"
                >

                    @csrf
                    @method('PATCH')


                    <div class="staff-form-group">

                        <label for="order_status">
                            Trạng thái tiếp theo
                        </label>


                        <select
                            name="order_status"
                            id="order_status"
                            class="staff-form-control"
                            required
                        >

                            <option value="">
                                -- Chọn trạng thái --
                            </option>


                            @foreach(
                                $nextStatuses
                                as $status
                            )

                                <option
                                    value="{{ $status }}"
                                >

                                    {{ $orderStatusService
                                        ->statusLabel(
                                            $status
                                        ) }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    @if(
                        $order->order_status
                        === 'shipping'
                        && $order->payment_method
                        !== 'cod'
                        && $order->payment_status
                        !== 'paid'
                    )

                        <div class="staff-order-warning-box">

                            Đơn thanh toán online chưa
                            thành công nên không thể
                            chuyển sang Hoàn thành.

                        </div>

                    @endif


                    <button
                        type="submit"
                        class="staff-btn staff-btn-primary staff-product-full-button"
                    >
                        Cập nhật trạng thái
                    </button>

                </form>

            @else

                <div class="staff-order-final-state">

                    @if(
                        $order->order_status
                        === 'completed'
                    )

                        <strong>
                            ✓ Đơn hàng đã hoàn thành
                        </strong>

                    @elseif(
                        $order->order_status
                        === 'cancelled'
                    )

                        <strong>
                            Đơn hàng đã bị hủy
                        </strong>

                    @else

                        <strong>
                            Không còn trạng thái tiếp theo
                        </strong>

                    @endif

                </div>

            @endif

        </section>



        {{-- CANCEL --}}

        @if(
            $order->order_status
            === 'pending'
        )

            <section class="staff-form-card staff-order-cancel-card">

                <div class="staff-form-card-heading">

                    <h2>
                        Hủy đơn
                    </h2>

                </div>


                @if(
                    $order->payment_status
                    === 'paid'
                )

                    <p>
                        Đơn đã thanh toán.
                        Cần xử lý hoàn tiền trước
                        khi hủy.
                    </p>

                @else

                    <p>
                        Đơn Pending có thể được hủy.
                        Tồn kho sẽ được hoàn lại tự động.
                    </p>


                    <form
                        action="{{ route(
                            'staff.orders.cancel',
                            $order
                        ) }}"
                        method="POST"
                        onsubmit="
                            return confirm(
                                'Bạn có chắc muốn hủy đơn hàng này?'
                            );
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <button
                            type="submit"
                            class="staff-btn staff-btn-danger staff-product-full-button"
                        >
                            Hủy đơn hàng
                        </button>

                    </form>

                @endif

            </section>

        @endif



        {{-- PAYMENT --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thanh toán
                </h2>

            </div>


            <div class="staff-order-sidebar-info">

                <span>
                    Phương thức
                </span>

                <strong>

                    {{ $paymentMethodLabels[
                        $order->payment_method
                    ] ?? strtoupper(
                        $order->payment_method
                    ) }}

                </strong>


                <span>
                    Trạng thái
                </span>

                <strong>

                    {{ $paymentStatusLabels[
                        $order->payment_status
                    ] ?? $order->payment_status }}

                </strong>


                @if($order->payment)

                    <span>
                        Số tiền Payment
                    </span>

                    <strong>

                        {{ number_format(
                            (float) $order
                                ->payment
                                ->amount,
                            0,
                            ',',
                            '.'
                        ) }}đ

                    </strong>


                    @if(
                        $order
                            ->payment
                            ->paid_at
                    )

                        <span>
                            Thanh toán lúc
                        </span>

                        <strong>

                            {{ \Illuminate\Support\Carbon::parse(
                                $order
                                    ->payment
                                    ->paid_at
                            )->format(
                                'H:i d/m/Y'
                            ) }}

                        </strong>

                    @endif

                @endif

            </div>

        </section>



        {{-- ORDER TOTAL --}}

        <section class="staff-form-card">

            <div class="staff-order-money-row">

                <span>
                    Tạm tính
                </span>

                <strong>

                    {{ number_format(
                        (float) $order->subtotal,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </strong>

            </div>


            @if(
                (float) $order
                    ->discount_amount > 0
            )

                <div class="staff-order-money-row discount">

                    <span>
                        Giảm giá
                    </span>

                    <strong>

                        -{{ number_format(
                            (float) $order
                                ->discount_amount,
                            0,
                            ',',
                            '.'
                        ) }}đ

                    </strong>

                </div>

            @endif


            <div class="staff-order-money-row">

                <span>
                    Phí vận chuyển
                </span>

                <strong>

                    {{ number_format(
                        (float) $order->shipping_fee,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </strong>

            </div>


            <div class="staff-order-money-total">

                <span>
                    Tổng thanh toán
                </span>

                <strong>

                    {{ number_format(
                        (float) $order->total,
                        0,
                        ',',
                        '.'
                    ) }}đ

                </strong>

            </div>

        </section>

    </aside>

</div>

@endsection