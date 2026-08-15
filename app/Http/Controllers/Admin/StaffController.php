<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    /**
     * Danh sách Staff.
     */
    public function index(
        Request $request
    ) {
        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );


        $status =
            $request->query('status');


        if (
            $status
            && !in_array(
                $status,
                [
                    'active',
                    'inactive',
                ],
                true
            )
        ) {
            $status = null;
        }


        $staffMembers =
            User::query()

                /*
                 * Chỉ lấy Staff.
                 */
                ->whereHas(
                    'role',
                    function ($query) {

                        $query->where(
                            'name',
                            'staff'
                        );
                    }
                )

                /*
                 * Search:
                 *
                 * - Tên
                 * - Email
                 * - SĐT
                 * - Chức vụ
                 */
                ->when(
                    $keyword !== '',
                    function ($query) use ($keyword) {

                        $query->where(
                            function ($subQuery) use ($keyword) {

                                $subQuery
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhere(
                                        'email',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhere(
                                        'phone',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhere(
                                        'position',
                                        'like',
                                        "%{$keyword}%"
                                    );
                            }
                        );
                    }
                )

                /*
                 * Filter Active.
                 */
                ->when(
                    $status === 'active',
                    function ($query) {

                        $query->where(
                            'is_active',
                            true
                        );
                    }
                )

                /*
                 * Filter Inactive.
                 */
                ->when(
                    $status === 'inactive',
                    function ($query) {

                        $query->where(
                            'is_active',
                            false
                        );
                    }
                )

                ->latest()

                ->paginate(15)

                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $staffQuery =
            User::query()
                ->whereHas(
                    'role',
                    function ($query) {

                        $query->where(
                            'name',
                            'staff'
                        );
                    }
                );


        $totalStaff =
            (clone $staffQuery)
                ->count();


        $activeStaff =
            (clone $staffQuery)
                ->where(
                    'is_active',
                    true
                )
                ->count();


        $inactiveStaff =
            (clone $staffQuery)
                ->where(
                    'is_active',
                    false
                )
                ->count();


        return view(
            'admin.staff.index',
            compact(
                'staffMembers',
                'keyword',
                'status',
                'totalStaff',
                'activeStaff',
                'inactiveStaff'
            )
        );
    }


    /**
     * Form tạo Staff.
     */
    public function create()
    {
        return view(
            'admin.staff.create'
        );
    }


    /**
     * Lưu Staff.
     */
    public function store(
        StoreStaffRequest $request
    ) {
        $data =
            $request->validated();


        /*
         * Role được xác định ở Backend.
         *
         * Không nhận role_id từ Form.
         */
        $staffRole =
            Role::query()
                ->where(
                    'name',
                    'staff'
                )
                ->firstOrFail();


        $staff =
            User::create([
                'role_id' =>
                    $staffRole->id,

                'name' =>
                    $data['name'],

                'email' =>
                    $data['email'],

                'phone' =>
                    $data['phone'],

                'position' =>
                    $data['position']
                    ?? null,

                'is_active' =>
                    $data['is_active'],

                /*
                 * User Model cast password => hashed.
                 */
                'password' =>
                    $data['password'],
            ]);


        return redirect()
            ->route(
                'admin.staff.show',
                $staff
            )
            ->with(
                'success',
                'Tạo tài khoản nhân viên thành công.'
            );
    }


    /**
     * Xem Staff.
     */
    public function show(
        User $staff
    ) {
        abort_unless(
            $staff->isStaff(),
            404
        );


        $staff->load([
            'role',
        ]);


        return view(
            'admin.staff.show',
            compact('staff')
        );
    }


    /**
     * Form sửa Staff.
     */
    public function edit(
        User $staff
    ) {
        abort_unless(
            $staff->isStaff(),
            404
        );


        return view(
            'admin.staff.edit',
            compact('staff')
        );
    }


    /**
     * Cập nhật Staff.
     */
    public function update(
        UpdateStaffRequest $request,
        User $staff
    ) {
        abort_unless(
            $staff->isStaff(),
            404
        );


        $data =
            $request->validated();


        $updateData = [
            'name' =>
                $data['name'],

            'email' =>
                $data['email'],

            'phone' =>
                $data['phone'],

            'position' =>
                $data['position']
                ?? null,

            'is_active' =>
                $data['is_active'],
        ];


        /*
         * Chỉ đổi Password khi Admin nhập Password mới.
         */
        if (
            !empty(
                $data['password']
            )
        ) {
            $updateData['password'] =
                $data['password'];
        }


        /*
         * Không cập nhật role_id.
         */
        $staff->update(
            $updateData
        );


        return redirect()
            ->route(
                'admin.staff.show',
                $staff
            )
            ->with(
                'success',
                'Cập nhật thông tin nhân viên thành công.'
            );
    }


    /**
     * Khóa / mở Staff.
     */
    public function toggleActive(
        User $staff
    ) {
        abort_unless(
            $staff->isStaff(),
            404
        );


        DB::transaction(
            function () use ($staff) {

                $lockedStaff =
                    User::query()
                        ->where(
                            'id',
                            $staff->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                 * Kiểm tra lại Role sau khi lock.
                 */
                $lockedStaff->load('role');


                abort_unless(
                    $lockedStaff->isStaff(),
                    403
                );


                $lockedStaff->update([
                    'is_active' =>
                        !$lockedStaff
                            ->is_active,
                ]);
            }
        );


        $staff->refresh();


        return back()->with(
            'success',
            $staff->is_active
                ? 'Đã mở khóa tài khoản nhân viên.'
                : 'Đã khóa tài khoản nhân viên.'
        );
    }
}