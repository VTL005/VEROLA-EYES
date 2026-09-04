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
            'chat_message_attachments',
            function (Blueprint $table) {

                $table->id();

                /*
                |--------------------------------------------------------------------------
                | CHAT MESSAGE
                |--------------------------------------------------------------------------
                |
                | Khi message bị xóa
                | attachment trong database cũng bị xóa theo.
                |
                */

                $table
                    ->foreignId(
                        'chat_message_id'
                    )
                    ->constrained(
                        'chat_messages'
                    )
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | LOẠI FILE
                |--------------------------------------------------------------------------
                |
                | Hiện tại:
                | image
                |
                | Sau này nếu cần có thể mở rộng:
                | file, document...
                |
                */

                $table
                    ->string(
                        'attachment_type',
                        20
                    )
                    ->default('image');

                /*
                |--------------------------------------------------------------------------
                | STORAGE DISK
                |--------------------------------------------------------------------------
                |
                | Hiện tại sẽ lưu ở disk public.
                |
                */

                $table
                    ->string(
                        'disk',
                        30
                    )
                    ->default('public');

                /*
                |--------------------------------------------------------------------------
                | ĐƯỜNG DẪN FILE
                |--------------------------------------------------------------------------
                |
                | Ví dụ:
                |
                | chat/11/2026/09/abc123.webp
                |
                */

                $table
                    ->string(
                        'file_path',
                        500
                    );

                /*
                |--------------------------------------------------------------------------
                | TÊN FILE GỐC
                |--------------------------------------------------------------------------
                */

                $table
                    ->string(
                        'original_name'
                    )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | MIME TYPE
                |--------------------------------------------------------------------------
                |
                | image/jpeg
                | image/png
                | image/webp
                |
                */

                $table
                    ->string(
                        'mime_type',
                        100
                    )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | FILE SIZE
                |--------------------------------------------------------------------------
                |
                | Đơn vị: byte
                |
                */

                $table
                    ->unsignedBigInteger(
                        'file_size'
                    )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | THỨ TỰ HIỂN THỊ
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedTinyInteger(
                        'sort_order'
                    )
                    ->default(0);

                $table->timestamps();

                /*
                |--------------------------------------------------------------------------
                | INDEX
                |--------------------------------------------------------------------------
                */

                $table->index([
                    'chat_message_id',
                    'sort_order',
                ]);

                $table->index(
                    'attachment_type'
                );
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'chat_message_attachments'
        );
    }
};
