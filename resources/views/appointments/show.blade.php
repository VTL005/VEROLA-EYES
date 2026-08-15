@extends('layouts.app')


@section(
    'title',
    'Lịch hẹn ' . $appointment->appointment_code . ' - VELORA Eyes'
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


    $canCancel =
        $appointment->isCancellableByCustomer();

@endphp



{{-- =========================================================
    HERO
========================================================= --}}

<section class="appointment-detail-hero">

    <div class="velora-container">

        <a
            href="{{ route('appointments.index') }}"
            class="appointment-detail-back"
        >
            ← Lịch hẹn của tôi
        </a>


        <div class="appointment-detail-hero-row">

            <div>

                <span class="hero-kicker">
                    APPOINTMENT DETAIL
                </span>


                <h1>
                    {{ $appointment->appointment_code }}
                </h1>


                <p class="text-muted mb-0">
                    Thông tin chi tiết lịch hẹn
                    chăm sóc thị lực tại VELORA Eyes.
                </p>

            </div>


            <div>

                @switch($appointment->status)

                    @case('pending')

                        <span class="appointment-status appointment-pending appointment-status-large">
                            Chờ xác nhận
                        </span>

                        @break


                    @case('confirmed')

                        <span class="appointment-status appointment-confirmed appointment-status-large">
                            Đã xác nhận
                        </span>

                        @break


                    @case('completed')

                        <span class="appointment-status appointment-completed appointment-status-large">
                            Hoàn thành
                        </span>

                        @break


                    @case('cancelled')

                        <span class="appointment-status appointment-cancelled appointment-status-large">
                            Đã hủy
                        </span>

                        @break


                    @case('no_show')

                        <span class="appointment-status appointment-noshow appointment-status-large">
                            Không đến
                        </span>

                        @break


                    @default

                        <span class="appointment-status appointment-status-large">

                            {{ $statusLabels[
                                $appointment->status
                            ] ?? $appointment->status }}

                        </span>

                @endswitch

            </div>

        </div>

    </div>

</section>



<section class="section">

    <div class="velora-container">

        <div class="appointment-detail-layout">


            {{-- =================================================
                LEFT
            ================================================== --}}

            <div class="appointment-detail-main">


                {{-- =============================================
                    APPOINTMENT SCHEDULE
                ============================================== --}}

                <div class="appointment-detail-card">

                    <div class="appointment-detail-card-heading">

                        <div class="appointment-detail-card-icon">
                            ◷
                        </div>


                        <div>

                            <h2>
                                Thời gian hẹn
                            </h2>

                            <p>
                                Thời gian bạn đã đăng ký
                                với VELORA Eyes.
                            </p>

                        </div>

                    </div>


                    <div class="appointment-schedule-box">


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

                                {{ str_replace(
                                    '-',
                                    ' - ',
                                    $appointment->time_slot
                                ) }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                Dịch vụ
                            </span>


                            <strong>

                                {{ $serviceLabels[
                                    $appointment->service_type
                                ]
                                ?? $appointmentService
                                    ->serviceLabel(
                                        $appointment->service_type
                                    ) }}

                            </strong>

                        </div>

                    </div>

                </div>



                {{-- =============================================
                    CUSTOMER INFORMATION
                ============================================== --}}

                <div class="appointment-detail-card">

                    <div class="appointment-detail-card-heading">

                        <div class="appointment-detail-card-icon">
                            👤
                        </div>


                        <div>

                            <h2>
                                Thông tin khách hàng
                            </h2>

                            <p>
                                Thông tin đã được sử dụng
                                khi đặt lịch.
                            </p>

                        </div>

                    </div>


                    <div class="appointment-customer-grid">


                        <div>

                            <span>
                                Họ và tên
                            </span>

                            <strong>
                                {{ $appointment->customer_name }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Số điện thoại
                            </span>

                            <strong>
                                {{ $appointment->phone }}
                            </strong>

                        </div>


                        <div class="appointment-customer-full">

                            <span>
                                Email
                            </span>

                            <strong>
                                {{ $appointment->email }}
                            </strong>

                        </div>

                    </div>

                </div>



                {{-- =============================================
                    NOTE
                ============================================== --}}

                @if($appointment->note)

                    <div class="appointment-detail-card">

                        <div class="appointment-detail-card-heading">

                            <div class="appointment-detail-card-icon">
                                ✎
                            </div>


                            <div>

                                <h2>
                                    Ghi chú
                                </h2>

                                <p>
                                    Nội dung bạn đã gửi
                                    khi đặt lịch.
                                </p>

                            </div>

                        </div>


                        <div class="appointment-note-box">

                            {!! nl2br(
                                e($appointment->note)
                            ) !!}

                        </div>

                    </div>

                @endif



                {{-- =============================================
                    STATUS INFORMATION
                ============================================== --}}

                <div class="appointment-detail-card">

                    <div class="appointment-detail-card-heading">

                        <div class="appointment-detail-card-icon">
                            ✓
                        </div>


                        <div>

                            <h2>
                                Trạng thái lịch hẹn
                            </h2>

                            <p>
                                Theo dõi tiến trình xử lý
                                lịch hẹn của bạn.
                            </p>

                        </div>

                    </div>


                    <div class="appointment-status-flow">


                        {{-- CREATED --}}

                        <div class="appointment-flow-item done">

                            <div class="appointment-flow-dot">
                                ✓
                            </div>


                            <div>

                                <strong>
                                    Đã đặt lịch
                                </strong>

                                <p>
                                    Yêu cầu đặt lịch đã được
                                    gửi tới VELORA.
                                </p>

                            </div>

                        </div>



                        {{-- CONFIRMED --}}

                        <div
                            class="appointment-flow-item
                            {{
                                in_array(
                                    $appointment->status,
                                    [
                                        'confirmed',
                                        'completed',
                                        'no_show'
                                    ],
                                    true
                                )
                                    ? 'done'
                                    : ''
                            }}"
                        >

                            <div class="appointment-flow-dot">

                                @if(
                                    in_array(
                                        $appointment->status,
                                        [
                                            'confirmed',
                                            'completed',
                                            'no_show'
                                        ],
                                        true
                                    )
                                )

                                    ✓

                                @else

                                    2

                                @endif

                            </div>


                            <div>

                                <strong>
                                    Xác nhận lịch
                                </strong>

                                <p>

                                    @if(
                                        in_array(
                                            $appointment->status,
                                            [
                                                'confirmed',
                                                'completed',
                                                'no_show'
                                            ],
                                            true
                                        )
                                    )

                                        Lịch hẹn đã được
                                        VELORA xác nhận.

                                    @elseif(
                                        $appointment->status
                                        === 'cancelled'
                                    )

                                        Lịch đã bị hủy.

                                    @else

                                        Đang chờ nhân viên
                                        xác nhận lịch.

                                    @endif

                                </p>


                                @if($appointment->confirmer)

                                    <small>

                                        Xác nhận bởi:
                                        {{ $appointment
                                            ->confirmer
                                            ->name }}

                                    </small>

                                @endif

                            </div>

                        </div>



                        {{-- COMPLETED --}}

                        <div
                            class="appointment-flow-item
                            {{
                                $appointment->status === 'completed'
                                    ? 'done'
                                    : ''
                            }}"
                        >

                            <div class="appointment-flow-dot">

                                @if(
                                    $appointment->status
                                    === 'completed'
                                )

                                    ✓

                                @else

                                    3

                                @endif

                            </div>


                            <div>

                                <strong>
                                    Hoàn thành
                                </strong>

                                <p>

                                    @if(
                                        $appointment->status
                                        === 'completed'
                                    )

                                        Buổi hẹn đã được
                                        hoàn thành.

                                    @elseif(
                                        $appointment->status
                                        === 'no_show'
                                    )

                                        Khách hàng không đến
                                        theo lịch hẹn.

                                    @elseif(
                                        $appointment->status
                                        === 'cancelled'
                                    )

                                        Lịch không tiếp tục
                                        do đã bị hủy.

                                    @else

                                        Chờ hoàn thành
                                        buổi kiểm tra.

                                    @endif

                                </p>

                            </div>

                        </div>

                    </div>



                    @if(
                        $appointment->status
                        === 'cancelled'
                    )

                        <div class="appointment-cancelled-notice">

                            <strong>
                                Lịch hẹn đã bị hủy
                            </strong>

                            <span>
                                Lịch này sẽ không tiếp tục
                                được xử lý.
                            </span>

                        </div>

                    @elseif(
                        $appointment->status
                        === 'no_show'
                    )

                        <div class="appointment-noshow-notice">

                            <strong>
                                Không đến theo lịch hẹn
                            </strong>

                            <span>
                                Bạn có thể đặt một lịch mới
                                nếu vẫn muốn sử dụng dịch vụ.
                            </span>

                        </div>

                    @endif

                </div>

            </div>



            {{-- =================================================
                RIGHT
            ================================================== --}}

            <aside class="appointment-detail-sidebar">


                {{-- SUMMARY --}}

                <div class="appointment-detail-card">

                    <h2>
                        Thông tin lịch
                    </h2>


                    <div class="appointment-summary-list">


                        <div>

                            <span>
                                Mã lịch
                            </span>

                            <strong>
                                {{ $appointment->appointment_code }}
                            </strong>

                        </div>


                        <div>

                            <span>
                                Ngày
                            </span>

                            <strong>

                                {{ $appointment
                                    ->appointment_date
                                    ->format('d/m/Y') }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                Thời gian
                            </span>

                            <strong>

                                {{ str_replace(
                                    '-',
                                    ' - ',
                                    $appointment->time_slot
                                ) }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                Dịch vụ
                            </span>

                            <strong>

                                {{ $serviceLabels[
                                    $appointment->service_type
                                ]
                                ?? $appointment->service_type }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                Trạng thái
                            </span>

                            <strong>

                                {{ $statusLabels[
                                    $appointment->status
                                ]
                                ?? $appointment->status }}

                            </strong>

                        </div>

                    </div>

                </div>



                {{-- CANCEL --}}

                @if($canCancel)

                    <div class="appointment-cancel-card">

                        <h3>
                            Muốn hủy lịch?
                        </h3>


                        <p>

                            Lịch hiện vẫn ở trạng thái
                            cho phép Customer yêu cầu hủy.

                        </p>


                        <form
                            action="{{ route(
                                'appointments.cancel',
                                $appointment
                            ) }}"
                            method="POST"
                            onsubmit="
                                return confirm(
                                    'Bạn có chắc muốn hủy lịch hẹn này?'
                                );
                            "
                        >

                            @csrf
                            @method('PATCH')


                            <button
                                type="submit"
                                class="btn btn-danger"
                                style="width:100%;"
                            >
                                Hủy lịch hẹn
                            </button>

                        </form>


                        <small>

                            Hệ thống sẽ kiểm tra lại
                            điều kiện hủy trước khi xử lý.

                        </small>

                    </div>

                @endif



                {{-- COMPLETED --}}

                @if(
                    $appointment->status
                    === 'completed'
                )

                    <div class="appointment-completed-card">

                        <strong>
                            Lịch đã hoàn thành
                        </strong>


                        <p>

                            Nếu nhân viên đã lưu kết quả
                            đo mắt, bạn có thể xem trong
                            hồ sơ thị lực của mình.

                        </p>


                        @if(
                            Route::has(
                                'eye-prescriptions.index'
                            )
                        )

                            <a
                                href="{{ route(
                                    'eye-prescriptions.index'
                                ) }}"
                                class="btn btn-primary"
                                style="width:100%;"
                            >
                                Xem hồ sơ đo mắt
                            </a>

                        @endif

                    </div>

                @endif



                {{-- NO SHOW / CANCELLED --}}

                @if(
                    in_array(
                        $appointment->status,
                        [
                            'cancelled',
                            'no_show'
                        ],
                        true
                    )
                )

                    <a
                        href="{{ route(
                            'appointments.create'
                        ) }}"
                        class="btn btn-primary"
                        style="width:100%;"
                    >
                        + Đặt lịch mới
                    </a>

                @endif


                <a
                    href="{{ route(
                        'appointments.index'
                    ) }}"
                    class="btn btn-outline"
                    style="width:100%;"
                >
                    ← Quay lại danh sách
                </a>

            </aside>

        </div>

    </div>

</section>

@endsection