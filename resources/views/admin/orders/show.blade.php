@extends('layouts.admin')


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
        'pending' => 'Chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thanh toán thất bại',
        'refunded' => 'Đã hoàn tiền',
    ];

@endphp



<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            ORDER DETAIL
        </span>

        <h1>
            {{ $order->order_code }}
        </h1>

        <p>
            Đặt lúc
            {{ $order
                ->created_at
                ->format(
                    'H:i - d/m/Y'
                ) }}
        </p>

    </div>


    <div class="admin-order-header-actions">

        <a
            href="{{ route(
                'admin.orders.index'
            ) }}"
            class="admin-btn admin-btn-secondary"
        >
            <i class="bi bi-arrow-left"></i>

            Danh sách
        </a>


        @if($order->payment)

            <a
                href="{{ route(
                    'admin.payments.show',
                    $order->payment
                ) }}"
                class="admin-btn admin-btn-secondary"
            >
                <i class="bi bi-credit-card"></i>

                Giao dịch
            </a>

        @endif

    </div>

</div>



{{-- STATUS SUMMARY --}}

<div class="admin-order-summary">

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

        <strong class="money">

            {{ number_format(
                (float) $order->total,
                0,
                ',',
                '.'
            ) }}đ

        </strong>

    </div>


    <div>

        <span>
            Số sản phẩm
        </span>

        <strong>

            {{ $order
                ->details
                ->sum('quantity') }}

        </strong>

    </div>

</div>



<div class="admin-order-detail-layout">


    {{-- =====================================================
        MAIN
    ====================================================== --}}

    <div class="admin-order-detail-main">


        {{-- CUSTOMER --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Khách hàng & giao hàng
                    </h2>

                </div>

            </div>


            <div class="admin-order-customer-grid">

                <div>

                    <i class="bi bi-person"></i>

                    <span>

                        <small>
                            Khách hàng
                        </small>

                        <strong>
                            {{ $order->customer_name }}
                        </strong>

                    </span>

                </div>


                <div>

                    <i class="bi bi-telephone"></i>

                    <span>

                        <small>
                            Số điện thoại
                        </small>

                        <strong>
                            {{ $order->phone }}
                        </strong>

                    </span>

                </div>


                <div>

                    <i class="bi bi-envelope"></i>

                    <span>

                        <small>
                            Email
                        </small>

                        <strong>
                            {{ $order->email }}
                        </strong>

                    </span>

                </div>


                <div>

                    <i class="bi bi-geo-alt"></i>

                    <span>

                        <small>
                            Địa chỉ giao hàng
                        </small>

                        <strong>
                            {{ $order->address }}
                        </strong>

                    </span>

                </div>

            </div>


            @if($order->note)

                <div class="admin-order-note">

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

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Sản phẩm trong đơn
                    </h2>

                    <p>
                        {{ $order
                            ->details
                            ->count() }}
                        dòng sản phẩm
                    </p>

                </div>

            </div>


            @if(
                $order
                    ->details
                    ->isEmpty()
            )

                <div class="admin-empty-state">
                    Đơn hàng chưa có sản phẩm.
                </div>

            @else

                <div class="admin-table-responsive">

                    <table class="admin-table">

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
                                            {{ $detail
                                                ->product_name }}
                                        </strong>

                                    </td>


                                    <td>

                                        <code class="admin-product-sku">

                                            {{ $detail->sku }}

                                        </code>

                                    </td>


                                    <td>

                                        <div class="admin-order-variant">

                                            <span>
                                                {{ $detail->color ?: '—' }}
                                            </span>

                                            <strong>
                                                {{ $detail->size ?: '—' }}
                                            </strong>

                                        </div>

                                    </td>


                                    <td>

                                        {{ number_format(
                                            (float) $detail
                                                ->unit_price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}đ

                                    </td>


                                    <td>

                                        <strong>
                                            {{ $detail->quantity }}
                                        </strong>

                                    </td>


                                    <td>

                                        <strong class="admin-money">

                                            {{ number_format(
                                                (float) $detail
                                                    ->subtotal,
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
                                                        'admin.warranties.show',
                                                        $detail->warranty
                                                    ) }}"
                                                    class="admin-table-action"
                                                >
                                                    Xem bảo hành
                                                </a>

                                            @else

                                                <a
                                                    href="{{ route(
                                                        'admin.warranties.create',
                                                        $detail
                                                    ) }}"
                                                    class="admin-table-action"
                                                >
                                                    Cấp bảo hành
                                                </a>

                                            @endif

                                        @else

                                            <span class="admin-table-muted">
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

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Hành trình đơn hàng
                    </h2>

                    <p>
                        Lịch sử thay đổi trạng thái
                    </p>

                </div>

            </div>


            @if(
                $order
                    ->statusHistories
                    ->isEmpty()
            )

                <div class="admin-empty-state">
                    Chưa có lịch sử trạng thái.
                </div>

            @else

                <div class="admin-order-timeline">

                    @foreach(
                        $order->statusHistories
                        as $history
                    )

                        <div class="admin-order-timeline-item">

                            <div class="admin-order-timeline-marker">

                                <i class="bi bi-check"></i>

                            </div>


                            <div class="admin-order-timeline-content">

                                <div class="admin-order-timeline-head">

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


                                @if($history->description)

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

    <aside class="admin-order-detail-sidebar">


        {{-- UPDATE STATUS --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Xử lý đơn hàng</h2>
                </div>

            </div>


            <div class="admin-order-process">

                <div class="admin-order-current-status">

                    <span>
                        Trạng thái hiện tại
                    </span>

                    <strong>

                        {{ $orderStatusService
                            ->statusLabel(
                                $order->order_status
                            ) }}

                    </strong>

                </div>


                @if(!empty($nextStatuses))

                    <form
                        action="{{ route(
                            'admin.orders.update-status',
                            $order
                        ) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PATCH')


                        <div class="admin-form-group">

                            <label for="order_status">
                                Trạng thái tiếp theo
                            </label>


                            <select
                                name="order_status"
                                id="order_status"
                                class="admin-form-control"
                                required
                            >

                                <option value="">
                                    Chọn trạng thái
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

                            <div class="admin-order-warning">

                                <i class="bi bi-exclamation-triangle"></i>

                                <span>
                                    Đơn thanh toán online chưa Paid nên chưa thể hoàn thành.
                                </span>

                            </div>

                        @endif


                        <button
                            type="submit"
                            class="admin-btn admin-btn-primary admin-btn-full"
                        >
                            <i class="bi bi-arrow-right-circle"></i>

                            Cập nhật trạng thái
                        </button>

                    </form>

                @else

                    <div class="admin-order-final-state">

                        @if(
                            $order->order_status
                            === 'completed'
                        )

                            <i class="bi bi-check-circle"></i>

                            <strong>
                                Đơn hàng đã hoàn thành
                            </strong>

                        @elseif(
                            $order->order_status
                            === 'cancelled'
                        )

                            <i class="bi bi-x-circle"></i>

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

            </div>

        </section>



        {{-- CANCEL / REFUND --}}

        @if(
            $order->order_status
            === 'pending'
        )

            <section class="admin-panel">

                <div class="admin-panel-header">

                    <div>
                        <h2>Hủy đơn hàng</h2>
                    </div>

                </div>


                <div class="admin-order-cancel">

                    @if(
                        $order->payment_status
                        === 'paid'
                    )

                        <div class="admin-order-cancel-paid">

                            <i class="bi bi-credit-card"></i>

                            <p>
                                Đơn đã thanh toán.
                                Không được hủy trực tiếp.
                            </p>

                        </div>


                        @if($order->payment)

                            <a
                                href="{{ route(
                                    'admin.payments.show',
                                    $order->payment
                                ) }}"
                                class="admin-btn admin-btn-secondary admin-btn-full"
                            >
                                Xử lý hoàn tiền
                            </a>

                        @endif

                    @else

                        <p>
                            Đơn Pending có thể hủy.
                            Tồn kho sẽ được hoàn lại tự động.
                        </p>


                        <form
                            action="{{ route(
                                'admin.orders.cancel',
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
                                class="admin-btn admin-btn-danger admin-btn-full"
                            >
                                <i class="bi bi-x-circle"></i>

                                Hủy đơn hàng
                            </button>

                        </form>

                    @endif

                </div>

            </section>

        @endif



        {{-- PAYMENT --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Thanh toán</h2>
                </div>

            </div>


            <div class="admin-order-sidebar-info">

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
                        Mã giao dịch
                    </span>

                    <strong>
                        {{ $order
                            ->payment
                            ->transaction_code
                            ?: '—' }}
                    </strong>


                    <span>
                        Số tiền
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

                            {{ $order
                                ->payment
                                ->paid_at
                                ->format(
                                    'H:i d/m/Y'
                                ) }}

                        </strong>

                    @endif

                @endif

            </div>

        </section>



        {{-- TOTAL --}}

        <section class="admin-panel">

            <div class="admin-order-money">

                <div>

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

                    <div class="discount">

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


                <div>

                    <span>
                        Phí vận chuyển
                    </span>

                    <strong>

                        {{ number_format(
                            (float) $order
                                ->shipping_fee,
                            0,
                            ',',
                            '.'
                        ) }}đ

                    </strong>

                </div>


                <div class="total">

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

            </div>

        </section>

    </aside>

</div>

@endsection