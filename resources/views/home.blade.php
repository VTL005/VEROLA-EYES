@extends('layouts.app')


@section(
    'title',
    'VELORA Eyes - Kính mắt thời trang và chăm sóc thị lực'
)


@section(
    'meta_description',
    'Khám phá kính mắt thời trang, đặt lịch đo mắt và các dịch vụ chăm sóc thị lực tại VELORA Eyes.'
)


@section('content')


{{-- =========================================================
    HERO - VIDEO BLEND
========================================================= --}}

<section class="velora-home-hero">

    {{-- VIDEO BACKGROUND RIGHT --}}
    <div
        class="velora-hero-video-area"
        aria-hidden="true"
    >

        <video
            class="velora-hero-video"
            autoplay
            muted
            loop
            playsinline
            preload="metadata"
        >
            <source
                src="{{ asset('videos/velora-hero.mp4') }}"
                type="video/mp4"
            >
        </video>


        {{-- lớp hòa video vào background --}}
        <div class="velora-hero-video-blend"></div>

    </div>


    {{-- glow nền --}}
    <div class="velora-hero-glow"></div>


    <div class="container velora-hero-container">

        <div class="velora-hero-content">

            <span class="velora-hero-badge">
                VELORA EYES
            </span>


            <h1>
                Nhìn rõ hơn.
                <br>
                Sống phong cách hơn.
            </h1>


            <p>
                Khám phá những mẫu kính phù hợp
                với khuôn mặt, phong cách và nhu cầu
                thị lực của riêng bạn.
            </p>


            <div class="velora-hero-actions">

                <a
                    href="{{ route('products.index') }}"
                    class="velora-hero-btn velora-hero-btn-primary"
                >
                    Khám phá sản phẩm
                </a>


                <a
                    href="{{ route('appointments.create') }}"
                    class="velora-hero-btn velora-hero-btn-light"
                >
                    Đặt lịch đo mắt
                </a>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    ĐIỂM NỔI BẬT
========================================================= --}}

<section
    class="section"
    style="padding-top:34px;padding-bottom:34px;"
>

    <div class="velora-container">

        <div class="grid grid-3">

            <div class="card">

                <h3>
                    Kính mắt chọn lọc
                </h3>

                <p class="text-muted mb-0">

                    Nhiều kiểu dáng, màu sắc và chất liệu
                    phù hợp với nhiều phong cách khác nhau.

                </p>

            </div>


            <div class="card">

                <h3>
                    Đo mắt chuyên nghiệp
                </h3>

                <p class="text-muted mb-0">

                    Chủ động đặt lịch đo mắt,
                    lưu kết quả và theo dõi lịch sử thị lực.

                </p>

            </div>


            <div class="card">

                <h3>
                    Bảo hành điện tử
                </h3>

                <p class="text-muted mb-0">

                    Tra cứu bảo hành thuận tiện
                    bằng mã bảo hành của sản phẩm.

                </p>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    DANH MỤC
========================================================= --}}

<section class="section section-white">

    <div class="velora-container">

        <div class="section-heading">

            <span class="hero-kicker">
                DANH MỤC
            </span>

            <h2>
                Tìm kính theo nhu cầu
            </h2>

            <p>

                Khám phá các dòng sản phẩm
                đang có tại VELORA Eyes.

            </p>

        </div>


        @if($categories->count() > 0)

            <div class="grid grid-3">

                @foreach($categories as $category)

                    <article
                        class="card card-hover"
                        style="
                            padding:0;
                            overflow:hidden;
                        "
                    >

                        <a
                            href="{{ route(
                                'categories.show',
                                $category
                            ) }}"
                            style="
                                height:190px;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                background:#f8fafc;
                            "
                        >

                            @if($category->image)

                                <img
                                    src="{{ asset(
                                        $category->image
                                    ) }}"
                                    alt="{{ $category->name }}"
                                    style="
                                        width:100%;
                                        height:100%;
                                        object-fit:contain;
                                        padding:20px;
                                    "
                                >

                            @else

                                <div
                                    style="
                                        font-size:56px;
                                        color:#cbd5e1;
                                    "
                                >
                                    ◯
                                </div>

                            @endif

                        </a>


                        <div style="padding:22px;">

                            <h3>
                                {{ $category->name }}
                            </h3>


                            @if($category->description)

                                <p class="text-muted">

                                    {{ \Illuminate\Support\Str::limit(
                                        $category->description,
                                        100
                                    ) }}

                                </p>

                            @endif


                            <a
                                href="{{ route(
                                    'categories.show',
                                    $category
                                ) }}"
                                class="btn btn-outline btn-sm"
                            >
                                Xem danh mục
                            </a>

                        </div>

                    </article>

                @endforeach

            </div>

        @else

            <div class="empty-state">

                Hiện chưa có danh mục sản phẩm.

            </div>

        @endif

    </div>

</section>



{{-- =========================================================
    SẢN PHẨM MỚI
========================================================= --}}

<section class="section">

    <div class="velora-container">

        <div class="section-heading-row">

            <div>

                <span class="hero-kicker">
                    NEW ARRIVALS
                </span>

                <h2 class="mb-1">
                    Sản phẩm mới
                </h2>

                <p class="text-muted mb-0">

                    Những thiết kế mới
                    vừa được cập nhật tại VELORA.

                </p>

            </div>


            <a
                href="{{ route('products.index') }}"
                class="btn btn-outline"
            >
                Xem tất cả
            </a>

        </div>


        @if($newProducts->count() > 0)

            <div class="grid grid-4">

                @foreach($newProducts as $product)

                    @include(
                        'components.product-card',
                        [
                            'product' => $product
                        ]
                    )

                @endforeach

            </div>

        @else

            <div class="empty-state">

                Hiện chưa có sản phẩm mới.

            </div>

        @endif

    </div>

</section>



{{-- =========================================================
    KHUYẾN MÃI
========================================================= --}}

@if($saleProducts->count() > 0)

    <section class="section section-white">

        <div class="velora-container">

            <div class="section-heading-row">

                <div>

                    <span class="hero-kicker">
                        SPECIAL OFFER
                    </span>

                    <h2 class="mb-1">
                        Sản phẩm đang ưu đãi
                    </h2>

                    <p class="text-muted mb-0">

                        Cơ hội sở hữu những mẫu kính
                        yêu thích với mức giá tốt hơn.

                    </p>

                </div>


                <a
                    href="{{ route('products.index') }}"
                    class="btn btn-outline"
                >
                    Khám phá thêm
                </a>

            </div>


            <div class="grid grid-4">

                @foreach($saleProducts as $product)

                    @include(
                        'components.product-card',
                        [
                            'product' => $product
                        ]
                    )

                @endforeach

            </div>

        </div>

    </section>

@endif



{{-- =========================================================
    ĐẶT LỊCH ĐO MẮT
========================================================= --}}

<section class="section">

    <div class="velora-container">

        <div
            class="card"
            style="
                padding:clamp(30px,5vw,60px);
                background:
                    linear-gradient(
                        135deg,
                        #0f2747,
                        #245789
                    );
                color:white;
                border:none;
            "
        >

            <div
                style="
                    max-width:700px;
                "
            >

                <span
                    class="badge"
                    style="
                        background:rgba(255,255,255,.15);
                        color:white;
                        margin-bottom:16px;
                    "
                >
                    VELORA VISION CARE
                </span>


                <h2
                    style="
                        color:white;
                        margin-bottom:14px;
                    "
                >
                    Đã bao lâu bạn chưa kiểm tra thị lực?
                </h2>


                <p
                    style="
                        color:#dbeafe;
                        font-size:1.05rem;
                    "
                >

                    Đặt lịch đo mắt tại VELORA để
                    kiểm tra tình trạng thị lực và
                    nhận tư vấn kính phù hợp.

                </p>


                @if(Route::has('appointments.create'))

                    <a
                        href="{{ route('appointments.create') }}"
                        class="btn"
                        style="
                            background:white;
                            color:#0f2747;
                            margin-top:10px;
                        "
                    >
                        Đặt lịch ngay
                    </a>

                @elseif(Route::has('appointments.index'))

                    @auth

                        @if(auth()->user()->isCustomer())

                            <a
                                href="{{ route('appointments.index') }}"
                                class="btn"
                                style="
                                    background:white;
                                    color:#0f2747;
                                    margin-top:10px;
                                "
                            >
                                Quản lý lịch hẹn
                            </a>

                        @endif

                    @endauth

                @endif

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    TRẢI NGHIỆM VELORA
========================================================= --}}

<section class="section section-white">

    <div class="velora-container">

        <div class="section-heading text-center">

            <span class="hero-kicker">
                WHY VELORA
            </span>

            <h2>
                Trải nghiệm mua kính dễ dàng hơn
            </h2>

        </div>


        <div class="grid grid-4">

            <div class="card card-hover">

                <span class="badge badge-blue mb-2">
                    01
                </span>

                <h3>
                    Tìm kính phù hợp
                </h3>

                <p class="text-muted mb-0">

                    Lọc theo kiểu dáng,
                    giới tính, chất liệu,
                    mức giá và phong cách.

                </p>

            </div>


            <div class="card card-hover">

                <span class="badge badge-blue mb-2">
                    02
                </span>

                <h3>
                    Mua hàng trực tuyến
                </h3>

                <p class="text-muted mb-0">

                    Giỏ hàng, voucher,
                    checkout và nhiều phương thức
                    thanh toán thuận tiện.

                </p>

            </div>


            <div class="card card-hover">

                <span class="badge badge-blue mb-2">
                    03
                </span>

                <h3>
                    Theo dõi đơn hàng
                </h3>

                <p class="text-muted mb-0">

                    Theo dõi trạng thái đơn
                    từ lúc đặt hàng tới khi hoàn tất.

                </p>

            </div>


            <div class="card card-hover">

                <span class="badge badge-blue mb-2">
                    04
                </span>

                <h3>
                    Chăm sóc sau mua
                </h3>

                <p class="text-muted mb-0">

                    Bảo hành điện tử,
                    lịch sử đo mắt
                    và hỗ trợ khách hàng lâu dài.

                </p>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    WARRANTY CTA
========================================================= --}}

@if(Route::has('warranties.lookup-form'))

    <section class="section">

        <div class="velora-container">

            <div
                class="card"
                style="
                    display:flex;
                    align-items:center;
                    justify-content:space-between;
                    gap:30px;
                    flex-wrap:wrap;
                "
            >

                <div>

                    <span class="badge badge-success mb-2">
                        Bảo hành điện tử
                    </span>

                    <h2 class="mb-1">
                        Bạn đã có mã bảo hành?
                    </h2>

                    <p class="text-muted mb-0">

                        Tra cứu nhanh tình trạng
                        và thời hạn bảo hành sản phẩm VELORA.

                    </p>

                </div>


                <a
                    href="{{ route(
                        'warranties.lookup-form'
                    ) }}"
                    class="btn btn-primary"
                >
                    Tra cứu bảo hành
                </a>

            </div>

        </div>

    </section>

@endif


@endsection