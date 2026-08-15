@extends('layouts.admin')


@section(
    'title',
    'Khách hàng - VELORA Eyes'
)


@section(
    'page-title',
    'Khách hàng'
)


@section('content')


{{-- =========================================================
    HEADER
========================================================= --}}

<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            CUSTOMER MANAGEMENT
        </span>

        <h1>
            Quản lý khách hàng
        </h1>

        <p>
            Theo dõi tài khoản, hoạt động mua hàng
            và trạng thái Customer.
        </p>

    </div>

</div>



{{-- =========================================================
    STATS
========================================================= --}}

<div class="admin-customer-stats">


    <div class="admin-customer-stat">

        <div class="admin-customer-stat-icon">

            <i class="bi bi-people"></i>

        </div>


        <div>

            <span>
                Tổng khách hàng
            </span>

            <strong>
                {{ $totalCustomers }}
            </strong>

            <small>
                tài khoản Customer
            </small>

        </div>

    </div>



    <a
        href="{{ route(
            'admin.customers.index',
            [
                'status' => 'active',
            ]
        ) }}"
        class="admin-customer-stat active"
    >

        <div class="admin-customer-stat-icon">

            <i class="bi bi-person-check"></i>

        </div>


        <div>

            <span>
                Đang hoạt động
            </span>

            <strong>
                {{ $activeCustomers }}
            </strong>

            <small>
                tài khoản có thể đăng nhập
            </small>

        </div>

    </a>



    <a
        href="{{ route(
            'admin.customers.index',
            [
                'status' => 'inactive',
            ]
        ) }}"
        class="admin-customer-stat inactive"
    >

        <div class="admin-customer-stat-icon">

            <i class="bi bi-person-lock"></i>

        </div>


        <div>

            <span>
                Đã khóa
            </span>

            <strong>
                {{ $inactiveCustomers }}
            </strong>

            <small>
                tài khoản bị vô hiệu hóa
            </small>

        </div>

    </a>

</div>



{{-- =========================================================
    FILTER
========================================================= --}}

<div class="admin-customer-filter">

    <form
        action="{{ route(
            'admin.customers.index'
        ) }}"
        method="GET"
        class="admin-customer-filter-form"
    >

        <div>

            <label for="keyword">
                Tìm kiếm khách hàng
            </label>


            <div class="admin-input-icon">

                <i class="bi bi-search"></i>


                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    class="admin-form-control"
                    placeholder="Tên, email hoặc số điện thoại..."
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
                    Tất cả trạng thái
                </option>


                <option
                    value="active"
                    {{
                        $status === 'active'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đang hoạt động
                </option>


                <option
                    value="inactive"
                    {{
                        $status === 'inactive'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đã khóa
                </option>

            </select>

        </div>



        <div class="admin-customer-filter-actions">

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
                        'admin.customers.index'
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
                Danh sách khách hàng
            </h2>

            <p>
                {{ $customers->total() }}
                khách hàng
            </p>

        </div>

    </div>



    @if($customers->isEmpty())

        <div class="admin-customer-empty">

            <div>
                <i class="bi bi-people"></i>
            </div>

            <h3>
                Không tìm thấy khách hàng
            </h3>

            <p>
                Hãy thử thay đổi từ khóa
                hoặc bộ lọc trạng thái.
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
                            Liên hệ
                        </th>

                        <th>
                            Đơn hàng
                        </th>

                        <th>
                            Lịch hẹn
                        </th>

                        <th>
                            Bảo hành
                        </th>

                        <th>
                            Đánh giá
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Tham gia
                        </th>

                        <th>
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $customers
                        as $customer
                    )

                        <tr>


                            {{-- CUSTOMER --}}

                            <td>

                                <div class="admin-customer-user">

                                    <div class="admin-customer-avatar">

                                        {{ strtoupper(
                                            mb_substr(
                                                $customer->name,
                                                0,
                                                1
                                            )
                                        ) }}

                                    </div>


                                    <div>

                                        <strong>
                                            {{ $customer->name }}
                                        </strong>

                                        <span>
                                            ID #{{ $customer->id }}
                                        </span>

                                    </div>

                                </div>

                            </td>



                            {{-- CONTACT --}}

                            <td>

                                <div class="admin-customer-contact">

                                    <span>
                                        {{ $customer->email }}
                                    </span>

                                    <small>
                                        {{ $customer->phone ?: '—' }}
                                    </small>

                                </div>

                            </td>



                            {{-- ORDER --}}

                            <td>

                                <strong class="admin-customer-count">

                                    {{ $customer
                                        ->orders_count }}

                                </strong>

                            </td>



                            {{-- APPOINTMENT --}}

                            <td>

                                <strong class="admin-customer-count">

                                    {{ $customer
                                        ->appointments_count }}

                                </strong>

                            </td>



                            {{-- WARRANTY --}}

                            <td>

                                <strong class="admin-customer-count">

                                    {{ $customer
                                        ->warranties_count }}

                                </strong>

                            </td>



                            {{-- REVIEW --}}

                            <td>

                                <strong class="admin-customer-count">

                                    {{ $customer
                                        ->reviews_count }}

                                </strong>

                            </td>



                            {{-- STATUS --}}

                            <td>

                                @if($customer->is_active)

                                    <span class="admin-status success">
                                        Hoạt động
                                    </span>

                                @else

                                    <span class="admin-status danger">
                                        Đã khóa
                                    </span>

                                @endif

                            </td>



                            {{-- DATE --}}

                            <td>

                                <div class="admin-table-primary">

                                    <strong>

                                        {{ $customer
                                            ->created_at
                                            ->format('d/m/Y') }}

                                    </strong>

                                    <span>

                                        {{ $customer
                                            ->created_at
                                            ->format('H:i') }}

                                    </span>

                                </div>

                            </td>



                            {{-- ACTION --}}

                            <td>

                                <a
                                    href="{{ route(
                                        'admin.customers.show',
                                        $customer
                                    ) }}"
                                    class="admin-table-action"
                                >
                                    Chi tiết
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="admin-pagination">

            {{ $customers->links() }}

        </div>

    @endif

</div>

@endsection