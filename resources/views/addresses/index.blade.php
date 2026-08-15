@extends('layouts.app')


@section('title', 'Địa chỉ của tôi - VELORA Eyes')


@section('content')

<section
    style="
        padding:54px 0 42px;
        background:linear-gradient(135deg,#f8fbff,#edf5fc);
        border-bottom:1px solid var(--velora-border);
    "
>

    <div class="velora-container">

        <span class="hero-kicker">
            MY ADDRESSES
        </span>

        <h1 style="margin-bottom:10px;">
            Địa chỉ của tôi
        </h1>

        <p class="text-muted mb-0">
            Quản lý các địa chỉ nhận hàng
            được sử dụng khi thanh toán.
        </p>

    </div>

</section>


<section class="section">

    <div class="velora-container">


        <div class="address-page-heading">

            <div>

                <strong>
                    {{ $addresses->count() }}
                </strong>

                <span class="text-muted">
                    địa chỉ đã lưu
                </span>

            </div>


            <a
                href="{{ route('addresses.create') }}"
                class="btn btn-primary"
            >
                + Thêm địa chỉ mới
            </a>

        </div>


        @if($addresses->isEmpty())

            <div class="address-empty">

                <div class="address-empty-icon">
                    ⌂
                </div>

                <h2>
                    Bạn chưa có địa chỉ nhận hàng
                </h2>

                <p>
                    Thêm địa chỉ đầu tiên để
                    quá trình thanh toán thuận tiện hơn.
                </p>

                <a
                    href="{{ route('addresses.create') }}"
                    class="btn btn-primary"
                >
                    Thêm địa chỉ đầu tiên
                </a>

            </div>

        @else

            <div class="address-grid">

                @foreach($addresses as $address)

                    <article
                        class="address-card {{ $address->is_default ? 'address-card-default' : '' }}"
                    >

                        <div class="address-card-header">

                            <div>

                                <div class="address-card-labels">

                                    @if($address->label)

                                        <span class="badge badge-blue">
                                            {{ $address->label }}
                                        </span>

                                    @endif


                                    @if($address->is_default)

                                        <span class="badge badge-success">
                                            Mặc định
                                        </span>

                                    @endif

                                </div>


                                <h3>
                                    {{ $address->recipient_name }}
                                </h3>

                            </div>


                            @if($address->is_default)

                                <div class="address-default-mark">
                                    ✓
                                </div>

                            @endif

                        </div>


                        <div class="address-information">

                            <div>

                                <span>
                                    Số điện thoại
                                </span>

                                <strong>
                                    {{ $address->phone }}
                                </strong>

                            </div>


                            <div>

                                <span>
                                    Địa chỉ
                                </span>

                                <p class="mb-0">

                                    {{ $address->detail_address }},
                                    {{ $address->ward }},
                                    {{ $address->district }},
                                    {{ $address->province }}

                                </p>

                            </div>

                        </div>


                        <div class="address-actions">

                            <a
                                href="{{ route(
                                    'addresses.edit',
                                    $address
                                ) }}"
                                class="btn btn-outline btn-sm"
                            >
                                Chỉnh sửa
                            </a>


                            <form
                                action="{{ route(
                                    'addresses.destroy',
                                    $address
                                ) }}"
                                method="POST"
                                onsubmit="
                                    return confirm(
                                        'Bạn có chắc muốn xóa địa chỉ này?'
                                    );
                                "
                            >

                                @csrf
                                @method('DELETE')


                                <button
                                    type="submit"
                                    class="btn btn-danger btn-sm"
                                >
                                    Xóa
                                </button>

                            </form>

                        </div>

                    </article>

                @endforeach

            </div>

        @endif


        <div class="address-bottom-actions">

            <a
                href="{{ route('cart.index') }}"
                class="btn btn-outline"
            >
                ← Giỏ hàng
            </a>


            <a
                href="{{ route('home') }}"
                class="btn btn-outline"
            >
                Trang chủ
            </a>

        </div>

    </div>

</section>

@endsection