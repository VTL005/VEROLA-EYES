@extends('layouts.staff')


@section(
    'title',
    'Nhập kết quả đo mắt - Staff'
)


@section(
    'page-title',
    'Nhập kết quả đo mắt'
)


@section('content')


{{-- =========================================================
    HEADER
========================================================= --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
            EYE PRESCRIPTION
        </span>

        <h1>
            Nhập kết quả đo mắt
        </h1>

        <p>
            Hồ sơ thị lực của
            {{ $appointment->customer_name }}
        </p>

    </div>


    <a
        href="{{ route(
            'staff.appointments.show',
            $appointment
        ) }}"
        class="staff-btn staff-btn-secondary"
    >
        ← Lịch hẹn
    </a>

</div>



{{-- =========================================================
    APPOINTMENT INFO
========================================================= --}}

<div class="staff-eye-appointment-summary">

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
            Khách hàng
        </span>

        <strong>
            {{ $appointment->customer_name }}
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



@if($errors->has('prescription'))

    <div class="staff-eye-validation-alert">

        {{ $errors->first(
            'prescription'
        ) }}

    </div>

@endif


@if($errors->has('appointment'))

    <div class="staff-eye-validation-alert">

        {{ $errors->first(
            'appointment'
        ) }}

    </div>

@endif



<form
    action="{{ route(
        'staff.eye-prescriptions.store',
        $appointment
    ) }}"
    method="POST"
    class="staff-eye-form-layout"
>

    @csrf



    <div class="staff-eye-form-main">


        {{-- =================================================
            TWO EYES
        ================================================== --}}

        <div class="staff-eye-columns">


            {{-- RIGHT EYE --}}

            <section class="staff-eye-card right">

                <div class="staff-eye-card-head">

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



                <div class="staff-eye-field">

                    <label for="right_sph">

                        SPH

                        <small>
                            Độ cầu
                        </small>

                    </label>


                    <div class="staff-eye-input-unit">

                        <input
                            type="number"
                            id="right_sph"
                            name="right_sph"
                            value="{{ old(
                                'right_sph'
                            ) }}"
                            min="-30"
                            max="30"
                            step="0.01"
                            class="staff-form-control
                                @error('right_sph')
                                    staff-input-error
                                @enderror"
                            placeholder="0.00"
                        >

                        <span>
                            D
                        </span>

                    </div>


                    @error('right_sph')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <div class="staff-eye-field">

                    <label for="right_cyl">

                        CYL

                        <small>
                            Độ loạn
                        </small>

                    </label>


                    <div class="staff-eye-input-unit">

                        <input
                            type="number"
                            id="right_cyl"
                            name="right_cyl"
                            value="{{ old(
                                'right_cyl'
                            ) }}"
                            min="-15"
                            max="15"
                            step="0.01"
                            class="staff-form-control
                                @error('right_cyl')
                                    staff-input-error
                                @enderror"
                            placeholder="0.00"
                        >

                        <span>
                            D
                        </span>

                    </div>


                    @error('right_cyl')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <div class="staff-eye-field">

                    <label for="right_axis">

                        AXIS

                        <small>
                            Trục loạn
                        </small>

                    </label>


                    <div class="staff-eye-input-unit">

                        <input
                            type="number"
                            id="right_axis"
                            name="right_axis"
                            value="{{ old(
                                'right_axis'
                            ) }}"
                            min="0"
                            max="180"
                            step="1"
                            class="staff-form-control
                                @error('right_axis')
                                    staff-input-error
                                @enderror"
                            placeholder="0"
                        >

                        <span>
                            °
                        </span>

                    </div>


                    @error('right_axis')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </section>



            {{-- LEFT EYE --}}

            <section class="staff-eye-card left">

                <div class="staff-eye-card-head">

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



                <div class="staff-eye-field">

                    <label for="left_sph">

                        SPH

                        <small>
                            Độ cầu
                        </small>

                    </label>


                    <div class="staff-eye-input-unit">

                        <input
                            type="number"
                            id="left_sph"
                            name="left_sph"
                            value="{{ old(
                                'left_sph'
                            ) }}"
                            min="-30"
                            max="30"
                            step="0.01"
                            class="staff-form-control
                                @error('left_sph')
                                    staff-input-error
                                @enderror"
                            placeholder="0.00"
                        >

                        <span>
                            D
                        </span>

                    </div>


                    @error('left_sph')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <div class="staff-eye-field">

                    <label for="left_cyl">

                        CYL

                        <small>
                            Độ loạn
                        </small>

                    </label>


                    <div class="staff-eye-input-unit">

                        <input
                            type="number"
                            id="left_cyl"
                            name="left_cyl"
                            value="{{ old(
                                'left_cyl'
                            ) }}"
                            min="-15"
                            max="15"
                            step="0.01"
                            class="staff-form-control
                                @error('left_cyl')
                                    staff-input-error
                                @enderror"
                            placeholder="0.00"
                        >

                        <span>
                            D
                        </span>

                    </div>


                    @error('left_cyl')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <div class="staff-eye-field">

                    <label for="left_axis">

                        AXIS

                        <small>
                            Trục loạn
                        </small>

                    </label>


                    <div class="staff-eye-input-unit">

                        <input
                            type="number"
                            id="left_axis"
                            name="left_axis"
                            value="{{ old(
                                'left_axis'
                            ) }}"
                            min="0"
                            max="180"
                            step="1"
                            class="staff-form-control
                                @error('left_axis')
                                    staff-input-error
                                @enderror"
                            placeholder="0"
                        >

                        <span>
                            °
                        </span>

                    </div>


                    @error('left_axis')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </section>

        </div>



        {{-- =================================================
            COMMON INFORMATION
        ================================================== --}}

        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Thông tin chung
                </h2>

                <p>
                    PD, ngày thực hiện và ghi chú chuyên môn.
                </p>

            </div>


            <div class="staff-eye-common-grid">

                <div class="staff-form-group">

                    <label for="pd">

                        PD

                        <span>
                            Khoảng cách đồng tử
                        </span>

                    </label>


                    <div class="staff-eye-input-unit">

                        <input
                            type="number"
                            id="pd"
                            name="pd"
                            value="{{ old('pd') }}"
                            min="30"
                            max="100"
                            step="0.01"
                            class="staff-form-control
                                @error('pd')
                                    staff-input-error
                                @enderror"
                            placeholder="Ví dụ: 62"
                        >

                        <span>
                            mm
                        </span>

                    </div>


                    @error('pd')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>



                <div class="staff-form-group">

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
                        max="{{ now()->format(
                            'Y-m-d'
                        ) }}"
                        class="staff-form-control
                            @error('exam_date')
                                staff-input-error
                            @enderror"
                        required
                    >


                    @error('exam_date')

                        <div class="staff-field-error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>



            <div class="staff-form-group">

                <label for="note">
                    Ghi chú
                </label>


                <textarea
                    id="note"
                    name="note"
                    rows="6"
                    maxlength="2000"
                    class="staff-form-control
                        @error('note')
                            staff-input-error
                        @enderror"
                    placeholder="Ghi chú kết quả đo, tình trạng mắt hoặc khuyến nghị..."
                >{{ old('note') }}</textarea>


                <small class="staff-form-help">
                    Tối đa 2000 ký tự.
                </small>


                @error('note')

                    <div class="staff-field-error">
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </section>

    </div>



    {{-- =====================================================
        SIDEBAR
    ====================================================== --}}

    <aside class="staff-eye-sidebar">


        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Khách hàng
                </h2>

            </div>


            <div class="staff-eye-sidebar-info">

                <span>
                    Họ tên
                </span>

                <strong>
                    {{ $appointment->customer_name }}
                </strong>


                <span>
                    Số điện thoại
                </span>

                <strong>
                    {{ $appointment->phone }}
                </strong>


                <span>
                    Email
                </span>

                <strong>
                    {{ $appointment->email }}
                </strong>

            </div>

        </section>



        <section class="staff-form-card">

            <div class="staff-form-card-heading">

                <h2>
                    Người thực hiện
                </h2>

            </div>


            <div class="staff-eye-performer">

                <div>
                    {{ strtoupper(
                        mb_substr(
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
                        Nhân viên thực hiện
                    </small>

                </span>

            </div>

        </section>



        <section class="staff-eye-guide">

            <strong>
                Lưu ý
            </strong>

            <p>
                Phải nhập ít nhất một thông số
                SPH, CYL, AXIS hoặc PD.
            </p>

            <p>
                Hồ sơ sau khi lưu sẽ được
                Customer xem trong lịch sử thị lực.
            </p>

        </section>



        <section class="staff-form-card staff-form-actions-card">

            <button
                type="submit"
                class="staff-btn staff-btn-primary"
            >
                Lưu hồ sơ thị lực
            </button>


            <a
                href="{{ route(
                    'staff.appointments.show',
                    $appointment
                ) }}"
                class="staff-btn staff-btn-secondary"
            >
                Hủy
            </a>

        </section>

    </aside>

</form>

@endsection