<?php

use App\Models\ChatConversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| USER PRIVATE CHANNEL
|--------------------------------------------------------------------------
*/

Broadcast::channel(
    'App.Models.User.{id}',
    function ($user, $id) {

        return (int) $user->id === (int) $id;
    }
);

/*
|--------------------------------------------------------------------------
| CHAT CONVERSATION PRIVATE CHANNEL
|--------------------------------------------------------------------------
|
| Chỉ cho phép:
|
| - Customer sở hữu cuộc trò chuyện
| - Admin đang phụ trách cuộc trò chuyện
|
| nghe dữ liệu realtime của hội thoại đó.
|
*/

Broadcast::channel(
    'chat.conversation.{conversationId}',
    function ($user, $conversationId) {

        $conversation =
            ChatConversation::query()
                ->select([
                    'id',
                    'customer_id',
                    'admin_id',
                ])
                ->find($conversationId);

        if (! $conversation) {
            return false;
        }

        /*
         * Customer sở hữu hội thoại.
         */
        if (
            $user->isCustomer()
            && (int) $conversation->customer_id
                === (int) $user->id
        ) {
            return true;
        }

        /*
         * Admin đang phụ trách hội thoại.
         */
        if (
            $user->isAdmin()
            && $conversation->admin_id !== null
            && (int) $conversation->admin_id
                === (int) $user->id
        ) {
            return true;
        }

        return false;
    }
);

/*
|--------------------------------------------------------------------------
| ADMIN CHAT INBOX
|--------------------------------------------------------------------------
|
| Channel dùng cho danh sách Chat của Admin.
| Chỉ tài khoản Admin mới được phép subscribe.
|
*/

Broadcast::channel(
    'admin.chat.inbox',
    function ($user) {

        return $user->isAdmin();
    }
);
/*
|--------------------------------------------------------------------------
| ADMIN PERSONAL CHAT INBOX
|--------------------------------------------------------------------------
|
| Mỗi Admin chỉ được subscribe inbox của chính mình.
|
*/

Broadcast::channel(
    'admin.chat.inbox.{adminId}',
    function ($user, $adminId) {

        return $user->isAdmin()
            && (int) $user->id === (int) $adminId;
    }
);
