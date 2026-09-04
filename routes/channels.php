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
| - Staff đang phụ trách cuộc trò chuyện
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
                    'staff_id',
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
         * Staff đang phụ trách hội thoại.
         */
        if (
            $user->isStaff()
            && $conversation->staff_id !== null
            && (int) $conversation->staff_id
                === (int) $user->id
        ) {
            return true;
        }

        return false;
    }
);

/*
|--------------------------------------------------------------------------
| STAFF CHAT INBOX
|--------------------------------------------------------------------------
|
| Channel dùng cho danh sách Chat của Staff.
| Chỉ tài khoản Staff mới được phép subscribe.
|
*/

Broadcast::channel(
    'staff.chat.inbox',
    function ($user) {

        return $user->isStaff();
    }
);
/*
|--------------------------------------------------------------------------
| STAFF PERSONAL CHAT INBOX
|--------------------------------------------------------------------------
|
| Mỗi Staff chỉ được subscribe inbox của chính mình.
|
*/

Broadcast::channel(
    'staff.chat.inbox.{staffId}',
    function ($user, $staffId) {

        return $user->isStaff()
            && (int) $user->id === (int) $staffId;
    }
);
