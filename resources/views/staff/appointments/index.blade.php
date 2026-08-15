@extends('layouts.staff')


@section(
    'title',
    'Lịch đo mắt - Staff'
)


@section(
    'page-title',
    'Lịch đo mắt'
)


@section('content')

@php

    $statusLabels = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
        'no_show' => 'Không đến',
    ];

    $serviceLabels = [
        'eye_exam' => 'Đo mắt cận',
        'recheck' => 'Kiểm tra lại độ kính',
        'lens_consultation' => 'Tư vấn tròng kính',
        'frame_consultation' => 'Tư vấn chọn gọng',
    ];

@endphp



{{-- HEADER --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            EYE CARE
        </span>

        <h1>
            Quản lý lịch đo mắt
        </h1>

        <p>
            Theo dõi lịch hẹn, xác nhận khách
            và quản lý kết quả đo mắt.
        </p>

    </div>

</div>



{{-- STATS --}}

<div class="staff-appointment-stats">

    <a
        href="{{ route(
            'staff.appointments.index',
            [
                'status' => 'pending'
            ]
        ) }}"
        class="staff-appointment-stat warning"
    >

        <span>
            Chờ xác nhận
        </span>

        <strong>
            {{ $pendingCount }}
        </strong>

        <small>
            lịch cần xử lý
        </small>

    </a>


    <div class="staff-appointment-stat info">

        <span>
            Lịch hôm nay
        </span>

        <strong>
            {{ $todayCount }}
        </strong>

        <small>
            lịch trong ngày
        </small>

    </div>


    <a
        href="{{ route(
            'staff.appointments.index',
            [
                'status' => 'confirmed'
            ]
        ) }}"
        class="staff-appointment-stat success"
    >

        <span>
            Đã xác nhận
        </span>

        <strong>
            {{ $confirmedCount }}
        </strong>

        <small>
            đang chờ thực hiện
        </small>

    </a>

</div>



{{-- FILTER --}}

<div class="staff-appointment-filter">

    <form
        action="{{ route(
            'staff.appointments.index'
        ) }}"
        method="GET"
        class="staff-appointment-filter-form"
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
                placeholder="Mã lịch, tên, SĐT..."
            >

        </div>


        <div>

            <label for="status">
                Trạng thái
            </label>

            <select
                id="status"
                name="status"
                class="staff-form-control"
            >

                <option value="">
                    Tất cả trạng thái
                </option>

                @foreach(
                    $statusLabels
                    as $value => $label
                )

                    <option
                        value="{{ $value }}"
                        {{
                            $status === $value
                                ? 'selected'
                                : ''
                        }}
                    >
                        {{ $label }}
                    </option>

                @endforeach

            </select>

        </div>


        <div>

            <label for="appointment_date">
                Ngày hẹn
            </label>

            <input
                type="date"
                id="appointment_date"
                name="appointment_date"
                value="{{ $appointmentDate }}"
                class="staff-form-control"
            >

        </div>


        <div class="staff-appointment-filter-actions">

            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Lọc
            </button>


            @if(
                $keyword !== ''
                || $status
                || $appointmentDate
            )

                <a
                    href="{{ route(
                        'staff.appointments.index'
                    ) }}"
                    class="staff-btn staff-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



{{-- TABLE --}}

<div class="staff-table-card">

    <div class="staff-table-card-header">

        <div>

            <h2>
                Danh sách lịch hẹn
            </h2>

            <p>
                {{ $appointments->total() }}
                lịch hẹn
            </p>

        </div>

    </div>


    @if($appointments->isEmpty())

        <div class="staff-appointment-empty">

            <div>
                ◷
            </div>

            <h3>
                Không có lịch hẹn phù hợp
            </h3>

            <p>
                Hãy thử thay đổi bộ lọc
                hoặc ngày tìm kiếm.
            </p>

        </div>

    @else

        <div class="staff-table-responsive">

            <table class="staff-table">

                <thead>

                    <tr>

                        <th>
                            Mã lịch
                        </th>

                        <th>
                            Khách hàng
                        </th>

                        <th>
                            Ngày / Giờ
                        </th>

                        <th>
                            Dịch vụ
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Xác nhận bởi
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $appointments
                        as $appointment
                    )

                        <tr>

                            <td>

                                <div class="staff-appointment-code">

                                    <strong>
                                        {{ $appointment
                                            ->appointment_code }}
                                    </strong>

                                    <span>
                                        #{{ $appointment->id }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                <div class="staff-appointment-customer">

                                    <strong>
                                        {{ $appointment
                                            ->customer_name }}
                                    </strong>

                                    <span>
                                        {{ $appointment->phone }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                <div class="staff-appointment-time">

                                    <strong>

                                        {{ $appointment
                                            ->appointment_date
                                            ->format('d/m/Y') }}

                                    </strong>

                                    <span>
                                        {{ $appointment
                                            ->time_slot }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                {{ $serviceLabels[
                                    $appointment
                                        ->service_type
                                ] ?? $appointment
                                    ->service_type }}

                            </td>


                            <td>

                                @switch(
                                    $appointment->status
                                )

                                    @case('pending')

                                        <span class="staff-status staff-status-warning">
                                            Chờ xác nhận
                                        </span>

                                        @break


                                    @case('confirmed')

                                        <span class="staff-status staff-status-info">
                                            Đã xác nhận
                                        </span>

                                        @break


                                    @case('completed')

                                        <span class="staff-status staff-status-success">
                                            Hoàn thành
                                        </span>

                                        @break


                                    @case('cancelled')

                                        <span class="staff-status staff-status-danger">
                                            Đã hủy
                                        </span>

                                        @break


                                    @case('no_show')

                                        <span class="staff-status staff-status-muted">
                                            Không đến
                                        </span>

                                        @break

                                @endswitch

                            </td>


                            <td>

                                @if($appointment->confirmer)

                                    <div class="staff-appointment-confirmed">

                                        <strong>
                                            {{ $appointment
                                                ->confirmer
                                                ->name }}
                                        </strong>

                                        @if(
                                            $appointment
                                                ->confirmed_at
                                        )

                                            <span>

                                                {{ $appointment
                                                    ->confirmed_at
                                                    ->format(
                                                        'd/m/Y H:i'
                                                    ) }}

                                            </span>

                                        @endif

                                    </div>

                                @else

                                    <span class="staff-table-muted">
                                        —
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'staff.appointments.show',
                                        $appointment
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

            {{ $appointments->links() }}

        </div>

    @endif

</div>

@endsection