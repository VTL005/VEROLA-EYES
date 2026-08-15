<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <title>
        Xác nhận đơn hàng
    </title>

</head>

<body>

    <h1>
        VELORA Eyes
    </h1>


    <h2>
        Xác nhận đơn hàng
    </h2>


    <p>
        Xin chào
        <strong>
            {{ $order->customer_name }}
        </strong>,
    </p>


    <p>
        VELORA Eyes đã tiếp nhận đơn hàng của bạn.
    </p>


    <p>

        Mã đơn hàng:

        <strong>
            {{ $order->order_code }}
        </strong>

    </p>


    <p>

        Ngày đặt:

        {{ $order
            ->created_at
            ->format('d/m/Y H:i') }}

    </p>


    <hr>


    <h3>
        Thông tin nhận hàng
    </h3>


    <p>

        Người nhận:

        <strong>
            {{ $order->customer_name }}
        </strong>

    </p>


    <p>
        Số điện thoại:
        {{ $order->phone }}
    </p>


    <p>
        Email:
        {{ $order->email }}
    </p>


    <p>
        Địa chỉ:
        {{ $order->address }}
    </p>


    @if($order->note)

        <p>
            Ghi chú:
            {{ $order->note }}
        </p>

    @endif


    <hr>


    <h3>
        Sản phẩm
    </h3>


    <table
        border="1"
        cellpadding="8"
        cellspacing="0"
        width="100%"
    >

        <thead>

            <tr>
                <th>Sản phẩm</th>
                <th>Phân loại</th>
                <th>Đơn giá</th>
                <th>SL</th>
                <th>Thành tiền</th>
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

                        SKU:
                        {{ $detail->sku }}

                    </td>


                    <td>

                        Màu:
                        {{ $detail->color ?? '-' }}

                        <br>

                        Size:
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

                        {{ number_format(
                            (float) $detail->subtotal,
                            0,
                            ',',
                            '.'
                        ) }}đ

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    <br>


    <p>

        Tạm tính:

        <strong>

            {{ number_format(
                (float) $order->subtotal,
                0,
                ',',
                '.'
            ) }}đ

        </strong>

    </p>


    <p>

        Giảm giá:

        <strong>

            -{{ number_format(
                (float) $order->discount_amount,
                0,
                ',',
                '.'
            ) }}đ

        </strong>

    </p>


    <p>

        Phí vận chuyển:

        <strong>

            {{ number_format(
                (float) $order->shipping_fee,
                0,
                ',',
                '.'
            ) }}đ

        </strong>

    </p>


    <p>

        Tổng thanh toán:

        <strong>

            {{ number_format(
                (float) $order->total,
                0,
                ',',
                '.'
            ) }}đ

        </strong>

    </p>


    <hr>


    <h3>
        Thanh toán
    </h3>


    <p>

        Phương thức:

        <strong>

            @switch($order->payment_method)

                @case('cod')
                    Thanh toán khi nhận hàng (COD)
                    @break

                @case('qr')
                    Thanh toán QR
                    @break

                @case('vnpay')
                    VNPay
                    @break

                @default
                    {{ $order->payment_method }}

            @endswitch

        </strong>

    </p>


    <p>

        Trạng thái thanh toán:

        <strong>
            {{ $order->payment_status }}
        </strong>

    </p>


    <p>

        Trạng thái đơn hàng:

        <strong>
            {{ $order->order_status }}
        </strong>

    </p>


    <hr>


    <p>
        Cảm ơn bạn đã lựa chọn VELORA Eyes.
    </p>


    <p>
        Đây là email tự động, vui lòng không trả lời email này.
    </p>

</body>

</html>