@extends('layouts.admin')


@section(
    'title',
    'Giao dịch #' . $payment->id
)


@section(
    'page-title',
    'Chi tiết thanh toán'
)


@section('content')

@php

    $methodLabels = [
        'cod' => 'Thanh toán khi nhận hàng (COD)',
        'qr' => 'Chuyển khoản QR',
        'vnpay' => 'VNPay',
    ];


    $statusLabels = [
        'unpaid' => 'Chưa thanh toán',
        'pending' => 'Chờ thanh toán',
        'paid' => 'Đã thanh toán',
        'failed' => 'Thanh toán thất bại',
        'refunded' => 'Đã hoàn tiền',
    ];


    $canRefund =
        $payment->status === 'paid'
        && in_array(
            $payment->payment_method,
            ['qr', 'vnpay'],
            true
        )
        && $payment->order
        && $payment->order->payment_status === 'paid'
        && $payment->order->order_status === 'pending';

@endphp



<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            PAYMENT DETAIL
        </span>

        <h1>
            Giao dịch #{{ $payment->id }}
        </h1>

        <p>

            {{ $payment->transaction_code
                ?: 'Chưa có mã giao dịch' }}

        </p>

    </div>


    <div class="admin-payment-header-actions">

        <a
            href="{{ route(
                'admin.payments.index'
            ) }}"
            class="admin-btn admin-btn-secondary"
        >
            <i class="bi bi-arrow-left"></i>

            Danh sách
        </a>


        @if($payment->order)

            <a
                href="{{ route(
                    'admin.orders.show',
                    $payment->order
                ) }}"
                class="admin-btn admin-btn-secondary"
            >
                <i class="bi bi-receipt"></i>

                Xem đơn hàng
            </a>

        @endif

    </div>

</div>



{{-- SUMMARY --}}

<div class="admin-payment-detail-summary">

    <div>

        <span>
            Số tiền
        </span>

        <strong class="money">

            {{ number_format(
                (float) $payment->amount,
                0,
                ',',
                '.'
            ) }}đ

        </strong>

    </div>


    <div>

        <span>
            Phương thức
        </span>

        <strong>

            {{ $methodLabels[
                $payment->payment_method
            ] ?? strtoupper(
                $payment->payment_method
            ) }}

        </strong>

    </div>


    <div>

        <span>
            Trạng thái
        </span>

        <strong>

            {{ $statusLabels[
                $payment->status
            ] ?? $payment->status }}

        </strong>

    </div>


    <div>

        <span>
            Mã giao dịch
        </span>

        <strong>

            {{ $payment->transaction_code
                ?: '—' }}

        </strong>

    </div>

</div>



<div class="admin-payment-detail-layout">


    <div class="admin-payment-detail-main">


        {{-- TRANSACTION --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin giao dịch
                    </h2>

                </div>

            </div>


            <div class="admin-payment-info-grid">

                <div>

                    <span>
                        Payment ID
                    </span>

                    <strong>
                        #{{ $payment->id }}
                    </strong>

                </div>


                <div>

                    <span>
                        Mã giao dịch
                    </span>

                    <strong>
                        {{ $payment->transaction_code ?: '—' }}
                    </strong>

                </div>


                <div>

                    <span>
                        Response Code
                    </span>

                    <strong>
                        {{ $payment->response_code ?: '—' }}
                    </strong>

                </div>


                <div>

                    <span>
                        Ngày tạo
                    </span>

                    <strong>

                        {{ $payment->created_at
                            ?->format(
                                'H:i d/m/Y'
                            )
                            ?? '—' }}

                    </strong>

                </div>


                <div>

                    <span>
                        Thanh toán lúc
                    </span>

                    <strong>

                        {{ $payment->paid_at
                            ?->format(
                                'H:i d/m/Y'
                            )
                            ?? '—' }}

                    </strong>

                </div>


                <div>

                    <span>
                        Hoàn tiền lúc
                    </span>

                    <strong>

                        {{ $payment->refunded_at
                            ?->format(
                                'H:i d/m/Y'
                            )
                            ?? '—' }}

                    </strong>

                </div>

            </div>

        </section>



        {{-- ORDER --}}

        @if($payment->order)

            <section class="admin-panel">

                <div class="admin-panel-header">

                    <div>

                        <h2>
                            Đơn hàng liên quan
                        </h2>

                    </div>


                    <a
                        href="{{ route(
                            'admin.orders.show',
                            $payment->order
                        ) }}"
                        class="admin-table-action"
                    >
                        Xem đơn hàng
                    </a>

                </div>


                <div class="admin-payment-order-card">

                    <div>

                        <span>
                            Mã đơn hàng
                        </span>

                        <strong>
                            {{ $payment
                                ->order
                                ->order_code }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Trạng thái đơn
                        </span>

                        <strong>
                            {{ ucfirst(
                                $payment
                                    ->order
                                    ->order_status
                            ) }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Trạng thái thanh toán
                        </span>

                        <strong>
                            {{ $statusLabels[
                                $payment
                                    ->order
                                    ->payment_status
                            ]
                            ?? $payment
                                ->order
                                ->payment_status }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Tổng đơn
                        </span>

                        <strong>

                            {{ number_format(
                                (float) $payment
                                    ->order
                                    ->total,
                                0,
                                ',',
                                '.'
                            ) }}đ

                        </strong>

                    </div>

                </div>

            </section>



            {{-- CUSTOMER --}}

            <section class="admin-panel">

                <div class="admin-panel-header">

                    <div>
                        <h2>Khách hàng</h2>
                    </div>

                </div>


                <div class="admin-payment-customer-detail">

                    <div>

                        <i class="bi bi-person"></i>

                        <span>
                            <small>Họ tên</small>

                            <strong>
                                {{ $payment
                                    ->order
                                    ->customer_name }}
                            </strong>
                        </span>

                    </div>


                    <div>

                        <i class="bi bi-telephone"></i>

                        <span>
                            <small>Số điện thoại</small>

                            <strong>
                                {{ $payment
                                    ->order
                                    ->phone }}
                            </strong>
                        </span>

                    </div>


                    <div>

                        <i class="bi bi-envelope"></i>

                        <span>
                            <small>Email</small>

                            <strong>
                                {{ $payment
                                    ->order
                                    ->email }}
                            </strong>
                        </span>

                    </div>

                </div>

            </section>



            {{-- PRODUCTS --}}

            <section class="admin-panel">

                <div class="admin-panel-header">

                    <div>

                        <h2>
                            Sản phẩm
                        </h2>

                        <p>
                            {{ $payment
                                ->order
                                ->details
                                ->count() }}
                            dòng sản phẩm
                        </p>

                    </div>

                </div>


                <div class="admin-table-responsive">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>Sản phẩm</th>
                                <th>SKU</th>
                                <th>Màu / Size</th>
                                <th>SL</th>
                                <th>Thành tiền</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach(
                                $payment
                                    ->order
                                    ->details
                                as $detail
                            )

                                <tr>

                                    <td>

                                        <strong>
                                            {{ $detail->product_name }}
                                        </strong>

                                    </td>


                                    <td>

                                        <code class="admin-product-sku">

                                            {{ $detail->sku }}

                                        </code>

                                    </td>


                                    <td>

                                        {{ $detail->color ?: '—' }}
                                        /
                                        {{ $detail->size ?: '—' }}

                                    </td>


                                    <td>
                                        {{ $detail->quantity }}
                                    </td>


                                    <td>

                                        <strong class="admin-money">

                                            {{ number_format(
                                                (float) $detail->subtotal,
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

            </section>

        @endif

    </div>



    {{-- SIDEBAR --}}

    <aside class="admin-payment-detail-sidebar">


        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Trạng thái</h2>
                </div>

            </div>


            <div class="admin-payment-status-card">

                @switch($payment->status)

                    @case('paid')

                        <div class="paid">

                            <i class="bi bi-check-circle"></i>

                            <span>

                                <strong>
                                    Đã thanh toán
                                </strong>

                                <small>
                                    Giao dịch đã được ghi nhận thành công.
                                </small>

                            </span>

                        </div>

                        @break


                    @case('refunded')

                        <div class="refunded">

                            <i class="bi bi-arrow-counterclockwise"></i>

                            <span>

                                <strong>
                                    Đã hoàn tiền
                                </strong>

                                <small>
                                    Không thể hoàn tiền lần thứ hai.
                                </small>

                            </span>

                        </div>

                        @break


                    @case('failed')

                        <div class="failed">

                            <i class="bi bi-x-circle"></i>

                            <span>

                                <strong>
                                    Thanh toán thất bại
                                </strong>

                                <small>
                                    Giao dịch chưa thành công.
                                </small>

                            </span>

                        </div>

                        @break


                    @default

                        <div class="pending">

                            <i class="bi bi-clock-history"></i>

                            <span>

                                <strong>
                                    {{ $statusLabels[
                                        $payment->status
                                    ] ?? $payment->status }}
                                </strong>

                                <small>
                                    Chưa đủ điều kiện hoàn tiền.
                                </small>

                            </span>

                        </div>

                @endswitch

            </div>

        </section>



        {{-- REFUND --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Hoàn tiền</h2>
                </div>

            </div>


            <div class="admin-payment-refund">

                @if($canRefund)

                    <div class="admin-payment-refund-warning">

                        <i class="bi bi-exclamation-triangle"></i>

                        <div>

                            <strong>
                                Thao tác tài chính quan trọng
                            </strong>

                            <p>
                                Khi xác nhận, Payment sẽ chuyển sang Refunded,
                                Order sẽ bị hủy và tồn kho được hoàn lại.
                            </p>

                        </div>

                    </div>


                    <form
                        action="{{ route(
                            'admin.payments.refund',
                            $payment
                        ) }}"
                        method="POST"
                        onsubmit="
                            return confirm(
                                'Bạn chắc chắn muốn ghi nhận hoàn tiền và hủy đơn hàng này?'
                            );
                        "
                    >

                        @csrf
                        @method('PATCH')


                        <button
                            type="submit"
                            class="admin-btn admin-btn-danger admin-btn-full"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>

                            Hoàn tiền & hủy đơn
                        </button>

                    </form>

                @elseif(
                    $payment->status
                    === 'refunded'
                )

                    <div class="admin-payment-refund-done">

                        <i class="bi bi-check-circle"></i>

                        <strong>
                            Giao dịch đã được hoàn tiền.
                        </strong>

                    </div>

                @else

                    <div class="admin-payment-refund-disabled">

                        <i class="bi bi-lock"></i>

                        <strong>
                            Không đủ điều kiện hoàn tiền
                        </strong>

                        <span>

                            Chỉ QR/VNPay đã Paid và
                            Order còn Pending mới được hoàn tiền.

                        </span>

                    </div>

                @endif

            </div>

        </section>



        <section class="admin-payment-security-note">

            <i class="bi bi-shield-check"></i>

            <div>

                <strong>
                    Bảo vệ dữ liệu
                </strong>

                <span>
                    Refund được kiểm tra lại ở Service,
                    không phụ thuộc vào việc nút có hiển thị hay không.
                </span>

            </div>

        </section>

    </aside>

</div>

@endsection