@extends('layouts.app')


@section('title', 'Sản phẩm - VELORA Eyes')


@section(
    'meta_description',
    'Khám phá các mẫu kính mắt tại VELORA Eyes và tìm sản phẩm phù hợp với phong cách của bạn.'
)


@section('content')


{{-- =========================================================
    PAGE HERO
========================================================= --}}

<section
    style="
        padding:56px 0 44px;
        background:
            linear-gradient(
                135deg,
                #f8fbff,
                #edf5fc
            );
        border-bottom:1px solid var(--velora-border);
    "
>

    <div class="velora-container">

        <span class="hero-kicker">
            VELORA COLLECTION
        </span>

        <h1 style="margin-bottom:12px;">
            Sản phẩm kính mắt
        </h1>

        <p
            class="text-muted mb-0"
            style="max-width:650px;font-size:1.05rem;"
        >
            Tìm chiếc kính phù hợp với nhu cầu thị lực,
            phong cách và cá tính của riêng bạn.
        </p>

    </div>

</section>



{{-- =========================================================
    PRODUCT LIST
========================================================= --}}

<section class="section">

    <div class="velora-container">

        <div class="product-page-layout">


            {{-- =================================================
                FILTER SIDEBAR
            ================================================== --}}

            <aside class="product-filter-panel">

                <div class="product-filter-header">

                    <div>

                        <span class="badge badge-blue">
                            Bộ lọc
                        </span>

                        <h2 class="product-filter-title">
                            Tìm sản phẩm
                        </h2>

                    </div>

                    @if(request()->hasAny([
                        'keyword',
                        'category_id',
                        'min_price',
                        'max_price',
                        'shape',
                        'material',
                        'gender',
                        'color',
                        'sort'
                    ]))

                        <a
                            href="{{ route('products.index') }}"
                            class="filter-reset"
                        >
                            Xóa lọc
                        </a>

                    @endif

                </div>


                <form
                    action="{{ route('products.index') }}"
                    method="GET"
                >

                    {{-- SEARCH --}}

                    <div class="form-group">

                        <label
                            for="keyword"
                            class="form-label"
                        >
                            Tìm kiếm
                        </label>

                        <input
                            type="text"
                            id="keyword"
                            name="keyword"
                            class="form-control"
                            value="{{ request('keyword') }}"
                            placeholder="Tên, SKU, kiểu dáng, màu..."
                        >

                    </div>


                    {{-- CATEGORY --}}

                    <div class="form-group">

                        <label
                            for="category_id"
                            class="form-label"
                        >
                            Danh mục
                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            class="form-control"
                        >

                            <option value="">
                                Tất cả danh mục
                            </option>

                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    {{ (string) request('category_id')
                                        === (string) $category->id
                                        ? 'selected'
                                        : '' }}
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- PRICE --}}

                    <div class="form-group">

                        <label class="form-label">
                            Khoảng giá
                        </label>

                        <div class="price-filter-grid">

                            <input
                                type="number"
                                name="min_price"
                                class="form-control"
                                min="0"
                                value="{{ request('min_price') }}"
                                placeholder="Giá từ"
                            >

                            <input
                                type="number"
                                name="max_price"
                                class="form-control"
                                min="0"
                                value="{{ request('max_price') }}"
                                placeholder="Giá đến"
                            >

                        </div>

                    </div>


                    {{-- SHAPE --}}

                    <div class="form-group">

                        <label
                            for="shape"
                            class="form-label"
                        >
                            Kiểu dáng
                        </label>

                        <select
                            id="shape"
                            name="shape"
                            class="form-control"
                        >

                            <option value="">
                                Tất cả kiểu dáng
                            </option>

                            @foreach($shapes as $shape)

                                <option
                                    value="{{ $shape }}"
                                    {{ request('shape') === $shape
                                        ? 'selected'
                                        : '' }}
                                >
                                    {{ ucfirst($shape) }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- MATERIAL --}}

                    <div class="form-group">

                        <label
                            for="material"
                            class="form-label"
                        >
                            Chất liệu
                        </label>

                        <select
                            id="material"
                            name="material"
                            class="form-control"
                        >

                            <option value="">
                                Tất cả chất liệu
                            </option>

                            @foreach($materials as $material)

                                <option
                                    value="{{ $material }}"
                                    {{ request('material') === $material
                                        ? 'selected'
                                        : '' }}
                                >
                                    {{ ucfirst($material) }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- GENDER --}}

                    <div class="form-group">

                        <label
                            for="gender"
                            class="form-label"
                        >
                            Đối tượng
                        </label>

                        <select
                            id="gender"
                            name="gender"
                            class="form-control"
                        >

                            <option value="">
                                Tất cả đối tượng
                            </option>

                            @foreach($genders as $gender)

                                <option
                                    value="{{ $gender }}"
                                    {{ request('gender') === $gender
                                        ? 'selected'
                                        : '' }}
                                >
                                    @switch($gender)

                                        @case('male')
                                            Nam
                                            @break

                                        @case('female')
                                            Nữ
                                            @break

                                        @case('unisex')
                                            Unisex
                                            @break

                                        @default
                                            {{ ucfirst($gender) }}

                                    @endswitch
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- COLOR --}}

                    <div class="form-group">

                        <label
                            for="color"
                            class="form-label"
                        >
                            Màu sắc
                        </label>

                        <input
                            type="text"
                            id="color"
                            name="color"
                            class="form-control"
                            value="{{ request('color') }}"
                            placeholder="Ví dụ: black, gold..."
                        >

                    </div>


                    {{-- SORT MOBILE / SIDEBAR --}}

                    <div class="form-group">

                        <label
                            for="sort"
                            class="form-label"
                        >
                            Sắp xếp
                        </label>

                        <select
                            id="sort"
                            name="sort"
                            class="form-control"
                        >

                            <option
                                value="newest"
                                {{ request('sort', 'newest') === 'newest'
                                    ? 'selected'
                                    : '' }}
                            >
                                Mới nhất
                            </option>

                            <option
                                value="price_asc"
                                {{ request('sort') === 'price_asc'
                                    ? 'selected'
                                    : '' }}
                            >
                                Giá thấp đến cao
                            </option>

                            <option
                                value="price_desc"
                                {{ request('sort') === 'price_desc'
                                    ? 'selected'
                                    : '' }}
                            >
                                Giá cao đến thấp
                            </option>

                        </select>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary"
                        style="width:100%;"
                    >
                        Áp dụng bộ lọc
                    </button>

                </form>

            </aside>



            {{-- =================================================
                PRODUCTS
            ================================================== --}}

            <div class="product-results">


                {{-- TOOLBAR --}}

                <div class="product-toolbar">

                    <div>

                        <span class="text-muted">
                            Tìm thấy
                        </span>

                        <strong class="product-result-count">
                            {{ $products->total() }}
                        </strong>

                        <span class="text-muted">
                            sản phẩm
                        </span>

                    </div>


                    @auth

                        @if(auth()->user()->isCustomer())

                            <div class="product-toolbar-actions">

                                <a
                                    href="{{ route('wishlist.index') }}"
                                    class="btn btn-outline btn-sm"
                                >
                                    ♡ Yêu thích
                                </a>

                                <a
                                    href="{{ route('cart.index') }}"
                                    class="btn btn-outline btn-sm"
                                >
                                    Giỏ hàng
                                </a>

                            </div>

                        @endif

                    @endauth

                </div>



                {{-- ACTIVE FILTERS --}}

                @if(
                    request('keyword')
                    || request('category_id')
                    || request('min_price')
                    || request('max_price')
                    || request('shape')
                    || request('material')
                    || request('gender')
                    || request('color')
                )

                    <div class="active-filters">

                        <span class="text-muted">
                            Đang lọc:
                        </span>

                        @if(request('keyword'))

                            <span class="badge badge-blue">
                                "{{ request('keyword') }}"
                            </span>

                        @endif


                        @if(request('category_id'))

                            @php
                                $selectedCategory =
                                    $categories->firstWhere(
                                        'id',
                                        (int) request('category_id')
                                    );
                            @endphp

                            @if($selectedCategory)

                                <span class="badge">
                                    {{ $selectedCategory->name }}
                                </span>

                            @endif

                        @endif


                        @if(request('shape'))

                            <span class="badge">
                                {{ request('shape') }}
                            </span>

                        @endif


                        @if(request('material'))

                            <span class="badge">
                                {{ request('material') }}
                            </span>

                        @endif


                        @if(request('gender'))

                            <span class="badge">
                                {{ request('gender') }}
                            </span>

                        @endif


                        @if(request('color'))

                            <span class="badge">
                                Màu: {{ request('color') }}
                            </span>

                        @endif


                        @if(
                            request('min_price')
                            || request('max_price')
                        )

                            <span class="badge">

                                Giá:

                                @if(request('min_price'))

                                    {{ number_format(
                                        (float) request('min_price'),
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                @else

                                    0đ

                                @endif

                                –

                                @if(request('max_price'))

                                    {{ number_format(
                                        (float) request('max_price'),
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                @else

                                    ∞

                                @endif

                            </span>

                        @endif

                    </div>

                @endif



                {{-- PRODUCT GRID --}}

                @if($products->count() > 0)

                    <div class="product-list-grid">

                        @foreach($products as $product)

                            @include(
                                'components.product-card',
                                [
                                    'product' => $product
                                ]
                            )

                        @endforeach

                    </div>


                    @if($products->hasPages())

                        <div class="product-pagination">

                            {{ $products->links() }}

                        </div>

                    @endif

                @else

                    <div class="empty-state">

                        <h3>
                            Không tìm thấy sản phẩm
                        </h3>

                        <p>
                            Hãy thử thay đổi từ khóa
                            hoặc điều kiện lọc.
                        </p>

                        <a
                            href="{{ route('products.index') }}"
                            class="btn btn-primary"
                        >
                            Xóa toàn bộ bộ lọc
                        </a>

                    </div>

                @endif

            </div>

        </div>

    </div>

</section>


@endsection