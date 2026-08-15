@extends('layouts.admin')


@section(
    'title',
    'Hồ sơ đo mắt - VELORA Eyes'
)


@section(
    'page-title',
    'Hồ sơ đo mắt'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            EYE PRESCRIPTIONS
        </span>

        <h1>
            Hồ sơ đo mắt
        </h1>

        <p>
            Theo dõi lịch sử thị lực và
            kết quả đo mắt của khách hàng.
        </p>

    </div>

</div>



<div class="admin-eye-stats">

    <div class="admin-eye-stat">

        <div class="all">
            <i class="bi bi-eye"></i>
        </div>

        <span>
            <small>Tổng hồ sơ</small>
            <strong>{{ $totalPrescriptions }}</strong>
        </span>

    </div>


    <a
        href="{{ route(
            'admin.eye-prescriptions.index',
            [
                'exam_date' =>
                    now()->format('Y-m-d'),
            ]
        ) }}"
        class="admin-eye-stat"
    >

        <div class="today">
            <i class="bi bi-calendar-check"></i>
        </div>

        <span>
            <small>Đo hôm nay</small>
            <strong>{{ $todayPrescriptions }}</strong>
        </span>

    </a>


    <div class="admin-eye-stat">

        <div class="month">
            <i class="bi bi-calendar3"></i>
        </div>

        <span>
            <small>Tháng này</small>
            <strong>{{ $monthPrescriptions }}</strong>
        </span>

    </div>

</div>



<div class="admin-eye-filter">

    <form
        action="{{ route(
            'admin.eye-prescriptions.index'
        ) }}"
        method="GET"
        class="admin-eye-filter-form"
    >

        <div>

            <label for="keyword">
                Tìm kiếm
            </label>

            <div class="admin-input-icon">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    class="admin-form-control"
                    placeholder="Tên, email, SĐT hoặc mã lịch..."
                >

            </div>

        </div>


        <div>

            <label for="exam_date">
                Ngày đo
            </label>

            <input
                type="date"
                id="exam_date"
                name="exam_date"
                value="{{ $examDate }}"
                class="admin-form-control"
            >

        </div>


        <div class="admin-eye-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-funnel"></i>
                Lọc
            </button>


            @if(
                $keyword !== ''
                || $examDate
            )

                <a
                    href="{{ route(
                        'admin.eye-prescriptions.index'
                    ) }}"
                    class="admin-btn admin-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



<div class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Danh sách hồ sơ
            </h2>

            <p>
                {{ $prescriptions->total() }}
                hồ sơ
            </p>

        </div>

    </div>


    @if($prescriptions->isEmpty())

        <div class="admin-eye-empty">

            <i class="bi bi-eye-slash"></i>

            <h3>
                Chưa có hồ sơ phù hợp
            </h3>

            <p>
                Không tìm thấy kết quả đo mắt.
            </p>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table">

                <thead>

                    <tr>
                        <th>Khách hàng</th>
                        <th>Lịch hẹn</th>
                        <th>Ngày đo</th>
                        <th>Mắt phải</th>
                        <th>Mắt trái</th>
                        <th>PD</th>
                        <th>Thực hiện bởi</th>
                        <th></th>
                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $prescriptions
                        as $prescription
                    )

                        <tr>

                            <td>

                                <div class="admin-eye-customer">

                                    <strong>

                                        {{ $prescription
                                            ->user
                                            ?->name
                                            ?? '—' }}

                                    </strong>

                                    <span>

                                        {{ $prescription
                                            ->user
                                            ?->phone
                                            ?? '—' }}

                                    </span>

                                    <small>

                                        {{ $prescription
                                            ->user
                                            ?->email
                                            ?? '—' }}

                                    </small>

                                </div>

                            </td>


                            <td>

                                @if($prescription->appointment)

                                    <a
                                        href="{{ route(
                                            'admin.appointments.show',
                                            $prescription->appointment
                                        ) }}"
                                        class="admin-table-action"
                                    >
                                        {{ $prescription
                                            ->appointment
                                            ->appointment_code }}
                                    </a>

                                @else

                                    <span class="admin-table-muted">
                                        —
                                    </span>

                                @endif

                            </td>


                            <td>

                                <strong>

                                    {{ $prescription
                                        ->exam_date
                                        ->format(
                                            'd/m/Y'
                                        ) }}

                                </strong>

                            </td>


                            <td>

                                <div class="admin-eye-mini-result">

                                    <span>
                                        SPH
                                        <strong>
                                            {{ $prescription->right_sph ?? '—' }}
                                        </strong>
                                    </span>

                                    <span>
                                        CYL
                                        <strong>
                                            {{ $prescription->right_cyl ?? '—' }}
                                        </strong>
                                    </span>

                                </div>

                            </td>


                            <td>

                                <div class="admin-eye-mini-result">

                                    <span>
                                        SPH
                                        <strong>
                                            {{ $prescription->left_sph ?? '—' }}
                                        </strong>
                                    </span>

                                    <span>
                                        CYL
                                        <strong>
                                            {{ $prescription->left_cyl ?? '—' }}
                                        </strong>
                                    </span>

                                </div>

                            </td>


                            <td>

                                <strong>
                                    {{ $prescription->pd ?? '—' }}
                                </strong>

                            </td>


                            <td>

                                {{ $prescription
                                    ->performer
                                    ?->name
                                    ?? '—' }}

                            </td>


                            <td>

                                <a
                                    href="{{ route(
                                        'admin.eye-prescriptions.show',
                                        $prescription
                                    ) }}"
                                    class="admin-order-view"
                                    title="Xem hồ sơ"
                                >
                                    <i class="bi bi-eye"></i>
                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="admin-pagination">
            {{ $prescriptions->links() }}
        </div>

    @endif

</div>

@endsection