@extends('layouts.app')

@section('title', 'Kết quả đơn hàng - VELORA Eyes')

@section('content')

@php
$paymentIsPaid = $order->payment_status === 'paid'
|| $order->payment?->isPaid();

$isOnlinePayment = in_array(
$order->payment_method,
['qr', 'onepay', 'vnpay'],
true
);

$paymentLabel = match ($order->payment_method) {
'cod' => 'COD',
'qr' => 'QR qua payOS',
'onepay' => 'OnePAY',
'vnpay' => 'VNPay (đơn cũ)',
default => strtoupper((string) $order->payment_method),
};
@endphp

<section class="order-success-section">
  <div class="velora-container">
    <div class="order-success-card">

      <div class="order-success-icon">✓</div>

      <span class="badge badge-success">
        @if($isOnlinePayment && $paymentIsPaid)
        Thanh toán thành công
        @else
        Đặt hàng thành công
        @endif
      </span>

      <h1>
        @if($isOnlinePayment && $paymentIsPaid)
        Thanh toán thành công!
        @else
        Cảm ơn bạn đã lựa chọn VELORA.
        @endif
      </h1>

      <p class="order-success-lead">
        @if($isOnlinePayment && $paymentIsPaid)
        Giao dịch đã được xác minh và đơn hàng của bạn đã được tiếp nhận.
        @else
        Đơn hàng của bạn đã được tiếp nhận. Bạn có thể theo dõi trạng thái
        trong mục Đơn hàng của tôi.
        @endif
      </p>

      <div class="order-success-code">
        <span>Mã đơn hàng</span>
        <strong>{{ $order->order_code }}</strong>
      </div>

      <div class="success-info-grid">
        <div>
          <span>Người nhận</span>
          <strong>{{ $order->customer_name }}</strong>
        </div>

        <div>
          <span>Số điện thoại</span>
          <strong>{{ $order->phone }}</strong>
        </div>

        <div>
          <span>Phương thức thanh toán</span>
          <strong>{{ $paymentLabel }}</strong>
        </div>

        <div>
          <span>Trạng thái thanh toán</span>
          <strong>
            {{ $paymentIsPaid ? 'Đã thanh toán' : 'Chưa thanh toán' }}
          </strong>
        </div>
      </div>

      @if($isOnlinePayment && $paymentIsPaid && $order->payment)
      <div class="success-address">
        <span>Thông tin giao dịch</span>

        <p class="mb-1">
          Mã giao dịch:
          <strong>{{ $order->payment->transaction_code ?: '-' }}</strong>
        </p>

        <p class="mb-0">
          Thời gian thanh toán:
          <strong>
            {{ $order->payment->paid_at?->format('d/m/Y H:i:s') ?: '-' }}
          </strong>
        </p>
      </div>
      @endif

      <div class="success-address">
        <span>Địa chỉ nhận hàng</span>
        <p class="mb-0">{{ $order->address }}</p>
      </div>

      <div class="table-wrapper">
        <table class="velora-table">
          <thead>
            <tr>
              <th>Sản phẩm</th>
              <th>Màu</th>
              <th>Size</th>
              <th>Đơn giá</th>
              <th>SL</th>
              <th>Thành tiền</th>
            </tr>
          </thead>

          <tbody>
            @foreach($order->details as $detail)
            <tr>
              <td>
                <strong>{{ $detail->product_name }}</strong>
                <br>
                <small>{{ $detail->sku }}</small>
              </td>
              <td>{{ $detail->color ?? '-' }}</td>
              <td>{{ $detail->size ?? '-' }}</td>
              <td>
                {{ number_format((float) $detail->unit_price, 0, ',', '.') }}đ
              </td>
              <td>{{ $detail->quantity }}</td>
              <td>
                <strong>
                  {{ number_format((float) $detail->subtotal, 0, ',', '.') }}đ
                </strong>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="success-total-box">
        <div>
          <span>Tạm tính</span>
          <strong>
            {{ number_format((float) $order->subtotal, 0, ',', '.') }}đ
          </strong>
        </div>

        @if((float) $order->discount_amount > 0)
        <div>
          <span>Giảm giá</span>
          <strong style="color:var(--velora-success);">
            -{{ number_format((float) $order->discount_amount, 0, ',', '.') }}đ
          </strong>
        </div>
        @endif

        <div>
          <span>Phí vận chuyển</span>
          <strong>
            {{ number_format((float) $order->shipping_fee, 0, ',', '.') }}đ
          </strong>
        </div>

        <div class="success-grand-total">
          <span>Tổng thanh toán</span>
          <strong>
            {{ number_format((float) $order->total, 0, ',', '.') }}đ
          </strong>
        </div>
      </div>

      @if($order->payment_method === 'cod')
      <div class="alert alert-success">
        Bạn sẽ thanh toán khi nhận hàng.
      </div>
      @elseif($paymentIsPaid)
      <div class="alert alert-success">
        Khoản thanh toán đã được hệ thống xác minh thành công.
      </div>
      @endif

      <div class="order-success-actions">
        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary">
          Xem chi tiết đơn hàng
        </a>

        <a href="{{ route('orders.index') }}" class="btn btn-outline">
          Đơn hàng của tôi
        </a>

        <a href="{{ route('products.index') }}" class="btn btn-outline">
          Tiếp tục mua sắm
        </a>
      </div>

    </div>
  </div>
</section>

@endsection