@extends('layouts.admin')


@section(
    'title',
    'Thanh toán - VELORA Eyes'
)


@section(
    'page-title',
    'Thanh toán'
)


@section('content')

@php

    $methodLabels = [
        'cod' => 'COD',
        'qr' => 'QR',
        'vnpay' => 'VNPay',
    ];


    $statusLabels = [
        'unpaid' => 'Chưa thanh toán',
        'pending' => 'Chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thất bại',
        'refunded' => 'Đã hoàn tiền',
    ];

@endphp



<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            PAYMENT MANAGEMENT
        </span>

        <h1>
            Quản lý thanh toán
        </h1>

        <p>
            Theo dõi giao dịch COD,
            QR và VNPay của hệ thống.
        </p>

    </div>

</div>



{{-- STATS --}}

<div class="admin-payment-stats">

    <div class="admin-payment-stat">

        <div class="all">
            <i class="bi bi-credit-card"></i>
        </div>

        <span>

            <small>
                Tổng giao dịch
            </small>

            <strong>
                {{ $totalPayments }}
            </strong>

        </span>

    </div>


    <a
        href="{{ route(
            'admin.payments.index',
            ['status' => 'pending']
        ) }}"
        class="admin-payment-stat"
    >

        <div class="pending">
            <i class="bi bi-clock-history"></i>
        </div>

        <span>

            <small>
                Chờ thanh toán
            </small>

            <strong>
                {{ $pendingPayments }}
            </strong>

        </span>

    </a>


    <a
        href="{{ route(
            'admin.payments.index',
            ['status' => 'paid']
        ) }}"
        class="admin-payment-stat"
    >

        <div class="paid">
            <i class="bi bi-check-circle"></i>
        </div>

        <span>

            <small>
                Đã thanh toán
            </small>

            <strong>
                {{ $paidPayments }}
            </strong>

        </span>

    </a>


    <a
        href="{{ route(
            'admin.payments.index',
            ['status' => 'refunded']
        ) }}"
        class="admin-payment-stat"
    >

        <div class="refunded">
            <i class="bi bi-arrow-counterclockwise"></i>
        </div>

        <span>

            <small>
                Đã hoàn tiền
            </small>

            <strong>
                {{ $refundedPayments }}
            </strong>

        </span>

    </a>

</div>



<div class="admin-payment-revenue">

    <i class="bi bi-cash-stack"></i>

    <div>

        <span>
            Tổng giá trị giao dịch đang ở trạng thái Paid
        </span>

        <strong>

            {{ number_format(
                $paidAmount,
                0,
                ',',
                '.'
            ) }}đ

        </strong>

    </div>

</div>



{{-- FILTER --}}

<div class="admin-payment-filter">

    <form
        action="{{ route(
            'admin.payments.index'
        ) }}"
        method="GET"
        class="admin-payment-filter-form"
    >

        <div>

            <label for="keyword">
                Tìm kiếm
            </label>


            <div class="admin-input-icon">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    class="admin-form-control"
                    placeholder="Mã đơn, giao dịch, tên, email..."
                >

            </div>

        </div>


        <div>

            <label for="payment_method">
                Phương thức
            </label>

            <select
                id="payment_method"
                name="payment_method"
                class="admin-form-control"
            >

                <option value="">
                    Tất cả
                </option>

                @foreach(
                    $methodLabels
                    as $value => $label
                )

                    <option
                        value="{{ $value }}"
                        {{
                            $paymentMethod === $value
                                ? 'selected'
                                : ''
                        }}
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

        </div>


        <div>

            <label for="status">
                Trạng thái
            </label>

            <select
                id="status"
                name="status"
                class="admin-form-control"
            >

                <option value="">
                    Tất cả
                </option>

                @foreach(
                    $statusLabels
                    as $value => $label
                )

                    <option
                        value="{{ $value }}"
                        {{
                            $status === $value
                                ? 'selected'
                                : ''
                        }}
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

        </div>


        <div>

            <label for="payment_date">
                Ngày giao dịch
            </label>

            <input
                type="date"
                id="payment_date"
                name="payment_date"
                value="{{ $paymentDate }}"
                class="admin-form-control"
            >

        </div>


        <div class="admin-payment-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-funnel"></i>

                Lọc
            </button>


            @if(
                $keyword !== ''
                || $paymentMethod
                || $status
                || $paymentDate
            )

                <a
                    href="{{ route(
                        'admin.payments.index'
                    ) }}"
                    class="admin-btn admin-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



{{-- TABLE --}}

<div class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Danh sách giao dịch
            </h2>

            <p>
                {{ $payments->total() }}
                giao dịch
            </p>

        </div>

    </div>


    @if($payments->isEmpty())

        <div class="admin-payment-empty">

            <i class="bi bi-credit-card"></i>

            <h3>
                Không có giao dịch phù hợp
            </h3>

            <p>
                Hãy thử thay đổi bộ lọc.
            </p>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>
                            Giao dịch
                        </th>

                        <th>
                            Đơn hàng
                        </th>

                        <th>
                            Khách hàng
                        </th>

                        <th>
                            Phương thức
                        </th>

                        <th>
                            Số tiền
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Thanh toán lúc
                        </th>

                        <th>
                            Ngày tạo
                        </th>

                        <th>
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $payments
                        as $payment
                    )

                        <tr>

                            <td>

                                <div class="admin-payment-code">

                                    <strong>
                                        #{{ $payment->id }}
                                    </strong>

                                    <span>

                                        {{ $payment
                                            ->transaction_code
                                            ?: 'Chưa có mã giao dịch' }}

                                    </span>

                                </div>

                            </td>


                            <td>

                                @if($payment->order)

                                    <a
                                        href="{{ route(
                                            'admin.orders.show',
                                            $payment->order
                                        ) }}"
                                        class="admin-table-action"
                                    >
                                        {{ $payment
                                            ->order
                                            ->order_code }}
                                    </a>

                                @else

                                    <span class="admin-table-muted">
                                        —
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if($payment->order)

                                    <div class="admin-payment-customer">

                                        <strong>
                                            {{ $payment
                                                ->order
                                                ->customer_name }}
                                        </strong>

                                        <span>
                                            {{ $payment
                                                ->order
                                                ->phone }}
                                        </span>

                                    </div>

                                @else

                                    —

                                @endif

                            </td>


                            <td>

                                <span class="admin-order-payment-method">

                                    {{ $methodLabels[
                                        $payment->payment_method
                                    ] ?? strtoupper(
                                        $payment->payment_method
                                    ) }}

                                </span>

                            </td>


                            <td>

                                <strong class="admin-money">

                                    {{ number_format(
                                        (float) $payment->amount,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </td>


                            <td>

                                @switch($payment->status)

                                    @case('paid')

                                        <span class="admin-status success">
                                            Đã thanh toán
                                        </span>

                                        @break


                                    @case('pending')

                                        <span class="admin-status warning">
                                            Chờ thanh toán
                                        </span>

                                        @break


                                    @case('failed')

                                        <span class="admin-status danger">
                                            Thất bại
                                        </span>

                                        @break


                                    @case('refunded')

                                        <span class="admin-status info">
                                            Đã hoàn tiền
                                        </span>

                                        @break


                                    @default

                                        <span class="admin-status muted">
                                            Chưa thanh toán
                                        </span>

                                @endswitch

                            </td>


                            <td>

                                {{ $payment->paid_at
                                    ?->format(
                                        'd/m/Y H:i'
                                    )
                                    ?? '—' }}

                            </td>


                            <td>

                                {{ $payment->created_at
                                    ?->format(
                                        'd/m/Y H:i'
                                    )
                                    ?? '—' }}

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'admin.payments.show',
                                        $payment
                                    ) }}"
                                    class="admin-order-view"
                                    title="Chi tiết giao dịch"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="admin-pagination">

            {{ $payments->links() }}

        </div>

    @endif

</div>

@endsection