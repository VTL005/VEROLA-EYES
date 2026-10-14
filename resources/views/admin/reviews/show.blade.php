@extends('layouts.admin')

@section('title', 'Đánh giá #' . $review->id)
@section('page-title', 'Chi tiết đánh giá')

@section('content')
<div class="admin-page-header">
    <div>
        <span class="admin-page-kicker">REVIEW CONVERSATION</span>
        <h1>Đánh giá #{{ $review->id }}</h1>
        <p>Gửi lúc {{ $review->created_at->format('H:i - d/m/Y') }}</p>
    </div>
    <a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-secondary"><i class="bi bi-arrow-left"></i> Danh sách</a>
</div>

<div class="admin-review-detail-summary">
    <div><span>Số sao</span><strong>{{ $review->rating }}/5</strong></div>
    <div><span>Phản hồi VELORA</span><strong>{{ $review->adminReply ? 'Đã phản hồi' : 'Chờ phản hồi' }}</strong></div>
    <div><span>Khách hàng</span><strong>{{ $review->user?->name ?? '—' }}</strong></div>
    <div><span>Sản phẩm</span><strong>{{ $review->product?->name ?? '—' }}</strong></div>
</div>

<div class="admin-review-detail-layout">
    <div class="admin-review-detail-main">
        <section class="admin-review-content-card">
            <div class="admin-review-big-rating">
                <strong>{{ $review->rating }}</strong><span>/ 5</span>
                <div>@for($star = 1; $star <= 5; $star++)<i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor</div>
            </div>
            <div class="admin-review-full-comment">
                <span>Nội dung đánh giá</span>
                <p>{{ $review->comment ?: 'Khách hàng không để lại nhận xét.' }}</p>
            </div>
        </section>

        <section class="admin-panel admin-review-reply-panel">
            <div class="admin-panel-header">
                <div><span class="admin-page-kicker">VELORA OFFICIAL</span><h2>Phản hồi đánh giá</h2></div>
                @if($review->adminReply)<span class="admin-status success"><i class="bi bi-patch-check"></i> Đã công khai</span>@endif
            </div>

            @if($review->adminReply)
                <div class="admin-review-official-reply" @if(session('review_reply_updated')) data-velora-status-highlight @endif>
                    <div class="admin-review-official-avatar"><i class="bi bi-patch-check-fill"></i></div>
                    <div>
                        <div class="admin-review-official-head"><strong>VELORA Eyes</strong><span>Phản hồi bởi {{ $review->adminReply->admin?->name ?? 'VELORA Admin' }} · {{ $review->adminReply->updated_at->format('H:i d/m/Y') }}</span></div>
                        <p>{{ $review->adminReply->content }}</p>
                    </div>
                </div>
            @else
                <div class="admin-review-reply-empty"><i class="bi bi-chat-dots"></i><span>Khách hàng chưa nhận được phản hồi từ VELORA.</span></div>
            @endif

            <form action="{{ route('admin.reviews.reply', $review) }}" method="POST" class="admin-review-reply-form">
                @csrf
                <label for="reviewReplyContent">{{ $review->adminReply ? 'Cập nhật phản hồi' : 'Viết phản hồi chính thức' }}</label>
                <textarea id="reviewReplyContent" name="content" class="admin-form-control admin-review-reply-input" rows="3" maxlength="1000" placeholder="Cảm ơn khách hàng, chia sẻ hướng xử lý hoặc hỗ trợ thêm...">{{ old('content', $review->adminReply?->content) }}</textarea>
                @error('content')<p class="admin-review-reply-error">{{ $message }}</p>@enderror
                <div class="admin-review-reply-actions">
                    <small><i class="bi bi-globe2"></i> Phản hồi sẽ hiển thị công khai dưới đánh giá của khách.</small>
                    <button type="submit" class="admin-btn admin-btn-primary"><i class="bi bi-send"></i> {{ $review->adminReply ? 'Cập nhật phản hồi' : 'Gửi phản hồi' }}</button>
                </div>
            </form>
        </section>

        <section class="admin-panel">
            <div class="admin-panel-header"><div><h2>Sản phẩm</h2></div>@if($review->product)<a href="{{ route('admin.products.show', $review->product) }}" class="admin-table-action">Xem sản phẩm</a>@endif</div>
            <div class="admin-review-product-detail"><div><i class="bi bi-eyeglasses"></i></div><span><strong>{{ $review->product?->name ?? 'Sản phẩm không còn tồn tại' }}</strong><small>SKU: {{ $review->product?->sku ?? '—' }}</small></span></div>
        </section>
    </div>

    <aside class="admin-review-detail-sidebar">
        <section class="admin-panel">
            <div class="admin-panel-header"><div><h2>Khách hàng</h2></div></div>
            <div class="admin-review-person"><div><i class="bi bi-person"></i></div><span><strong>{{ $review->user?->name ?? '—' }}</strong><small>{{ $review->user?->phone ?? '—' }}</small><small>{{ $review->user?->email ?? '—' }}</small></span></div>
        </section>
        <section class="admin-panel">
            <div class="admin-panel-header"><div><h2>Thông tin hệ thống</h2></div></div>
            <div class="admin-review-meta"><span>Review ID<strong>#{{ $review->id }}</strong></span><span>Ngày gửi<strong>{{ $review->created_at->format('H:i d/m/Y') }}</strong></span><span>Cập nhật cuối<strong>{{ $review->updated_at->format('H:i d/m/Y') }}</strong></span></div>
        </section>
    </aside>
</div>
@endsection
