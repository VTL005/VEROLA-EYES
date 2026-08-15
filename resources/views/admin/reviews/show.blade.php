@extends('layouts.admin')


@section(
    'title',
    'Đánh giá #' . $review->id
)


@section(
    'page-title',
    'Chi tiết đánh giá'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            REVIEW DETAIL
        </span>

        <h1>
            Đánh giá #{{ $review->id }}
        </h1>

        <p>
            Gửi lúc
            {{ $review
                ->created_at
                ->format(
                    'H:i - d/m/Y'
                ) }}
        </p>

    </div>


    <a
        href="{{ route(
            'admin.reviews.index'
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Danh sách
    </a>

</div>



{{-- =========================================================
    SUMMARY
========================================================= --}}

<div class="admin-review-detail-summary">

    <div>

        <span>
            Số sao
        </span>

        <strong>
            {{ $review->rating }}/5
        </strong>

    </div>


    <div>

        <span>
            Trạng thái
        </span>

        <strong>
            {{ $review->is_visible
                ? 'Đang hiển thị'
                : 'Đã ẩn' }}
        </strong>

    </div>


    <div>

        <span>
            Khách hàng
        </span>

        <strong>
            {{ $review->user?->name ?? '—' }}
        </strong>

    </div>


    <div>

        <span>
            Sản phẩm
        </span>

        <strong>
            {{ $review->product?->name ?? '—' }}
        </strong>

    </div>

</div>



<div class="admin-review-detail-layout">


    <div class="admin-review-detail-main">


        {{-- REVIEW --}}

        <section class="admin-review-content-card">

            <div class="admin-review-big-rating">

                <strong>
                    {{ $review->rating }}
                </strong>

                <span>
                    / 5
                </span>


                <div>

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

                </div>

            </div>


            <div class="admin-review-full-comment">

                <span>
                    Nội dung đánh giá
                </span>


                <p>

                    {{ $review->comment
                        ?: 'Khách hàng không để lại nhận xét.' }}

                </p>

            </div>

        </section>



        {{-- PRODUCT --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Sản phẩm</h2>
                </div>


                @if($review->product)

                    <a
                        href="{{ route(
                            'admin.products.show',
                            $review->product
                        ) }}"
                        class="admin-table-action"
                    >
                        Xem sản phẩm
                    </a>

                @endif

            </div>


            <div class="admin-review-product-detail">

                <div>
                    <i class="bi bi-eyeglasses"></i>
                </div>


                <span>

                    <strong>

                        {{ $review
                            ->product
                            ?->name
                            ?? 'Sản phẩm không còn tồn tại' }}

                    </strong>


                    <small>

                        SKU:
                        {{ $review
                            ->product
                            ?->sku
                            ?? '—' }}

                    </small>

                </span>

            </div>

        </section>

    </div>



    {{-- =====================================================
        SIDEBAR
    ====================================================== --}}

    <aside class="admin-review-detail-sidebar">


        {{-- CUSTOMER --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Khách hàng</h2>
                </div>

            </div>


            <div class="admin-review-person">

                <div>
                    <i class="bi bi-person"></i>
                </div>


                <span>

                    <strong>
                        {{ $review->user?->name ?? '—' }}
                    </strong>

                    <small>
                        {{ $review->user?->phone ?? '—' }}
                    </small>

                    <small>
                        {{ $review->user?->email ?? '—' }}
                    </small>

                </span>

            </div>

        </section>



        {{-- MODERATION --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Kiểm duyệt</h2>
                </div>

            </div>


            <div class="admin-review-moderation">

                @if($review->is_visible)

                    <div class="visible">

                        <i class="bi bi-eye"></i>

                        <span>

                            <strong>
                                Đang hiển thị
                            </strong>

                            <small>
                                Khách hàng có thể nhìn thấy đánh giá này.
                            </small>

                        </span>

                    </div>

                @else

                    <div class="hidden">

                        <i class="bi bi-eye-slash"></i>

                        <span>

                            <strong>
                                Đang bị ẩn
                            </strong>

                            <small>
                                Đánh giá không xuất hiện trên trang sản phẩm.
                            </small>

                        </span>

                    </div>

                @endif



                <form
                    action="{{ route(
                        'admin.reviews.toggle-visibility',
                        $review
                    ) }}"
                    method="POST"
                    onsubmit="
                        return confirm(
                            '{{ $review->is_visible
                                ? 'Bạn muốn ẩn đánh giá này khỏi website?'
                                : 'Bạn muốn hiển thị lại đánh giá này?' }}'
                        );
                    "
                >

                    @csrf
                    @method('PATCH')


                    @if($review->is_visible)

                        <button
                            type="submit"
                            class="admin-btn admin-btn-danger admin-btn-full"
                        >
                            <i class="bi bi-eye-slash"></i>

                            Ẩn đánh giá
                        </button>

                    @else

                        <button
                            type="submit"
                            class="admin-btn admin-btn-primary admin-btn-full"
                        >
                            <i class="bi bi-eye"></i>

                            Hiển thị lại
                        </button>

                    @endif

                </form>

            </div>

        </section>



        {{-- META --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Thông tin hệ thống</h2>
                </div>

            </div>


            <div class="admin-review-meta">

                <span>

                    Review ID

                    <strong>
                        #{{ $review->id }}
                    </strong>

                </span>


                <span>

                    Ngày gửi

                    <strong>
                        {{ $review
                            ->created_at
                            ->format(
                                'H:i d/m/Y'
                            ) }}
                    </strong>

                </span>


                <span>

                    Cập nhật cuối

                    <strong>
                        {{ $review
                            ->updated_at
                            ->format(
                                'H:i d/m/Y'
                            ) }}
                    </strong>

                </span>

            </div>

        </section>

    </aside>

</div>

@endsection