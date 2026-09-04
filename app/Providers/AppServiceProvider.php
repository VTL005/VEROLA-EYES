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
        | STAFF LAYOUT DATA
        |--------------------------------------------------------------------------
        |
        | Cung cấp số tin nhắn Customer chưa đọc
        | cho toàn bộ các trang sử dụng layouts.staff.
        |
        */

        View::composer(
            'layouts.staff',
            function ($view) {

                $staffChatUnreadCount = 0;

                $user = auth()->user();

                /*
                 * Chỉ tính khi:
                 *
                 * - Đã đăng nhập
                 * - Tài khoản là Staff
                 */
                if (
                    $user
                    && $user->isStaff()
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
                             * Staff được nhìn thấy:
                             *
                             * - Chat chưa có Staff nhận
                             * - Chat do chính Staff này phụ trách
                             */
                            ->where(
                                function ($query) use ($user) {

                                    $query
                                        ->whereNull(
                                            'staff_id'
                                        )
                                        ->orWhere(
                                            'staff_id',
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

                    $staffChatUnreadCount =
                        (int) $conversations->sum(
                            'unread_messages_count'
                        );
                }

                /*
                 * Biến này sẽ dùng được trong:
                 *
                 * resources/views/layouts/staff.blade.php
                 */
                $view->with(
                    'staffChatUnreadCount',
                    $staffChatUnreadCount
                );
            }
        );
    }
}
