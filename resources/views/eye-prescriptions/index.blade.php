@extends('layouts.app')


@section('title', 'Hồ sơ thị lực của tôi - VELORA Eyes')


@section('content')

@php

    /*
     * Format thông số SPH/CYL.
     *
     * Ví dụ:
     * -2.00
     * +1.25
     */
    $formatPower = function ($value) {

        if ($value === null) {
            return '—';
        }

        return sprintf(
            '%+.2f',
            (float) $value
        );
    };


    $latestPrescription =
        $prescriptions->first();

@endphp



{{-- =========================================================
    HERO
========================================================= --}}

<section class="prescription-hero">

    <div class="velora-container">

        <span class="hero-kicker">
            VISION PROFILE
        </span>

        <h1>
            Hồ sơ thị lực của tôi
        </h1>

        <p class="text-muted mb-0">
            Theo dõi lịch sử kết quả đo mắt
            của bạn tại VELORA Eyes.
        </p>

    </div>

</section>



<section class="section">

    <div class="velora-container">


        {{-- =================================================
            ACTION BAR
        ================================================== --}}

        <div class="prescription-page-heading">

            <div>

                <strong>
                    {{ $prescriptions->total() }}
                </strong>

                <span class="text-muted">
                    kết quả đo mắt
                </span>

            </div>


            <a
                href="{{ route('appointments.create') }}"
                class="btn btn-primary"
            >
                + Đặt lịch đo mắt
            </a>

        </div>



        {{-- =================================================
            FILTER
        ================================================== --}}

        <div class="prescription-filter-card">

            <form
                action="{{ route(
                    'eye-prescriptions.index'
                ) }}"
                method="GET"
                class="prescription-filter-form"
            >

                <div>

                    <label
                        for="exam_date"
                        class="form-label"
                    >
                        Lọc theo ngày đo
                    </label>


                    <input
                        type="date"
                        id="exam_date"
                        name="exam_date"
                        class="form-control"
                        value="{{ request('exam_date') }}"
                    >

                </div>


                <div class="prescription-filter-actions">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Lọc
                    </button>


                    @if(request('exam_date'))

                        <a
                            href="{{ route(
                                'eye-prescriptions.index'
                            ) }}"
                            class="btn btn-outline"
                        >
                            Đặt lại
                        </a>

                    @endif

                </div>

            </form>

        </div>



        {{-- =================================================
            EMPTY
        ================================================== --}}

        @if($prescriptions->isEmpty())

            <div class="prescription-empty">

                <div class="prescription-empty-icon">
                    ◉
                </div>


                @if(request('exam_date'))

                    <h2>
                        Không tìm thấy kết quả đo mắt
                    </h2>

                    <p>
                        Không có hồ sơ nào vào ngày
                        bạn đã chọn.
                    </p>


                    <a
                        href="{{ route(
                            'eye-prescriptions.index'
                        ) }}"
                        class="btn btn-outline"
                    >
                        Xem tất cả hồ sơ
                    </a>

                @else

                    <h2>
                        Bạn chưa có hồ sơ đo mắt
                    </h2>

                    <p>
                        Sau khi thực hiện đo mắt tại
                        VELORA, kết quả sẽ được lưu
                        tại đây.
                    </p>


                    <a
                        href="{{ route(
                            'appointments.create'
                        ) }}"
                        class="btn btn-primary"
                    >
                        Đặt lịch đo mắt
                    </a>

                @endif

            </div>

        @else


            {{-- =================================================
                LATEST RESULT
            ================================================== --}}

            @if(
                !request('exam_date')
                && $latestPrescription
            )

                <div class="prescription-latest">

                    <div class="prescription-latest-heading">

                        <div>

                            <span class="hero-kicker">
                                LATEST RESULT
                            </span>

                            <h2>
                                Kết quả đo gần nhất
                            </h2>

                        </div>


                        <span class="prescription-date-badge">

                            {{ $latestPrescription
                                ->exam_date
                                ->format('d/m/Y') }}

                        </span>

                    </div>



                    <div class="prescription-eye-table">

                        <div class="prescription-eye-row prescription-eye-header">

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


                        <div class="prescription-eye-row">

                            <div>
                                SPH
                            </div>

                            <strong>

                                {{ $formatPower(
                                    $latestPrescription->right_sph
                                ) }}

                            </strong>

                            <strong>

                                {{ $formatPower(
                                    $latestPrescription->left_sph
                                ) }}

                            </strong>

                        </div>


                        <div class="prescription-eye-row">

                            <div>
                                CYL
                            </div>

                            <strong>

                                {{ $formatPower(
                                    $latestPrescription->right_cyl
                                ) }}

                            </strong>

                            <strong>

                                {{ $formatPower(
                                    $latestPrescription->left_cyl
                                ) }}

                            </strong>

                        </div>


                        <div class="prescription-eye-row">

                            <div>
                                AXIS
                            </div>

                            <strong>

                                {{ $latestPrescription->right_axis !== null
                                    ? $latestPrescription->right_axis . '°'
                                    : '—' }}

                            </strong>

                            <strong>

                                {{ $latestPrescription->left_axis !== null
                                    ? $latestPrescription->left_axis . '°'
                                    : '—' }}

                            </strong>

                        </div>

                    </div>



                    <div class="prescription-latest-footer">

                        <div>

                            <span>
                                PD
                            </span>

                            <strong>

                                {{ $latestPrescription->pd !== null
                                    ? number_format(
                                        (float) $latestPrescription->pd,
                                        2
                                    ) . ' mm'
                                    : '—' }}

                            </strong>

                        </div>


                        <div>

                            <span>
                                Nhân viên thực hiện
                            </span>

                            <strong>

                                {{ $latestPrescription
                                    ->performer
                                    ->name ?? 'VELORA Eyes' }}

                            </strong>

                        </div>


                        <a
                            href="{{ route(
                                'eye-prescriptions.show',
                                $latestPrescription
                            ) }}"
                            class="btn btn-primary"
                        >
                            Xem chi tiết
                        </a>

                    </div>

                </div>

            @endif



            {{-- =================================================
                HISTORY
            ================================================== --}}

            <div class="prescription-history-heading">

                <h2>
                    Lịch sử đo mắt
                </h2>

                <p>
                    Toàn bộ kết quả đã được lưu
                    trong hồ sơ của bạn.
                </p>

            </div>


            <div class="prescription-list">

                @foreach(
                    $prescriptions
                    as $prescription
                )

                    <article class="prescription-card">

                        <div class="prescription-card-header">

                            <div>

                                <span>
                                    Ngày đo
                                </span>

                                <strong>

                                    {{ $prescription
                                        ->exam_date
                                        ->format('d/m/Y') }}

                                </strong>

                            </div>


                            @if($prescription->appointment)

                                <a
                                    href="{{ route(
                                        'appointments.show',
                                        $prescription->appointment
                                    ) }}"
                                    class="prescription-appointment-code"
                                >

                                    {{ $prescription
                                        ->appointment
                                        ->appointment_code }}

                                </a>

                            @endif

                        </div>



                        <div class="prescription-card-measurements">

                            <div>

                                <span>
                                    Mắt phải · SPH
                                </span>

                                <strong>

                                    {{ $formatPower(
                                        $prescription->right_sph
                                    ) }}

                                </strong>

                            </div>


                            <div>

                                <span>
                                    Mắt trái · SPH
                                </span>

                                <strong>

                                    {{ $formatPower(
                                        $prescription->left_sph
                                    ) }}

                                </strong>

                            </div>


                            <div>

                                <span>
                                    PD
                                </span>

                                <strong>

                                    {{ $prescription->pd !== null
                                        ? number_format(
                                            (float) $prescription->pd,
                                            2
                                        ) . ' mm'
                                        : '—' }}

                                </strong>

                            </div>

                        </div>



                        <div class="prescription-card-footer">

                            <div>

                                <span>
                                    Thực hiện bởi
                                </span>

                                <strong>

                                    {{ $prescription
                                        ->performer
                                        ->name ?? 'VELORA Eyes' }}

                                </strong>

                            </div>


                            <a
                                href="{{ route(
                                    'eye-prescriptions.show',
                                    $prescription
                                ) }}"
                                class="btn btn-outline btn-sm"
                            >
                                Xem chi tiết
                            </a>

                        </div>

                    </article>

                @endforeach

            </div>



            <div class="prescription-pagination">

                {{ $prescriptions->links() }}

            </div>

        @endif

    </div>

</section>

@endsection