@extends('layouts.app')


@section('title', 'Đặt lịch đo mắt - VELORA Eyes')


@section('content')

<section class="appointment-create-section">

    <div class="velora-container">

        <div class="appointment-create-layout">


            {{-- =============================================
                INTRO
            ============================================== --}}

            <div class="appointment-create-intro">

                <span class="hero-kicker">
                    BOOK AN EYE EXAM
                </span>

                <h1>
                    Đặt lịch đo mắt
                </h1>

                <p>
                    Chọn thời gian phù hợp để được
                    kiểm tra thị lực và tư vấn trực tiếp
                    tại VELORA Eyes.
                </p>


                <div class="appointment-benefits">

                    <div>
                        <strong>01</strong>

                        <span>
                            Kiểm tra thị lực
                        </span>
                    </div>


                    <div>
                        <strong>02</strong>

                        <span>
                            Tư vấn tròng kính
                        </span>
                    </div>


                    <div>
                        <strong>03</strong>

                        <span>
                            Tư vấn lựa chọn gọng
                        </span>
                    </div>

                </div>


                <a
                    href="{{ route('appointments.index') }}"
                    class="appointment-back-link"
                >
                    ← Lịch hẹn của tôi
                </a>

            </div>



            {{-- =============================================
                FORM
            ============================================== --}}

            <div class="appointment-form-card">

                <div class="appointment-form-heading">

                    <h2>
                        Thông tin lịch hẹn
                    </h2>

                    <p>
                        Vui lòng điền đầy đủ thông tin
                        bên dưới.
                    </p>

                </div>


                <form
                    action="{{ route('appointments.store') }}"
                    method="POST"
                >

                    @csrf


                    <div class="appointment-form-grid">


                        {{-- NAME --}}

                        <div class="form-group">

                            <label
                                for="customer_name"
                                class="form-label"
                            >
                                Họ và tên
                            </label>


                            <input
                                type="text"
                                id="customer_name"
                                name="customer_name"
                                class="form-control @error('customer_name') input-error @enderror"
                                value="{{ old(
                                    'customer_name',
                                    $user->name
                                ) }}"
                                maxlength="100"
                                required
                            >


                            @error('customer_name')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- PHONE --}}

                        <div class="form-group">

                            <label
                                for="phone"
                                class="form-label"
                            >
                                Số điện thoại
                            </label>


                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-control @error('phone') input-error @enderror"
                                value="{{ old(
                                    'phone',
                                    $user->phone
                                ) }}"
                                placeholder="0912345678"
                                maxlength="10"
                                required
                            >


                            @error('phone')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- EMAIL --}}

                        <div class="form-group appointment-grid-full">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email
                            </label>


                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control @error('email') input-error @enderror"
                                value="{{ old(
                                    'email',
                                    $user->email
                                ) }}"
                                required
                            >


                            @error('email')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- DATE --}}

                        <div class="form-group">

                            <label
                                for="appointment_date"
                                class="form-label"
                            >
                                Ngày hẹn
                            </label>


                            <input
                                type="date"
                                id="appointment_date"
                                name="appointment_date"
                                class="form-control @error('appointment_date') input-error @enderror"
                                value="{{ old('appointment_date') }}"
                                min="{{ now()->format('Y-m-d') }}"
                                required
                            >


                            @error('appointment_date')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- TIME SLOT --}}

                        <div class="form-group">

                            <label
                                for="time_slot"
                                class="form-label"
                            >
                                Khung giờ
                            </label>


                            <select
                                id="time_slot"
                                name="time_slot"
                                class="form-control @error('time_slot') input-error @enderror"
                                required
                            >

                                <option value="">
                                    -- Chọn khung giờ --
                                </option>


                                @foreach([
                                    '08:00-09:00',
                                    '09:00-10:00',
                                    '10:00-11:00',
                                    '14:00-15:00',
                                    '15:00-16:00',
                                    '16:00-17:00',
                                ] as $slot)

                                    <option
                                        value="{{ $slot }}"
                                        {{ old('time_slot') === $slot
                                            ? 'selected'
                                            : '' }}
                                    >
                                        {{ str_replace(
                                            '-',
                                            ' - ',
                                            $slot
                                        ) }}
                                    </option>

                                @endforeach

                            </select>


                            @error('time_slot')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- SERVICE --}}

                        <div class="form-group appointment-grid-full">

                            <label
                                for="service_type"
                                class="form-label"
                            >
                                Dịch vụ
                            </label>


                            <select
                                id="service_type"
                                name="service_type"
                                class="form-control @error('service_type') input-error @enderror"
                                required
                            >

                                <option value="">
                                    -- Chọn dịch vụ --
                                </option>


                                <option
                                    value="eye_exam"
                                    {{ old('service_type') === 'eye_exam'
                                        ? 'selected'
                                        : '' }}
                                >
                                    Đo mắt cận
                                </option>


                                <option
                                    value="recheck"
                                    {{ old('service_type') === 'recheck'
                                        ? 'selected'
                                        : '' }}
                                >
                                    Kiểm tra lại độ kính
                                </option>


                                <option
                                    value="lens_consultation"
                                    {{ old('service_type') === 'lens_consultation'
                                        ? 'selected'
                                        : '' }}
                                >
                                    Tư vấn tròng kính
                                </option>


                                <option
                                    value="frame_consultation"
                                    {{ old('service_type') === 'frame_consultation'
                                        ? 'selected'
                                        : '' }}
                                >
                                    Tư vấn chọn gọng
                                </option>

                            </select>


                            @error('service_type')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- NOTE --}}

                        <div class="form-group appointment-grid-full">

                            <label
                                for="note"
                                class="form-label"
                            >
                                Ghi chú
                            </label>


                            <textarea
                                id="note"
                                name="note"
                                class="form-control @error('note') input-error @enderror"
                                rows="5"
                                maxlength="1000"
                                placeholder="Ví dụ: Tôi đang đeo kính cận khoảng 2 độ..."
                            >{{ old('note') }}</textarea>


                            @error('note')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>



                    <div class="appointment-form-notice">

                        <strong>
                            Lưu ý
                        </strong>

                        <p class="mb-0">
                            Sau khi đặt lịch, lịch hẹn sẽ
                            ở trạng thái chờ xác nhận.
                            Nhân viên VELORA sẽ tiếp nhận
                            và xác nhận lịch của bạn.
                        </p>

                    </div>



                    <div class="appointment-form-actions">

                        <a
                            href="{{ route('appointments.index') }}"
                            class="btn btn-outline"
                        >
                            Hủy
                        </a>


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Đặt lịch đo mắt
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</section>

@endsection