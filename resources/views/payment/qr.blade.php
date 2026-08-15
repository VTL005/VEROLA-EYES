@extends('layouts.app')


@section('title', 'Thanh toán QR - VELORA Eyes')


@section('content')

<section class="payment-page">

    <div class="velora-container">

        <div class="payment-page-grid">


            <div class="payment-card">

                <span class="hero-kicker">
                    QR PAYMENT
                </span>

                <h1>
                    Thanh toán bằng QR
                </h1>

                <p class="text-muted">

                    Đây là màn hình thanh toán QR
                    mô phỏng phục vụ quá trình
                    xây dựng và kiểm thử hệ thống.

                </p>


                <div class="mock-qr">

                    <div class="mock-qr-inner">

                        <strong>
                            VELORA EYES
                        </strong>

                        <span>
                            QR PAYMENT
                        </span>

                        <div>
                            {{ $order->order_code }}
                        </div>

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


                <div class="payment-transfer-content">

                    <span>
                        Nội dung chuyển khoản
                    </span>

                    <strong>
                        {{ $order->order_code }}
                    </strong>

                </div>


                <form
                    action="{{ route(
                        'payments.qr.confirm',
                        $order
                    ) }}"
                    method="POST"
                    onsubmit="
                        return confirm(
                            'Xác nhận bạn đã thanh toán QR?'
                        );
                    "
                >

                    @csrf


                    <button
                        type="submit"
                        class="btn btn-primary payment-confirm-button"
                    >
                        Tôi đã thanh toán
                    </button>

                </form>

            </div>



            <aside class="payment-order-summary">

                <span class="badge badge-warning">
                    Chờ thanh toán
                </span>


                <h2>
                    Thông tin đơn hàng
                </h2>


                <div>

                    <span>
                        Mã đơn
                    </span>

                    <strong>
                        {{ $order->order_code }}
                    </strong>

                </div>


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
                        Số tiền
                    </span>

                    <strong class="payment-big-amount">

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
                        Trạng thái
                    </span>

                    <strong>
                        Chờ thanh toán
                    </strong>

                </div>


                <a
                    href="{{ route(
                        'orders.show',
                        $order
                    ) }}"
                    class="btn btn-outline"
                    style="width:100%;margin-top:18px;"
                >
                    Xem đơn hàng
                </a>

            </aside>

        </div>

    </div>

</section>

@endsection