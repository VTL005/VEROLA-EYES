@extends('layouts.app')


@section('title', 'Thanh toán VNPay - VELORA Eyes')


@section('content')

<section class="payment-page">

    <div class="velora-container">

        <div class="payment-page-grid">


            <div class="payment-card">

                <span class="hero-kicker">
                    VNPAY
                </span>

                <h1>
                    Cổng thanh toán VNPay
                </h1>


                <p class="text-muted">

                    VNPay mô phỏng phục vụ
                    quá trình xây dựng và kiểm thử
                    VELORA Eyes.

                </p>


                <div class="vnpay-bank-section">

                    <label
                        for="bank"
                        class="form-label"
                    >
                        Chọn ngân hàng
                    </label>


                    <select
                        id="bank"
                        class="form-control"
                    >

                        <option value="vcb">
                            Vietcombank
                        </option>

                        <option value="mb">
                            MB Bank
                        </option>

                        <option value="bidv">
                            BIDV
                        </option>

                        <option value="agribank">
                            Agribank
                        </option>

                        <option value="techcombank">
                            Techcombank
                        </option>

                    </select>

                </div>


                <div class="vnpay-notice">

                    <strong>
                        Môi trường mô phỏng
                    </strong>

                    <p class="mb-0">

                        Không có giao dịch ngân hàng
                        thực tế được thực hiện.

                    </p>

                </div>


                <form
                    action="{{ route(
                        'payments.vnpay.confirm',
                        $order
                    ) }}"
                    method="POST"
                    onsubmit="
                        return confirm(
                            'Xác nhận thanh toán VNPay?'
                        );
                    "
                >

                    @csrf


                    <button
                        type="submit"
                        class="btn btn-primary payment-confirm-button"
                    >
                        Thanh toán
                    </button>

                </form>

            </div>



            <aside class="payment-order-summary">

                <span class="badge badge-warning">
                    Chờ thanh toán
                </span>


                <h2>
                    Thông tin giao dịch
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
                        Điện thoại
                    </span>

                    <strong>
                        {{ $order->phone }}
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


                <a
                    href="{{ route(
                        'orders.show',
                        $order
                    ) }}"
                    class="btn btn-outline"
                    style="width:100%;margin-top:18px;"
                >
                    ← Quay lại đơn hàng
                </a>

            </aside>

        </div>

    </div>

</section>

@endsection