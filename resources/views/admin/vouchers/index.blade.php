@extends('layouts.admin')


@section(
    'title',
    'Voucher - VELORA Eyes'
)


@section(
    'page-title',
    'Voucher'
)


@section('content')

@php

    $typeLabels = [
        'percentage' => 'Giảm phần trăm',
        'fixed' => 'Giảm tiền cố định',
    ];

@endphp



<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            VOUCHER MANAGEMENT
        </span>

        <h1>
            Quản lý Voucher
        </h1>

        <p>
            Quản lý mã giảm giá,
            thời gian áp dụng và lượt sử dụng.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.vouchers.create'
        ) }}"
        class="admin-btn admin-btn-primary"
    >
        <i class="bi bi-plus-lg"></i>

        Thêm Voucher
    </a>

</div>



{{-- STATS --}}

<div class="admin-voucher-stats">

    <div class="admin-voucher-stat">

        <div class="all">
            <i class="bi bi-ticket-perforated"></i>
        </div>

        <span>
            <small>Tổng Voucher</small>
            <strong>{{ $totalVouchers }}</strong>
        </span>

    </div>


    <a
        href="{{ route(
            'admin.vouchers.index',
            ['status' => 'active']
        ) }}"
        class="admin-voucher-stat"
    >

        <div class="active">
            <i class="bi bi-check-circle"></i>
        </div>

        <span>
            <small>Đang sử dụng</small>
            <strong>{{ $activeVouchers }}</strong>
        </span>

    </a>


    <a
        href="{{ route(
            'admin.vouchers.index',
            ['status' => 'expired']
        ) }}"
        class="admin-voucher-stat"
    >

        <div class="expired">
            <i class="bi bi-clock-history"></i>
        </div>

        <span>
            <small>Đã hết hạn</small>
            <strong>{{ $expiredVouchers }}</strong>
        </span>

    </a>


    <a
        href="{{ route(
            'admin.vouchers.index',
            ['status' => 'exhausted']
        ) }}"
        class="admin-voucher-stat"
    >

        <div class="exhausted">
            <i class="bi bi-slash-circle"></i>
        </div>

        <span>
            <small>Hết lượt</small>
            <strong>{{ $exhaustedVouchers }}</strong>
        </span>

    </a>

</div>



{{-- FILTER --}}

<div class="admin-voucher-filter">

    <form
        action="{{ route(
            'admin.vouchers.index'
        ) }}"
        method="GET"
        class="admin-voucher-filter-form"
    >

        <div>

            <label for="keyword">
                Tìm mã Voucher
            </label>

            <div class="admin-input-icon">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    class="admin-form-control"
                    placeholder="Ví dụ: VELORA10"
                >

            </div>

        </div>


        <div>

            <label for="discount_type">
                Loại giảm
            </label>

            <select
                id="discount_type"
                name="discount_type"
                class="admin-form-control"
            >

                <option value="">
                    Tất cả
                </option>

                <option
                    value="percentage"
                    {{
                        $discountType === 'percentage'
                            ? 'selected'
                            : ''
                    }}
                >
                    Phần trăm
                </option>

                <option
                    value="fixed"
                    {{
                        $discountType === 'fixed'
                            ? 'selected'
                            : ''
                    }}
                >
                    Tiền cố định
                </option>

            </select>

        </div>


        <div>

            <label for="status">
                Trạng thái
            </label>

            <select
                id="status"
                name="status"
                class="admin-form-control"
            >

                <option value="">
                    Tất cả
                </option>

                <option
                    value="active"
                    {{ $status === 'active' ? 'selected' : '' }}
                >
                    Đang sử dụng
                </option>

                <option
                    value="upcoming"
                    {{ $status === 'upcoming' ? 'selected' : '' }}
                >
                    Chưa bắt đầu
                </option>

                <option
                    value="expired"
                    {{ $status === 'expired' ? 'selected' : '' }}
                >
                    Hết hạn
                </option>

                <option
                    value="exhausted"
                    {{ $status === 'exhausted' ? 'selected' : '' }}
                >
                    Hết lượt
                </option>

                <option
                    value="inactive"
                    {{ $status === 'inactive' ? 'selected' : '' }}
                >
                    Đã khóa
                </option>

            </select>

        </div>


        <div class="admin-voucher-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-funnel"></i>
                Lọc
            </button>


            @if(
                $keyword !== ''
                || $discountType
                || $status
            )

                <a
                    href="{{ route(
                        'admin.vouchers.index'
                    ) }}"
                    class="admin-btn admin-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



<div class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Danh sách Voucher
            </h2>

            <p>
                {{ $vouchers->total() }}
                Voucher
            </p>

        </div>

    </div>


    @if($vouchers->isEmpty())

        <div class="admin-voucher-empty">

            <i class="bi bi-ticket-perforated"></i>

            <h3>
                Không tìm thấy Voucher
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
                        <th>Mã Voucher</th>
                        <th>Mức giảm</th>
                        <th>Đơn tối thiểu</th>
                        <th>Thời hạn</th>
                        <th>Lượt sử dụng</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $vouchers
                        as $voucher
                    )

                        @php

                            $isExhausted =
                                $voucher->usage_limit !== null
                                && $voucher->usage_count
                                    >= $voucher->usage_limit;


                            if (!$voucher->is_active) {

                                $effectiveStatus =
                                    'inactive';

                            } elseif (
                                now()->lt(
                                    $voucher->starts_at
                                )
                            ) {

                                $effectiveStatus =
                                    'upcoming';

                            } elseif (
                                now()->gt(
                                    $voucher->ends_at
                                )
                            ) {

                                $effectiveStatus =
                                    'expired';

                            } elseif ($isExhausted) {

                                $effectiveStatus =
                                    'exhausted';

                            } else {

                                $effectiveStatus =
                                    'active';
                            }


                            $usagePercent =
                                $voucher->usage_limit
                                    ? min(
                                        100,
                                        (
                                            $voucher->usage_count
                                            / $voucher->usage_limit
                                        ) * 100
                                    )
                                    : null;

                        @endphp


                        <tr>

                            <td>

                                <div class="admin-voucher-code">

                                    <i class="bi bi-ticket-perforated"></i>

                                    <span>

                                        <strong>
                                            {{ $voucher->code }}
                                        </strong>

                                        <small>
                                            #{{ $voucher->id }}
                                        </small>

                                    </span>

                                </div>

                            </td>


                            <td>

                                <div class="admin-voucher-discount">

                                    @if(
                                        $voucher->discount_type
                                        === 'percentage'
                                    )

                                        <strong>
                                            {{ number_format(
                                                (float) $voucher
                                                    ->discount_value,
                                                0,
                                                ',',
                                                '.'
                                            ) }}%
                                        </strong>

                                        <span>
                                            Giảm theo phần trăm
                                        </span>

                                    @else

                                        <strong>

                                            {{ number_format(
                                                (float) $voucher
                                                    ->discount_value,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ

                                        </strong>

                                        <span>
                                            Giảm tiền cố định
                                        </span>

                                    @endif

                                </div>

                            </td>


                            <td>

                                <strong class="admin-money">

                                    {{ number_format(
                                        (float) $voucher
                                            ->minimum_order_amount,
                                        0,
                                        ',',
                                        '.'
                                    ) }}đ

                                </strong>

                            </td>


                            <td>

                                <div class="admin-voucher-period">

                                    <span>

                                        {{ $voucher
                                            ->starts_at
                                            ->format(
                                                'd/m/Y H:i'
                                            ) }}

                                    </span>

                                    <i class="bi bi-arrow-down"></i>

                                    <strong>

                                        {{ $voucher
                                            ->ends_at
                                            ->format(
                                                'd/m/Y H:i'
                                            ) }}

                                    </strong>

                                </div>

                            </td>


                            <td>

                                <div class="admin-voucher-usage">

                                    <div>

                                        <strong>
                                            {{ $voucher->usage_count }}
                                        </strong>

                                        <span>
                                            /
                                            {{ $voucher->usage_limit
                                                ?? '∞' }}
                                        </span>

                                    </div>


                                    @if(
                                        $usagePercent !== null
                                    )

                                        <div class="admin-voucher-usage-bar">

                                            <span
                                                style="
                                                    width:
                                                    {{ $usagePercent }}%;
                                                "
                                            ></span>

                                        </div>

                                    @endif

                                </div>

                            </td>


                            <td>

                                @switch($effectiveStatus)

                                    @case('active')

                                        <span class="admin-status success">
                                            Đang sử dụng
                                        </span>

                                        @break


                                    @case('upcoming')

                                        <span class="admin-status info">
                                            Chưa bắt đầu
                                        </span>

                                        @break


                                    @case('expired')

                                        <span class="admin-status warning">
                                            Hết hạn
                                        </span>

                                        @break


                                    @case('exhausted')

                                        <span class="admin-status danger">
                                            Hết lượt
                                        </span>

                                        @break


                                    @default

                                        <span class="admin-status muted">
                                            Đã khóa
                                        </span>

                                @endswitch

                            </td>


                            <td>

                                <div class="admin-voucher-actions">

                                    <a
                                        href="{{ route(
                                            'admin.vouchers.edit',
                                            $voucher
                                        ) }}"
                                        title="Chỉnh sửa"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </a>


                                    <form
                                        action="{{ route(
                                            'admin.vouchers.toggle-active',
                                            $voucher
                                        ) }}"
                                        method="POST"
                                        onsubmit="
                                            return confirm(
                                                '{{ $voucher->is_active
                                                    ? 'Bạn muốn khóa Voucher này?'
                                                    : 'Bạn muốn mở lại Voucher này?' }}'
                                            );
                                        "
                                    >

                                        @csrf
                                        @method('PATCH')


                                        <button
                                            type="submit"
                                            class="{{
                                                $voucher->is_active
                                                    ? 'lock'
                                                    : 'unlock'
                                            }}"
                                            title="{{
                                                $voucher->is_active
                                                    ? 'Khóa Voucher'
                                                    : 'Mở Voucher'
                                            }}"
                                        >

                                            <i
                                                class="bi {{
                                                    $voucher->is_active
                                                        ? 'bi-lock'
                                                        : 'bi-unlock'
                                                }}"
                                            ></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="admin-pagination">
            {{ $vouchers->links() }}
        </div>

    @endif

</div>

@endsection