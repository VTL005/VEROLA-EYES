@extends('layouts.admin')

@section('title', 'Đánh giá - VELORA Eyes')
@section('page-title', 'Đánh giá')

@section('content')
<div class="admin-page-header">
    <div>
        <span class="admin-page-kicker">CUSTOMER FEEDBACK</span>
        <h1>Quản lý đánh giá</h1>
        <p>Đọc phản hồi của khách hàng và trả lời trực tiếp dưới danh nghĩa VELORA.</p>
    </div>
</div>

<div class="admin-review-stats">
    <div class="admin-review-stat">
        <div class="all"><i class="bi bi-chat-square-text"></i></div>
        <span><small>Tổng đánh giá</small><strong>{{ $totalReviews }}</strong><em>{{ $averageRating !== null ? 'Trung bình ' . $averageRating . '/5' : 'Chưa có dữ liệu' }}</em></span>
    </div>
    <div class="admin-review-stat">
        <div class="waiting"><i class="bi bi-chat-dots"></i></div>
        <span><small>Chờ phản hồi</small><strong>{{ $waitingReplies }}</strong><em>Cần VELORA quan tâm</em></span>
    </div>
    <div class="admin-review-stat">
        <div class="replied"><i class="bi bi-chat-check"></i></div>
        <span><small>Đã phản hồi</small><strong>{{ $repliedReviews }}</strong><em>Phản hồi chính thức</em></span>
    </div>
    <a href="{{ route('admin.reviews.index', ['rating' => 5]) }}" class="admin-review-stat">
        <div class="five"><i class="bi bi-star-fill"></i></div>
        <span><small>Đánh giá 5 sao</small><strong>{{ $fiveStarReviews }}</strong><em>Khách hàng hài lòng</em></span>
    </a>
</div>

<div class="admin-review-filter">
    <form action="{{ route('admin.reviews.index') }}" method="GET" class="admin-review-filter-form admin-review-filter-form--reply">
        <div>
            <label for="keyword">Tìm kiếm</label>
            <div class="admin-input-icon">
                <i class="bi bi-search"></i>
                <input type="text" id="keyword" name="keyword" value="{{ $keyword }}" class="admin-form-control" placeholder="Khách hàng, sản phẩm hoặc nội dung...">
            </div>
        </div>
        <div>
            <label for="rating">Số sao</label>
            <select id="rating" name="rating" class="admin-form-control">
                <option value="">Tất cả</option>
                @for($star = 5; $star >= 1; $star--)
                    <option value="{{ $star }}" @selected((string) $rating === (string) $star)>{{ $star }} sao</option>
                @endfor
            </select>
        </div>
        <div class="admin-review-filter-actions">
            <button type="submit" class="admin-btn admin-btn-primary"><i class="bi bi-funnel"></i> Lọc</button>
            @if($keyword !== '' || $rating)
                <a href="{{ route('admin.reviews.index') }}" class="admin-btn admin-btn-secondary">Đặt lại</a>
            @endif
        </div>
    </form>
</div>

<div class="admin-panel">
    <div class="admin-panel-header">
        <div><h2>Danh sách đánh giá</h2><p>{{ $reviews->total() }} đánh giá</p></div>
    </div>

    @if($reviews->isEmpty())
        <div class="admin-review-empty">
            <i class="bi bi-chat-square"></i><h3>Không tìm thấy đánh giá</h3><p>Hãy thử thay đổi bộ lọc.</p>
        </div>
    @else
        <div class="admin-table-responsive">
            <table class="admin-table">
                <thead><tr><th>Khách hàng</th><th>Sản phẩm</th><th>Đánh giá</th><th>Nội dung</th><th>Phản hồi</th><th>Ngày gửi</th><th></th></tr></thead>
                <tbody>
                    @foreach($reviews as $review)
                        <tr>
                            <td><div class="admin-review-customer"><strong>{{ $review->user?->name ?? '—' }}</strong><span>{{ $review->user?->phone ?? '—' }}</span><small>{{ $review->user?->email ?? '—' }}</small></div></td>
                            <td><div class="admin-review-product"><strong>{{ $review->product?->name ?? 'Sản phẩm không còn tồn tại' }}</strong><span>{{ $review->product?->sku ?? '—' }}</span></div></td>
                            <td><div class="admin-review-stars">@for($star = 1; $star <= 5; $star++)<i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor<strong>{{ $review->rating }}/5</strong></div></td>
                            <td><div class="admin-review-comment">{{ \Illuminate\Support\Str::limit($review->comment ?: 'Không có nhận xét.', 90) }}</div></td>
                            <td>
                                @if($review->adminReply)
                                    <span class="admin-status success"><i class="bi bi-chat-check"></i> Đã phản hồi</span>
                                @else
                                    <span class="admin-status muted"><i class="bi bi-chat-dots"></i> Chờ phản hồi</span>
                                @endif
                            </td>
                            <td><div class="admin-table-primary"><strong>{{ $review->created_at->format('d/m/Y') }}</strong><span>{{ $review->created_at->format('H:i') }}</span></div></td>
                            <td><a href="{{ route('admin.reviews.show', $review) }}" class="admin-order-view" title="Xem và phản hồi đánh giá"><i class="bi bi-reply"></i></a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="admin-pagination">{{ $reviews->links() }}</div>
    @endif
</div>
@endsection
