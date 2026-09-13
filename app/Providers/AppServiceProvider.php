<?php

namespace App\Providers;

use App\Models\ChatConversation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ADMIN LAYOUT DATA
        |--------------------------------------------------------------------------
        |
        | Cung cấp số tin nhắn Customer chưa đọc
        | cho toàn bộ các trang sử dụng layouts.admin.
        |
        */

        View::composer(
            'layouts.admin',
            function ($view) {

                $adminChatUnreadCount = 0;

                $user = auth()->user();

                /*
                 * Chỉ tính khi:
                 *
                 * - Đã đăng nhập
                 * - Tài khoản là Admin
                 */
                if (
                    $user
                    && $user->isAdmin()
                ) {

                    $conversations =
                        ChatConversation::query()

                            /*
                             * Chỉ lấy hội thoại đang mở.
                             */
                            ->where(
                                'status',
                                'open'
                            )

                            /*
                             * Admin được nhìn thấy:
                             *
                             * - Chat chưa có Admin nhận
                             * - Chat do chính Admin này phụ trách
                             */
                            ->where(
                                function ($query) use ($user) {

                                    $query
                                        ->whereNull(
                                            'admin_id'
                                        )
                                        ->orWhere(
                                            'admin_id',
                                            $user->id
                                        );
                                }
                            )

                            /*
                             * Đếm tin Customer chưa đọc.
                             */
                            ->withCount([
                                'messages as unread_messages_count' => function ($query) {

                                    $query
                                        ->whereNull(
                                            'read_at'
                                        )
                                        ->whereColumn(
                                            'chat_messages.sender_id',
                                            'chat_conversations.customer_id'
                                        );
                                },
                            ])

                            ->get();

                    $adminChatUnreadCount =
                        (int) $conversations->sum(
                            'unread_messages_count'
                        );
                }

                /*
                 * Biến này sẽ dùng được trong:
                 *
                 * resources/views/layouts/admin.blade.php
                 */
                $view->with(
                    'adminChatUnreadCount',
                    $adminChatUnreadCount
                );
            }
        );
    }
}
