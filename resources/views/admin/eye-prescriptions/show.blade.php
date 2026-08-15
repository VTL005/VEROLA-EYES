@extends('layouts.admin')


@section(
    'title',
    'Hồ sơ đo mắt #' . $eyePrescription->id
)


@section(
    'page-title',
    'Chi tiết hồ sơ đo mắt'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            EYE PRESCRIPTION DETAIL
        </span>

        <h1>
            Hồ sơ đo mắt #{{ $eyePrescription->id }}
        </h1>

        <p>

            Ngày đo
            {{ $eyePrescription
                ->exam_date
                ->format('d/m/Y') }}

        </p>

    </div>


    <div class="admin-eye-header-actions">

        <a
            href="{{ route(
                'admin.eye-prescriptions.index'
            ) }}"
            class="admin-btn admin-btn-secondary"
        >
            <i class="bi bi-arrow-left"></i>

            Danh sách
        </a>


        @if($eyePrescription->appointment)

            <a
                href="{{ route(
                    'admin.appointments.show',
                    $eyePrescription->appointment
                ) }}"
                class="admin-btn admin-btn-secondary"
            >
                <i class="bi bi-calendar3"></i>

                Lịch hẹn
            </a>

        @endif

    </div>

</div>



<div class="admin-eye-detail-summary">

    <div>

        <span>
            Khách hàng
        </span>

        <strong>
            {{ $eyePrescription
                ->user
                ?->name
                ?? '—' }}
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
            PD
        </span>

        <strong>
            {{ $eyePrescription->pd ?? '—' }}
        </strong>

    </div>


    <div>

        <span>
            Thực hiện bởi
        </span>

        <strong>
            {{ $eyePrescription
                ->performer
                ?->name
                ?? '—' }}
        </strong>

    </div>

</div>



<div class="admin-eye-detail-layout">


    <div class="admin-eye-detail-main">


        <div class="admin-eye-two-columns">


            <section class="admin-eye-detail-card">

                <div class="admin-eye-detail-card-header">

                    <div>
                        <i class="bi bi-eye"></i>
                    </div>

                    <span>

                        <strong>
                            Mắt phải
                        </strong>

                        <small>
                            RIGHT / OD
                        </small>

                    </span>

                </div>


                <div class="admin-eye-values">

                    <div>
                        <span>SPH</span>
                        <strong>
                            {{ $eyePrescription->right_sph ?? '—' }}
                        </strong>
                    </div>

                    <div>
                        <span>CYL</span>
                        <strong>
                            {{ $eyePrescription->right_cyl ?? '—' }}
                        </strong>
                    </div>

                    <div>
                        <span>AXIS</span>
                        <strong>
                            {{ $eyePrescription->right_axis ?? '—' }}
                        </strong>
                    </div>

                </div>

            </section>



            <section class="admin-eye-detail-card">

                <div class="admin-eye-detail-card-header">

                    <div>
                        <i class="bi bi-eye"></i>
                    </div>

                    <span>

                        <strong>
                            Mắt trái
                        </strong>

                        <small>
                            LEFT / OS
                        </small>

                    </span>

                </div>


                <div class="admin-eye-values">

                    <div>
                        <span>SPH</span>
                        <strong>
                            {{ $eyePrescription->left_sph ?? '—' }}
                        </strong>
                    </div>

                    <div>
                        <span>CYL</span>
                        <strong>
                            {{ $eyePrescription->left_cyl ?? '—' }}
                        </strong>
                    </div>

                    <div>
                        <span>AXIS</span>
                        <strong>
                            {{ $eyePrescription->left_axis ?? '—' }}
                        </strong>
                    </div>

                </div>

            </section>

        </div>



        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Ghi chú chuyên môn</h2>
                </div>
            </div>


            <div class="admin-eye-note-content">

                {{ $eyePrescription->note
                    ?: 'Không có ghi chú.' }}

            </div>

        </section>

    </div>



    <aside class="admin-eye-detail-sidebar">


        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Khách hàng</h2>
                </div>
            </div>


            <div class="admin-eye-profile">

                <div>
                    <i class="bi bi-person"></i>
                </div>

                <span>

                    <strong>
                        {{ $eyePrescription
                            ->user
                            ?->name
                            ?? '—' }}
                    </strong>

                    <small>
                        {{ $eyePrescription
                            ->user
                            ?->phone
                            ?? '—' }}
                    </small>

                    <small>
                        {{ $eyePrescription
                            ->user
                            ?->email
                            ?? '—' }}
                    </small>

                </span>

            </div>

        </section>



        @if($eyePrescription->appointment)

            <section class="admin-panel">

                <div class="admin-panel-header">
                    <div>
                        <h2>Lịch hẹn</h2>
                    </div>
                </div>


                <div class="admin-eye-meta">

                    <span>

                        Mã lịch

                        <strong>
                            {{ $eyePrescription
                                ->appointment
                                ->appointment_code }}
                        </strong>

                    </span>


                    <span>

                        Ngày hẹn

                        <strong>
                            {{ $eyePrescription
                                ->appointment
                                ->appointment_date
                                ->format('d/m/Y') }}
                        </strong>

                    </span>


                    <span>

                        Khung giờ

                        <strong>
                            {{ $eyePrescription
                                ->appointment
                                ->time_slot }}
                        </strong>

                    </span>

                </div>

            </section>

        @endif



        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Thông tin hồ sơ</h2>
                </div>
            </div>


            <div class="admin-eye-meta">

                <span>

                    Ngày đo

                    <strong>
                        {{ $eyePrescription
                            ->exam_date
                            ->format('d/m/Y') }}
                    </strong>

                </span>


                <span>

                    Người thực hiện

                    <strong>
                        {{ $eyePrescription
                            ->performer
                            ?->name
                            ?? '—' }}
                    </strong>

                </span>


                <span>

                    Ngày tạo

                    <strong>
                        {{ $eyePrescription
                            ->created_at
                            ->format('H:i d/m/Y') }}
                    </strong>

                </span>

            </div>

        </section>

    </aside>

</div>

@endsection