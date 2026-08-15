<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWarrantyRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Warranty;
use App\Services\WarrantyService;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    /**
     * Danh sách bảo hành.
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
                    Warranty::STATUS_ACTIVE,
                    Warranty::STATUS_EXPIRED,
                    Warranty::STATUS_CANCELLED,
                ],
                true
            )
        ) {
            $status = null;
        }


        /*
        |--------------------------------------------------------------------------
        | QUERY
        |--------------------------------------------------------------------------
        */

        $warranties =
            Warranty::query()

                ->with([
                    'user',
                    'product',
                    'orderDetail.order',
                ])

                /*
                 * Search:
                 * - Warranty code
                 * - Customer
                 * - Product
                 * - Order code
                 */
                ->when(
                    $keyword !== '',
                    function ($query) use ($keyword) {

                        $query->where(
                            function ($subQuery) use ($keyword) {

                                $subQuery
                                    ->where(
                                        'warranty_code',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhereHas(
                                        'user',
                                        function ($userQuery) use ($keyword) {

                                            $userQuery
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
                                    )

                                    ->orWhereHas(
                                        'product',
                                        function ($productQuery) use ($keyword) {

                                            $productQuery->where(
                                                'name',
                                                'like',
                                                "%{$keyword}%"
                                            );
                                        }
                                    )

                                    ->orWhereHas(
                                        'orderDetail.order',
                                        function ($orderQuery) use ($keyword) {

                                            $orderQuery->where(
                                                'order_code',
                                                'like',
                                                "%{$keyword}%"
                                            );
                                        }
                                    );
                            }
                        );
                    }
                )


                /*
                 * ACTIVE thực tế:
                 * DB active + chưa hết hạn.
                 */
                ->when(
                    $status
                    === Warranty::STATUS_ACTIVE,
                    function ($query) {

                        $query
                            ->where(
                                'status',
                                Warranty::STATUS_ACTIVE
                            )
                            ->whereDate(
                                'start_date',
                                '<=',
                                today()
                            )
                            ->whereDate(
                                'end_date',
                                '>=',
                                today()
                            );
                    }
                )


                /*
                 * EXPIRED thực tế.
                 */
                ->when(
                    $status
                    === Warranty::STATUS_EXPIRED,
                    function ($query) {

                        $query->where(
                            function ($subQuery) {

                                $subQuery
                                    ->where(
                                        'status',
                                        Warranty::STATUS_EXPIRED
                                    )

                                    ->orWhere(
                                        function ($expiredQuery) {

                                            $expiredQuery
                                                ->where(
                                                    'status',
                                                    '!=',
                                                    Warranty::STATUS_CANCELLED
                                                )
                                                ->whereDate(
                                                    'end_date',
                                                    '<',
                                                    today()
                                                );
                                        }
                                    );
                            }
                        );
                    }
                )


                ->when(
                    $status
                    === Warranty::STATUS_CANCELLED,
                    fn ($query) =>
                        $query->where(
                            'status',
                            Warranty::STATUS_CANCELLED
                        )
                )

                ->latest()

                ->paginate(15)

                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $totalWarranties =
            Warranty::query()
                ->count();


        $activeWarranties =
            Warranty::query()
                ->where(
                    'status',
                    Warranty::STATUS_ACTIVE
                )
                ->whereDate(
                    'start_date',
                    '<=',
                    today()
                )
                ->whereDate(
                    'end_date',
                    '>=',
                    today()
                )
                ->count();


        $expiredWarranties =
            Warranty::query()
                ->where(
                    function ($query) {

                        $query
                            ->where(
                                'status',
                                Warranty::STATUS_EXPIRED
                            )

                            ->orWhere(
                                function ($expiredQuery) {

                                    $expiredQuery
                                        ->where(
                                            'status',
                                            '!=',
                                            Warranty::STATUS_CANCELLED
                                        )
                                        ->whereDate(
                                            'end_date',
                                            '<',
                                            today()
                                        );
                                }
                            );
                    }
                )
                ->count();


        $cancelledWarranties =
            Warranty::query()
                ->where(
                    'status',
                    Warranty::STATUS_CANCELLED
                )
                ->count();


        return view(
            'admin.warranties.index',
            compact(
                'warranties',
                'keyword',
                'status',
                'totalWarranties',
                'activeWarranties',
                'expiredWarranties',
                'cancelledWarranties'
            )
        );
    }


    /**
     * Form cấp bảo hành.
     */
    public function create(
        OrderDetail $orderDetail
    ) {
        $orderDetail->load([
            'order.user',
            'product',
            'warranty',
        ]);


        /*
         * Chỉ Order Completed.
         */
        if (
            !$orderDetail->order
            || $orderDetail->order->order_status
                !== Order::STATUS_COMPLETED
        ) {
            abort(403);
        }


        /*
         * Đã có Warranty thì chuyển thẳng
         * tới trang chi tiết.
         */
        if ($orderDetail->warranty) {

            return redirect()
                ->route(
                    'admin.warranties.show',
                    $orderDetail->warranty
                )
                ->with(
                    'info',
                    'Sản phẩm này đã được cấp bảo hành điện tử.'
                );
        }


        return view(
            'admin.warranties.create',
            compact('orderDetail')
        );
    }


    /**
     * Lưu bảo hành.
     */
    public function store(
        StoreWarrantyRequest $request,
        OrderDetail $orderDetail,
        WarrantyService $warrantyService
    ) {
        $warranty =
            $warrantyService->create(
                auth()->user(),
                $orderDetail,
                $request->validated()[
                    'warranty_content'
                ] ?? null
            );


        return redirect()
            ->route(
                'admin.warranties.show',
                $warranty
            )
            ->with(
                'success',
                'Cấp bảo hành điện tử thành công.'
            );
    }


    /**
     * Chi tiết bảo hành.
     */
    public function show(
        Warranty $warranty,
        WarrantyService $warrantyService
    ) {
        $warranty->load([
            'user',
            'product',
            'orderDetail.order',
        ]);


        return view(
            'admin.warranties.show',
            compact(
                'warranty',
                'warrantyService'
            )
        );
    }
}