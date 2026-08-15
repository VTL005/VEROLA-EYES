@extends('layouts.admin')


@section(
    'title',
    'Bảo hành - VELORA Eyes'
)


@section(
    'page-title',
    'Bảo hành'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            WARRANTY MANAGEMENT
        </span>

        <h1>
            Bảo hành điện tử
        </h1>

        <p>
            Theo dõi thời hạn và hồ sơ
            bảo hành sản phẩm của khách hàng.
        </p>

    </div>


    <a
        href="{{ route(
            'warranties.lookup-form'
        ) }}"
        class="admin-btn admin-btn-secondary"
        target="_blank"
    >
        <i class="bi bi-search"></i>

        Tra cứu công khai
    </a>

</div>



{{-- STATS --}}

<div class="admin-warranty-stats">

    <div class="admin-warranty-stat">

        <div class="all">
            <i class="bi bi-shield-check"></i>
        </div>

        <span>
            <small>Tổng bảo hành</small>
            <strong>{{ $totalWarranties }}</strong>
        </span>

    </div>


    <a
        href="{{ route(
            'admin.warranties.index',
            ['status' => 'active']
        ) }}"
        class="admin-warranty-stat"
    >

        <div class="active">
            <i class="bi bi-check-circle"></i>
        </div>

        <span>
            <small>Còn hiệu lực</small>
            <strong>{{ $activeWarranties }}</strong>
        </span>

    </a>


    <a
        href="{{ route(
            'admin.warranties.index',
            ['status' => 'expired']
        ) }}"
        class="admin-warranty-stat"
    >

        <div class="expired">
            <i class="bi bi-hourglass-bottom"></i>
        </div>

        <span>
            <small>Hết hạn</small>
            <strong>{{ $expiredWarranties }}</strong>
        </span>

    </a>


    <a
        href="{{ route(
            'admin.warranties.index',
            ['status' => 'cancelled']
        ) }}"
        class="admin-warranty-stat"
    >

        <div class="cancelled">
            <i class="bi bi-x-circle"></i>
        </div>

        <span>
            <small>Đã hủy</small>
            <strong>{{ $cancelledWarranties }}</strong>
        </span>

    </a>

</div>



{{-- FILTER --}}

<div class="admin-warranty-filter">

    <form
        action="{{ route(
            'admin.warranties.index'
        ) }}"
        method="GET"
        class="admin-warranty-filter-form"
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
                    placeholder="Mã BH, khách hàng, sản phẩm, mã đơn..."
                >

            </div>

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
                    {{
                        $status === 'active'
                            ? 'selected'
                            : ''
                    }}
                >
                    Còn hiệu lực
                </option>

                <option
                    value="expired"
                    {{
                        $status === 'expired'
                            ? 'selected'
                            : ''
                    }}
                >
                    Hết hạn
                </option>

                <option
                    value="cancelled"
                    {{
                        $status === 'cancelled'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đã hủy
                </option>

            </select>

        </div>


        <div class="admin-warranty-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-funnel"></i>
                Lọc
            </button>


            @if(
                $keyword !== ''
                || $status
            )

                <a
                    href="{{ route(
                        'admin.warranties.index'
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
                Danh sách bảo hành
            </h2>

            <p>
                {{ $warranties->total() }}
                hồ sơ
            </p>

        </div>

    </div>


    @if($warranties->isEmpty())

        <div class="admin-warranty-empty">

            <i class="bi bi-shield-x"></i>

            <h3>
                Không tìm thấy bảo hành
            </h3>

            <p>
                Chưa có hồ sơ phù hợp với bộ lọc.
            </p>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table">

                <thead>

                    <tr>
                        <th>Mã bảo hành</th>
                        <th>Khách hàng</th>
                        <th>Sản phẩm</th>
                        <th>Đơn hàng</th>
                        <th>Bắt đầu</th>
                        <th>Hết hạn</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $warranties
                        as $warranty
                    )

                        @php

                            if (
                                $warranty->status
                                === 'cancelled'
                            ) {
                                $effectiveStatus =
                                    'cancelled';
                            } elseif (
                                $warranty->isExpired()
                            ) {
                                $effectiveStatus =
                                    'expired';
                            } else {
                                $effectiveStatus =
                                    'active';
                            }

                        @endphp


                        <tr>

                            <td>

                                <code class="admin-warranty-code">
                                    {{ $warranty->warranty_code }}
                                </code>

                            </td>


                            <td>

                                <div class="admin-warranty-customer">

                                    <strong>
                                        {{ $warranty->user?->name ?? '—' }}
                                    </strong>

                                    <span>
                                        {{ $warranty->user?->phone ?? '—' }}
                                    </span>

                                    <small>
                                        {{ $warranty->user?->email ?? '—' }}
                                    </small>

                                </div>

                            </td>


                            <td>

                                <div class="admin-warranty-product">

                                    <strong>

                                        {{ $warranty
                                            ->product
                                            ?->name
                                            ?? $warranty
                                                ->orderDetail
                                                ?->product_name
                                            ?? '—' }}

                                    </strong>

                                    <span>

                                        {{ $warranty
                                            ->orderDetail
                                            ?->sku
                                            ?? '—' }}

                                    </span>

                                </div>

                            </td>


                            <td>

                                @if(
                                    $warranty
                                        ->orderDetail
                                        ?->order
                                )

                                    <a
                                        href="{{ route(
                                            'admin.orders.show',
                                            $warranty
                                                ->orderDetail
                                                ->order
                                        ) }}"
                                        class="admin-table-action"
                                    >
                                        {{ $warranty
                                            ->orderDetail
                                            ->order
                                            ->order_code }}
                                    </a>

                                @else

                                    —

                                @endif

                            </td>


                            <td>

                                {{ $warranty
                                    ->start_date
                                    ->format('d/m/Y') }}

                            </td>


                            <td>

                                {{ $warranty
                                    ->end_date
                                    ->format('d/m/Y') }}

                            </td>


                            <td>

                                @if(
                                    $effectiveStatus
                                    === 'active'
                                )

                                    <span class="admin-status success">
                                        Còn hiệu lực
                                    </span>

                                @elseif(
                                    $effectiveStatus
                                    === 'expired'
                                )

                                    <span class="admin-status warning">
                                        Hết hạn
                                    </span>

                                @else

                                    <span class="admin-status danger">
                                        Đã hủy
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'admin.warranties.show',
                                        $warranty
                                    ) }}"
                                    class="admin-order-view"
                                    title="Xem bảo hành"
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
            {{ $warranties->links() }}
        </div>

    @endif

</div>

@endsection