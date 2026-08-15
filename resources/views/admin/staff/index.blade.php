@extends('layouts.admin')


@section(
    'title',
    'Nhân viên - VELORA Eyes'
)


@section(
    'page-title',
    'Nhân viên'
)


@section('content')


<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            STAFF MANAGEMENT
        </span>

        <h1>
            Quản lý nhân viên
        </h1>

        <p>
            Tạo và quản lý tài khoản nhân viên
            vận hành hệ thống VELORA Eyes.
        </p>

    </div>


    <a
        href="{{ route(
            'admin.staff.create'
        ) }}"
        class="admin-btn admin-btn-primary"
    >
        <i class="bi bi-person-plus"></i>

        Thêm nhân viên
    </a>

</div>



{{-- STATS --}}

<div class="admin-staff-stats">

    <div class="admin-staff-stat">

        <div>
            <i class="bi bi-person-badge"></i>
        </div>

        <span>

            <small>
                Tổng nhân viên
            </small>

            <strong>
                {{ $totalStaff }}
            </strong>

        </span>

    </div>


    <a
        href="{{ route(
            'admin.staff.index',
            [
                'status' => 'active',
            ]
        ) }}"
        class="admin-staff-stat active"
    >

        <div>
            <i class="bi bi-person-check"></i>
        </div>

        <span>

            <small>
                Đang hoạt động
            </small>

            <strong>
                {{ $activeStaff }}
            </strong>

        </span>

    </a>


    <a
        href="{{ route(
            'admin.staff.index',
            [
                'status' => 'inactive',
            ]
        ) }}"
        class="admin-staff-stat inactive"
    >

        <div>
            <i class="bi bi-person-lock"></i>
        </div>

        <span>

            <small>
                Đã khóa
            </small>

            <strong>
                {{ $inactiveStaff }}
            </strong>

        </span>

    </a>

</div>



{{-- FILTER --}}

<div class="admin-staff-filter">

    <form
        action="{{ route(
            'admin.staff.index'
        ) }}"
        method="GET"
        class="admin-staff-filter-form"
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
                    placeholder="Tên, email, SĐT hoặc chức vụ..."
                >

            </div>

        </div>


        <div>

            <label for="status">
                Trạng thái
            </label>

            <select
                id="status"
                name="status"
                class="admin-form-control"
            >

                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="active"
                    {{
                        $status === 'active'
                            ? 'selected'
                            : ''
                    }}
                >
                    Hoạt động
                </option>

                <option
                    value="inactive"
                    {{
                        $status === 'inactive'
                            ? 'selected'
                            : ''
                    }}
                >
                    Đã khóa
                </option>

            </select>

        </div>


        <div class="admin-staff-filter-actions">

            <button
                type="submit"
                class="admin-btn admin-btn-primary"
            >
                <i class="bi bi-funnel"></i>

                Lọc
            </button>


            @if(
                $keyword !== ''
                || $status
            )

                <a
                    href="{{ route(
                        'admin.staff.index'
                    ) }}"
                    class="admin-btn admin-btn-secondary"
                >
                    Đặt lại
                </a>

            @endif

        </div>

    </form>

</div>



{{-- TABLE --}}

<div class="admin-panel">

    <div class="admin-panel-header">

        <div>

            <h2>
                Danh sách nhân viên
            </h2>

            <p>
                {{ $staffMembers->total() }}
                tài khoản Staff
            </p>

        </div>

    </div>


    @if($staffMembers->isEmpty())

        <div class="admin-staff-empty">

            <i class="bi bi-person-badge"></i>

            <h3>
                Không có nhân viên phù hợp
            </h3>

            <p>
                Hãy thử thay đổi bộ lọc.
            </p>

        </div>

    @else

        <div class="admin-table-responsive">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>
                            Nhân viên
                        </th>

                        <th>
                            Liên hệ
                        </th>

                        <th>
                            Chức vụ
                        </th>

                        <th>
                            Trạng thái
                        </th>

                        <th>
                            Ngày tạo
                        </th>

                        <th>
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach(
                        $staffMembers
                        as $staff
                    )

                        <tr>

                            <td>

                                <div class="admin-staff-user">

                                    <div class="admin-staff-avatar">

                                        {{ strtoupper(
                                            mb_substr(
                                                $staff->name,
                                                0,
                                                1
                                            )
                                        ) }}

                                    </div>


                                    <div>

                                        <strong>
                                            {{ $staff->name }}
                                        </strong>

                                        <span>
                                            Staff #{{ $staff->id }}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <div class="admin-staff-contact">

                                    <strong>
                                        {{ $staff->email }}
                                    </strong>

                                    <span>
                                        {{ $staff->phone }}
                                    </span>

                                </div>

                            </td>


                            <td>

                                @if($staff->position)

                                    <span class="admin-staff-position">

                                        {{ $staff->position }}

                                    </span>

                                @else

                                    <span class="admin-table-muted">
                                        Chưa cập nhật
                                    </span>

                                @endif

                            </td>


                            <td>

                                @if($staff->is_active)

                                    <span class="admin-status success">
                                        Hoạt động
                                    </span>

                                @else

                                    <span class="admin-status danger">
                                        Đã khóa
                                    </span>

                                @endif

                            </td>


                            <td>

                                <div class="admin-table-primary">

                                    <strong>

                                        {{ $staff
                                            ->created_at
                                            ->format(
                                                'd/m/Y'
                                            ) }}

                                    </strong>

                                    <span>

                                        {{ $staff
                                            ->created_at
                                            ->format(
                                                'H:i'
                                            ) }}

                                    </span>

                                </div>

                            </td>


                            <td>

                                <div class="admin-staff-actions">

                                    <a
                                        href="{{ route(
                                            'admin.staff.show',
                                            $staff
                                        ) }}"
                                        class="admin-table-action"
                                    >
                                        Xem
                                    </a>


                                    <a
                                        href="{{ route(
                                            'admin.staff.edit',
                                            $staff
                                        ) }}"
                                        class="admin-table-action"
                                    >
                                        Sửa
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="admin-pagination">

            {{ $staffMembers->links() }}

        </div>

    @endif

</div>

@endsection