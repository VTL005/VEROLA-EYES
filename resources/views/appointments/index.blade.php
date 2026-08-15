@extends('layouts.app')


@section('title', 'Lịch đo mắt của tôi - VELORA Eyes')


@section('content')

<section class="appointments-hero">

    <div class="velora-container">

        <span class="hero-kicker">
            EYE CARE APPOINTMENTS
        </span>

        <h1>
            Lịch đo mắt của tôi
        </h1>

        <p class="text-muted mb-0">
            Theo dõi và quản lý các lịch hẹn
            chăm sóc thị lực tại VELORA Eyes.
        </p>

    </div>

</section>


<section class="section">

    <div class="velora-container">


        {{-- =============================================
            HEADER ACTIONS
        ============================================== --}}

        <div class="appointment-page-heading">

            <div>

                <strong>
                    {{ $appointments->total() }}
                </strong>

                <span class="text-muted">
                    lịch hẹn
                </span>

            </div>


            <a
                href="{{ route('appointments.create') }}"
                class="btn btn-primary"
            >
                + Đặt lịch mới
            </a>

        </div>



        {{-- =============================================
            FILTER
        ============================================== --}}

        <div class="appointment-filter-tabs">

            <a
                href="{{ route('appointments.index') }}"
                class="appointment-filter-tab {{ !request('status') ? 'active' : '' }}"
            >
                Tất cả
            </a>


            <a
                href="{{ route(
                    'appointments.index',
                    ['status' => 'pending']
                ) }}"
                class="appointment-filter-tab {{ request('status') === 'pending' ? 'active' : '' }}"
            >
                Chờ xác nhận
            </a>


            <a
                href="{{ route(
                    'appointments.index',
                    ['status' => 'confirmed']
                ) }}"
                class="appointment-filter-tab {{ request('status') === 'confirmed' ? 'active' : '' }}"
            >
                Đã xác nhận
            </a>


            <a
                href="{{ route(
                    'appointments.index',
                    ['status' => 'completed']
                ) }}"
                class="appointment-filter-tab {{ request('status') === 'completed' ? 'active' : '' }}"
            >
                Hoàn thành
            </a>


            <a
                href="{{ route(
                    'appointments.index',
                    ['status' => 'cancelled']
                ) }}"
                class="appointment-filter-tab {{ request('status') === 'cancelled' ? 'active' : '' }}"
            >
                Đã hủy
            </a>


            <a
                href="{{ route(
                    'appointments.index',
                    ['status' => 'no_show']
                ) }}"
                class="appointment-filter-tab {{ request('status') === 'no_show' ? 'active' : '' }}"
            >
                Không đến
            </a>

        </div>



        {{-- =============================================
            EMPTY
        ============================================== --}}

        @if($appointments->isEmpty())

            <div class="appointments-empty">

                <div class="appointments-empty-icon">
                    ◷
                </div>


                @if(request('status'))

                    <h2>
                        Không có lịch hẹn ở trạng thái này
                    </h2>

                    <p>
                        Bạn có thể xem tất cả lịch hẹn
                        hoặc đặt một lịch mới.
                    </p>


                    <div class="appointments-empty-actions">

                        <a
                            href="{{ route('appointments.index') }}"
                            class="btn btn-outline"
                        >
                            Xem tất cả
                        </a>


                        <a
                            href="{{ route('appointments.create') }}"
                            class="btn btn-primary"
                        >
                            Đặt lịch mới
                        </a>

                    </div>

                @else

                    <h2>
                        Bạn chưa có lịch hẹn nào
                    </h2>

                    <p>
                        Đặt lịch kiểm tra thị lực
                        và nhận tư vấn tại VELORA Eyes.
                    </p>


                    <a
                        href="{{ route('appointments.create') }}"
                        class="btn btn-primary"
                    >
                        Đặt lịch đo mắt
                    </a>

                @endif

            </div>

        @else


            {{-- =============================================
                APPOINTMENT CARDS
            ============================================== --}}

            <div class="appointment-list">

                @foreach($appointments as $appointment)

                    <article class="appointment-card">


                        <div class="appointment-card-header">

                            <div>

                                <span class="appointment-code-label">
                                    Mã lịch hẹn
                                </span>

                                <strong class="appointment-code">
                                    {{ $appointment->appointment_code }}
                                </strong>

                            </div>


                            @switch($appointment->status)

                                @case('pending')

                                    <span class="appointment-status appointment-pending">
                                        Chờ xác nhận
                                    </span>

                                    @break


                                @case('confirmed')

                                    <span class="appointment-status appointment-confirmed">
                                        Đã xác nhận
                                    </span>

                                    @break


                                @case('completed')

                                    <span class="appointment-status appointment-completed">
                                        Hoàn thành
                                    </span>

                                    @break


                                @case('cancelled')

                                    <span class="appointment-status appointment-cancelled">
                                        Đã hủy
                                    </span>

                                    @break


                                @case('no_show')

                                    <span class="appointment-status appointment-noshow">
                                        Không đến
                                    </span>

                                    @break


                                @default

                                    <span class="appointment-status">
                                        {{ $appointmentService->statusLabel(
                                            $appointment->status
                                        ) }}
                                    </span>

                            @endswitch

                        </div>



                        <div class="appointment-card-body">


                            <div class="appointment-info-item">

                                <span>
                                    Ngày hẹn
                                </span>

                                <strong>

                                    {{ $appointment
                                        ->appointment_date
                                        ->format('d/m/Y') }}

                                </strong>

                            </div>


                            <div class="appointment-info-item">

                                <span>
                                    Khung giờ
                                </span>

                                <strong>
                                    {{ $appointment->time_slot }}
                                </strong>

                            </div>


                            <div class="appointment-info-item">

                                <span>
                                    Dịch vụ
                                </span>

                                <strong>

                                    {{ $appointmentService
                                        ->serviceLabel(
                                            $appointment->service_type
                                        ) }}

                                </strong>

                            </div>

                        </div>



                        <div class="appointment-card-footer">

                            <a
                                href="{{ route(
                                    'appointments.show',
                                    $appointment
                                ) }}"
                                class="btn btn-primary btn-sm"
                            >
                                Xem chi tiết
                            </a>

                        </div>

                    </article>

                @endforeach

            </div>


            <div class="appointments-pagination">
                {{ $appointments->links() }}
            </div>

        @endif

    </div>

</section>

@endsection