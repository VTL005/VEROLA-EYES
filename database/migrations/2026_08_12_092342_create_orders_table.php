<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            /*
             * Mã đơn dễ đọc.
             *
             * Ví dụ:
             * VLR-20260813-000001
             */
            $table->string('order_code', 50)
                ->unique();

            /*
             * Customer tạo đơn.
             *
             * Checkout yêu cầu đăng nhập,
             * nên user_id không nullable.
             */
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Voucher sử dụng cho Order.
             *
             * Có thể NULL nếu khách không dùng Voucher.
             */
            $table->foreignId('voucher_id')
                ->nullable()
                ->constrained('vouchers')
                ->nullOnDelete();

            /*
             * Snapshot thông tin nhận hàng.
             *
             * Không phụ thuộc vào Address hiện tại
             * vì Customer có thể sửa Address sau này.
             */
            $table->string('customer_name', 100);

            $table->string('phone', 20);

            $table->string('email', 255);

            $table->text('address');

            /*
             * Tiền hàng trước Voucher và Shipping.
             */
            $table->decimal(
                'subtotal',
                15,
                2
            );

            /*
             * Số tiền Voucher thực tế đã giảm.
             */
            $table->decimal(
                'discount_amount',
                15,
                2
            )
            ->default(0);

            /*
             * Phí vận chuyển.
             */
            $table->decimal(
                'shipping_fee',
                15,
                2
            )
            ->default(0);

            /*
             * Tổng thanh toán cuối cùng.
             *
             * total =
             * subtotal
             * - discount_amount
             * + shipping_fee
             */
            $table->decimal(
                'total',
                15,
                2
            );

            /*
             * cod
             * qr
             * vnpay
             */
            $table->string(
                'payment_method',
                20
            );

            /*
             * unpaid
             * pending
             * paid
             * failed
             * refunded
             */
            $table->string(
                'payment_status',
                20
            )
            ->default('pending');

            /*
             * pending
             * confirmed
             * preparing
             * packed
             * shipping
             * completed
             * cancelled
             */
            $table->string(
                'order_status',
                30
            )
            ->default('pending');

            $table->text('note')
                ->nullable();

            /*
             * Dùng để đảm bảo một Order
             * chỉ được hoàn tồn kho đúng một lần.
             */
            $table->timestamp(
                'stock_restored_at'
            )
            ->nullable();

            $table->timestamps();


            /*
             * Index phục vụ lịch sử Order,
             * Admin Dashboard và quản lý Order.
             */
            $table->index([
                'user_id',
                'created_at',
            ]);

            $table->index('order_status');

            $table->index('payment_status');

            $table->index('payment_method');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};