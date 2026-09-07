<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm các mã địa chỉ riêng của GHN.
     *
     * Các trường này tách biệt hoàn toàn với:
     * - province_code
     * - ward_code
     *
     * vì hai trường trên đang dùng mã hành chính
     * của API địa chỉ Việt Nam trước đây.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->unsignedInteger('ghn_province_id')
                ->nullable()
                ->after('province_code');

            $table->unsignedInteger('ghn_district_id')
                ->nullable()
                ->after('district');

            $table->string('ghn_ward_code', 20)
                ->nullable()
                ->after('ward_code');
        });
    }

    /**
     * Hoàn tác migration.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn([
                'ghn_province_id',
                'ghn_district_id',
                'ghn_ward_code',
            ]);
        });
    }
};