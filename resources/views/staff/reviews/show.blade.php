@extends('layouts.staff')


@section(
    'title',
    'Đánh giá #' . $review->id
)


@section(
    'page-title',
    'Chi tiết đánh giá'
)


@section('content')


{{-- =========================================================
    HEADER
========================================================= --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            CUSTOMER REVIEW
        </span>

        <h1>
            Đánh giá #{{ $review->id }}
        </h1>

        <p>
            Phản hồi của khách hàng
            về sản phẩm đã mua.
        </p>

    </div>


    <a
        href="{{ route(
            'staff.reviews.index'
        ) }}"
        class="staff-btn staff-btn-secondary"
    >
        ← Danh sách đánh giá
    </a>

</div>



{{-- =========================================================
    STATUS
========================================================= --}}

<div class="staff-review-detail-header">

    <div class="staff-review-detail-stars">

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


        <strong>
            {{ $review->rating }}/5
        </strong>

    </div>


    <div>

        @if($review->is_visible)

            <span class="staff-status staff-status-success">
                Đang hiển thị
            </span>

        @else

            <span class="staff-status staff-status-danger">
                Đang ẩn
            </span>

        @endif

    </div>

</div>



<div class="staff-review-detail-layout">


    {{-- =====================================================
        MAIN
    ====================================================== --}}

    <div class="staff-review-detail-main">


        {{-- REVIEW CONTENT --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Nội dung đánh giá
                </h2>

                <p>
                    Nội dung do Customer gửi.
                </p>

            </div>


            @if($review->comment)

                <div class="staff-review-full-comment">

                    <span>
                        “
                    </span>


                    <p>
                        {{ $review->comment }}
                    </p>

                </div>

            @else

                <div class="staff-review-no-comment">

                    Khách hàng chỉ đánh giá
                    {{ $review->rating }} sao
                    và không để lại nội dung.

                </div>

            @endif

        </section>



        {{-- CUSTOMER --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Khách hàng
                </h2>

            </div>


            <div class="staff-review-info-grid">

                <div>

                    <span>
                        Họ tên
                    </span>

                    <strong>

                        {{ $review
                            ->user
                            ?->name
                            ?? 'Không xác định' }}

                    </strong>

                </div>


                <div>

                    <span>
                        Email
                    </span>

                    <strong>

                        {{ $review
                            ->user
                            ?->email
                            ?? '—' }}

                    </strong>

                </div>

            </div>

        </section>



        {{-- PRODUCT --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Sản phẩm được đánh giá
                </h2>

            </div>


            <div class="staff-review-product-card">

                <div class="staff-review-product-icon">
                    V
                </div>


                <div>

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

            </div>

        </section>

    </div>



    {{-- =====================================================
        SIDEBAR
    ====================================================== --}}

    <aside class="staff-review-detail-sidebar">


        {{-- MODERATION --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Kiểm duyệt
                </h2>

            </div>


            @if($review->is_visible)

                <div class="staff-review-visibility-info visible">

                    <span></span>

                    <div>

                        <strong>
                            Đang hiển thị
                        </strong>

                        <small>
                            Customer đang nhìn thấy đánh giá này.
                        </small>

                    </div>

                </div>


                <form
                    action="{{ route(
                        'staff.reviews.toggle-visibility',
                        $review
                    ) }}"
                    method="POST"
                    onsubmit="
                        return confirm(
                            'Bạn có chắc muốn ẩn đánh giá này?'
                        );
                    "
                >

                    @csrf
                    @method('PATCH')


                    <button
                        type="submit"
                        class="staff-btn staff-btn-danger staff-product-full-button"
                    >
                        Ẩn đánh giá
                    </button>

                </form>

            @else

                <div class="staff-review-visibility-info hidden">

                    <span></span>

                    <div>

                        <strong>
                            Đang bị ẩn
                        </strong>

                        <small>
                            Đánh giá hiện không hiển thị cho Customer.
                        </small>

                    </div>

                </div>


                <form
                    action="{{ route(
                        'staff.reviews.toggle-visibility',
                        $review
                    ) }}"
                    method="POST"
                    onsubmit="
                        return confirm(
                            'Hiển thị lại đánh giá này?'
                        );
                    "
                >

                    @csrf
                    @method('PATCH')


                    <button
                        type="submit"
                        class="staff-btn staff-btn-primary staff-product-full-button"
                    >
                        Hiển thị lại
                    </button>

                </form>

            @endif

        </section>



        {{-- META --}}

        <section class="staff-form-card staff-review-meta">

            <span>
                Mã đánh giá
            </span>

            <strong>
                #{{ $review->id }}
            </strong>


            <span>
                Điểm đánh giá
            </span>

            <strong>
                {{ $review->rating }}/5 sao
            </strong>


            <span>
                Ngày đánh giá
            </span>

            <strong>

                {{ $review
                    ->created_at
                    ->format(
                        'd/m/Y H:i'
                    ) }}

            </strong>


            <span>
                Trạng thái
            </span>

            <strong>

                {{ $review->is_visible
                    ? 'Đang hiển thị'
                    : 'Đang ẩn' }}

            </strong>

        </section>



        {{-- RULE --}}

        <section class="staff-review-rule-box">

            <strong>
                Quyền của Staff
            </strong>

            <p>
                Staff chỉ có thể ẩn hoặc
                hiển thị đánh giá.
            </p>

            <p>
                Không được thay đổi số sao
                hay nội dung mà Customer đã gửi.
            </p>

        </section>

    </aside>

</div>

@endsection