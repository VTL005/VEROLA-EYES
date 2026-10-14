<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->restrictOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        // Từ thời điểm này, đánh giá không còn bị che khỏi khách hàng.
        DB::table('reviews')->where('is_visible', false)->update(['is_visible' => true]);
    }

    public function down(): void
    {
        Schema::dropIfExists('review_replies');
    }
};
