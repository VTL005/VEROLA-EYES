<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Danh sách Customer.
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


        $customers =
            User::query()

                /*
                 * Chỉ lấy Customer.
                 */
                ->whereHas(
                    'role',
                    function ($query) {

                        $query->where(
                            'name',
                            'customer'
                        );
                    }
                )

                /*
                 * Search tên / email / phone.
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
                                    );
                            }
                        );
                    }
                )

                /*
                 * Filter trạng thái.
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

                ->when(
                    $status === 'inactive',
                    function ($query) {

                        $query->where(
                            'is_active',
                            false
                        );
                    }
                )

                /*
                 * Thống kê theo từng Customer.
                 */
                ->withCount([
                    'orders',
                    'appointments',
                    'warranties',
                    'reviews',
                ])

                ->latest()

                ->paginate(15)

                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | THỐNG KÊ NHANH
        |--------------------------------------------------------------------------
        */

        $customerQuery =
            User::query()
                ->whereHas(
                    'role',
                    function ($query) {

                        $query->where(
                            'name',
                            'customer'
                        );
                    }
                );


        $totalCustomers =
            (clone $customerQuery)
                ->count();


        $activeCustomers =
            (clone $customerQuery)
                ->where(
                    'is_active',
                    true
                )
                ->count();


        $inactiveCustomers =
            (clone $customerQuery)
                ->where(
                    'is_active',
                    false
                )
                ->count();


        return view(
            'admin.customers.index',
            compact(
                'customers',
                'keyword',
                'status',
                'totalCustomers',
                'activeCustomers',
                'inactiveCustomers'
            )
        );
    }


    /**
     * Chi tiết Customer.
     */
    public function show(
        User $customer
    ) {
        /*
         * Không cho URL Customer
         * mở Staff/Admin.
         */
        abort_unless(
            $customer->isCustomer(),
            404
        );


        $customer->load([
            'role',

            'addresses',

            'orders' =>
                function ($query) {

                    $query
                        ->latest()
                        ->limit(10);
                },

            'appointments' =>
                function ($query) {

                    $query
                        ->latest()
                        ->limit(10);
                },

            'eyePrescriptions' =>
                function ($query) {

                    $query
                        ->latest('exam_date')
                        ->limit(10);
                },

            'warranties' =>
                function ($query) {

                    $query
                        ->latest()
                        ->limit(10);
                },

            'reviews' =>
                function ($query) {

                    $query
                        ->with('product')
                        ->latest()
                        ->limit(10);
                },
        ]);


        return view(
            'admin.customers.show',
            compact('customer')
        );
    }


    /**
     * Khóa / mở Customer.
     */
    public function toggleActive(
        User $customer
    ) {
        /*
         * Chỉ được thao tác Customer.
         */
        abort_unless(
            $customer->isCustomer(),
            404
        );


        DB::transaction(
            function () use ($customer) {

                $lockedCustomer =
                    User::query()
                        ->where(
                            'id',
                            $customer->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                /*
                 * Kiểm tra lại Role
                 * sau khi lock.
                 */
                $lockedCustomer
                    ->load('role');


                abort_unless(
                    $lockedCustomer
                        ->isCustomer(),
                    403
                );


                $lockedCustomer->update([
                    'is_active' =>
                        !$lockedCustomer
                            ->is_active,
                ]);
            }
        );


        $customer->refresh();


        return back()->with(
            'success',
            $customer->is_active
                ? 'Đã mở khóa tài khoản khách hàng.'
                : 'Đã khóa tài khoản khách hàng.'
        );
    }
}