@extends('layouts.app')


@section(
    'title',
    'Đặt hàng thành công - VELORA Eyes'
)


@section('content')

<section class="order-success-section">

    <div class="velora-container">

        <div class="order-success-card">


            <div class="order-success-icon">
                ✓
            </div>


            <span class="badge badge-success">
                Đặt hàng thành công
            </span>


            <h1>
                Cảm ơn bạn đã lựa chọn VELORA.
            </h1>


            <p class="order-success-lead">

                Đơn hàng của bạn đã được tiếp nhận.
                Bạn có thể theo dõi trạng thái
                trong mục Đơn hàng của tôi.

            </p>


            <div class="order-success-code">

                <span>
                    Mã đơn hàng
                </span>

                <strong>
                    {{ $order->order_code }}
                </strong>

            </div>



            <div class="success-info-grid">

                <div>

                    <span>
                        Người nhận
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
                        Phương thức thanh toán
                    </span>

                    <strong>

                        @switch($order->payment_method)

                            @case('cod')
                                COD
                                @break

                            @case('qr')
                                QR
                                @break

                            @case('vnpay')
                                VNPay
                                @break

                        @endswitch

                    </strong>

                </div>


                <div>

                    <span>
                        Trạng thái
                    </span>

                    <strong>
                        Chờ xác nhận
                    </strong>

                </div>

            </div>



            <div class="success-address">

                <span>
                    Địa chỉ nhận hàng
                </span>

                <p class="mb-0">
                    {{ $order->address }}
                </p>

            </div>



            <div class="table-wrapper">

                <table class="velora-table">

                    <thead>

                        <tr>

                            <th>
                                Sản phẩm
                            </th>

                            <th>
                                Màu
                            </th>

                            <th>
                                Size
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

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($order->details as $detail)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $detail->product_name }}
                                    </strong>

                                    <br>

                                    <small>
                                        {{ $detail->sku }}
                                    </small>

                                </td>


                                <td>
                                    {{ $detail->color ?? '-' }}
                                </td>


                                <td>
                                    {{ $detail->size ?? '-' }}
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

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>



            <div class="success-total-box">

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
                    (float) $order->discount_amount > 0
                )

                    <div>

                        <span>
                            Giảm giá
                        </span>

                        <strong
                            style="
                                color:var(--velora-success);
                            "
                        >

                            -
                            {{ number_format(
                                (float) $order->discount_amount,
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
                            (float) $order->shipping_fee,
                            0,
                            ',',
                            '.'
                        ) }}đ

                    </strong>

                </div>


                <div class="success-grand-total">

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



            @if($order->payment_method === 'cod')

                <div class="alert alert-success">

                    Bạn sẽ thanh toán khi nhận hàng.

                </div>

            @endif



            <div class="order-success-actions">

                <a
                    href="{{ route(
                        'orders.show',
                        $order
                    ) }}"
                    class="btn btn-primary"
                >
                    Xem chi tiết đơn hàng
                </a>


                <a
                    href="{{ route('orders.index') }}"
                    class="btn btn-outline"
                >
                    Đơn hàng của tôi
                </a>


                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-outline"
                >
                    Tiếp tục mua sắm
                </a>

            </div>

        </div>

    </div>

</section>

@endsection