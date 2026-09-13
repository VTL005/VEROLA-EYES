@extends('layouts.admin')


@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/admin-chat.css') }}?v={{ filemtime(public_path('css/admin-chat.css')) }}"
>
@endpush


@section('title', 'Hội thoại khách hàng - Admin')

@section('page-title', 'Tư vấn khách hàng')


@section('content')

@php

    $isMine =
        (int) $conversation->admin_id
        === (int) auth()->id();

    $isWaiting =
        $conversation->admin_id === null;

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
            Hội thoại với
            {{ $conversation->customer?->name ?? 'Khách hàng' }}
        </h1>

        <p>
            Theo dõi và phản hồi trực tiếp
            yêu cầu hỗ trợ của khách hàng.
        </p>

    </div>


    <a
        href="{{ route('admin.chat.index') }}"
        class="admin-chat-back"
    >
        ← Danh sách hội thoại
    </a>

</div>


{{-- =========================================================
    SUCCESS
========================================================= --}}

@if (session('chat_success'))

    <div class="admin-chat-success">
        {{ session('chat_success') }}
    </div>

@endif


{{-- =========================================================
    CHAT WORKSPACE
========================================================= --}}

<div class="admin-chat-workspace">


    {{-- =====================================================
        CUSTOMER INFO
    ====================================================== --}}

    <aside class="admin-chat-profile">

        <div class="admin-chat-profile-avatar">

            {{ mb_strtoupper(
                mb_substr(
                    $conversation->customer?->name
                        ?? 'K',
                    0,
                    1
                )
            ) }}

        </div>


        <h2>
            {{ $conversation->customer?->name
                ?? 'Khách hàng'
            }}
        </h2>


        <span class="admin-chat-profile-role">
            Khách hàng VELORA Eyes
        </span>


        <div class="admin-chat-profile-info">

            <div>

                <span>
                    Email
                </span>

                <strong>
                    {{ $conversation->customer?->email
                        ?? 'Chưa cập nhật'
                    }}
                </strong>

            </div>


            <div>

                <span>
                    Số điện thoại
                </span>

                <strong>
                    {{ $conversation->customer?->phone
                        ?? 'Chưa cập nhật'
                    }}
                </strong>

            </div>


            <div>

                <span>
                    Trạng thái
                </span>

                <strong>

                    @if ($isMine)

                        Đang được bạn hỗ trợ

                    @elseif ($isWaiting)

                        Đang chờ tiếp nhận

                    @else

                        Đã có quản trị viên phụ trách

                    @endif

                </strong>

            </div>

        </div>


        {{-- =================================================
            ACCEPT CHAT
        ================================================== --}}

        @if ($isWaiting)

            <form
                action="{{ route(
                    'admin.chat.accept',
                    $conversation
                ) }}"
                method="POST"
                class="admin-chat-profile-action"
            >

                @csrf


                <button
                    type="submit"
                    class="admin-chat-accept-btn"
                >
                    Tiếp nhận cuộc trò chuyện
                </button>

            </form>

        @endif


        {{-- =================================================
            CLOSE CHAT
        ================================================== --}}

        @if ($isMine && $conversation->isOpen())

            <form
                action="{{ route(
                    'admin.chat.close',
                    $conversation
                ) }}"
                method="POST"
                class="admin-chat-profile-action"
                onsubmit="return confirm('Bạn có chắc muốn kết thúc cuộc trò chuyện này?');"
            >

                @csrf
                @method('PATCH')


                <button
                    type="submit"
                    class="admin-chat-close-btn"
                >
                    Kết thúc hỗ trợ
                </button>

            </form>

        @endif

    </aside>


    {{-- =====================================================
        CONVERSATION
    ====================================================== --}}

    <section
        class="admin-chat-conversation"
        data-open-conversation-id="{{ $conversation->id }}"
    >


        {{-- =================================================
            CONVERSATION HEADER
        ================================================== --}}

        <div class="admin-chat-conversation-header">

            <div>

                <span>
                    HỘI THOẠI
                </span>

                <h2>
                    {{ $conversation->customer?->name
                        ?? 'Khách hàng'
                    }}
                </h2>

            </div>


            <div class="admin-chat-conversation-status">

                <span></span>

                @if ($isMine)

                    Đang hỗ trợ

                @elseif ($isWaiting)

                    Đang chờ

                @else

                    Đang xử lý

                @endif

            </div>

        </div>


        {{-- =================================================
            MESSAGES
        ================================================== --}}

        <div
            class="admin-chat-messages"
            id="adminChatMessages"
        >

            @forelse (
                $conversation->messages
                as $chatMessage
            )

                @php

                    $isAdminMessage =
                        (int) $chatMessage->sender_id
                        === (int) auth()->id();

                @endphp


                <div
                    class="admin-chat-message-row
                    {{ $isAdminMessage
                        ? 'is-admin'
                        : 'is-customer'
                    }}"
                >

                    <div class="admin-chat-message-box">


                        {{-- =================================
                            SENDER
                        ================================== --}}

                        @unless ($isAdminMessage)

                            <div class="admin-chat-message-name">

                                {{ $chatMessage->sender?->name
                                    ?? 'Khách hàng'
                                }}

                            </div>

                        @endunless


                        {{-- =================================
    MESSAGE CONTENT
================================== --}}

<div class="admin-chat-message-content">{{ $chatMessage->message }}</div>

{{-- =================================
    IMAGE ATTACHMENTS
================================== --}}

@if ($chatMessage->attachments->isNotEmpty())

    <div class="admin-chat-message-images">

        @foreach ($chatMessage->attachments as $attachment)

            @if ($attachment->isImage())

                <a
                    href="{{ $attachment->url }}"
                    class="admin-chat-message-image"
                    target="_blank"
                    rel="noopener"
                >

                    <img
                        src="{{ $attachment->url }}"
                        alt="{{ $attachment->original_name ?? 'Ảnh trong cuộc trò chuyện' }}"
                        loading="lazy"
                    >

                </a>

            @endif

        @endforeach

    </div>

@endif


{{-- =================================
    PRODUCT SUGGESTION CARDS
================================== --}}

@if (
    $chatMessage->isProductList()
    && $chatMessage->products->isNotEmpty()
)

    <div class="admin-chat-message-products">

        @foreach ($chatMessage->products as $product)

            @php

                $imagePath =
                    $product->primaryImage?->image_path
                    ?? 'images/no-image.png';

                $currentPrice =
                    (float) $product->current_price;

                $originalPrice =
                    (float) $product->price;

                $hasSale =
                    $product->sale_price !== null
                    && (float) $product->sale_price > 0
                    && (float) $product->sale_price < $originalPrice;

            @endphp


            <a
                href="{{ route(
                    'products.show',
                    $product
                ) }}"
                class="admin-chat-message-product-card"
                target="_blank"
                rel="noopener"
            >

                <span class="admin-chat-message-product-image">

                    <img
                        src="{{ asset($imagePath) }}"
                        alt="{{ $product->name }}"
                        loading="lazy"
                    >

                </span>


                <span class="admin-chat-message-product-info">

                    <strong>
                        {{ $product->name }}
                    </strong>


                    @if ($product->sku)

                        <small>
                            {{ $product->sku }}
                        </small>

                    @endif


                    <span class="admin-chat-message-product-price">

                        {{ number_format(
                            $currentPrice,
                            0,
                            ',',
                            '.'
                        ) }}đ


                        @if ($hasSale)

                            <del>
                                {{ number_format(
                                    $originalPrice,
                                    0,
                                    ',',
                                    '.'
                                ) }}đ
                            </del>

                        @endif

                    </span>

                </span>


                <span class="admin-chat-message-product-action">
                    Xem →
                </span>

            </a>

        @endforeach

    </div>

@endif


                        {{-- =================================
                            META
                        ================================== --}}

                        <div class="admin-chat-message-meta">

                            <span>
                                {{ $chatMessage->created_at->format('H:i') }}
                            </span>


                            @if ($isAdminMessage)

                                <span
                                    class="admin-chat-read-status"
                                    data-message-id="{{ $chatMessage->id }}"
                                >
                                    {{ $chatMessage->read_at
                                        ? 'Đã đọc'
                                        : 'Đã gửi'
                                    }}
                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                <div class="admin-chat-messages-empty">
                    Chưa có tin nhắn trong cuộc trò chuyện.
                </div>

            @endforelse

        </div>


        {{-- =================================================
            MESSAGE ERROR
        ================================================== --}}

        @error('message')

            <div class="admin-chat-message-error">
                {{ $message }}
            </div>

        @enderror


        {{-- =================================================
            PRODUCT SUGGESTION
        ================================================== --}}

        @if ($isMine && $conversation->isOpen())

            <div class="admin-chat-product-suggestion">


                {{-- =========================================
                    TOGGLE
                ========================================== --}}

                <button
                    type="button"
                    class="admin-chat-product-toggle"
                    id="adminChatProductToggle"
                >

                    <span>
                        + Gợi ý sản phẩm
                    </span>

                    <small>
                        Chọn tối đa 5 sản phẩm
                    </small>

                </button>


                {{-- =========================================
                    PRODUCT PANEL
                ========================================== --}}

                <div
                    class="admin-chat-product-panel"
                    id="adminChatProductPanel"
                    hidden
                >


                    {{-- =====================================
                        HEADER
                    ====================================== --}}

                    <div class="admin-chat-product-panel-header">

                        <div>

                            <strong>
                                Gợi ý sản phẩm cho khách hàng
                            </strong>

                            <span>
                                Chọn từ 1 đến 5 sản phẩm phù hợp.
                            </span>

                        </div>


                        <button
                            type="button"
                            class="admin-chat-product-close"
                            id="adminChatProductClose"
                            aria-label="Đóng"
                        >
                            ×
                        </button>

                    </div>


                    {{-- =====================================
                        SEARCH
                    ====================================== --}}

                    <div class="admin-chat-product-search-wrap">

                        <input
                            type="search"
                            id="adminChatProductSearch"
                            class="admin-chat-product-search"
                            placeholder="Tìm theo tên hoặc mã sản phẩm..."
                            autocomplete="off"
                        >


                        <span
                            class="admin-chat-product-selected-count"
                            id="adminChatProductSelectedCount"
                        >
                            Đã chọn 0/5
                        </span>

                    </div>


                    {{-- =====================================
                        PRODUCT FORM
                    ====================================== --}}

                    <form
                        action="{{ route(
                            'admin.chat.products.store',
                            $conversation
                        ) }}"
                        method="POST"
                        id="adminChatProductForm"
                    >

                        @csrf


                        @error('product_ids')

                            <div class="admin-chat-message-error">
                                {{ $message }}
                            </div>

                        @enderror


                        {{-- =================================
                            LIST
                        ================================== --}}

                        <div
                            class="admin-chat-product-list"
                            id="adminChatProductList"
                        >

                            @forelse ($products as $product)

                                @php

                                    $imagePath =
                                        $product->primaryImage?->image_path
                                        ?? 'images/no-image.png';

                                    $currentPrice =
                                        (float) $product->current_price;

                                    $originalPrice =
                                        (float) $product->price;

                                    $hasSale =
                                        $product->sale_price !== null
                                        && (float) $product->sale_price > 0
                                        && (float) $product->sale_price < $originalPrice;

                                @endphp


                                <label
                                    class="admin-chat-product-option"
                                    data-product-name="{{ mb_strtolower($product->name) }}"
                                    data-product-sku="{{ mb_strtolower($product->sku ?? '') }}"
                                >


                                    <input
                                        type="checkbox"
                                        name="product_ids[]"
                                        value="{{ $product->id }}"
                                        class="admin-chat-product-checkbox"
                                    >


                                    {{-- IMAGE --}}

                                    <span class="admin-chat-product-image">

                                        <img
                                            src="{{ asset($imagePath) }}"
                                            alt="{{ $product->name }}"
                                            loading="lazy"
                                        >

                                    </span>


                                    {{-- INFO --}}

                                    <span class="admin-chat-product-info">

                                        <strong>
                                            {{ $product->name }}
                                        </strong>


                                        <small>

                                            {{ $product->category?->name
                                                ?? 'Sản phẩm kính'
                                            }}

                                            @if ($product->sku)

                                                • {{ $product->sku }}

                                            @endif

                                        </small>


                                        <span class="admin-chat-product-price">

                                            {{ number_format(
                                                $currentPrice,
                                                0,
                                                ',',
                                                '.'
                                            ) }}đ


                                            @if ($hasSale)

                                                <del>

                                                    {{ number_format(
                                                        $originalPrice,
                                                        0,
                                                        ',',
                                                        '.'
                                                    ) }}đ

                                                </del>

                                            @endif

                                        </span>

                                    </span>


                                    {{-- CHECK --}}

                                    <span class="admin-chat-product-checkmark">
                                        ✓
                                    </span>

                                </label>

                            @empty

                                <div class="admin-chat-product-empty">
                                    Hiện chưa có sản phẩm đang kinh doanh.
                                </div>

                            @endforelse

                        </div>


                        {{-- =================================
                            FOOTER
                        ================================== --}}

                        <div class="admin-chat-product-footer">

                            <span>
                                Admin chỉ nên chọn những sản phẩm
                                phù hợp với nhu cầu của khách hàng.
                            </span>


                            <button
                                type="submit"
                                class="admin-chat-product-send"
                                id="adminChatProductSend"
                                disabled
                            >
                                Gửi sản phẩm đã chọn
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        @endif


        {{-- =================================================
            MESSAGE FORM
        ================================================== --}}

        @if ($isMine && $conversation->isOpen())

            <form
                action="{{ route(
                    'admin.chat.messages.store',
                    $conversation
                ) }}"
                method="POST"
                enctype="multipart/form-data"
                class="admin-chat-reply-form"
                id="adminChatReplyForm"
            >

                @csrf


                {{-- =========================================
                    IMAGE PREVIEW
                ========================================== --}}

                <div
                    class="admin-chat-upload-preview"
                    id="adminChatUploadPreview"
                    hidden
                >

                    <div class="admin-chat-upload-preview-header">

                        <span>
                            Ảnh đã chọn
                        </span>

                        <small id="adminChatUploadCount">
                            0/5 ảnh
                        </small>

                    </div>


                    <div
                        class="admin-chat-upload-preview-list"
                        id="adminChatUploadPreviewList"
                    ></div>

                </div>


                {{-- =========================================
                    REPLY AREA
                ========================================== --}}

                <div class="admin-chat-reply-wrap">


                    <button
                        type="button"
                        class="admin-chat-upload-button"
                        id="adminChatUploadButton"
                        aria-label="Chọn ảnh"
                        title="Gửi hình ảnh"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="3"
                                width="18"
                                height="18"
                                rx="2"
                                ry="2"
                            ></rect>

                            <circle
                                cx="8.5"
                                cy="8.5"
                                r="1.5"
                            ></circle>

                            <path
                                d="m21 15-5-5L5 21"
                            ></path>
                        </svg>

                    </button>


                    <input
                        type="file"
                        name="images[]"
                        id="adminChatImages"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        multiple
                        hidden
                    >


                    <textarea
                        name="message"
                        id="adminChatInput"
                        rows="1"
                        maxlength="2000"
                        class="admin-chat-reply-input"
                        placeholder="Nhập nội dung trả lời..."
                    >{{ old('message') }}</textarea>


                    <button
                        type="submit"
                        class="admin-chat-send-btn"
                        id="adminChatSendButton"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path d="M22 2 11 13"></path>

                            <path
                                d="m22 2-7 20-4-9-9-4 20-7Z"
                            ></path>
                        </svg>


                        <span>
                            Gửi
                        </span>

                    </button>

                </div>


                <small id="adminChatReplyNote">
                    Enter để gửi • Shift + Enter để xuống dòng
                    • Tối đa 5 ảnh
                </small>

            </form>


        @elseif ($isWaiting)

            <div class="admin-chat-disabled">

                Bạn cần tiếp nhận cuộc trò chuyện
                trước khi có thể trả lời khách hàng.

            </div>


        @else

            <div class="admin-chat-disabled">

                Bạn không phụ trách cuộc trò chuyện này.

            </div>

        @endif

    </section>

</div>

@endsection


{{-- =========================================================
    SCRIPTS
========================================================= --}}

@push('scripts')

<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
        |--------------------------------------------------------------------------
        | CHAT ELEMENTS
        |--------------------------------------------------------------------------
        */

        const messages =
            document.getElementById(
                'adminChatMessages'
            );


        const form =
            document.getElementById(
                'adminChatReplyForm'
            );


        const input =
            document.getElementById(
                'adminChatInput'
            );


        /*
        |--------------------------------------------------------------------------
        | IMAGE ELEMENTS
        |--------------------------------------------------------------------------
        */

        const uploadButton =
            document.getElementById(
                'adminChatUploadButton'
            );


        const uploadInput =
            document.getElementById(
                'adminChatImages'
            );


        const uploadPreview =
            document.getElementById(
                'adminChatUploadPreview'
            );


        const uploadPreviewList =
            document.getElementById(
                'adminChatUploadPreviewList'
            );


        const uploadCount =
            document.getElementById(
                'adminChatUploadCount'
            );


        const sendButton =
            document.getElementById(
                'adminChatSendButton'
            );


        const replyNote =
            document.getElementById(
                'adminChatReplyNote'
            );


        let selectedImageFiles = [];


        /*
        |--------------------------------------------------------------------------
        | PRODUCT ELEMENTS
        |--------------------------------------------------------------------------
        */

        const productToggle =
            document.getElementById(
                'adminChatProductToggle'
            );


        const productPanel =
            document.getElementById(
                'adminChatProductPanel'
            );


        const productClose =
            document.getElementById(
                'adminChatProductClose'
            );


        const productSearch =
            document.getElementById(
                'adminChatProductSearch'
            );


        const productForm =
            document.getElementById(
                'adminChatProductForm'
            );


        const productSend =
            document.getElementById(
                'adminChatProductSend'
            );


        const selectedCount =
            document.getElementById(
                'adminChatProductSelectedCount'
            );


        const productOptions =
            document.querySelectorAll(
                '.admin-chat-product-option'
            );


        const productCheckboxes =
            document.querySelectorAll(
                '.admin-chat-product-checkbox'
            );


        /*
        |--------------------------------------------------------------------------
        | IMAGE SETTINGS
        |--------------------------------------------------------------------------
        */

        const allowedImageTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];


        const maxImageSize =
            5 * 1024 * 1024;


        const maxImageCount =
            5;


        /*
        |--------------------------------------------------------------------------
        | SYNC FILE INPUT
        |--------------------------------------------------------------------------
        */

        function updateUploadInputFiles() {

            if (!uploadInput) {
                return;
            }


            const dataTransfer =
                new DataTransfer();


            selectedImageFiles.forEach(
                function (file) {

                    dataTransfer.items.add(
                        file
                    );

                }
            );


            uploadInput.files =
                dataTransfer.files;

        }


        /*
        |--------------------------------------------------------------------------
        | RENDER IMAGE PREVIEW
        |--------------------------------------------------------------------------
        */

        function renderUploadPreviews() {

            if (
                !uploadPreview
                || !uploadPreviewList
                || !uploadCount
            ) {
                return;
            }


            uploadPreviewList.innerHTML =
                '';


            uploadCount.textContent =
                selectedImageFiles.length
                + '/5 ảnh';


            if (
                selectedImageFiles.length === 0
            ) {

                uploadPreview.hidden =
                    true;

                return;
            }


            uploadPreview.hidden =
                false;


            selectedImageFiles.forEach(
                function (file, index) {

                    const item =
                        document.createElement(
                            'div'
                        );


                    item.className =
                        'admin-chat-upload-preview-item';


                    const image =
                        document.createElement(
                            'img'
                        );


                    const objectUrl =
                        URL.createObjectURL(
                            file
                        );


                    image.src =
                        objectUrl;


                    image.alt =
                        'Ảnh đã chọn';


                    image.addEventListener(
                        'load',
                        function () {

                            URL.revokeObjectURL(
                                objectUrl
                            );

                        },
                        {
                            once: true,
                        }
                    );


                    const removeButton =
                        document.createElement(
                            'button'
                        );


                    removeButton.type =
                        'button';


                    removeButton.className =
                        'admin-chat-upload-remove';


                    removeButton.textContent =
                        '×';


                    removeButton.setAttribute(
                        'aria-label',
                        'Bỏ ảnh'
                    );


                    removeButton.title =
                        'Bỏ ảnh';


                    removeButton.addEventListener(
                        'click',
                        function () {

                            selectedImageFiles.splice(
                                index,
                                1
                            );


                            updateUploadInputFiles();

                            renderUploadPreviews();

                        }
                    );


                    item.appendChild(
                        image
                    );


                    item.appendChild(
                        removeButton
                    );


                    uploadPreviewList.appendChild(
                        item
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | OPEN IMAGE PICKER
        |--------------------------------------------------------------------------
        */

        if (
            uploadButton
            && uploadInput
        ) {

            uploadButton.addEventListener(
                'click',
                function () {

                    uploadInput.click();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | SELECT IMAGES
        |--------------------------------------------------------------------------
        */

        if (uploadInput) {

            uploadInput.addEventListener(
                'change',
                function () {

                    const newFiles =
                        Array.from(
                            uploadInput.files
                            || []
                        );


                    const acceptedFiles =
                        [];


                    newFiles.forEach(
                        function (file) {

                            if (
                                !allowedImageTypes.includes(
                                    file.type
                                )
                            ) {

                                alert(
                                    'Ảnh "'
                                    + file.name
                                    + '" không đúng định dạng. '
                                    + 'Chỉ chấp nhận JPG, JPEG, PNG hoặc WebP.'
                                );

                                return;
                            }


                            if (
                                file.size
                                > maxImageSize
                            ) {

                                alert(
                                    'Ảnh "'
                                    + file.name
                                    + '" vượt quá 5MB.'
                                );

                                return;
                            }


                            const duplicated =
                                selectedImageFiles.some(
                                    function (
                                        selectedFile
                                    ) {

                                        return (
                                            selectedFile.name
                                                === file.name
                                            && selectedFile.size
                                                === file.size
                                            && selectedFile.lastModified
                                                === file.lastModified
                                        );

                                    }
                                );


                            if (duplicated) {
                                return;
                            }


                            acceptedFiles.push(
                                file
                            );

                        }
                    );


                    const remainingSlots =
                        maxImageCount
                        - selectedImageFiles.length;


                    if (
                        acceptedFiles.length
                        > remainingSlots
                    ) {

                        alert(
                            'Mỗi lần chỉ được gửi tối đa 5 ảnh.'
                        );

                    }


                    selectedImageFiles =
                        selectedImageFiles.concat(
                            acceptedFiles.slice(
                                0,
                                remainingSlots
                            )
                        );


                    updateUploadInputFiles();

                    renderUploadPreviews();

                }
            );

        }


        renderUploadPreviews();


        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        const currentUserId =
            {{ (int) auth()->id() }};


        const conversationId =
            {{ (int) $conversation->id }};


        const isMine =
            {{ $isMine ? 'true' : 'false' }};


        const markReadUrl =
            @json(
                route(
                    'admin.chat.read',
                    $conversation
                )
            );


        /*
        |--------------------------------------------------------------------------
        | CSRF
        |--------------------------------------------------------------------------
        */

        const csrfInput =
            document.querySelector(
                'input[name="_token"]'
            );


        const csrfToken =
            csrfInput
                ? csrfInput.value
                : null;


        /*
        |--------------------------------------------------------------------------
        | SCROLL TO LAST MESSAGE
        |--------------------------------------------------------------------------
        */

        if (messages) {

            messages.scrollTop =
                messages.scrollHeight;

        }


        /*
        |--------------------------------------------------------------------------
        | MARK CUSTOMER MESSAGES AS READ
        |--------------------------------------------------------------------------
        */

        async function markCustomerMessagesAsRead() {

            if (
                !isMine
                || !csrfToken
            ) {
                return;
            }


            try {

                const response =
                    await fetch(
                        markReadUrl,
                        {
                            method: 'PATCH',

                            headers: {

                                'X-CSRF-TOKEN':
                                    csrfToken,

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'Accept':
                                    'application/json',
                            },
                        }
                    );


                if (!response.ok) {

                    console.error(
                        'Đánh dấu tin Customer đã đọc thất bại:',
                        response.status
                    );

                }

            } catch (error) {

                console.error(
                    'Không thể đánh dấu tin Customer đã đọc.',
                    error
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER READ RECEIPT
        |--------------------------------------------------------------------------
        */

        function markAdminMessagesAsReadRealtime(
            event
        ) {

            if (!event) {
                return;
            }


            if (
                Number(
                    event.conversation_id
                )
                !== conversationId
            ) {
                return;
            }


            if (
                Number(
                    event.reader_id
                )
                === currentUserId
            ) {
                return;
            }


            const statuses =
                document.querySelectorAll(
                    '.admin-chat-read-status'
                );


            statuses.forEach(
                function (status) {

                    status.textContent =
                        'Đã đọc';

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | APPEND CUSTOMER MESSAGE
        |--------------------------------------------------------------------------
        */

        function appendRealtimeMessage(
            chatMessage
        ) {

            if (!messages) {
                return;
            }


            const row =
                document.createElement(
                    'div'
                );


            row.className =
                'admin-chat-message-row is-customer';


            const messageBox =
                document.createElement(
                    'div'
                );


            messageBox.className =
                'admin-chat-message-box';


            const sender =
                document.createElement(
                    'div'
                );


            sender.className =
                'admin-chat-message-name';


            sender.textContent =
                chatMessage.sender_name
                || 'Khách hàng';


            const content =
                document.createElement(
                    'div'
                );


            content.className =
                'admin-chat-message-content';


            /*
             * Không sử dụng innerHTML.
             */
            content.textContent =
                chatMessage.content
                || '';


            const meta =
                document.createElement(
                    'div'
                );


            meta.className =
                'admin-chat-message-meta';


            const time =
                document.createElement(
                    'span'
                );


            time.textContent =
                chatMessage.time
                || '';


            meta.appendChild(
                time
            );


            messageBox.appendChild(
                sender
            );


            messageBox.appendChild(
                content
            );

            /*
|--------------------------------------------------------------------------
| IMAGE ATTACHMENTS
|--------------------------------------------------------------------------
*/

if (
    Array.isArray(
        chatMessage.attachments
    )
    && chatMessage.attachments.length > 0
) {

    const imageAttachments =
        chatMessage.attachments.filter(
            function (attachment) {

                return (
                    attachment.type === 'image'
                    && attachment.url
                );

            }
        );


    if (imageAttachments.length > 0) {

        const imagesContainer =
            document.createElement(
                'div'
            );


        imagesContainer.className =
            'admin-chat-message-images';


        imageAttachments.forEach(
            function (attachment) {

                /*
                |--------------------------------------------------------------------------
                | IMAGE LINK
                |--------------------------------------------------------------------------
                */

                const imageLink =
                    document.createElement(
                        'a'
                    );


                imageLink.className =
                    'admin-chat-message-image';


                imageLink.href =
                    attachment.url;


                imageLink.target =
                    '_blank';


                imageLink.rel =
                    'noopener';


                /*
                |--------------------------------------------------------------------------
                | IMAGE
                |--------------------------------------------------------------------------
                */

                const image =
                    document.createElement(
                        'img'
                    );


                image.src =
                    attachment.url;


                image.alt =
                    attachment.original_name
                    || 'Ảnh trong cuộc trò chuyện';


                image.loading =
                    'lazy';


                imageLink.appendChild(
                    image
                );


                imagesContainer.appendChild(
                    imageLink
                );

            }
        );


        messageBox.appendChild(
            imagesContainer
        );

    }

}

            messageBox.appendChild(
                meta
            );


            row.appendChild(
                messageBox
            );


            messages.appendChild(
                row
            );


            messages.scrollTop =
                messages.scrollHeight;


            /*
             * Admin đang mở chat
             * nên tin Customer được xem là đã đọc.
             */
            markCustomerMessagesAsRead();

        }


        /*
        |--------------------------------------------------------------------------
        | REALTIME CHAT
        |--------------------------------------------------------------------------
        */

        if (
            window.Echo
            && conversationId
            && messages
            && isMine
        ) {

            const channel =
                window.Echo.private(
                    'chat.conversation.'
                    + conversationId
                );


            /*
             * Tin nhắn mới.
             */
            channel.listen(
                '.chat.message.sent',
                function (event) {

                    if (
                        !event
                        || !event.message
                    ) {
                        return;
                    }


                    const chatMessage =
                        event.message;


                    /*
                     * Tin do chính Admin gửi
                     * không chèn lại.
                     */
                    if (
                        Number(
                            chatMessage.sender_id
                        )
                        === currentUserId
                    ) {
                        return;
                    }


                    appendRealtimeMessage(
                        chatMessage
                    );

                }
            );


            /*
             * Customer đã đọc tin Admin.
             */
            channel.listen(
                '.chat.messages.read',
                function (event) {

                    markAdminMessagesAsReadRealtime(
                        event
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCT PANEL OPEN / CLOSE
        |--------------------------------------------------------------------------
        */

        if (
            productToggle
            && productPanel
        ) {

            productToggle.addEventListener(
                'click',
                function () {

                    productPanel.hidden =
                        !productPanel.hidden;


                    if (
                        !productPanel.hidden
                        && productSearch
                    ) {

                        productSearch.focus();

                    }

                }
            );

        }


        if (
            productClose
            && productPanel
        ) {

            productClose.addEventListener(
                'click',
                function () {

                    productPanel.hidden =
                        true;

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCT SELECTION
        |--------------------------------------------------------------------------
        */

        function updateProductSelection() {

            const checked =
                Array.from(
                    productCheckboxes
                ).filter(
                    function (checkbox) {

                        return checkbox.checked;

                    }
                );


            const count =
                checked.length;


            /*
             * Counter.
             */
            if (selectedCount) {

                selectedCount.textContent =
                    'Đã chọn '
                    + count
                    + '/5';

            }


            /*
             * Submit button.
             */
            if (productSend) {

                productSend.disabled =
                    count < 1;

            }


            /*
             * Selected UI.
             */
            productOptions.forEach(
                function (option) {

                    const checkbox =
                        option.querySelector(
                            '.admin-chat-product-checkbox'
                        );


                    option.classList.toggle(
                        'is-selected',
                        checkbox
                            ? checkbox.checked
                            : false
                    );

                }
            );


            /*
             * Tối đa 5 sản phẩm.
             */
            productCheckboxes.forEach(
                function (checkbox) {

                    if (
                        count >= 5
                        && !checkbox.checked
                    ) {

                        checkbox.disabled =
                            true;

                    } else {

                        checkbox.disabled =
                            false;

                    }

                }
            );

        }


        productCheckboxes.forEach(
            function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    updateProductSelection
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | PRODUCT SEARCH
        |--------------------------------------------------------------------------
        */

        if (productSearch) {

            productSearch.addEventListener(
                'input',
                function () {

                    const keyword =
                        productSearch
                            .value
                            .trim()
                            .toLowerCase();


                    productOptions.forEach(
                        function (option) {

                            const name =
                                option.dataset.productName
                                || '';


                            const sku =
                                option.dataset.productSku
                                || '';


                            const matched =
                                name.includes(
                                    keyword
                                )
                                || sku.includes(
                                    keyword
                                );


                            option.style.display =
                                matched
                                    ? ''
                                    : 'none';

                        }
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PRODUCT FORM VALIDATION
        |--------------------------------------------------------------------------
        */

        if (productForm) {

            productForm.addEventListener(
                'submit',
                function (event) {

                    const checked =
                        Array.from(
                            productCheckboxes
                        ).filter(
                            function (checkbox) {

                                return checkbox.checked;

                            }
                        );


                    if (
                        checked.length < 1
                        || checked.length > 5
                    ) {

                        event.preventDefault();

                        return;
                    }


                    if (productSend) {

                        productSend.disabled =
                            true;

                        productSend.textContent =
                            'Đang gửi...';

                    }

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | INITIAL PRODUCT STATE
        |--------------------------------------------------------------------------
        */

        updateProductSelection();


        /*
        |--------------------------------------------------------------------------
        | CHAT FORM
        |--------------------------------------------------------------------------
        */

        if (
            !form
            || !input
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDATE BEFORE SUBMIT
        |--------------------------------------------------------------------------
        */

        form.addEventListener(
            'submit',
            function (event) {

                const messageValue =
                    input.value.trim();


                if (
                    messageValue === ''
                    && selectedImageFiles.length === 0
                ) {

                    event.preventDefault();


                    if (replyNote) {

                        replyNote.textContent =
                            'Vui lòng nhập tin nhắn hoặc chọn ít nhất một ảnh.';

                    }


                    input.focus();

                    return;
                }


                updateUploadInputFiles();


                if (sendButton) {

                    sendButton.disabled =
                        true;


                    const sendText =
                        sendButton.querySelector(
                            'span'
                        );


                    if (sendText) {

                        sendText.textContent =
                            'Đang gửi...';

                    }

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | AUTO RESIZE
        |--------------------------------------------------------------------------
        */

        input.addEventListener(
            'input',
            function () {

                input.style.height =
                    'auto';


                input.style.height =
                    Math.min(
                        input.scrollHeight,
                        120
                    ) + 'px';


                if (replyNote) {

                    replyNote.textContent =
                        'Enter để gửi • Shift + Enter để xuống dòng • Tối đa 5 ảnh';

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ENTER = SEND
        | SHIFT + ENTER = NEW LINE
        |--------------------------------------------------------------------------
        */

        input.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Enter'
                    && !event.shiftKey
                ) {

                    event.preventDefault();


                    const value =
                        input.value.trim();


                    if (
                        value === ''
                        && selectedImageFiles.length === 0
                    ) {
                        return;
                    }


                    form.requestSubmit();

                }

            }
        );

    }
);
</script>

@endpush
