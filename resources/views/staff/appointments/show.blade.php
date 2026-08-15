@extends('layouts.staff')


@section(
    'title',
    $appointment->appointment_code . ' - Staff'
)


@section(
    'page-title',
    'Chi tiết lịch hẹn'
)


@section('content')


<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            APPOINTMENT
        </span>

        <h1>
            {{ $appointment->appointment_code }}
        </h1>

        <p>
            {{ $appointmentService
                ->serviceLabel(
                    $appointment->service_type
                ) }}
        </p>

    </div>


    <a
        href="{{ route(
            'staff.appointments.index'
        ) }}"
        class="staff-btn staff-btn-secondary"
    >
        ← Danh sách lịch
    </a>

</div>



{{-- STATUS BAR --}}

<div class="staff-appointment-status-bar">

    <div>

        <span>
            Trạng thái
        </span>

        <strong>
            {{ $appointmentService
                ->statusLabel(
                    $appointment->status
                ) }}
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

</div>



<div class="staff-appointment-detail-layout">


    {{-- MAIN --}}

    <div class="staff-appointment-detail-main">


        {{-- CUSTOMER --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thông tin khách hàng
                </h2>

            </div>


            <div class="staff-appointment-info-grid">

                <div>

                    <span>
                        Họ tên
                    </span>

                    <strong>
                        {{ $appointment
                            ->customer_name }}
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


                <div>

                    <span>
                        Email
                    </span>

                    <strong>
                        {{ $appointment->email }}
                    </strong>

                </div>


                <div>

                    <span>
                        Tài khoản
                    </span>

                    <strong>

                        {{ $appointment
                            ->user
                            ?->name
                            ?? 'Không xác định' }}

                    </strong>

                </div>

            </div>

        </section>



        {{-- APPOINTMENT --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thông tin lịch hẹn
                </h2>

            </div>


            <div class="staff-appointment-info-grid">

                <div>

                    <span>
                        Dịch vụ
                    </span>

                    <strong>

                        {{ $appointmentService
                            ->serviceLabel(
                                $appointment
                                    ->service_type
                            ) }}

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
                        Khung giờ
                    </span>

                    <strong>
                        {{ $appointment
                            ->time_slot }}
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
                            ?? 'Chưa xác nhận' }}

                    </strong>

                </div>

            </div>


            @if($appointment->note)

                <div class="staff-appointment-note">

                    <span>
                        Ghi chú
                    </span>

                    <p>
                        {{ $appointment->note }}
                    </p>

                </div>

            @endif

        </section>



        {{-- EYE PRESCRIPTIONS --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading staff-appointment-section-heading">

                <div>

                    <h2>
                        Hồ sơ thị lực
                    </h2>

                    <p>
                        Kết quả đo mắt từ lịch hẹn này.
                    </p>

                </div>


                @if(
                    in_array(
                        $appointment->status,
                        [
                            'confirmed',
                            'completed',
                        ],
                        true
                    )
                )

                    <a
                        href="{{ route(
                            'staff.eye-prescriptions.create',
                            $appointment
                        ) }}"
                        class="staff-btn staff-btn-primary"
                    >
                        + Nhập kết quả
                    </a>

                @endif

            </div>


            @if(
                $appointment
                    ->eyePrescriptions
                    ->isEmpty()
            )

                <div class="staff-appointment-prescription-empty">

                    <div>
                        ◉
                    </div>

                    <strong>
                        Chưa có hồ sơ thị lực
                    </strong>

                    <span>
                        Kết quả đo mắt sẽ xuất hiện tại đây.
                    </span>

                </div>

            @else

                <div class="staff-prescription-list">

                    @foreach(
                        $appointment
                            ->eyePrescriptions
                        as $prescription
                    )

                        <a
                            href="{{ route(
                                'staff.eye-prescriptions.show',
                                $prescription
                            ) }}"
                            class="staff-prescription-list-item"
                        >

                            <div class="staff-prescription-icon">
                                ◉
                            </div>


                            <div>

                                <strong>
                                    Hồ sơ thị lực #{{ $prescription->id }}
                                </strong>

                                <span>

                                    Ngày đo:
                                    {{ $prescription
                                        ->exam_date
                                        ->format('d/m/Y') }}

                                </span>

                            </div>


                            <span class="staff-prescription-arrow">
                                →
                            </span>

                        </a>

                    @endforeach

                </div>

            @endif

        </section>

    </div>



    {{-- SIDEBAR --}}

    <aside class="staff-appointment-detail-sidebar">


        {{-- STATUS --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Xử lý lịch hẹn
                </h2>

            </div>


            @if(!empty($nextStatuses))

                <form
                    action="{{ route(
                        'staff.appointments.update-status',
                        $appointment
                    ) }}"
                    method="POST"
                >

                    @csrf
                    @method('PATCH')


                    <div class="staff-form-group">

                        <label for="status">
                            Trạng thái tiếp theo
                        </label>


                        <select
                            id="status"
                            name="status"
                            class="staff-form-control"
                            required
                        >

                            <option value="">
                                -- Chọn trạng thái --
                            </option>


                            @foreach(
                                $nextStatuses
                                as $status
                            )

                                <option
                                    value="{{ $status }}"
                                >

                                    {{ $appointmentService
                                        ->statusLabel(
                                            $status
                                        ) }}

                                </option>

                            @endforeach

                        </select>


                        @error('status')

                            <div class="staff-field-error">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    <button
                        type="submit"
                        class="staff-btn staff-btn-primary staff-product-full-button"
                    >
                        Cập nhật trạng thái
                    </button>

                </form>

            @else

                <div class="staff-appointment-final">

                    @switch(
                        $appointment->status
                    )

                        @case('completed')

                            <strong>
                                ✓ Lịch hẹn đã hoàn thành
                            </strong>

                            @break


                        @case('cancelled')

                            <strong>
                                Lịch hẹn đã bị hủy
                            </strong>

                            @break


                        @case('no_show')

                            <strong>
                                Khách hàng không đến
                            </strong>

                            @break

                    @endswitch

                </div>

            @endif

        </section>



        {{-- APPOINTMENT META --}}

        <section class="staff-form-card staff-appointment-meta">

            <span>
                Mã lịch
            </span>

            <strong>
                {{ $appointment
                    ->appointment_code }}
            </strong>


            <span>
                Tạo lúc
            </span>

            <strong>

                {{ $appointment
                    ->created_at
                    ->format(
                        'd/m/Y H:i'
                    ) }}

            </strong>


            @if(
                $appointment
                    ->confirmed_at
            )

                <span>
                    Xác nhận lúc
                </span>

                <strong>

                    {{ $appointment
                        ->confirmed_at
                        ->format(
                            'd/m/Y H:i'
                        ) }}

                </strong>

            @endif


            <span>
                Hồ sơ thị lực
            </span>

            <strong>
                {{ $appointment
                    ->eyePrescriptions
                    ->count() }}
            </strong>

        </section>



        {{-- PRESCRIPTION SHORTCUT --}}

        @if(
            in_array(
                $appointment->status,
                [
                    'confirmed',
                    'completed',
                ],
                true
            )
        )

            <section class="staff-form-card">

                <div class="staff-form-card-heading">

                    <h2>
                        Đo mắt
                    </h2>

                    <p>
                        Nhập SPH, CYL, AXIS
                        và khoảng cách đồng tử.
                    </p>

                </div>


                <a
                    href="{{ route(
                        'staff.eye-prescriptions.create',
                        $appointment
                    ) }}"
                    class="staff-btn staff-btn-primary staff-product-full-button"
                >
                    Nhập kết quả đo
                </a>

            </section>

        @endif

    </aside>

</div>

@endsection