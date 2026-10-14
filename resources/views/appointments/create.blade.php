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

                    <ol class="appointment-wizard-steps" aria-label="Tiến trình đặt lịch">
                        <li class="is-active" data-wizard-indicator="1"><span>1</span> Chọn lịch</li>
                        <li data-wizard-indicator="2"><span>2</span> Thông tin</li>
                    </ol>

                </div>


                <form
                    action="{{ route('appointments.store') }}"
                    method="POST"
                    data-appointment-wizard
                >

                    @csrf


                    <div class="appointment-form-grid">


                        {{-- NAME --}}

                        <div class="form-group" data-wizard-step="2">

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

                        <div class="form-group" data-wizard-step="2">

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

                        <div class="form-group appointment-grid-full" data-wizard-step="2">

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

                        <div class="form-group" data-wizard-step="1">

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

                            <div
                                class="appointment-availability-calendar"
                                data-appointment-availability
                                data-availability-url="{{ route('appointments.availability') }}"
                            >
                                <div class="appointment-availability-calendar__heading">
                                    <strong>Chọn nhanh trong 14 ngày tới</strong>
                                    <span data-availability-status>Chọn ngày để xem chỗ trống</span>
                                </div>
                                <div class="appointment-availability-calendar__days" role="list" aria-label="Ngày có thể đặt lịch">
                                    @for($offset = 0; $offset < 14; $offset++)
                                        @php($calendarDate = now()->startOfDay()->addDays($offset))
                                        <button
                                            type="button"
                                            class="appointment-availability-calendar__day"
                                            data-appointment-date="{{ $calendarDate->format('Y-m-d') }}"
                                            aria-label="Chọn ngày {{ $calendarDate->format('d/m/Y') }}"
                                        >
                                            <small>{{ $offset === 0 ? 'Hôm nay' : 'T' . $calendarDate->isoFormat('dd') }}</small>
                                            <strong>{{ $calendarDate->format('d') }}</strong>
                                            <em>{{ $calendarDate->format('m') }}</em>
                                        </button>
                                    @endfor
                                </div>
                                <button type="button" class="appointment-availability-calendar__retry" data-appointment-retry hidden>
                                    Thử lại
                                </button>
                            </div>


                            @error('appointment_date')

                                <div class="field-error">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>



                        {{-- TIME SLOT --}}

                        <div class="form-group" data-wizard-step="1">

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

                        <div class="form-group appointment-grid-full" data-wizard-step="1">

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

                        <div class="form-group appointment-grid-full" data-wizard-step="2">

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



                    <div class="appointment-form-notice" data-wizard-step="2">

                        <strong>
                            Lưu ý
                        </strong>

                        <p class="mb-0">
                            Sau khi đặt lịch, lịch hẹn sẽ
                            ở trạng thái chờ xác nhận.
                            Admin VELORA sẽ tiếp nhận
                            và xác nhận lịch của bạn.
                        </p>

                    </div>



                    <div class="appointment-form-actions">

                        <a
                            href="{{ route('appointments.index') }}"
                            class="btn btn-outline"
                            data-wizard-cancel
                        >
                            Hủy
                        </a>

                        <button type="button" class="btn btn-outline" data-wizard-back hidden>
                            Quay lại
                        </button>


                        <button type="button" class="btn btn-primary" data-wizard-next>
                            Tiếp tục
                        </button>

                        <button
                            type="submit"
                            class="btn btn-primary"
                            data-wizard-submit
                            data-velora-shimmer
                        >
                            Đặt lịch đo mắt
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('[data-appointment-wizard]');
    if (!form) return;

    form.classList.add('is-enhanced');

    var step = 1;
    var next = form.querySelector('[data-wizard-next]');
    var submit = form.querySelector('[data-wizard-submit]');
    var back = form.querySelector('[data-wizard-back]');
    var cancel = form.querySelector('[data-wizard-cancel]');
    var steps = Array.prototype.slice.call(form.querySelectorAll('[data-wizard-step]'));
    var indicators = Array.prototype.slice.call(document.querySelectorAll('[data-wizard-indicator]'));

    function render() {
        steps.forEach(function (element) {
            element.hidden = Number(element.dataset.wizardStep) !== step;
        });
        indicators.forEach(function (indicator) {
            var current = Number(indicator.dataset.wizardIndicator) === step;
            indicator.classList.toggle('is-active', current);
            indicator.classList.toggle('is-done', Number(indicator.dataset.wizardIndicator) < step);
            indicator.setAttribute('aria-current', current ? 'step' : 'false');
        });
        next.hidden = step === 2;
        submit.hidden = step !== 2;
        if (back) back.hidden = step !== 2;
        if (cancel) cancel.hidden = false;
    }

    next.addEventListener('click', function () {
        var fields = Array.prototype.slice.call(form.querySelectorAll('[data-wizard-step="1"] input, [data-wizard-step="1"] select, [data-wizard-step="1"] textarea'));
        var invalid = fields.find(function (field) { return !field.checkValidity(); });
        if (invalid) {
            invalid.reportValidity();
            return;
        }
        step = 2;
        render();
        var firstField = form.querySelector('[data-wizard-step="2"] input, [data-wizard-step="2"] textarea');
        if (firstField) firstField.focus();
    });

    back.addEventListener('click', function () {
        step = 1;
        render();
        var firstField = form.querySelector('[data-wizard-step="1"] input, [data-wizard-step="1"] select');
        if (firstField) firstField.focus();
    });

    form.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && step === 1 && event.target.tagName !== 'TEXTAREA') {
            event.preventDefault();
            next.click();
        }
    });

    render();
});
</script>
@endpush

@endsection
