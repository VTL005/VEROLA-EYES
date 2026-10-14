@extends('layouts.app')

@section('title', 'Không tìm thấy trang - VELORA Eyes')

@section('content')
<section class="velora-not-found">
    <div class="velora-not-found__orb" aria-hidden="true"></div>
    <div class="velora-not-found__content">
        <span>VELORA EYES · 404</span>
        <h1>Đường dẫn này<br>không còn rõ nét.</h1>
        <p>Trang bạn tìm có thể đã được di chuyển, đổi địa chỉ hoặc chưa từng tồn tại.</p>
        <div class="velora-not-found__actions">
            <a href="{{ route('home') }}" class="vl-btn vl-btn-primary">Về trang chủ</a>
            <a href="{{ route('products.index') }}" class="vl-btn vl-btn-outline">Khám phá sản phẩm</a>
        </div>
    </div>
</section>
@endsection
