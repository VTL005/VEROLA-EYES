@extends('layouts.app')


@section(
    'title',
    'Kết quả đo mắt - VELORA Eyes'
)


@section('content')

@php

    $formatPower = function ($value) {

        if ($value === null) {
            return '—';
        }

        return sprintf(
            '%+.2f',
            (float) $value
        );
    };

@endphp



{{-- =========================================================
    HERO
========================================================= --}}

<section class="prescription-detail-hero">

    <div class="velora-container">

        <a
            href="{{ route(
                'eye-prescriptions.index'
            ) }}"
            class="prescription-back-link"
        >
            ← Hồ sơ thị lực của tôi
        </a>


        <div class="prescription-detail-hero-row">

            <div>

                <span class="hero-kicker">
                    EYE EXAM RESULT
                </span>

                <h1>
                    Kết quả đo mắt
                </h1>

                <p class="text-muted mb-0">

                    Ngày đo:

                    <strong>

                        {{ $eyePrescription
                            ->exam_date
                            ->format('d/m/Y') }}

                    </strong>

                </p>

            </div>


            <div class="prescription-detail-date">

                {{ $eyePrescription
                    ->exam_date
                    ->format('d/m/Y') }}

            </div>

        </div>

    </div>

</section>



<section class="section">

    <div class="velora-container">

        <div class="prescription-detail-layout">


            {{-- =================================================
                MAIN
            ================================================== --}}

            <main class="prescription-detail-main">


                {{-- =============================================
                    MEASUREMENT
                ============================================== --}}

                <div class="prescription-detail-card">

                    <div class="prescription-detail-heading">

                        <div>

                            <span class="hero-kicker">
                                PRESCRIPTION
                            </span>

                            <h2>
                                Thông số đo mắt
                            </h2>

                        </div>

                    </div>



                    <div class="prescription-detail-table">

                        <div class="prescription-detail-row prescription-detail-table-head">

                            <div>
                                Thông số
                            </div>

                            <div>
                                Mắt phải
                            </div>

                            <div>
                                Mắt trái
                            </div>

                        </div>



                        {{-- SPH --}}

                        <div class="prescription-detail-row">

                            <div>

                                <strong>
                                    SPH
                                </strong>

                                <small>
                                    Sphere
                                </small>

                            </div>


                            <div class="prescription-value">

                                {{ $formatPower(
                                    $eyePrescription->right_sph
                                ) }}

                            </div>


                            <div class="prescription-value">

                                {{ $formatPower(
                                    $eyePrescription->left_sph
                                ) }}

                            </div>

                        </div>



                        {{-- CYL --}}

                        <div class="prescription-detail-row">

                            <div>

                                <strong>
                                    CYL
                                </strong>

                                <small>
                                    Cylinder
                                </small>

                            </div>


                            <div class="prescription-value">

                                {{ $formatPower(
                                    $eyePrescription->right_cyl
                                ) }}

                            </div>


                            <div class="prescription-value">

                                {{ $formatPower(
                                    $eyePrescription->left_cyl
                                ) }}

                            </div>

                        </div>



                        {{-- AXIS --}}

                        <div class="prescription-detail-row">

                            <div>

                                <strong>
                                    AXIS
                                </strong>

                                <small>
                                    Trục
                                </small>

                            </div>


                            <div class="prescription-value">

                                {{ $eyePrescription->right_axis !== null
                                    ? $eyePrescription->right_axis . '°'
                                    : '—' }}

                            </div>


                            <div class="prescription-value">

                                {{ $eyePrescription->left_axis !== null
                                    ? $eyePrescription->left_axis . '°'
                                    : '—' }}

                            </div>

                        </div>

                    </div>



                    <div class="prescription-pd-card">

                        <div>

                            <span>
                                PD
                            </span>

                            <small>
                                Khoảng cách đồng tử
                            </small>

                        </div>


                        <strong>

                            {{ $eyePrescription->pd !== null
                                ? number_format(
                                    (float) $eyePrescription->pd,
                                    2
                                ) . ' mm'
                                : '—' }}

                        </strong>

                    </div>

                </div>



                {{-- =============================================
                    NOTE
                ============================================== --}}

                <div class="prescription-detail-card">

                    <h2>
                        Ghi chú
                    </h2>


                    @if($eyePrescription->note)

                        <div class="prescription-note">

                            {!! nl2br(
                                e($eyePrescription->note)
                            ) !!}

                        </div>

                    @else

                        <p class="text-muted mb-0">
                            Không có ghi chú bổ sung
                            cho lần đo này.
                        </p>

                    @endif

                </div>



                {{-- =============================================
                    NOTICE
                ============================================== --}}

                <div class="prescription-readonly-notice">

                    <div>
                        i
                    </div>

                    <p>

                        <strong>
                            Hồ sơ chỉ đọc
                        </strong>

                        Các thông số đo mắt được lưu
                        bởi nhân viên VELORA và Customer
                        không thể tự chỉnh sửa.

                    </p>

                </div>

            </main>



            {{-- =================================================
                SIDEBAR
            ================================================== --}}

            <aside class="prescription-detail-sidebar">


                {{-- EXAM INFO --}}

                <div class="prescription-detail-card">

                    <h2>
                        Thông tin lần đo
                    </h2>


                    <div class="prescription-meta-list">


                        <div>

                            <span>
                                Ngày đo
                            </span>

                            <strong>

                                {{ $eyePrescription
                                    ->exam_date
                                    ->format('d/m/Y') }}

                            </strong>

                        </div>



                        <div>

                            <span>
                                Nhân viên thực hiện
                            </span>

                            <strong>

                                {{ $eyePrescription
                                    ->performer
                                    ->name ?? 'VELORA Eyes' }}

                            </strong>

                        </div>



                        @if(
                            $eyePrescription->appointment
                        )

                            <div>

                                <span>
                                    Mã lịch hẹn
                                </span>

                                <strong>

                                    {{ $eyePrescription
                                        ->appointment
                                        ->appointment_code }}

                                </strong>

                            </div>

                        @endif

                    </div>

                </div>



                {{-- APPOINTMENT --}}

                @if(
                    $eyePrescription->appointment
                )

                    <a
                        href="{{ route(
                            'appointments.show',
                            $eyePrescription->appointment
                        ) }}"
                        class="btn btn-outline"
                        style="width:100%;"
                    >
                        Xem lịch hẹn liên quan
                    </a>

                @endif



                <a
                    href="{{ route(
                        'appointments.create'
                    ) }}"
                    class="btn btn-primary"
                    style="width:100%;"
                >
                    + Đặt lịch đo mắt mới
                </a>


                <a
                    href="{{ route(
                        'eye-prescriptions.index'
                    ) }}"
                    class="btn btn-outline"
                    style="width:100%;"
                >
                    ← Quay lại lịch sử
                </a>

            </aside>

        </div>

    </div>

</section>

@endsection