@extends('layouts.staff')


@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/staff-chat.css') }}?v={{ filemtime(public_path('css/staff-chat.css')) }}"
>
@endpush


@section('title', 'Tư vấn khách hàng - Staff')

@section('page-title', 'Tư vấn khách hàng')


@section('content')

@php

    $waitingCount =
        $conversations
            ->whereNull('staff_id')
            ->count();

    $myCount =
        $conversations
            ->where('staff_id', auth()->id())
            ->count();

    $unreadCount =
        $conversations
            ->sum('unread_messages_count');

@endphp


{{-- =========================================================
    PAGE HEADER
========================================================= --}}

<div class="staff-page-header">

    <div>

        <span class="staff-page-kicker">
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

<div class="staff-chat-stats">


    {{-- ĐANG CHỜ --}}
    <div class="staff-chat-stat">

        <span class="staff-chat-stat-label">
            Đang chờ
        </span>

        <strong id="staffChatWaitingCount">
            {{ $waitingCount }}
        </strong>

        <small>
            cuộc trò chuyện
        </small>

    </div>


    {{-- TÔI ĐANG HỖ TRỢ --}}
    <div class="staff-chat-stat">

        <span class="staff-chat-stat-label">
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
    <div class="staff-chat-stat">

        <span class="staff-chat-stat-label">
            Tin chưa đọc
        </span>

        <strong id="staffChatUnreadCount">
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

<div class="staff-chat-panel">


    {{-- =====================================================
        PANEL HEADER
    ====================================================== --}}

    <div class="staff-chat-panel-header">

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
            class="staff-chat-total"
            id="staffChatTotal"
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
            class="staff-chat-empty"
            id="staffChatEmpty"
        >

            <div class="staff-chat-empty-icon">

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
            class="staff-chat-list"
            id="staffChatList"
        >

            @foreach ($conversations as $conversation)

                @php

                    $isMine =
                        $conversation->staff_id
                        === auth()->id();

                    $isWaiting =
                        $conversation->staff_id
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
                    class="staff-chat-item
                    {{ $isMine ? 'is-mine' : 'is-waiting' }}
                    {{ $unread > 0 ? 'has-unread' : '' }}"
                >


                    {{-- =====================================
                        CUSTOMER AVATAR
                    ====================================== --}}

                    <div class="staff-chat-customer-avatar">

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

                    <div class="staff-chat-item-main">


                        {{-- =================================
                            TOP
                        ================================== --}}

                        <div class="staff-chat-item-top">

                            <div>

                                <h3>
                                    {{ $conversation->customer?->name
                                        ?? 'Khách hàng'
                                    }}
                                </h3>


                                <div class="staff-chat-customer-meta">

                                    <span>
                                        {{ $conversation->customer?->email
                                            ?? 'Chưa có email'
                                        }}
                                    </span>


                                    @if ($conversation->customer?->phone)

                                        <span class="staff-chat-meta-separator">
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

                            <div class="staff-chat-item-status">

                                @if ($isMine)

                                    <span class="staff-chat-badge mine">
                                        Đang hỗ trợ
                                    </span>

                                @else

                                    <span class="staff-chat-badge waiting">
                                        Đang chờ
                                    </span>

                                @endif


                                @if ($unread > 0)

                                    <span class="staff-chat-unread">
                                        {{ $unread }}
                                    </span>

                                @endif

                            </div>

                        </div>


                        {{-- =================================
                            LAST MESSAGE
                        ================================== --}}

                        <div class="staff-chat-preview">

                            @if ($latestMessage)

                                <span class="staff-chat-preview-sender">

                                    @if (
                                        $latestMessage->sender_id
                                        === $conversation->customer_id
                                    )

                                        Khách:

                                    @else

                                        Bạn:

                                    @endif

                                </span>


                                <span class="staff-chat-preview-message">

                                    {{ \Illuminate\Support\Str::limit(
                                        $latestMessage->message,
                                        95
                                    ) }}

                                </span>

                            @else

                                <span class="staff-chat-preview-sender">
                                    Khách:
                                </span>

                                <span class="staff-chat-preview-message">
                                    Chưa có tin nhắn.
                                </span>

                            @endif

                        </div>


                        {{-- =================================
                            BOTTOM
                        ================================== --}}

                        <div class="staff-chat-item-bottom">

                            <span class="staff-chat-time">

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

                            <div class="staff-chat-actions">

                                @if ($isWaiting)

                                    <form
                                        action="{{ route(
                                            'staff.chat.accept',
                                            $conversation
                                        ) }}"
                                        method="POST"
                                    >

                                        @csrf


                                        <button
                                            type="submit"
                                            class="staff-chat-btn primary"
                                        >
                                            Tiếp nhận
                                        </button>

                                    </form>

                                @endif


                                <a
                                    href="{{ route(
                                        'staff.chat.show',
                                        $conversation
                                    ) }}"
                                    class="staff-chat-btn secondary"
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

        const currentStaffId =
            {{ (int) auth()->id() }};


        const chatList =
            document.getElementById(
                'staffChatList'
            );


        const unreadTotal =
            document.getElementById(
                'staffChatUnreadCount'
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
                    '.staff-chat-preview-sender'
                );


            const previewMessage =
                item.querySelector(
                    '.staff-chat-preview-message'
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
                    '.staff-chat-time'
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
                    '.staff-chat-unread'
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
                    'staff-chat-unread';


                unreadBadge.textContent =
                    '1';


                const status =
                    item.querySelector(
                        '.staff-chat-item-status'
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
        | Hội thoại chưa được Staff nào tiếp nhận.
        |
        */

        window.Echo
            .private(
                'staff.chat.inbox'
            )
            .listen(
                '.staff.chat.inbox.updated',
                handleInboxUpdate
            );


        /*
        |--------------------------------------------------------------------------
        | CHANNEL 2
        |--------------------------------------------------------------------------
        |
        | Hội thoại mà Staff hiện tại đang phụ trách.
        |
        */

        window.Echo
            .private(
                'staff.chat.inbox.'
                + currentStaffId
            )
            .listen(
                '.staff.chat.inbox.updated',
                handleInboxUpdate
            );

    }
);
</script>

@endpush