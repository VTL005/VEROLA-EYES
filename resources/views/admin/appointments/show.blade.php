@extends('layouts.admin')


@section(
    'title',
    $appointment->appointment_code
    . ' - VELORA Eyes'
)


@section(
    'page-title',
    'Chi tiết lịch hẹn'
)


@section('content')

@php

    $serviceLabel =
        $appointmentService
            ->serviceLabel(
                $appointment->service_type
            );


    $statusLabel =
        $appointmentService
            ->statusLabel(
                $appointment->status
            );


    $canCreatePrescription =
        in_array(
            $appointment->status,
            [
                'confirmed',
                'completed',
            ],
            true
        );

@endphp



<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            APPOINTMENT DETAIL
        </span>

        <h1>
            {{ $appointment->appointment_code }}
        </h1>

        <p>

            {{ $appointment
                ->appointment_date
                ->format('d/m/Y') }}

            ·

            {{ $appointment->time_slot }}

        </p>

    </div>


    <a
        href="{{ route(
            'admin.appointments.index'
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Danh sách
    </a>

</div>



{{-- SUMMARY --}}

<div class="admin-appointment-summary">

    <div>

        <span>
            Trạng thái
        </span>

        <strong>
            {{ $statusLabel }}
        </strong>

    </div>


    <div>

        <span>
            Ngày hẹn
        </span>

        <strong>
            {{ $appointment
                ->appointment_date
                ->format('d/m/Y') }}
        </strong>

    </div>


    <div>

        <span>
            Khung giờ
        </span>

        <strong>
            {{ $appointment->time_slot }}
        </strong>

    </div>


    <div>

        <span>
            Dịch vụ
        </span>

        <strong>
            {{ $serviceLabel }}
        </strong>

    </div>

</div>



<div class="admin-appointment-detail-layout">


    <div class="admin-appointment-detail-main">


        {{-- CUSTOMER --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Thông tin khách hàng
                    </h2>

                </div>

            </div>


            <div class="admin-appointment-info-grid">

                <div>

                    <i class="bi bi-person"></i>

                    <span>

                        <small>Họ tên</small>

                        <strong>
                            {{ $appointment
                                ->customer_name }}
                        </strong>

                    </span>

                </div>


                <div>

                    <i class="bi bi-telephone"></i>

                    <span>

                        <small>
                            Số điện thoại
                        </small>

                        <strong>
                            {{ $appointment->phone }}
                        </strong>

                    </span>

                </div>


                <div>

                    <i class="bi bi-envelope"></i>

                    <span>

                        <small>Email</small>

                        <strong>
                            {{ $appointment->email }}
                        </strong>

                    </span>

                </div>


                <div>

                    <i class="bi bi-person-badge"></i>

                    <span>

                        <small>
                            Customer ID
                        </small>

                        <strong>
                            #{{ $appointment->user_id }}
                        </strong>

                    </span>

                </div>

            </div>


            @if($appointment->note)

                <div class="admin-appointment-note">

                    <span>
                        Ghi chú của khách hàng
                    </span>

                    <p>
                        {{ $appointment->note }}
                    </p>

                </div>

            @endif

        </section>



        {{-- APPOINTMENT INFO --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Thông tin lịch hẹn</h2>
                </div>

            </div>


            <div class="admin-appointment-data-grid">

                <div>

                    <span>
                        Mã lịch
                    </span>

                    <strong>
                        {{ $appointment
                            ->appointment_code }}
                    </strong>

                </div>


                <div>

                    <span>
                        Dịch vụ
                    </span>

                    <strong>
                        {{ $serviceLabel }}
                    </strong>

                </div>


                <div>

                    <span>
                        Ngày hẹn
                    </span>

                    <strong>
                        {{ $appointment
                            ->appointment_date
                            ->format('d/m/Y') }}
                    </strong>

                </div>


                <div>

                    <span>
                        Khung giờ
                    </span>

                    <strong>
                        {{ $appointment->time_slot }}
                    </strong>

                </div>


                <div>

                    <span>
                        Người xác nhận
                    </span>

                    <strong>

                        {{ $appointment
                            ->confirmer
                            ?->name
                            ?? '—' }}

                    </strong>

                </div>


                <div>

                    <span>
                        Xác nhận lúc
                    </span>

                    <strong>

                        {{ $appointment
                            ->confirmed_at
                            ?->format(
                                'H:i d/m/Y'
                            )
                            ?? '—' }}

                    </strong>

                </div>

            </div>

        </section>



        {{-- PRESCRIPTIONS --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>

                    <h2>
                        Hồ sơ đo mắt
                    </h2>

                    <p>

                        {{ $appointment
                            ->eyePrescriptions
                            ->count() }}

                        hồ sơ

                    </p>

                </div>


                @if($canCreatePrescription)

                    <a
                        href="{{ route(
                            'admin.eye-prescriptions.create',
                            $appointment
                        ) }}"
                        class="admin-btn admin-btn-primary"
                    >
                        <i class="bi bi-plus-lg"></i>

                        Nhập kết quả đo mắt
                    </a>

                @endif

            </div>


            @if(
                $appointment
                    ->eyePrescriptions
                    ->isEmpty()
            )

                <div class="admin-appointment-prescription-empty">

                    <i class="bi bi-eye"></i>

                    <strong>
                        Chưa có hồ sơ đo mắt
                    </strong>

                    <span>

                        @if($canCreatePrescription)

                            Có thể nhập kết quả cho lịch hẹn này.

                        @else

                            Lịch phải được xác nhận trước khi nhập kết quả.

                        @endif

                    </span>

                </div>

            @else

                <div class="admin-table-responsive">

                    <table class="admin-table">

                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Ngày đo</th>

                                <th>SPH phải</th>

                                <th>SPH trái</th>

                                <th>PD</th>

                                <th>Thực hiện bởi</th>

                                <th></th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach(
                                $appointment
                                    ->eyePrescriptions
                                as $prescription
                            )

                                <tr>

                                    <td>

                                        #{{ $prescription->id }}

                                    </td>


                                    <td>

                                        <strong>

                                            {{ $prescription
                                                ->exam_date
                                                ->format(
                                                    'd/m/Y'
                                                ) }}

                                        </strong>

                                    </td>


                                    <td>

                                        {{ $prescription
                                            ->right_sph
                                            ?? '—' }}

                                    </td>


                                    <td>

                                        {{ $prescription
                                            ->left_sph
                                            ?? '—' }}

                                    </td>


                                    <td>

                                        {{ $prescription->pd
                                            ?? '—' }}

                                    </td>


                                    <td>

                                        {{ $prescription
                                            ->performer
                                            ?->name
                                            ?? '—' }}

                                    </td>


                                    <td>

                                        <a
                                            href="{{ route(
                                                'admin.eye-prescriptions.show',
                                                $prescription
                                            ) }}"
                                            class="admin-order-view"
                                            title="Xem hồ sơ"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </section>

    </div>



    {{-- SIDEBAR --}}

    <aside class="admin-appointment-detail-sidebar">


        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Xử lý lịch hẹn</h2>
                </div>

            </div>


            <div class="admin-appointment-process">

                <div class="admin-appointment-current">

                    <span>
                        Trạng thái hiện tại
                    </span>

                    <strong>
                        {{ $statusLabel }}
                    </strong>

                </div>


                @if(!empty($nextStatuses))

                    <form
                        action="{{ route(
                            'admin.appointments.update-status',
                            $appointment
                        ) }}"
                        method="POST"
                    >

                        @csrf
                        @method('PATCH')


                        <div class="admin-form-group">

                            <label for="status">
                                Trạng thái tiếp theo
                            </label>


                            <select
                                name="status"
                                id="status"
                                class="admin-form-control"
                                required
                            >

                                <option value="">
                                    Chọn trạng thái
                                </option>


                                @foreach(
                                    $nextStatuses
                                    as $nextStatus
                                )

                                    <option
                                        value="{{ $nextStatus }}"
                                    >

                                        {{ $appointmentService
                                            ->statusLabel(
                                                $nextStatus
                                            ) }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <button
                            type="submit"
                            class="admin-btn admin-btn-primary admin-btn-full"
                        >
                            <i class="bi bi-check-lg"></i>

                            Cập nhật trạng thái
                        </button>

                    </form>

                @else

                    <div class="admin-appointment-final">

                        @if(
                            $appointment->status
                            === 'completed'
                        )

                            <i class="bi bi-check-circle"></i>

                            <strong>
                                Lịch hẹn đã hoàn thành
                            </strong>

                        @elseif(
                            $appointment->status
                            === 'cancelled'
                        )

                            <i class="bi bi-x-circle"></i>

                            <strong>
                                Lịch hẹn đã bị hủy
                            </strong>

                        @elseif(
                            $appointment->status
                            === 'no_show'
                        )

                            <i class="bi bi-person-x"></i>

                            <strong>
                                Khách hàng không đến
                            </strong>

                        @endif

                    </div>

                @endif

            </div>

        </section>



        {{-- EYE PRESCRIPTION QUICK ACTION --}}

        @if($canCreatePrescription)

            <section class="admin-appointment-eye-card">

                <i class="bi bi-eye"></i>

                <div>

                    <strong>
                        Kết quả đo mắt
                    </strong>

                    <span>
                        Lịch đã đủ điều kiện để lưu hồ sơ thị lực.
                    </span>

                </div>


                <a
                    href="{{ route(
                        'admin.eye-prescriptions.create',
                        $appointment
                    ) }}"
                    class="admin-btn admin-btn-primary admin-btn-full"
                >
                    Nhập kết quả
                </a>

            </section>

        @endif



        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Thông tin hệ thống</h2>
                </div>

            </div>


            <div class="admin-appointment-meta">

                <span>

                    Ngày tạo

                    <strong>
                        {{ $appointment
                            ->created_at
                            ->format(
                                'H:i d/m/Y'
                            ) }}
                    </strong>

                </span>


                <span>

                    Cập nhật cuối

                    <strong>
                        {{ $appointment
                            ->updated_at
                            ->format(
                                'H:i d/m/Y'
                            ) }}
                    </strong>

                </span>


                <span>

                    Reminder

                    <strong>

                        {{ $appointment
                            ->reminder_sent_at
                            ? 'Đã gửi'
                            : 'Chưa gửi' }}

                    </strong>

                </span>

            </div>

        </section>

    </aside>

</div>

@endsection