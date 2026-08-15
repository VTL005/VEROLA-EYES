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
        Schema::create('payments', function (Blueprint $table) {

            $table->id();

            /*
             * Một Order có thể có nhiều
             * Payment attempt.
             *
             * Ví dụ VNPay lần 1 failed,
             * khách thanh toán lại lần 2.
             */
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

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
             * Số tiền của giao dịch.
             */
            $table->decimal(
                'amount',
                15,
                2
            );

            /*
             * unpaid
             * pending
             * paid
             * failed
             * refunded
             */
            $table->string(
                'status',
                20
            )
            ->default('pending');

            /*
             * Mã giao dịch bên ngoài.
             *
             * QR mô phỏng hoặc COD có thể NULL.
             */
            $table->string(
                'transaction_code',
                150
            )
            ->nullable()
            ->unique();

            /*
             * Mã phản hồi từ VNPay
             * hoặc cổng thanh toán.
             */
            $table->string(
                'response_code',
                50
            )
            ->nullable();

            /*
             * Thời điểm thanh toán thành công.
             */
            $table->timestamp(
                'paid_at'
            )
            ->nullable();

            /*
             * Thời điểm hoàn tiền nếu có.
             */
            $table->timestamp(
                'refunded_at'
            )
            ->nullable();

            $table->timestamps();


            $table->index([
                'order_id',
                'status',
            ]);

            $table->index('payment_method');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};