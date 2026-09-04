@extends('layouts.app')


@push('styles')
<link
    rel="stylesheet"
    href="{{ asset('css/customer-chat.css') }}?v={{ filemtime(public_path('css/customer-chat.css')) }}"
>
@endpush


@section('title', 'Hỗ trợ trực tuyến - VELORA Eyes')


@section(
    'meta_description',
    'Trao đổi trực tuyến với đội ngũ tư vấn VELORA Eyes.'
)


@section('content')

<section class="customer-chat-section">

    <div class="velora-container">

        <div class="customer-chat-shell">


            {{-- =========================================================
                HEADER
            ========================================================== --}}

            <div class="customer-chat-header">

                <div class="customer-chat-header-info">

                    <div class="customer-chat-avatar">
                        V
                    </div>


                    <div>

                        <span class="customer-chat-kicker">
                            VELORA EYES SUPPORT
                        </span>


                        <h1>
                            Tư vấn trực tuyến
                        </h1>


                        <p>

                            @if ($conversation?->staff)

                                Bạn đang được hỗ trợ bởi

                                <strong>
                                    {{ $conversation->staff->name }}
                                </strong>

                            @else

                                Hãy gửi câu hỏi.
                                Nhân viên VELORA Eyes sẽ hỗ trợ bạn.

                            @endif

                        </p>

                    </div>

                </div>


                {{-- =====================================================
                    STATUS
                ====================================================== --}}

                <div class="customer-chat-status">

                    <span
                        class="customer-chat-status-dot"
                        id="customerChatStatusDot"
                    ></span>

                    <span id="customerChatStatusText">
                        Hỗ trợ trực tuyến
                    </span>

                </div>

            </div>


            {{-- =========================================================
                MESSAGE AREA
            ========================================================== --}}

            <div
                class="customer-chat-messages"
                id="customerChatMessages"
            >

                @if (
                    $conversation
                    && $conversation->messages->isNotEmpty()
                )

                    @foreach ($conversation->messages as $chatMessage)

                        @php

                            $isMine =
                                $chatMessage->sender_id
                                === auth()->id();

                        @endphp


                        <div
                            class="customer-chat-message-row
                            {{ $isMine
                                ? 'is-customer'
                                : 'is-staff'
                            }}"
                        >

                            <div class="customer-chat-message">


                                {{-- =====================================
                                    SENDER
                                ====================================== --}}

                                @unless ($isMine)

                                    <div class="customer-chat-message-sender">

                                        {{ $chatMessage->sender?->name
                                            ?? 'Nhân viên VELORA Eyes'
                                        }}

                                    </div>

                                @endunless


                                {{-- =====================================
                                    MESSAGE
                                ====================================== --}}

                                <div class="customer-chat-message-content">{{ $chatMessage->message }}</div>

                                {{-- =====================================
    IMAGE ATTACHMENTS
====================================== --}}

@if ($chatMessage->attachments->isNotEmpty())

    <div class="customer-chat-message-images">

        @foreach ($chatMessage->attachments as $attachment)

            @if ($attachment->isImage())

                <a
                    href="{{ $attachment->url }}"
                    class="customer-chat-message-image"
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


@if (
    $chatMessage->isProductList()
    && $chatMessage->products->isNotEmpty()
)

    <div class="customer-chat-message-products">

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
                class="customer-chat-product-card"
                target="_blank"
                rel="noopener"
            >

                <span class="customer-chat-product-image">

                    <img
                        src="{{ asset($imagePath) }}"
                        alt="{{ $product->name }}"
                        loading="lazy"
                    >

                </span>


                <span class="customer-chat-product-info">

                    <strong>
                        {{ $product->name }}
                    </strong>


                    @if ($product->sku)

                        <small>
                            {{ $product->sku }}
                        </small>

                    @endif


                    <span class="customer-chat-product-price">

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


                <span class="customer-chat-product-action">
                    Xem sản phẩm →
                </span>

            </a>

        @endforeach

    </div>

@endif


                                {{-- =====================================
                                    META
                                ====================================== --}}

                                <div class="customer-chat-message-meta">

                                    <span>
                                        {{ $chatMessage->created_at->format('H:i') }}
                                    </span>


                                    @if ($isMine)

                                        <span
                                            class="customer-chat-read-status"
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

                    @endforeach


                @else

                    {{-- =================================================
                        EMPTY STATE
                    ================================================== --}}

                    <div class="customer-chat-empty">

                        <div class="customer-chat-empty-icon">

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


                        <h2>
                            Xin chào!
                        </h2>


                        <p>
                            Bạn cần tư vấn về sản phẩm,
                            đơn hàng hoặc dịch vụ của VELORA Eyes?
                            Hãy gửi tin nhắn cho chúng tôi.
                        </p>

                    </div>

                @endif

            </div>


            {{-- =========================================================
                VALIDATION ERROR
            ========================================================== --}}

            @error('message')

                <div class="customer-chat-error">
                    {{ $message }}
                </div>

            @enderror


            <form
    action="{{ route('customer.chat.store') }}"
    method="POST"
    enctype="multipart/form-data"
    class="customer-chat-form"
    id="customerChatForm"
>

    @csrf


    {{-- =====================================================
        IMAGE PREVIEW
    ====================================================== --}}

    <div
        class="customer-chat-image-preview"
        id="customerChatImagePreview"
        hidden
    >
        <div class="customer-chat-image-preview-header">

            <span>
                Ảnh đã chọn
            </span>

            <small id="customerChatImageCount">
                0/5 ảnh
            </small>

        </div>


        <div
            class="customer-chat-image-preview-list"
            id="customerChatImagePreviewList"
        ></div>

    </div>


    {{-- =====================================================
        INPUT AREA
    ====================================================== --}}

    <div class="customer-chat-input-wrap">


        {{-- IMAGE BUTTON --}}

        <button
            type="button"
            class="customer-chat-image-button"
            id="customerChatImageButton"
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


        {{-- HIDDEN FILE INPUT --}}

        <input
            type="file"
            name="images[]"
            id="customerChatImages"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            multiple
            hidden
        >


        {{-- TEXT MESSAGE --}}

        <textarea
            id="customerChatInput"
            name="message"
            class="customer-chat-input"
            rows="1"
            maxlength="2000"
            placeholder="Nhập tin nhắn..."
        >{{ old('message') }}</textarea>


        {{-- SEND BUTTON --}}

        <button
            type="submit"
            class="customer-chat-send"
            id="customerChatSend"
            aria-label="Gửi tin nhắn"
            title="Gửi tin nhắn"
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


    {{-- =====================================================
        NOTE
    ====================================================== --}}

    <div
        class="customer-chat-form-note"
        id="customerChatFormNote"
    >
        Enter để gửi • Shift + Enter để xuống dòng
        • Tối đa 5 ảnh
    </div>

</form>

        </div>

    </div>

</section>

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
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const form =
            document.getElementById(
                'customerChatForm'
            );


        const input =
            document.getElementById(
                'customerChatInput'
            );


        const messages =
            document.getElementById(
                'customerChatMessages'
            );


        const statusText =
            document.getElementById(
                'customerChatStatusText'
            );


        const statusDot =
            document.getElementById(
                'customerChatStatusDot'
            );


        const formNote =
            document.getElementById(
                'customerChatFormNote'
            );
/*
|--------------------------------------------------------------------------
| IMAGE ELEMENTS
|--------------------------------------------------------------------------
*/

const imageButton =
    document.getElementById(
        'customerChatImageButton'
    );


const imageInput =
    document.getElementById(
        'customerChatImages'
    );


const imagePreview =
    document.getElementById(
        'customerChatImagePreview'
    );


const imagePreviewList =
    document.getElementById(
        'customerChatImagePreviewList'
    );


const imageCount =
    document.getElementById(
        'customerChatImageCount'
    );


let selectedImageFiles = [];

/*
|--------------------------------------------------------------------------
| IMAGE VALIDATION
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
| UPDATE FILE INPUT
|--------------------------------------------------------------------------
*/

function updateImageInputFiles() {

    if (!imageInput) {
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


    imageInput.files =
        dataTransfer.files;

}


/*
|--------------------------------------------------------------------------
| RENDER IMAGE PREVIEW
|--------------------------------------------------------------------------
*/

function renderImagePreviews() {

    if (
        !imagePreview
        || !imagePreviewList
        || !imageCount
    ) {
        return;
    }


    imagePreviewList.innerHTML =
        '';


    imageCount.textContent =
        selectedImageFiles.length
        + '/5 ảnh';


    if (
        selectedImageFiles.length === 0
    ) {

        imagePreview.hidden =
            true;

        return;
    }


    imagePreview.hidden =
        false;


    selectedImageFiles.forEach(
        function (file, index) {

            /*
            |--------------------------------------------------------------------------
            | ITEM
            |--------------------------------------------------------------------------
            */

            const item =
                document.createElement(
                    'div'
                );


            item.className =
                'customer-chat-image-preview-item';


            /*
            |--------------------------------------------------------------------------
            | IMAGE
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | REMOVE BUTTON
            |--------------------------------------------------------------------------
            */

            const removeButton =
                document.createElement(
                    'button'
                );


            removeButton.type =
                'button';


            removeButton.className =
                'customer-chat-image-remove';


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


                    updateImageInputFiles();

                    renderImagePreviews();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | APPEND
            |--------------------------------------------------------------------------
            */

            item.appendChild(
                image
            );


            item.appendChild(
                removeButton
            );


            imagePreviewList.appendChild(
                item
            );

        }
    );

}


/*
|--------------------------------------------------------------------------
| OPEN FILE PICKER
|--------------------------------------------------------------------------
*/

if (
    imageButton
    && imageInput
) {

    imageButton.addEventListener(
        'click',
        function () {

            imageInput.click();

        }
    );

}


/*
|--------------------------------------------------------------------------
| SELECT IMAGES
|--------------------------------------------------------------------------
*/

if (imageInput) {

    imageInput.addEventListener(
        'change',
        function () {

            const newFiles =
                Array.from(
                    imageInput.files
                    || []
                );


            /*
             * Input được click lại nên cần
             * xây lại danh sách từ selectedImageFiles.
             */
            const acceptedFiles = [];


            newFiles.forEach(
                function (file) {

                    /*
                    |--------------------------------------------------------------------------
                    | FILE TYPE
                    |--------------------------------------------------------------------------
                    */

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


                    /*
                    |--------------------------------------------------------------------------
                    | FILE SIZE
                    |--------------------------------------------------------------------------
                    */

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


                    /*
                    |--------------------------------------------------------------------------
                    | DUPLICATE
                    |--------------------------------------------------------------------------
                    */

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


            /*
            |--------------------------------------------------------------------------
            | MAXIMUM 5 IMAGES
            |--------------------------------------------------------------------------
            */

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


            updateImageInputFiles();

            renderImagePreviews();

        }
    );

}


/*
|--------------------------------------------------------------------------
| INITIAL IMAGE STATE
|--------------------------------------------------------------------------
*/

renderImagePreviews();

        /*
        |--------------------------------------------------------------------------
        | DATA
        |--------------------------------------------------------------------------
        */

        const currentUserId =
            {{ (int) auth()->id() }};


        const conversationId =
            {{ $conversation
                ? (int) $conversation->id
                : 'null'
            }};


        const markReadUrl =
            @json(
                $conversation
                    ? route(
                        'customer.chat.read',
                        $conversation
                    )
                    : null
            );


        /*
        |--------------------------------------------------------------------------
        | CSRF
        |--------------------------------------------------------------------------
        */

        const csrfInput =
            form
                ? form.querySelector(
                    'input[name="_token"]'
                )
                : null;


        const csrfValue =
            csrfInput
                ? csrfInput.value
                : null;


        /*
        |--------------------------------------------------------------------------
        | SCROLL XUỐNG TIN MỚI NHẤT
        |--------------------------------------------------------------------------
        */

        if (messages) {

            messages.scrollTop =
                messages.scrollHeight;

        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER ĐÁNH DẤU TIN STAFF ĐÃ ĐỌC
        |--------------------------------------------------------------------------
        */

        async function markStaffMessagesAsRead() {

            if (
                !markReadUrl
                || !csrfValue
                || !conversationId
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
                                    csrfValue,

                                'X-Requested-With':
                                    'XMLHttpRequest',

                                'Accept':
                                    'application/json',
                            },
                        }
                    );


                if (!response.ok) {

                    console.error(
                        'Đánh dấu tin Staff đã đọc thất bại:',
                        response.status
                    );

                }

            } catch (error) {

                console.error(
                    'Không thể đánh dấu tin Staff đã đọc.',
                    error
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | STAFF ĐÃ ĐỌC TIN CUSTOMER
        |--------------------------------------------------------------------------
        */

        function markCustomerMessagesAsReadRealtime(
            event
        ) {

            if (!event) {
                return;
            }


            /*
             * Phải đúng conversation.
             */
            if (
                Number(
                    event.conversation_id
                )
                !== conversationId
            ) {
                return;
            }


            /*
             * Nếu Customer hiện tại chính là
             * người đọc thì không xử lý.
             *
             * Trường hợp cần xử lý:
             * Staff là người đọc.
             */
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
                    '.customer-chat-read-status'
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
        | STAFF KẾT THÚC HỘI THOẠI
        |--------------------------------------------------------------------------
        */

        /*
|--------------------------------------------------------------------------
| STAFF KẾT THÚC HỘI THOẠI
|--------------------------------------------------------------------------
*/

function handleConversationClosed(
    event
) {

    if (!event) {
        return;
    }


    /*
     * Chỉ xử lý đúng hội thoại
     * Customer hiện tại đang mở.
     */
    if (
        Number(
            event.conversation_id
        )
        !== conversationId
    ) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | CẬP NHẬT TRẠNG THÁI NGAY
    |--------------------------------------------------------------------------
    */

    if (statusText) {

        statusText.textContent =
            'Cuộc trò chuyện đã kết thúc';

    }


    if (statusDot) {

        statusDot.style.opacity =
            '0.35';

    }


    /*
    |--------------------------------------------------------------------------
    | TỰ ĐỒNG TẢI LẠI TRANG
    |--------------------------------------------------------------------------
    |
    | Controller chỉ lấy conversation đang open.
    |
    | Vì conversation vừa được closed nên sau reload,
    | giao diện sẽ trở về trạng thái bắt đầu chat mới.
    |
    */

    setTimeout(
        function () {

            window.location.reload();

        },
        500
    );

}


        /*
        |--------------------------------------------------------------------------
        | THÊM TIN NHẮN STAFF REALTIME
        |--------------------------------------------------------------------------
        */

        function appendRealtimeMessage(
            chatMessage
        ) {

            if (!messages) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | ROW
            |--------------------------------------------------------------------------
            */

            const row =
                document.createElement(
                    'div'
                );


            row.className =
                'customer-chat-message-row is-staff';


            /*
            |--------------------------------------------------------------------------
            | MESSAGE BOX
            |--------------------------------------------------------------------------
            */

            const messageBox =
                document.createElement(
                    'div'
                );


            messageBox.className =
                'customer-chat-message';


            /*
            |--------------------------------------------------------------------------
            | STAFF NAME
            |--------------------------------------------------------------------------
            */

            const sender =
                document.createElement(
                    'div'
                );


            sender.className =
                'customer-chat-message-sender';


            sender.textContent =
                chatMessage.sender_name
                || 'Nhân viên VELORA Eyes';


            /*
            |--------------------------------------------------------------------------
            | CONTENT
            |--------------------------------------------------------------------------
            */

            const content =
                document.createElement(
                    'div'
                );


            content.className =
                'customer-chat-message-content';


            /*
             * Không dùng innerHTML.
             *
             * textContent giúp nội dung chat
             * không thể chèn HTML/script.
             */
            content.textContent =
                chatMessage.content
                || '';


            /*
            |--------------------------------------------------------------------------
            | META
            |--------------------------------------------------------------------------
            */

            const meta =
                document.createElement(
                    'div'
                );


            meta.className =
                'customer-chat-message-meta';


            const time =
                document.createElement(
                    'span'
                );


            time.textContent =
                chatMessage.time
                || '';


            /*
            |--------------------------------------------------------------------------
            | APPEND
            |--------------------------------------------------------------------------
            */

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
            'customer-chat-message-images';


        imageAttachments.forEach(
            function (attachment) {

                const imageLink =
                    document.createElement(
                        'a'
                    );


                imageLink.className =
                    'customer-chat-message-image';


                imageLink.href =
                    attachment.url;


                imageLink.target =
                    '_blank';


                imageLink.rel =
                    'noopener';


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
/*
|--------------------------------------------------------------------------
| PRODUCT LIST
|--------------------------------------------------------------------------
*/

if (
    chatMessage.message_type
    === 'product_list'
    && Array.isArray(
        chatMessage.products
    )
    && chatMessage.products.length > 0
) {

    const productsContainer =
        document.createElement(
            'div'
        );


    productsContainer.className =
        'customer-chat-message-products';


    chatMessage.products.forEach(
        function (product) {

            const card =
                document.createElement(
                    'a'
                );


            card.className =
                'customer-chat-product-card';


            card.href =
                product.product_url
                || '#';


            card.target =
                '_blank';


            card.rel =
                'noopener';


            /*
            |--------------------------------------------------------------------------
            | IMAGE
            |--------------------------------------------------------------------------
            */

            const imageWrap =
                document.createElement(
                    'span'
                );


            imageWrap.className =
                'customer-chat-product-image';


            const image =
                document.createElement(
                    'img'
                );


            image.src =
                product.image_url
                || '';


            image.alt =
                product.name
                || 'Sản phẩm VELORA Eyes';


            image.loading =
                'lazy';


            imageWrap.appendChild(
                image
            );


            /*
            |--------------------------------------------------------------------------
            | INFO
            |--------------------------------------------------------------------------
            */

            const info =
                document.createElement(
                    'span'
                );


            info.className =
                'customer-chat-product-info';


            const name =
                document.createElement(
                    'strong'
                );


            name.textContent =
                product.name
                || 'Sản phẩm';


            info.appendChild(
                name
            );


            if (product.sku) {

                const sku =
                    document.createElement(
                        'small'
                    );


                sku.textContent =
                    product.sku;


                info.appendChild(
                    sku
                );

            }


            /*
            |--------------------------------------------------------------------------
            | PRICE
            |--------------------------------------------------------------------------
            */

            const price =
                document.createElement(
                    'span'
                );


            price.className =
                'customer-chat-product-price';


            const currentPrice =
                Number(
                    product.current_price
                    || 0
                );


            price.appendChild(
                document.createTextNode(
                    currentPrice
                        .toLocaleString(
                            'vi-VN'
                        )
                    + 'đ'
                )
            );


            const originalPrice =
                Number(
                    product.price
                    || 0
                );


            const salePrice =
                product.sale_price !== null
                    ? Number(
                        product.sale_price
                    )
                    : null;


            if (
                salePrice !== null
                && salePrice > 0
                && salePrice < originalPrice
            ) {

                const oldPrice =
                    document.createElement(
                        'del'
                    );


                oldPrice.textContent =
                    originalPrice
                        .toLocaleString(
                            'vi-VN'
                        )
                    + 'đ';


                price.appendChild(
                    oldPrice
                );

            }


            info.appendChild(
                price
            );


            /*
            |--------------------------------------------------------------------------
            | ACTION
            |--------------------------------------------------------------------------
            */

            const action =
                document.createElement(
                    'span'
                );


            action.className =
                'customer-chat-product-action';


            action.textContent =
                'Xem sản phẩm →';


            /*
            |--------------------------------------------------------------------------
            | APPEND CARD
            |--------------------------------------------------------------------------
            */

            card.appendChild(
                imageWrap
            );


            card.appendChild(
                info
            );


            card.appendChild(
                action
            );


            productsContainer.appendChild(
                card
            );

        }
    );


    messageBox.appendChild(
        productsContainer
    );

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


            /*
            |--------------------------------------------------------------------------
            | SCROLL
            |--------------------------------------------------------------------------
            */

            messages.scrollTop =
                messages.scrollHeight;


            /*
            |--------------------------------------------------------------------------
            | AUTO MARK READ
            |--------------------------------------------------------------------------
            |
            | Customer đang mở trực tiếp chat,
            | vì vậy tin Staff vừa tới được xem
            | là đã đọc.
            |
            */

            markStaffMessagesAsRead();

        }


        /*
        |--------------------------------------------------------------------------
        | REALTIME
        |--------------------------------------------------------------------------
        */

        if (
            window.Echo
            && conversationId
            && messages
        ) {

            const channel =
                window.Echo.private(
                    'chat.conversation.'
                    + conversationId
                );


            /*
            |--------------------------------------------------------------------------
            | MESSAGE SENT
            |--------------------------------------------------------------------------
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
                     * Tin do chính Customer gửi
                     * không chèn thêm.
                     *
                     * Form hiện tại POST rồi redirect,
                     * Laravel sẽ render message đó.
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
            |--------------------------------------------------------------------------
            | READ RECEIPT
            |--------------------------------------------------------------------------
            */

            channel.listen(
                '.chat.messages.read',
                function (event) {

                    markCustomerMessagesAsReadRealtime(
                        event
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | CONVERSATION CLOSED
            |--------------------------------------------------------------------------
            |
            | Staff kết thúc hỗ trợ
            | → Customer biết ngay mà không F5.
            |
            */

            channel.listen(
                '.chat.conversation.closed',
                function (event) {

                    handleConversationClosed(
                        event
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | FORM
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
| VALIDATE CHAT FORM BEFORE SUBMIT
|--------------------------------------------------------------------------
*/

if (form) {

    form.addEventListener(
        'submit',
        function (event) {

            const messageValue =
                input
                    ? input.value.trim()
                    : '';


            if (
                messageValue === ''
                && selectedImageFiles.length === 0
            ) {

                event.preventDefault();

                if (formNote) {

                    formNote.textContent =
                        'Vui lòng nhập tin nhắn hoặc chọn ít nhất một ảnh.';

                }

                return;
            }


            /*
             * Đồng bộ lần cuối trước khi POST.
             */
            updateImageInputFiles();

        }
    );

}

        /*
        |--------------------------------------------------------------------------
        | AUTO RESIZE TEXTAREA
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

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ENTER = GỬI
        | SHIFT + ENTER = XUỐNG DÒNG
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