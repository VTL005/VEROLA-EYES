<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderCancellationService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Danh sách đơn hàng của Customer.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | LẤY TRẠNG THÁI TỪ URL
        |--------------------------------------------------------------------------
        |
        | Ví dụ:
        |
        | /orders
        | /orders?status=pending
        | /orders?status=shipping
        | /orders?status=completed
        |
        */

        $status = $request->query('status');


        /*
        |--------------------------------------------------------------------------
        | DANH SÁCH TRẠNG THÁI HỢP LỆ
        |--------------------------------------------------------------------------
        */

        $allowedStatuses = [
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_PREPARING,
            Order::STATUS_PACKED,
            Order::STATUS_SHIPPING,
            Order::STATUS_COMPLETED,
            Order::STATUS_CANCELLED,
        ];


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA STATUS
        |--------------------------------------------------------------------------
        |
        | Nếu Customer tự sửa URL thành:
        |
        | /orders?status=abc
        |
        | thì bỏ filter đó đi.
        |
        */

        if (
            $status
            && !in_array(
                $status,
                $allowedStatuses,
                true
            )
        ) {
            $status = null;
        }


        /*
        |--------------------------------------------------------------------------
        | LẤY ĐƠN HÀNG
        |--------------------------------------------------------------------------
        */

        $orders = Order::query()

            /*
             * Chỉ lấy Order của Customer
             * đang đăng nhập.
             */
            ->where(
                'user_id',
                auth()->id()
            )


            /*
             * Nếu có status thì lọc.
             *
             * Ví dụ:
             *
             * ?status=pending
             *
             * => chỉ lấy Order pending.
             */
            ->when(
                $status,
                function ($query) use ($status) {

                    $query->where(
                        'order_status',
                        $status
                    );
                }
            )


            /*
             * Load thông tin Payment.
             */
            ->with([
                'payment',


                /*
                 * Load luôn OrderDetail
                 * để trang danh sách có thể
                 * hiển thị sản phẩm trong đơn.
                 */
                'details' => function ($query) {

                    $query->orderBy('id');
                },
            ])


            /*
             * Đơn mới nhất lên trước.
             */
            ->latest()


            /*
             * Mỗi trang 10 Order.
             */
            ->paginate(10)


            /*
             * Giữ lại query string khi phân trang.
             *
             * Ví dụ:
             *
             * /orders?status=pending&page=2
             */
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'orders.index',
            compact(
                'orders',
                'status'
            )
        );
    }


    /**
     * Chi tiết đơn hàng.
     */
    public function show(
        Order $order
    ) {
        /*
         * Customer chỉ được xem
         * Order của chính mình.
         */
        $this->ensureOwnership(
            $order
        );


        /*
         * Load dữ liệu phục vụ trang
         * chi tiết Order.
         */
        $order->load([
            'details',

            'payment',

            /*
             * Timeline Order:
             * trạng thái cũ trước,
             * trạng thái mới sau.
             */
            'statusHistories' => function ($query) {

                $query->oldest();
            },

            /*
             * Người cập nhật trạng thái.
             */
            'statusHistories.updater',
        ]);


        return view(
            'orders.show',
            compact('order')
        );
    }


    /**
     * Customer hủy Order.
     */
    public function cancel(
        Order $order,
        OrderCancellationService $cancellationService
    ) {
        /*
         * Customer chỉ được hủy
         * Order của chính mình.
         */
        $this->ensureOwnership(
            $order
        );


        /*
         * Logic kiểm tra:
         *
         * - trạng thái Order
         * - quyền hủy
         * - hoàn Stock
         *
         * được xử lý trong
         * OrderCancellationService.
         */
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
        /*
         * Nếu Order không thuộc
         * Customer đang đăng nhập
         * => trả về 403.
         */
        abort_if(
            $order->user_id
                !== auth()->id(),
            403
        );
    }
}