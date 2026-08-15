<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Danh sách giao dịch.
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


        $paymentMethod =
            $request->query(
                'payment_method'
            );


        $status =
            $request->query(
                'status'
            );


        $paymentDate =
            $request->query(
                'payment_date'
            );


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE FILTER
        |--------------------------------------------------------------------------
        */

        if (
            $paymentMethod
            && !in_array(
                $paymentMethod,
                [
                    Payment::METHOD_COD,
                    Payment::METHOD_QR,
                    Payment::METHOD_VNPAY,
                ],
                true
            )
        ) {
            $paymentMethod = null;
        }


        if (
            $status
            && !in_array(
                $status,
                [
                    Payment::STATUS_UNPAID,
                    Payment::STATUS_PENDING,
                    Payment::STATUS_PAID,
                    Payment::STATUS_FAILED,
                    Payment::STATUS_REFUNDED,
                ],
                true
            )
        ) {
            $status = null;
        }


        if (
            $paymentDate
            && !Validator::make(
                [
                    'payment_date' =>
                        $paymentDate,
                ],
                [
                    'payment_date' =>
                        'date_format:Y-m-d',
                ]
            )->passes()
        ) {
            $paymentDate = null;
        }


        /*
        |--------------------------------------------------------------------------
        | PAYMENT LIST
        |--------------------------------------------------------------------------
        */

        $payments =
            Payment::query()

                ->with([
                    'order.user',
                ])

                ->when(
                    $keyword !== '',
                    function ($query) use ($keyword) {

                        $query->where(
                            function ($subQuery) use ($keyword) {

                                $subQuery
                                    ->where(
                                        'transaction_code',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhereHas(
                                        'order',
                                        function ($orderQuery) use ($keyword) {

                                            $orderQuery
                                                ->where(
                                                    'order_code',
                                                    'like',
                                                    "%{$keyword}%"
                                                )

                                                ->orWhere(
                                                    'customer_name',
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
                        );
                    }
                )

                ->when(
                    $paymentMethod,
                    fn ($query) =>
                        $query->where(
                            'payment_method',
                            $paymentMethod
                        )
                )

                ->when(
                    $status,
                    fn ($query) =>
                        $query->where(
                            'status',
                            $status
                        )
                )

                ->when(
                    $paymentDate,
                    fn ($query) =>
                        $query->whereDate(
                            'created_at',
                            $paymentDate
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

        $totalPayments =
            Payment::query()
                ->count();


        $pendingPayments =
            Payment::query()
                ->where(
                    'status',
                    Payment::STATUS_PENDING
                )
                ->count();


        $paidPayments =
            Payment::query()
                ->where(
                    'status',
                    Payment::STATUS_PAID
                )
                ->count();


        $refundedPayments =
            Payment::query()
                ->where(
                    'status',
                    Payment::STATUS_REFUNDED
                )
                ->count();


        $paidAmount =
            (float) Payment::query()
                ->where(
                    'status',
                    Payment::STATUS_PAID
                )
                ->sum('amount');


        return view(
            'admin.payments.index',
            compact(
                'payments',
                'keyword',
                'paymentMethod',
                'status',
                'paymentDate',
                'totalPayments',
                'pendingPayments',
                'paidPayments',
                'refundedPayments',
                'paidAmount'
            )
        );
    }


    /**
     * Chi tiết giao dịch.
     */
    public function show(
        Payment $payment
    ) {
        $payment->load([
            'order.user',
            'order.details',
        ]);


        return view(
            'admin.payments.show',
            compact('payment')
        );
    }


    /**
     * Admin ghi nhận hoàn tiền và hủy Order.
     */
    public function refund(
        Payment $payment,
        PaymentService $paymentService
    ) {
        $paymentService
            ->refundAndCancel(
                auth()->user(),
                $payment
            );


        return redirect()
            ->route(
                'admin.payments.show',
                $payment
            )
            ->with(
                'success',
                'Hoàn tiền và hủy đơn hàng thành công.'
            );
    }
}