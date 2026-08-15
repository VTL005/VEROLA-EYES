@extends('layouts.admin')


@section(
    'title',
    'Lịch hẹn - VELORA Eyes'
)


@section(
    'page-title',
    'Lịch hẹn'
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



<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            APPOINTMENT MANAGEMENT
        </span>

        <h1>
            Quản lý lịch hẹn
        </h1>

        <p>
            Theo dõi và xử lý lịch đo mắt,
            tái khám và tư vấn của khách hàng.
        </p>

    </div>

</div>



{{-- STATS --}}

<div class="admin-appointment-stats">

    <div class="admin-appointment-stat">

        <div class="all">
            <i class="bi bi-calendar3"></i>
        </div>

        <span>

            <small>
                Tổng lịch hẹn
            </small>

            <strong>
                {{ $totalAppointments }}
            </strong>

        </span>

    </div>


    <a
        href="{{ route(
            'admin.appointments.index',
            ['status' => 'pending']
        ) }}"
        class="admin-appointment-stat"
    >

        <div class="pending">
            <i class="bi bi-clock-history"></i>
        </div>

        <span>

            <small>
                Chờ xác nhận
            </small>

            <strong>
                {{ $pendingAppointments }}
            </strong>

        </span>

    </a>


    <a
        href="{{ route(
            'admin.appointments.index',
            ['status' => 'confirmed']
        ) }}"
        class="admin-appointment-stat"
    >

        <div class="confirmed">
            <i class="bi bi-calendar-check"></i>
        </div>

        <span>

            <small>
                Đã xác nhận
            </small>

            <strong>
                {{ $confirmedAppointments }}
            </strong>

        </span>

    </a>


    <a
        href="{{ route(
            'admin.appointments.index',
            [
                'appointment_date' =>
                    now()->format('Y-m-d'),
            ]
        ) }}"
        class="admin-appointment-stat"
    >

        <div class="today">
            <i class="bi bi-calendar-event"></i>
        </div>

        <span>

            <small>
                Lịch hôm nay
            </small>

            <strong>
                {{ $todayAppointments }}
            </strong>

        </span>

    </a>

</div>



{{-- FILTER --}}

<div class="admin-appointment-filter">

    <form
        action="{{ route(
            'admin.appointments.index'
        ) }}"
        method="GET"
        class="admin-appointment-filter-form"
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
                    placeholder="Mã lịch, tên, email hoặc SĐT..."
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
                class="admin-form-control"
            >

        </div>


        <div class="admin-appointment-filter-actions">

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
                || $appointmentDate
            )

                <a
                    href="{{ route(
                        'admin.appointments.index'
                    ) }}"
                    class="admin-btn admin-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



{{-- TABLE --}}

<div class="admin-panel">

    <div class="admin-panel-header">

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

        <div class="admin-appointment-empty">

            <div>
                <i class="bi bi-calendar-x"></i>
            </div>

            <h3>
                Không tìm thấy lịch hẹn
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

                        <th>Mã lịch</th>

                        <th>Khách hàng</th>

                        <th>Ngày hẹn</th>

                        <th>Khung giờ</th>

                        <th>Dịch vụ</th>

                        <th>Trạng thái</th>

                        <th>Xác nhận bởi</th>

                        <th></th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $appointments
                        as $appointment
                    )

                        <tr>

                            <td>

                                <div class="admin-appointment-code">

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

                                <div class="admin-appointment-customer">

                                    <strong>
                                        {{ $appointment
                                            ->customer_name }}
                                    </strong>

                                    <span>
                                        {{ $appointment->phone }}
                                    </span>

                                    <small>
                                        {{ $appointment->email }}
                                    </small>

                                </div>

                            </td>


                            <td>

                                <strong>

                                    {{ $appointment
                                        ->appointment_date
                                        ->format('d/m/Y') }}

                                </strong>

                            </td>


                            <td>

                                <span class="admin-appointment-slot">

                                    <i class="bi bi-clock"></i>

                                    {{ $appointment->time_slot }}

                                </span>

                            </td>


                            <td>

                                <span class="admin-appointment-service">

                                    {{ $serviceLabels[
                                        $appointment->service_type
                                    ] ?? $appointment->service_type }}

                                </span>

                            </td>


                            <td>

                                @switch($appointment->status)

                                    @case('pending')

                                        <span class="admin-status warning">
                                            Chờ xác nhận
                                        </span>

                                        @break


                                    @case('confirmed')

                                        <span class="admin-status info">
                                            Đã xác nhận
                                        </span>

                                        @break


                                    @case('completed')

                                        <span class="admin-status success">
                                            Hoàn thành
                                        </span>

                                        @break


                                    @case('cancelled')

                                        <span class="admin-status danger">
                                            Đã hủy
                                        </span>

                                        @break


                                    @case('no_show')

                                        <span class="admin-status muted">
                                            Không đến
                                        </span>

                                        @break

                                @endswitch

                            </td>


                            <td>

                                @if($appointment->confirmer)

                                    <div class="admin-table-primary">

                                        <strong>
                                            {{ $appointment
                                                ->confirmer
                                                ->name }}
                                        </strong>

                                        <span>
                                            {{ $appointment
                                                ->confirmed_at
                                                ?->format(
                                                    'd/m/Y H:i'
                                                )
                                                ?? '' }}
                                        </span>

                                    </div>

                                @else

                                    <span class="admin-table-muted">
                                        —
                                    </span>

                                @endif

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'admin.appointments.show',
                                        $appointment
                                    ) }}"
                                    class="admin-order-view"
                                    title="Chi tiết lịch hẹn"
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

            {{ $appointments->links() }}

        </div>

    @endif

</div>

@endsection