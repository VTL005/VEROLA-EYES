@extends('layouts.staff')


@section(
    'title',
    'Hồ sơ thị lực #' . $eyePrescription->id
)


@section(
    'page-title',
    'Hồ sơ thị lực'
)


@section('content')

@php

    $formatPower = function ($value) {

        if ($value === null) {
            return '—';
        }

        $number = (float) $value;

        return ($number > 0 ? '+' : '')
            . number_format(
                $number,
                2,
                '.',
                ''
            );
    };

@endphp



{{-- HEADER --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            EYE PRESCRIPTION
        </span>

        <h1>
            Hồ sơ thị lực #{{ $eyePrescription->id }}
        </h1>

        <p>
            Ngày đo
            {{ $eyePrescription
                ->exam_date
                ->format('d/m/Y') }}
        </p>

    </div>


    <a
        href="{{ route(
            'staff.appointments.show',
            $eyePrescription->appointment
        ) }}"
        class="staff-btn staff-btn-secondary"
    >
        ← Lịch hẹn
    </a>

</div>



{{-- CUSTOMER BAR --}}

<div class="staff-eye-result-summary">

    <div>

        <span>
            Khách hàng
        </span>

        <strong>

            {{ $eyePrescription
                ->user
                ?->name
                ?? $eyePrescription
                    ->appointment
                    ?->customer_name
                ?? 'Không xác định' }}

        </strong>

    </div>


    <div>

        <span>
            Mã lịch
        </span>

        <strong>

            {{ $eyePrescription
                ->appointment
                ?->appointment_code
                ?? '—' }}

        </strong>

    </div>


    <div>

        <span>
            Người thực hiện
        </span>

        <strong>

            {{ $eyePrescription
                ->performer
                ?->name
                ?? 'Không xác định' }}

        </strong>

    </div>


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

</div>



{{-- EYE RESULT --}}

<div class="staff-eye-result-grid">


    {{-- RIGHT --}}

    <section class="staff-eye-result-card">

        <div class="staff-eye-result-heading">

            <div class="staff-eye-symbol">
                R
            </div>


            <div>

                <span>
                    RIGHT EYE
                </span>

                <h2>
                    Mắt phải
                </h2>

            </div>

        </div>


        <div class="staff-eye-result-values">

            <div>

                <span>
                    SPH
                </span>

                <strong>

                    {{ $formatPower(
                        $eyePrescription->right_sph
                    ) }}

                </strong>

                <small>
                    D
                </small>

            </div>


            <div>

                <span>
                    CYL
                </span>

                <strong>

                    {{ $formatPower(
                        $eyePrescription->right_cyl
                    ) }}

                </strong>

                <small>
                    D
                </small>

            </div>


            <div>

                <span>
                    AXIS
                </span>

                <strong>

                    {{ $eyePrescription
                        ->right_axis
                        !== null
                            ? $eyePrescription
                                ->right_axis
                            : '—' }}

                </strong>

                <small>
                    °
                </small>

            </div>

        </div>

    </section>



    {{-- LEFT --}}

    <section class="staff-eye-result-card">

        <div class="staff-eye-result-heading">

            <div class="staff-eye-symbol">
                L
            </div>


            <div>

                <span>
                    LEFT EYE
                </span>

                <h2>
                    Mắt trái
                </h2>

            </div>

        </div>


        <div class="staff-eye-result-values">

            <div>

                <span>
                    SPH
                </span>

                <strong>

                    {{ $formatPower(
                        $eyePrescription->left_sph
                    ) }}

                </strong>

                <small>
                    D
                </small>

            </div>


            <div>

                <span>
                    CYL
                </span>

                <strong>

                    {{ $formatPower(
                        $eyePrescription->left_cyl
                    ) }}

                </strong>

                <small>
                    D
                </small>

            </div>


            <div>

                <span>
                    AXIS
                </span>

                <strong>

                    {{ $eyePrescription
                        ->left_axis
                        !== null
                            ? $eyePrescription
                                ->left_axis
                            : '—' }}

                </strong>

                <small>
                    °
                </small>

            </div>

        </div>

    </section>

</div>



<div class="staff-eye-bottom-grid">


    {{-- COMMON --}}

    <section class="staff-form-card">

        <div class="staff-form-card-heading">

            <h2>
                Thông tin chung
            </h2>

        </div>


        <div class="staff-eye-common-result">

            <div>

                <span>
                    PD
                </span>

                <strong>

                    @if(
                        $eyePrescription->pd
                        !== null
                    )

                        {{ number_format(
                            (float) $eyePrescription->pd,
                            2,
                            '.',
                            ''
                        ) }}

                        <small>
                            mm
                        </small>

                    @else

                        —

                    @endif

                </strong>

            </div>


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
                    Người thực hiện
                </span>

                <strong>

                    {{ $eyePrescription
                        ->performer
                        ?->name
                        ?? 'Không xác định' }}

                </strong>

            </div>

        </div>

    </section>



    {{-- NOTE --}}

    <section class="staff-form-card">

        <div class="staff-form-card-heading">

            <h2>
                Ghi chú
            </h2>

        </div>


        @if($eyePrescription->note)

            <p class="staff-eye-result-note">
                {{ $eyePrescription->note }}
            </p>

        @else

            <p class="staff-eye-no-note">
                Không có ghi chú.
            </p>

        @endif

    </section>

</div>



<div class="staff-eye-readonly-notice">

    <strong>
        Hồ sơ chuyên môn
    </strong>

    <span>
        Hồ sơ này được lưu từ kết quả đo mắt
        và hiển thị cho Customer ở chế độ chỉ xem.
    </span>

</div>

@endsection