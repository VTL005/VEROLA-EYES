<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nâng cấp địa chỉ theo mô hình hành chính Việt Nam mới.
     *
     * Địa chỉ mới:
     * - Tỉnh / Thành phố
     * - Phường / Xã / Đặc khu
     * - Địa chỉ chi tiết
     *
     * Trường district vẫn được giữ lại để tương thích
     * với các địa chỉ cũ đã tồn tại trong hệ thống.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {

            /*
             * Mã Tỉnh / Thành phố.
             *
             * Nullable để các địa chỉ cũ chưa có mã
             * vẫn tiếp tục hoạt động bình thường.
             */
            $table->string(
                'province_code',
                20
            )
            ->nullable()
            ->after('province');


            /*
             * Quận / Huyện không còn bắt buộc
             * đối với địa chỉ theo cấu trúc mới.
             *
             * Không xóa cột để giữ dữ liệu địa chỉ cũ.
             */
            $table->string(
                'district',
                100
            )
            ->nullable()
            ->change();


            /*
             * Mã Phường / Xã / Đặc khu.
             *
             * Nullable để tương thích dữ liệu cũ.
             */
            $table->string(
                'ward_code',
                20
            )
            ->nullable()
            ->after('ward');

        });
    }


    /**
     * Hoàn tác migration.
     */
    public function down(): void
    {
        /*
         * Nếu có địa chỉ mới không có district,
         * gán chuỗi rỗng trước khi đưa cột này
         * trở lại trạng thái NOT NULL.
         */
        DB::table('addresses')
            ->whereNull('district')
            ->update([
                'district' => '',
            ]);


        Schema::table('addresses', function (Blueprint $table) {

            $table->dropColumn([
                'province_code',
                'ward_code',
            ]);


            $table->string(
                'district',
                100
            )
            ->nullable(false)
            ->change();

        });
    }
};