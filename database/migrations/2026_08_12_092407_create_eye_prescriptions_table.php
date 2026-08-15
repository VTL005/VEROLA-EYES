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
            'eye_prescriptions',
            function (Blueprint $table) {

                $table->id();

                /*
                 * Customer sở hữu hồ sơ.
                 */
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->restrictOnDelete();

                /*
                 * Lịch hẹn tạo ra kết quả đo.
                 *
                 * Đây là liên kết kỹ thuật bổ sung
                 * để nối đúng workflow Appointment
                 * → Eye Prescription.
                 */
                $table->foreignId('appointment_id')
                    ->nullable()
                    ->constrained('appointments')
                    ->nullOnDelete();

                /*
                 * Staff thực hiện đo mắt.
                 */
                $table->foreignId('performed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();


                /*
                 * ==========================
                 * MẮT PHẢI
                 * ==========================
                 */

                $table->decimal(
                    'right_sph',
                    5,
                    2
                )
                ->nullable();

                $table->decimal(
                    'right_cyl',
                    5,
                    2
                )
                ->nullable();

                $table->unsignedSmallInteger(
                    'right_axis'
                )
                ->nullable();


                /*
                 * ==========================
                 * MẮT TRÁI
                 * ==========================
                 */

                $table->decimal(
                    'left_sph',
                    5,
                    2
                )
                ->nullable();

                $table->decimal(
                    'left_cyl',
                    5,
                    2
                )
                ->nullable();

                $table->unsignedSmallInteger(
                    'left_axis'
                )
                ->nullable();


                /*
                 * Pupillary Distance.
                 *
                 * Ví dụ:
                 * 62.50 mm
                 */
                $table->decimal(
                    'pd',
                    5,
                    2
                )
                ->nullable();

                /*
                 * Ngày thực hiện đo.
                 */
                $table->date('exam_date');

                $table->text('note')
                    ->nullable();

                $table->timestamps();


                $table->index([
                    'user_id',
                    'exam_date',
                ]);

                $table->index('appointment_id');

                $table->index('performed_by');

            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'eye_prescriptions'
        );
    }
};