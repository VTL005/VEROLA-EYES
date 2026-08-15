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
        Schema::create(
            'order_status_histories',
            function (Blueprint $table) {

                $table->id();

                $table->foreignId('order_id')
                    ->constrained('orders')
                    ->cascadeOnDelete();

                /*
                 * Trạng thái tại thời điểm Timeline.
                 */
                $table->string(
                    'status',
                    30
                );

                /*
                 * Nội dung hiển thị cho Timeline.
                 *
                 * Ví dụ:
                 * "Cửa hàng đã xác nhận đơn hàng."
                 */
                $table->text(
                    'description'
                )
                ->nullable();

                /*
                 * Người cập nhật.
                 *
                 * Có thể NULL nếu trạng thái
                 * do hệ thống tự động tạo.
                 */
                $table->foreignId(
                    'updated_by'
                )
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

                $table->timestamps();


                $table->index([
                    'order_id',
                    'created_at',
                ]);

                $table->index('status');

            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'order_status_histories'
        );
    }
};