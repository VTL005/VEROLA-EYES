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
        Schema::create('appointments', function (Blueprint $table) {

            $table->id();

            /*
             * Mã lịch hẹn.
             *
             * Ví dụ:
             * APT-20260813-00001
             */
            $table->string('appointment_code', 50)
                ->unique();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            /*
             * Snapshot thông tin liên hệ
             * tại thời điểm đặt lịch.
             */
            $table->string('customer_name', 100);

            $table->string('email', 255);

            $table->string('phone', 20);

            /*
             * Ngày và khung giờ.
             */
            $table->date('appointment_date');

            $table->string('time_slot', 50);

            /*
             * Ví dụ:
             * eye_exam
             * recheck
             * lens_consultation
             * frame_consultation
             */
            $table->string('service_type', 50);

            $table->text('note')
                ->nullable();

            /*
             * pending
             * confirmed
             * completed
             * cancelled
             * no_show
             */
            $table->string('status', 30)
                ->default('pending');

            /*
             * Staff/Admin xác nhận lịch.
             */
            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('confirmed_at')
                ->nullable();

            /*
             * Theo dõi Email Reminder.
             */
            $table->timestamp('reminder_sent_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'appointment_date',
                'time_slot',
            ]);

            $table->index([
                'user_id',
                'status',
            ]);

            $table->index('status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};