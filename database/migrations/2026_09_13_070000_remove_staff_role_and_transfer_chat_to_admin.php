<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chuyển toàn bộ dữ liệu tư vấn sang Admin và loại bỏ role cũ.
     */
    public function up(): void
    {
        if (Schema::hasTable('chat_conversations')) {
            if (
                Schema::hasColumn('chat_conversations', 'staff_id')
                && ! Schema::hasColumn('chat_conversations', 'admin_id')
            ) {
                Schema::table('chat_conversations', function (Blueprint $table) {
                    $table->foreignId('admin_id')
                        ->nullable()
                        ->after('staff_id')
                        ->constrained('users')
                        ->nullOnDelete();
                });
            }

            if (
                Schema::hasColumn('chat_conversations', 'staff_id')
                && Schema::hasColumn('chat_conversations', 'admin_id')
            ) {
                DB::table('chat_conversations')
                    ->whereNotNull('staff_id')
                    ->whereNull('admin_id')
                    ->update([
                        'admin_id' => DB::raw('staff_id'),
                    ]);
            }

            if (Schema::hasColumn('chat_conversations', 'staff_id')) {
                $hasStaffForeignKey = collect(
                    Schema::getForeignKeys('chat_conversations')
                )->contains(function (array $foreignKey): bool {
                    return in_array(
                        'staff_id',
                        $foreignKey['columns'] ?? [],
                        true
                    );
                });

                if ($hasStaffForeignKey) {
                    Schema::table('chat_conversations', function (Blueprint $table) {
                        $table->dropForeign(['staff_id']);
                    });
                }

                if (
                    Schema::hasIndex(
                        'chat_conversations',
                        'chat_conversations_staff_id_status_index'
                    )
                ) {
                    Schema::table('chat_conversations', function (Blueprint $table) {
                        $table->dropIndex(
                            'chat_conversations_staff_id_status_index'
                        );
                    });
                }

                Schema::table('chat_conversations', function (Blueprint $table) {
                    $table->dropColumn('staff_id');
                });
            }

            if (
                Schema::hasColumn('chat_conversations', 'admin_id')
                && ! Schema::hasIndex(
                    'chat_conversations',
                    'chat_conversations_admin_id_status_index'
                )
            ) {
                Schema::table('chat_conversations', function (Blueprint $table) {
                    $table->index([
                        'admin_id',
                        'status',
                    ]);
                });
            }
        }

        if (
            ! Schema::hasTable('roles')
            || ! Schema::hasTable('users')
        ) {
            return;
        }

        $oldRoleId = DB::table('roles')
            ->where('name', 'staff')
            ->value('id');

        if (! $oldRoleId) {
            return;
        }

        $adminRoleId = DB::table('roles')
            ->where('name', 'admin')
            ->value('id');

        if (! $adminRoleId) {
            throw new RuntimeException(
                'Không thể loại bỏ role cũ vì hệ thống chưa có role admin.'
            );
        }

        DB::transaction(function () use ($oldRoleId, $adminRoleId) {
            $oldUserIds = DB::table('users')
                ->where('role_id', $oldRoleId)
                ->pluck('id');

            if (
                $oldUserIds->isNotEmpty()
                && Schema::hasTable('chat_conversations')
                && Schema::hasColumn('chat_conversations', 'admin_id')
            ) {
                DB::table('chat_conversations')
                    ->where('status', 'open')
                    ->whereIn('admin_id', $oldUserIds)
                    ->update([
                        'admin_id' => null,
                        'updated_at' => now(),
                    ]);
            }

            /*
             * Giữ nguyên User để không làm mất khóa ngoại và lịch sử.
             * Tài khoản được chuyển sang Admin nhưng khóa đăng nhập.
             */
            DB::table('users')
                ->where('role_id', $oldRoleId)
                ->update([
                    'role_id' => $adminRoleId,
                    'is_active' => false,
                    'updated_at' => now(),
                ]);

            DB::table('roles')
                ->where('id', $oldRoleId)
                ->delete();
        });
    }

    /**
     * Khôi phục cấu trúc cột khi rollback.
     * Việc gán lại vai trò từng tài khoản không thể suy luận an toàn.
     */
    public function down(): void
    {
        if (
            Schema::hasTable('roles')
            && ! DB::table('roles')->where('name', 'staff')->exists()
        ) {
            DB::table('roles')->insert([
                'name' => 'staff',
                'display_name' => 'Nhân viên',
                'description' => 'Vai trò được khôi phục khi rollback.',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (
            Schema::hasTable('chat_conversations')
            && Schema::hasColumn('chat_conversations', 'admin_id')
            && ! Schema::hasColumn('chat_conversations', 'staff_id')
        ) {
            Schema::table('chat_conversations', function (Blueprint $table) {
                $table->foreignId('staff_id')
                    ->nullable()
                    ->after('admin_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });

            DB::table('chat_conversations')
                ->whereNotNull('admin_id')
                ->orderBy('id')
                ->chunkById(500, function ($conversations) {
                    foreach ($conversations as $conversation) {
                        DB::table('chat_conversations')
                            ->where('id', $conversation->id)
                            ->update([
                                'staff_id' => $conversation->admin_id,
                            ]);
                    }
                });

            Schema::table('chat_conversations', function (Blueprint $table) {
                $table->dropIndex([
                    'admin_id',
                    'status',
                ]);
                $table->dropForeign(['admin_id']);
                $table->dropColumn('admin_id');
                $table->index([
                    'staff_id',
                    'status',
                ]);
            });
        }
    }
};
