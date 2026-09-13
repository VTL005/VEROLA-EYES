<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Services\OrderCancellationService;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng của Customer.
     */
    public function index(Request $request)
    {
        $status = $request->query('status');

        $allowedStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PREPARING,
            Order::STATUS_PACKED,
            Order::STATUS_SHIPPING,
            Order::STATUS_DELIVERED,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ];

        if (
            $status
            && ! in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            $status = null;
        }

        $orders = Order::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->when(
                $status,
                function ($query) use ($status) {
                    $query->where(
                        'order_status',
                        $status
                    );
                }
            )
            ->with([
                'payment',

                'details' => function ($query) {
                    $query
                        ->with('product')
                        ->orderBy('id');
                },
            ])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $productIds = $orders
            ->getCollection()
            ->flatMap(function (Order $order) {
                return $order->details->pluck('product_id');
            })
            ->filter()
            ->unique();

        $reviewedProductIds = Review::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->whereIn(
                'product_id',
                $productIds
            )
            ->pluck('product_id');

        return view(
            'orders.index',
            compact(
                'orders',
                'status',
                'reviewedProductIds'
            )
        );
    }

    /**
     * Chi tiết đơn hàng.
     */
    public function show(
        Order $order
    ) {
        $this->ensureOwnership(
            $order
        );

        $order->load([
            'details.product',

            'payment',

            'statusHistories' => function ($query) {
                $query->oldest();
            },

            'statusHistories.updater',
        ]);

        $reviewedProductIds = Review::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->whereIn(
                'product_id',
                $order->details
                    ->pluck('product_id')
                    ->filter()
                    ->unique()
            )
            ->pluck('product_id');

        return view(
            'orders.show',
            compact(
                'order',
                'reviewedProductIds'
            )
        );
    }

    /**
     * Customer xác nhận đã nhận được hàng.
     */
    public function confirmReceived(
        Order $order,
        OrderStatusService $orderStatusService
    ) {
        $this->ensureOwnership(
            $order
        );

        $orderStatusService->confirmReceived(
            $order,
            auth()->user()
        );

        $order->loadMissing('details.product');

        $reviewedProductIds = Review::query()
            ->where(
                'user_id',
                auth()->id()
            )
            ->whereIn(
                'product_id',
                $order->details
                    ->pluck('product_id')
                    ->filter()
                    ->unique()
            )
            ->pluck('product_id');

        $firstReviewableDetail = $order->details->first(
            function ($detail) use ($reviewedProductIds) {
                return $detail->product
                    && ! $reviewedProductIds->contains(
                        (int) $detail->product_id
                    );
            }
        );

        if ($firstReviewableDetail) {
            return redirect()
                ->route(
                    'reviews.create',
                    $firstReviewableDetail->product
                )
                ->with(
                    'success',
                    'Đã xác nhận nhận hàng. Bạn có thể đánh giá sản phẩm ngay hoặc để sau.'
                );
        }

        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'success',
                'Cảm ơn bạn đã xác nhận nhận hàng. Đơn hàng đã hoàn thành.'
            );
    }

    /**
     * Customer hủy Order.
     */
    public function cancel(
        Order $order,
        OrderCancellationService $cancellationService
    ) {
        $this->ensureOwnership(
            $order
        );

        $cancellationService->cancel(
            auth()->user(),
            $order
        );

        return redirect()
            ->route(
                'orders.show',
                $order
            )
            ->with(
                'success',
                'Hủy đơn hàng thành công. Tồn kho đã được hoàn lại.'
            );
    }

    /**
     * Kiểm tra quyền sở hữu Order.
     */
    private function ensureOwnership(
        Order $order
    ): void {
        abort_if(
            (int) $order->user_id
                !== (int) auth()->id(),
            403
        );
    }
}