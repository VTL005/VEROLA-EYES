@extends('layouts.admin')


@section(
    'title',
    'Đánh giá - VELORA Eyes'
)


@section(
    'page-title',
    'Đánh giá'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            REVIEW MANAGEMENT
        </span>

        <h1>
            Quản lý đánh giá
        </h1>

        <p>
            Theo dõi phản hồi của khách hàng
            và kiểm soát nội dung hiển thị.
        </p>

    </div>

</div>



{{-- =========================================================
    STATS
========================================================= --}}

<div class="admin-review-stats">

    <div class="admin-review-stat">

        <div class="all">
            <i class="bi bi-chat-square-text"></i>
        </div>

        <span>

            <small>
                Tổng đánh giá
            </small>

            <strong>
                {{ $totalReviews }}
            </strong>

            <em>

                @if($averageRating !== null)
                    Trung bình {{ $averageRating }}/5
                @else
                    Chưa có dữ liệu
                @endif

            </em>

        </span>

    </div>



    <a
        href="{{ route(
            'admin.reviews.index',
            [
                'visibility' =>
                    'visible',
            ]
        ) }}"
        class="admin-review-stat"
    >

        <div class="visible">
            <i class="bi bi-eye"></i>
        </div>

        <span>

            <small>
                Đang hiển thị
            </small>

            <strong>
                {{ $visibleReviews }}
            </strong>

        </span>

    </a>



    <a
        href="{{ route(
            'admin.reviews.index',
            [
                'visibility' =>
                    'hidden',
            ]
        ) }}"
        class="admin-review-stat"
    >

        <div class="hidden">
            <i class="bi bi-eye-slash"></i>
        </div>

        <span>

            <small>
                Đã ẩn
            </small>

            <strong>
                {{ $hiddenReviews }}
            </strong>

        </span>

    </a>



    <a
        href="{{ route(
            'admin.reviews.index',
            [
                'rating' => 5,
            ]
        ) }}"
        class="admin-review-stat"
    >

        <div class="five">
            <i class="bi bi-star-fill"></i>
        </div>

        <span>

            <small>
                Đánh giá 5 sao
            </small>

            <strong>
                {{ $fiveStarReviews }}
            </strong>

        </span>

    </a>

</div>



{{-- =========================================================
    FILTER
========================================================= --}}

<div class="admin-review-filter">

    <form
        action="{{ route(
            'admin.reviews.index'
        ) }}"
        method="GET"
        class="admin-review-filter-form"
    >

        <div>

            <label for="keyword">
                Tìm kiếm
            </label>


            <div class="admin-input-icon">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    class="admin-form-control"
                    placeholder="Khách hàng, sản phẩm hoặc nội dung..."
                >

            </div>

        </div>



        <div>

            <label for="rating">
                Số sao
            </label>


            <select
                id="rating"
                name="rating"
                class="admin-form-control"
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
                class="admin-form-control"
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
                    Đã ẩn
                </option>

            </select>

        </div>



        <div class="admin-review-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-funnel"></i>

                Lọc
            </button>


            @if(
                $keyword !== ''
                || $rating
                || $visibility
            )

                <a
                    href="{{ route(
                        'admin.reviews.index'
                    ) }}"
                    class="admin-btn admin-btn-secondary"
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

<div class="admin-panel">

    <div class="admin-panel-header">

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

        <div class="admin-review-empty">

            <i class="bi bi-chat-square"></i>

            <h3>
                Không tìm thấy đánh giá
            </h3>

            <p>
                Hãy thử thay đổi bộ lọc.
            </p>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>
                            Khách hàng
                        </th>

                        <th>
                            Sản phẩm
                        </th>

                        <th>
                            Đánh giá
                        </th>

                        <th>
                            Nội dung
                        </th>

                        <th>
                            Hiển thị
                        </th>

                        <th>
                            Ngày gửi
                        </th>

                        <th></th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $reviews
                        as $review
                    )

                        <tr>

                            <td>

                                <div class="admin-review-customer">

                                    <strong>
                                        {{ $review
                                            ->user
                                            ?->name
                                            ?? '—' }}
                                    </strong>

                                    <span>
                                        {{ $review
                                            ->user
                                            ?->phone
                                            ?? '—' }}
                                    </span>

                                    <small>
                                        {{ $review
                                            ->user
                                            ?->email
                                            ?? '—' }}
                                    </small>

                                </div>

                            </td>



                            <td>

                                <div class="admin-review-product">

                                    <strong>

                                        {{ $review
                                            ->product
                                            ?->name
                                            ?? 'Sản phẩm không còn tồn tại' }}

                                    </strong>


                                    <span>

                                        {{ $review
                                            ->product
                                            ?->sku
                                            ?? '—' }}

                                    </span>

                                </div>

                            </td>



                            <td>

                                <div class="admin-review-stars">

                                    @for(
                                        $star = 1;
                                        $star <= 5;
                                        $star++
                                    )

                                        <i
                                            class="bi {{
                                                $star <= $review->rating
                                                    ? 'bi-star-fill'
                                                    : 'bi-star'
                                            }}"
                                        ></i>

                                    @endfor


                                    <strong>
                                        {{ $review->rating }}/5
                                    </strong>

                                </div>

                            </td>



                            <td>

                                <div class="admin-review-comment">

                                    {{ \Illuminate\Support\Str::limit(
                                        $review->comment
                                            ?: 'Không có nhận xét.',
                                        90
                                    ) }}

                                </div>

                            </td>



                            <td>

                                @if($review->is_visible)

                                    <span class="admin-status success">

                                        <i class="bi bi-eye"></i>

                                        Đang hiển thị

                                    </span>

                                @else

                                    <span class="admin-status muted">

                                        <i class="bi bi-eye-slash"></i>

                                        Đã ẩn

                                    </span>

                                @endif

                            </td>



                            <td>

                                <div class="admin-table-primary">

                                    <strong>

                                        {{ $review
                                            ->created_at
                                            ->format(
                                                'd/m/Y'
                                            ) }}

                                    </strong>

                                    <span>

                                        {{ $review
                                            ->created_at
                                            ->format(
                                                'H:i'
                                            ) }}

                                    </span>

                                </div>

                            </td>



                            <td>

                                <a
                                    href="{{ route(
                                        'admin.reviews.show',
                                        $review
                                    ) }}"
                                    class="admin-order-view"
                                    title="Xem đánh giá"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="admin-pagination">

            {{ $reviews->links() }}

        </div>

    @endif

</div>

@endsection