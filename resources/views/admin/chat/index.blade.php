@extends('layouts.admin')


@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/admin-chat.css') }}?v={{ filemtime(public_path('css/admin-chat.css')) }}"
>
@endpush


@section('title', 'Tư vấn khách hàng - Admin')

@section('page-title', 'Tư vấn khách hàng')


@section('content')

@php

    $waitingCount =
        $conversations
            ->whereNull('admin_id')
            ->count();

    $myCount =
        $conversations
            ->where('admin_id', auth()->id())
            ->count();

    $unreadCount =
        $conversations
            ->sum('unread_messages_count');

@endphp


{{-- =========================================================
    PAGE HEADER
========================================================= --}}

<div class="admin-page-header">

    <div>

        <span class="admin-page-kicker">
            CUSTOMER SUPPORT
        </span>

        <h1>
            Tư vấn trực tuyến
        </h1>

        <p>
            Tiếp nhận và hỗ trợ khách hàng
            đang trao đổi với VELORA Eyes.
        </p>

    </div>

</div>


{{-- =========================================================
    STATS
========================================================= --}}

<div class="admin-chat-stats">


    {{-- ĐANG CHỜ --}}
    <div class="admin-chat-stat">

        <span class="admin-chat-stat-label">
            Đang chờ
        </span>

        <strong id="adminChatWaitingCount">
            {{ $waitingCount }}
        </strong>

        <small>
            cuộc trò chuyện
        </small>

    </div>


    {{-- TÔI ĐANG HỖ TRỢ --}}
    <div class="admin-chat-stat">

        <span class="admin-chat-stat-label">
            Tôi đang hỗ trợ
        </span>

        <strong>
            {{ $myCount }}
        </strong>

        <small>
            cuộc trò chuyện
        </small>

    </div>


    {{-- TIN CHƯA ĐỌC --}}
    <div class="admin-chat-stat">

        <span class="admin-chat-stat-label">
            Tin chưa đọc
        </span>

        <strong id="adminChatUnreadCount">
            {{ $unreadCount }}
        </strong>

        <small>
            tin nhắn
        </small>

    </div>

</div>


{{-- =========================================================
    CHAT PANEL
========================================================= --}}

<div class="admin-chat-panel">


    {{-- =====================================================
        PANEL HEADER
    ====================================================== --}}

    <div class="admin-chat-panel-header">

        <div>

            <h2>
                Hội thoại đang mở
            </h2>

            <p>
                Ưu tiên khách đang chờ và
                các cuộc trò chuyện có tin mới.
            </p>

        </div>


        <span
            class="admin-chat-total"
            id="adminChatTotal"
        >
            {{ $conversations->count() }}
            hội thoại
        </span>

    </div>


    {{-- =====================================================
        EMPTY STATE
    ====================================================== --}}

    @if ($conversations->isEmpty())

        <div
            class="admin-chat-empty"
            id="adminChatEmpty"
        >

            <div class="admin-chat-empty-icon">

                <svg
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"
                    ></path>

                    <path d="M8 9h8"></path>

                    <path d="M8 13h5"></path>
                </svg>

            </div>


            <h3>
                Chưa có khách hàng cần hỗ trợ
            </h3>

            <p>
                Các cuộc trò chuyện mới
                sẽ xuất hiện tại đây.
            </p>

        </div>


    {{-- =====================================================
        CHAT LIST
    ====================================================== --}}

    @else

        <div
            class="admin-chat-list"
            id="adminChatList"
        >

            @foreach ($conversations as $conversation)

                @php

                    $isMine =
                        $conversation->admin_id
                        === auth()->id();

                    $isWaiting =
                        $conversation->admin_id
                        === null;

                    $latestMessage =
                        $conversation->latestMessage;

                    $unread =
                        (int)
                        $conversation
                            ->unread_messages_count;

                @endphp


                <article
                    data-conversation-id="{{ $conversation->id }}"
                    class="admin-chat-item
                    {{ $isMine ? 'is-mine' : 'is-waiting' }}
                    {{ $unread > 0 ? 'has-unread' : '' }}"
                >


                    {{-- =====================================
                        CUSTOMER AVATAR
                    ====================================== --}}

                    <div class="admin-chat-customer-avatar">

                        {{ mb_strtoupper(
                            mb_substr(
                                $conversation->customer?->name
                                    ?? 'K',
                                0,
                                1
                            )
                        ) }}

                    </div>


                    {{-- =====================================
                        MAIN
                    ====================================== --}}

                    <div class="admin-chat-item-main">


                        {{-- =================================
                            TOP
                        ================================== --}}

                        <div class="admin-chat-item-top">

                            <div>

                                <h3>
                                    {{ $conversation->customer?->name
                                        ?? 'Khách hàng'
                                    }}
                                </h3>


                                <div class="admin-chat-customer-meta">

                                    <span>
                                        {{ $conversation->customer?->email
                                            ?? 'Chưa có email'
                                        }}
                                    </span>


                                    @if ($conversation->customer?->phone)

                                        <span class="admin-chat-meta-separator">
                                            •
                                        </span>

                                        <span>
                                            {{ $conversation->customer->phone }}
                                        </span>

                                    @endif

                                </div>

                            </div>


                            {{-- =============================
                                STATUS
                            ============================== --}}

                            <div class="admin-chat-item-status">

                                @if ($isMine)

                                    <span class="admin-chat-badge mine">
                                        Đang hỗ trợ
                                    </span>

                                @else

                                    <span class="admin-chat-badge waiting">
                                        Đang chờ
                                    </span>

                                @endif


                                @if ($unread > 0)

                                    <span class="admin-chat-unread">
                                        {{ $unread }}
                                    </span>

                                @endif

                            </div>

                        </div>


                        {{-- =================================
                            LAST MESSAGE
                        ================================== --}}

                        <div class="admin-chat-preview">

                            @if ($latestMessage)

                                <span class="admin-chat-preview-sender">

                                    @if (
                                        $latestMessage->sender_id
                                        === $conversation->customer_id
                                    )

                                        Khách:

                                    @else

                                        Bạn:

                                    @endif

                                </span>


                                <span class="admin-chat-preview-message">

                                    {{ \Illuminate\Support\Str::limit(
                                        $latestMessage->message,
                                        95
                                    ) }}

                                </span>

                            @else

                                <span class="admin-chat-preview-sender">
                                    Khách:
                                </span>

                                <span class="admin-chat-preview-message">
                                    Chưa có tin nhắn.
                                </span>

                            @endif

                        </div>


                        {{-- =================================
                            BOTTOM
                        ================================== --}}

                        <div class="admin-chat-item-bottom">

                            <span class="admin-chat-time">

                                @if ($conversation->last_message_at)

                                    Hoạt động
                                    {{ $conversation->last_message_at->diffForHumans() }}

                                @else

                                    Mới tạo

                                @endif

                            </span>


                            {{-- =============================
                                ACTIONS
                            ============================== --}}

                            <div class="admin-chat-actions">

                                @if ($isWaiting)

                                    <form
                                        action="{{ route(
                                            'admin.chat.accept',
                                            $conversation
                                        ) }}"
                                        method="POST"
                                    >

                                        @csrf


                                        <button
                                            type="submit"
                                            class="admin-chat-btn primary"
                                        >
                                            Tiếp nhận
                                        </button>

                                    </form>

                                @endif


                                <a
                                    href="{{ route(
                                        'admin.chat.show',
                                        $conversation
                                    ) }}"
                                    class="admin-chat-btn secondary"
                                >
                                    Xem hội thoại
                                </a>

                            </div>

                        </div>

                    </div>

                </article>

            @endforeach

        </div>

    @endif

</div>

@endsection


{{-- =========================================================
    REALTIME
========================================================= --}}

@push('scripts')

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        const currentAdminId =
            {{ (int) auth()->id() }};


        const chatList =
            document.getElementById(
                'adminChatList'
            );


        const unreadTotal =
            document.getElementById(
                'adminChatUnreadCount'
            );


        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA LARAVEL ECHO
        |--------------------------------------------------------------------------
        */

        if (!window.Echo) {

            console.warn(
                'Laravel Echo chưa được khởi tạo.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | XỬ LÝ REALTIME INBOX
        |--------------------------------------------------------------------------
        */

        function handleInboxUpdate(event) {

            /*
             * Event không hợp lệ.
             */
            if (
                !event
                || !event.conversation
            ) {
                return;
            }


            const conversation =
                event.conversation;


            /*
            |--------------------------------------------------------------------------
            | TÌM HỘI THOẠI TRÊN GIAO DIỆN
            |--------------------------------------------------------------------------
            */

            const item =
                document.querySelector(
                    '[data-conversation-id="'
                    + conversation.id
                    + '"]'
                );


            /*
            |--------------------------------------------------------------------------
            | HỘI THOẠI MỚI
            |--------------------------------------------------------------------------
            |
            | Nếu card hội thoại chưa tồn tại trên trang,
            | reload một lần để Laravel render đầy đủ card.
            |
            */

            if (!item) {

                window.location.reload();

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | CẬP NHẬT PREVIEW
            |--------------------------------------------------------------------------
            */

            let previewSender =
                item.querySelector(
                    '.admin-chat-preview-sender'
                );


            const previewMessage =
                item.querySelector(
                    '.admin-chat-preview-message'
                );


            if (previewSender) {

                previewSender.textContent =
                    'Khách:';

            }


            if (previewMessage) {

                const message =
                    conversation.latest_message
                    || '';


                if (message.length > 95) {

                    previewMessage.textContent =
                        message.substring(
                            0,
                            95
                        ) + '...';

                } else {

                    previewMessage.textContent =
                        message;

                }

            }


            /*
            |--------------------------------------------------------------------------
            | CẬP NHẬT THỜI GIAN
            |--------------------------------------------------------------------------
            */

            const time =
                item.querySelector(
                    '.admin-chat-time'
                );


            if (time) {

                time.textContent =
                    'Hoạt động vừa xong';

            }


            /*
            |--------------------------------------------------------------------------
            | BADGE TIN CHƯA ĐỌC
            |--------------------------------------------------------------------------
            */

            let unreadBadge =
                item.querySelector(
                    '.admin-chat-unread'
                );


            /*
             * Chưa có badge
             * → tạo badge mới.
             */
            if (!unreadBadge) {

                unreadBadge =
                    document.createElement(
                        'span'
                    );


                unreadBadge.className =
                    'admin-chat-unread';


                unreadBadge.textContent =
                    '1';


                const status =
                    item.querySelector(
                        '.admin-chat-item-status'
                    );


                if (status) {

                    status.appendChild(
                        unreadBadge
                    );

                }

            }

            /*
             * Đã có badge
             * → tăng thêm 1.
             */
            else {

                const currentUnread =
                    parseInt(
                        unreadBadge.textContent,
                        10
                    ) || 0;


                unreadBadge.textContent =
                    currentUnread + 1;

            }


            /*
            |--------------------------------------------------------------------------
            | TỔNG TIN CHƯA ĐỌC
            |--------------------------------------------------------------------------
            */

            if (unreadTotal) {

                const currentTotal =
                    parseInt(
                        unreadTotal.textContent,
                        10
                    ) || 0;


                unreadTotal.textContent =
                    currentTotal + 1;

            }


            /*
            |--------------------------------------------------------------------------
            | HIGHLIGHT HỘI THOẠI
            |--------------------------------------------------------------------------
            */

            item.classList.add(
                'has-unread'
            );


            /*
            |--------------------------------------------------------------------------
            | ĐƯA HỘI THOẠI MỚI NHẤT LÊN ĐẦU
            |--------------------------------------------------------------------------
            */

            if (chatList) {

                chatList.prepend(
                    item
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | CHANNEL 1
        |--------------------------------------------------------------------------
        |
        | Hội thoại chưa được Admin nào tiếp nhận.
        |
        */

        window.Echo
            .private(
                'admin.chat.inbox'
            )
            .listen(
                '.admin.chat.inbox.updated',
                handleInboxUpdate
            );


        /*
        |--------------------------------------------------------------------------
        | CHANNEL 2
        |--------------------------------------------------------------------------
        |
        | Hội thoại mà Admin hiện tại đang phụ trách.
        |
        */

        window.Echo
            .private(
                'admin.chat.inbox.'
                + currentAdminId
            )
            .listen(
                '.admin.chat.inbox.updated',
                handleInboxUpdate
            );

    }
);
</script>

@endpush
