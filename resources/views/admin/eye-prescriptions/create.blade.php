@extends('layouts.admin')


@section(
    'title',
    'Nhập kết quả đo mắt - VELORA Eyes'
)


@section(
    'page-title',
    'Nhập kết quả đo mắt'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            EYE EXAM RESULT
        </span>

        <h1>
            Nhập kết quả đo mắt
        </h1>

        <p>
            {{ $appointment->appointment_code }}
            · {{ $appointment->customer_name }}
        </p>

    </div>


    <a
        href="{{ route(
            'admin.appointments.show',
            $appointment
        ) }}"
        class="admin-btn admin-btn-secondary"
    >
        <i class="bi bi-arrow-left"></i>

        Lịch hẹn
    </a>

</div>



<div class="admin-eye-appointment-card">

    <div>

        <i class="bi bi-person"></i>

        <span>
            <small>Khách hàng</small>
            <strong>{{ $appointment->customer_name }}</strong>
        </span>

    </div>


    <div>

        <i class="bi bi-calendar3"></i>

        <span>
            <small>Ngày hẹn</small>

            <strong>
                {{ $appointment
                    ->appointment_date
                    ->format('d/m/Y') }}
            </strong>
        </span>

    </div>


    <div>

        <i class="bi bi-clock"></i>

        <span>
            <small>Khung giờ</small>
            <strong>{{ $appointment->time_slot }}</strong>
        </span>

    </div>

</div>



<form
    action="{{ route(
        'admin.eye-prescriptions.store',
        $appointment
    ) }}"
    method="POST"
    class="admin-eye-form-layout"
>

    @csrf


    <div class="admin-eye-form-main">


        {{-- TWO EYES --}}

        <div class="admin-eye-two-columns">


            {{-- RIGHT --}}

            <section class="admin-eye-result-card">

                <div class="admin-eye-result-heading">

                    <div>
                        <i class="bi bi-eye"></i>
                    </div>

                    <span>
                        <strong>Mắt phải</strong>
                        <small>RIGHT / OD</small>
                    </span>

                </div>


                <div class="admin-eye-result-fields">

                    <div class="admin-form-group">

                        <label for="right_sph">
                            SPH
                        </label>

                        <input
                            type="number"
                            step="0.25"
                            id="right_sph"
                            name="right_sph"
                            value="{{ old('right_sph') }}"
                            class="admin-form-control"
                            placeholder="VD: -2.50"
                        >

                        @error('right_sph')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="right_cyl">
                            CYL
                        </label>

                        <input
                            type="number"
                            step="0.25"
                            id="right_cyl"
                            name="right_cyl"
                            value="{{ old('right_cyl') }}"
                            class="admin-form-control"
                            placeholder="VD: -0.75"
                        >

                        @error('right_cyl')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="right_axis">
                            AXIS
                        </label>

                        <input
                            type="number"
                            min="0"
                            max="180"
                            id="right_axis"
                            name="right_axis"
                            value="{{ old('right_axis') }}"
                            class="admin-form-control"
                            placeholder="0 - 180"
                        >

                        @error('right_axis')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </section>



            {{-- LEFT --}}

            <section class="admin-eye-result-card">

                <div class="admin-eye-result-heading">

                    <div>
                        <i class="bi bi-eye"></i>
                    </div>

                    <span>
                        <strong>Mắt trái</strong>
                        <small>LEFT / OS</small>
                    </span>

                </div>


                <div class="admin-eye-result-fields">

                    <div class="admin-form-group">

                        <label for="left_sph">
                            SPH
                        </label>

                        <input
                            type="number"
                            step="0.25"
                            id="left_sph"
                            name="left_sph"
                            value="{{ old('left_sph') }}"
                            class="admin-form-control"
                            placeholder="VD: -2.25"
                        >

                        @error('left_sph')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="left_cyl">
                            CYL
                        </label>

                        <input
                            type="number"
                            step="0.25"
                            id="left_cyl"
                            name="left_cyl"
                            value="{{ old('left_cyl') }}"
                            class="admin-form-control"
                            placeholder="VD: -0.50"
                        >

                        @error('left_cyl')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="left_axis">
                            AXIS
                        </label>

                        <input
                            type="number"
                            min="0"
                            max="180"
                            id="left_axis"
                            name="left_axis"
                            value="{{ old('left_axis') }}"
                            class="admin-form-control"
                            placeholder="0 - 180"
                        >

                        @error('left_axis')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </section>

        </div>



        {{-- GENERAL --}}

        <section class="admin-panel">

            <div class="admin-panel-header">

                <div>
                    <h2>Thông tin chung</h2>
                </div>

            </div>


            <div class="admin-form-body">

                <div class="admin-form-grid">

                    <div class="admin-form-group">

                        <label for="pd">
                            PD - Khoảng cách đồng tử
                        </label>

                        <input
                            type="number"
                            step="0.1"
                            id="pd"
                            name="pd"
                            value="{{ old('pd') }}"
                            class="admin-form-control"
                            placeholder="Ví dụ: 62"
                        >

                        @error('pd')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="admin-form-group">

                        <label for="exam_date">
                            Ngày đo
                            <span>*</span>
                        </label>

                        <input
                            type="date"
                            id="exam_date"
                            name="exam_date"
                            value="{{ old(
                                'exam_date',
                                now()->format('Y-m-d')
                            ) }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="admin-form-control"
                            required
                        >

                        @error('exam_date')
                            <div class="admin-field-error">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                <div class="admin-form-group admin-eye-note">

                    <label for="note">
                        Ghi chú chuyên môn
                    </label>

                    <textarea
                        id="note"
                        name="note"
                        rows="5"
                        maxlength="2000"
                        class="admin-form-control"
                        placeholder="Nhập ghi chú về kết quả đo mắt..."
                    >{{ old('note') }}</textarea>

                    @error('note')
                        <div class="admin-field-error">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </section>

    </div>



    <aside class="admin-eye-form-sidebar">

        <section class="admin-eye-guide">

            <i class="bi bi-info-circle"></i>

            <div>

                <strong>
                    Thông tin độ kính
                </strong>

                <span>
                    SPH: độ cầu<br>
                    CYL: độ loạn<br>
                    AXIS: trục loạn 0–180°<br>
                    PD: khoảng cách đồng tử
                </span>

            </div>

        </section>


        <section class="admin-panel">

            <div class="admin-panel-header">
                <div>
                    <h2>Người thực hiện</h2>
                </div>
            </div>


            <div class="admin-eye-performer">

                <div>
                    {{ strtoupper(
                        substr(
                            auth()->user()->name,
                            0,
                            1
                        )
                    ) }}
                </div>

                <span>

                    <strong>
                        {{ auth()->user()->name }}
                    </strong>

                    <small>
                        Administrator
                    </small>

                </span>

            </div>

        </section>


        <section class="admin-panel admin-form-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary admin-btn-full"
            >
                <i class="bi bi-check-lg"></i>

                Lưu kết quả
            </button>


            <a
                href="{{ route(
                    'admin.appointments.show',
                    $appointment
                ) }}"
                class="admin-btn admin-btn-secondary admin-btn-full"
            >
                Hủy
            </a>

        </section>

    </aside>

</form>

@endsection