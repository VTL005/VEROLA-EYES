@extends('layouts.staff')


@section(
    'title',
    'Đánh giá - Staff'
)


@section(
    'page-title',
    'Đánh giá'
)


@section('content')


{{-- =========================================================
    HEADER
========================================================= --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            CUSTOMER REVIEWS
        </span>

        <h1>
            Quản lý đánh giá
        </h1>

        <p>
            Theo dõi phản hồi của khách hàng
            và quản lý trạng thái hiển thị.
        </p>

    </div>

</div>



{{-- =========================================================
    STATS
========================================================= --}}

<div class="staff-review-stats">

    <div class="staff-review-stat">

        <span>
            Tổng đánh giá
        </span>

        <strong>
            {{ $totalReviews }}
        </strong>

        <small>
            tất cả đánh giá
        </small>

    </div>


    <a
        href="{{ route(
            'staff.reviews.index',
            [
                'visibility' => 'visible',
            ]
        ) }}"
        class="staff-review-stat success"
    >

        <span>
            Đang hiển thị
        </span>

        <strong>
            {{ $visibleReviews }}
        </strong>

        <small>
            khách hàng đang nhìn thấy
        </small>

    </a>


    <a
        href="{{ route(
            'staff.reviews.index',
            [
                'visibility' => 'hidden',
            ]
        ) }}"
        class="staff-review-stat danger"
    >

        <span>
            Đang ẩn
        </span>

        <strong>
            {{ $hiddenReviews }}
        </strong>

        <small>
            đánh giá đã bị ẩn
        </small>

    </a>


    <a
        href="{{ route(
            'staff.reviews.index',
            [
                'rating' => 5,
            ]
        ) }}"
        class="staff-review-stat rating"
    >

        <span>
            Đánh giá 5 sao
        </span>

        <strong>
            {{ $fiveStarReviews }}
        </strong>

        <small>
            phản hồi tốt nhất
        </small>

    </a>

</div>



{{-- =========================================================
    FILTER
========================================================= --}}

<div class="staff-review-filter">

    <form
        action="{{ route(
            'staff.reviews.index'
        ) }}"
        method="GET"
        class="staff-review-filter-form"
    >

        <div>

            <label for="keyword">
                Tìm kiếm
            </label>

            <input
                type="text"
                id="keyword"
                name="keyword"
                value="{{ $keyword }}"
                class="staff-form-control"
                placeholder="Khách hàng, sản phẩm, SKU..."
            >

        </div>


        <div>

            <label for="rating">
                Số sao
            </label>

            <select
                id="rating"
                name="rating"
                class="staff-form-control"
            >

                <option value="">
                    Tất cả
                </option>


                @for($star = 5; $star >= 1; $star--)

                    <option
                        value="{{ $star }}"
                        {{
                            (string) $rating
                            === (string) $star
                                ? 'selected'
                                : ''
                        }}
                    >
                        {{ $star }} sao
                    </option>

                @endfor

            </select>

        </div>


        <div>

            <label for="visibility">
                Hiển thị
            </label>

            <select
                id="visibility"
                name="visibility"
                class="staff-form-control"
            >

                <option value="">
                    Tất cả
                </option>


                <option
                    value="visible"
                    {{
                        $visibility === 'visible'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đang hiển thị
                </option>


                <option
                    value="hidden"
                    {{
                        $visibility === 'hidden'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đang ẩn
                </option>

            </select>

        </div>


        <div class="staff-review-filter-actions">

            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Lọc
            </button>


            @if(
                $keyword !== ''
                || $rating
                || $visibility
            )

                <a
                    href="{{ route(
                        'staff.reviews.index'
                    ) }}"
                    class="staff-btn staff-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



{{-- =========================================================
    TABLE
========================================================= --}}

<div class="staff-table-card">

    <div class="staff-table-card-header">

        <div>

            <h2>
                Danh sách đánh giá
            </h2>

            <p>
                {{ $reviews->total() }}
                đánh giá
            </p>

        </div>

    </div>


    @if($reviews->isEmpty())

        <div class="staff-review-empty">

            <div>
                ★
            </div>

            <h3>
                Không tìm thấy đánh giá
            </h3>

            <p>
                Hãy thử thay đổi bộ lọc
                hoặc từ khóa tìm kiếm.
            </p>

        </div>

    @else

        <div class="staff-table-responsive">

            <table class="staff-table">

                <thead>

                    <tr>

                        <th>
                            Đánh giá
                        </th>

                        <th>
                            Khách hàng
                        </th>

                        <th>
                            Sản phẩm
                        </th>

                        <th>
                            Nội dung
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Ngày đánh giá
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($reviews as $review)

                        <tr>

                            {{-- RATING --}}

                            <td>

                                <div class="staff-review-rating">

                                    <div>

                                        @for(
                                            $star = 1;
                                            $star <= 5;
                                            $star++
                                        )

                                            <span
                                                class="{{
                                                    $star <= $review->rating
                                                        ? 'active'
                                                        : ''
                                                }}"
                                            >
                                                ★
                                            </span>

                                        @endfor

                                    </div>


                                    <small>
                                        {{ $review->rating }}/5
                                    </small>

                                </div>

                            </td>



                            {{-- CUSTOMER --}}

                            <td>

                                <div class="staff-review-user">

                                    <strong>

                                        {{ $review
                                            ->user
                                            ?->name
                                            ?? 'Không xác định' }}

                                    </strong>


                                    <span>

                                        {{ $review
                                            ->user
                                            ?->email
                                            ?? '—' }}

                                    </span>

                                </div>

                            </td>



                            {{-- PRODUCT --}}

                            <td>

                                <div class="staff-review-product">

                                    <strong>

                                        {{ $review
                                            ->product
                                            ?->name
                                            ?? 'Sản phẩm không tồn tại' }}

                                    </strong>


                                    <span>

                                        SKU:
                                        {{ $review
                                            ->product
                                            ?->sku
                                            ?? '—' }}

                                    </span>

                                </div>

                            </td>



                            {{-- COMMENT --}}

                            <td>

                                @if($review->comment)

                                    <p class="staff-review-comment-preview">

                                        {{ \Illuminate\Support\Str::limit(
                                            $review->comment,
                                            75
                                        ) }}

                                    </p>

                                @else

                                    <span class="staff-table-muted">
                                        Không có nội dung
                                    </span>

                                @endif

                            </td>



                            {{-- VISIBILITY --}}

                            <td>

                                @if($review->is_visible)

                                    <span class="staff-status staff-status-success">
                                        Đang hiển thị
                                    </span>

                                @else

                                    <span class="staff-status staff-status-danger">
                                        Đang ẩn
                                    </span>

                                @endif

                            </td>



                            {{-- DATE --}}

                            <td>

                                <div class="staff-review-date">

                                    <strong>

                                        {{ $review
                                            ->created_at
                                            ->format('d/m/Y') }}

                                    </strong>


                                    <span>

                                        {{ $review
                                            ->created_at
                                            ->format('H:i') }}

                                    </span>

                                </div>

                            </td>



                            {{-- ACTION --}}

                            <td>

                                <a
                                    href="{{ route(
                                        'staff.reviews.show',
                                        $review
                                    ) }}"
                                    class="staff-action-button"
                                >
                                    Xem chi tiết
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="staff-table-pagination">

            {{ $reviews->links() }}

        </div>

    @endif

</div>

@endsection