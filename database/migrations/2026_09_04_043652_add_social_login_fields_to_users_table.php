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
        Schema::table('users', function (Blueprint $table) {

            /*
             * ID tài khoản Google.
             */
            $table->string('google_id')
                ->nullable()
                ->unique()
                ->after('email');


            /*
             * ID tài khoản Facebook.
             *
             * Chưa dùng ngay ở bước Google Login,
             * nhưng tạo sẵn để sau này tích hợp Facebook
             * mà không phải sửa cấu trúc users lần nữa.
             */
            $table->string('facebook_id')
                ->nullable()
                ->unique()
                ->after('google_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropUnique([
                'google_id',
            ]);

            $table->dropUnique([
                'facebook_id',
            ]);

            $table->dropColumn([
                'google_id',
                'facebook_id',
            ]);
        });
    }
};