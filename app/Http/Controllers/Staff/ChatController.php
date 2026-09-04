<?php

namespace App\Http\Controllers\Staff;

use App\Events\ChatConversationClosed;
use App\Events\ChatMessageSent;
use App\Events\ChatMessagesRead;
use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Danh sách hội thoại dành cho Staff.
     */
    public function index(Request $request)
    {
        $staff = $request->user();

        abort_unless(
            $staff && $staff->isStaff(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | CHỈ HIỂN THỊ:
        | - Chat chưa có Staff nhận
        | - Chat Staff hiện tại đang phụ trách
        |--------------------------------------------------------------------------
        */

        $conversations = ChatConversation::query()
            ->where(
                'status',
                'open'
            )
            ->where(function ($query) use ($staff) {

                $query
                    ->whereNull('staff_id')
                    ->orWhere(
                        'staff_id',
                        $staff->id
                    );
            })
            ->with([
                'customer:id,name,email,phone,avatar',
                'staff:id,name,avatar',
                'latestMessage.sender:id,name',
            ])
            ->withCount([
                'messages as unread_messages_count' => function ($query) {

                    /*
                     * Chỉ đếm tin nhắn do Customer gửi
                     * mà Staff chưa đọc.
                     */
                    $query
                        ->whereNull('read_at')
                        ->whereColumn(
                            'chat_messages.sender_id',
                            'chat_conversations.customer_id'
                        );
                },
            ])
            ->orderByRaw(
                'CASE
                    WHEN staff_id = ? THEN 0
                    WHEN staff_id IS NULL THEN 1
                    ELSE 2
                END',
                [
                    $staff->id,
                ]
            )
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->get();

        return view(
            'staff.chat.index',
            [
                'conversations' => $conversations,
            ]
        );
    }

    /**
     * Hiển thị chi tiết cuộc trò chuyện.
     */
    public function show(
        Request $request,
        ChatConversation $conversation
    ) {
        $staff = $request->user();

        /*
        |--------------------------------------------------------------------------
        | CHỈ STAFF
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $staff
            && $staff->isStaff(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | STAFF KHÁC ĐÃ NHẬN CHAT
        |--------------------------------------------------------------------------
        */

        if (
            $conversation->staff_id
            && (int) $conversation->staff_id
                !== (int) $staff->id
        ) {

            abort(
                403,
                'Cuộc trò chuyện đang được nhân viên khác phụ trách.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ĐÁNH DẤU TIN CUSTOMER ĐÃ ĐỌC
        |--------------------------------------------------------------------------
        |
        | Chỉ đánh dấu khi Staff hiện tại
        | đang thực sự phụ trách hội thoại.
        |
        */

        if (
            (int) $conversation->staff_id
            === (int) $staff->id
        ) {

            $updated =
                ChatMessage::query()
                    ->where(
                        'chat_conversation_id',
                        $conversation->id
                    )
                    ->where(
                        'sender_id',
                        $conversation->customer_id
                    )
                    ->whereNull(
                        'read_at'
                    )
                    ->update([
                        'read_at' => now(),
                    ]);

            /*
            |--------------------------------------------------------------------------
            | READ RECEIPT REALTIME
            |--------------------------------------------------------------------------
            |
            | Customer đang mở chat sẽ thấy:
            |
            | Đã gửi → Đã đọc
            |
            */

            if ($updated > 0) {

                ChatMessagesRead::dispatch(
                    $conversation->id,
                    $staff->id
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD HỘI THOẠI
        |--------------------------------------------------------------------------
        */

        $conversation->load([

            'customer:id,name,email,phone,avatar',

            'staff:id,name,avatar',

            'messages' => function ($query) {

                $query
                    ->with([

                        /*
                         * Người gửi.
                         */
                        'sender:id,name,role_id',

                        /*
                         * Product suggestion.
                         */
                        'products.primaryImage',

                        /*
                         * Ảnh đính kèm trong Chat.
                         */
                        'attachments',

                    ])
                    ->orderBy('created_at')
                    ->orderBy('id');
            },
        ]);

        /*
        |--------------------------------------------------------------------------
        | DANH SÁCH SẢN PHẨM ĐỂ STAFF GỢI Ý
        |--------------------------------------------------------------------------
        |
        | Chỉ lấy Product đang hoạt động.
        |
        | Sau này Staff sẽ chọn tối đa 5 sản phẩm
        | từ danh sách này để gửi cho Customer.
        |
        */

        $products =
            Product::query()
                ->active()
                ->with([
                    'primaryImage',
                    'category',
                ])
                ->orderBy('name')
                ->get();

        /*
        |--------------------------------------------------------------------------
        | VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'staff.chat.show',
            [
                'conversation' => $conversation,

                'products' => $products,
            ]
        );
    }

    /**
     * Staff nhận cuộc trò chuyện.
     */
    public function accept(
        Request $request,
        ChatConversation $conversation
    ) {
        $staff = $request->user();

        abort_unless(
            $staff && $staff->isStaff(),
            403
        );

        DB::transaction(
            function () use (
                $conversation,
                $staff
            ) {

                /*
                 * Lock để tránh 2 Staff
                 * cùng nhận một lúc.
                 */
                $lockedConversation =
                    ChatConversation::query()
                        ->whereKey(
                            $conversation->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();

                /*
                 * Chat đã đóng.
                 */
                if (
                    $lockedConversation->status
                    !== 'open'
                ) {

                    abort(
                        409,
                        'Cuộc trò chuyện đã kết thúc.'
                    );
                }

                /*
                 * Nhân viên khác đã nhận.
                 */
                if (
                    $lockedConversation->staff_id
                    && $lockedConversation->staff_id
                        !== $staff->id
                ) {

                    abort(
                        409,
                        'Cuộc trò chuyện vừa được nhân viên khác tiếp nhận.'
                    );
                }

                /*
                 * Nhận chat.
                 */
                if (
                    ! $lockedConversation->staff_id
                ) {

                    $lockedConversation->update([
                        'staff_id' => $staff->id,
                    ]);
                }
            }
        );

        return redirect()
            ->route(
                'staff.chat.show',
                $conversation
            )
            ->with(
                'chat_success',
                'Bạn đã tiếp nhận cuộc trò chuyện.'
            );
    }

    /**
     * Staff gửi tin nhắn.
     *
     * Hỗ trợ:
     * - Text
     * - Image
     * - Text + Image
     */
    public function store(
        Request $request,
        ChatConversation $conversation
    ) {
        $staff = $request->user();

        /*
        |--------------------------------------------------------------------------
        | CHỈ STAFF
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $staff
            && $staff->isStaff(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATE
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate(
                [
                    'message' => [
                        'nullable',
                        'string',
                        'max:2000',
                    ],

                    'images' => [
                        'nullable',
                        'array',
                        'max:5',
                    ],

                    'images.*' => [
                        'file',
                        'mimes:jpg,jpeg,png,webp',
                        'max:5120',
                    ],
                ],
                [
                    'message.string' => 'Nội dung tin nhắn không hợp lệ.',

                    'message.max' => 'Tin nhắn không được vượt quá 2000 ký tự.',

                    'images.array' => 'Danh sách hình ảnh không hợp lệ.',

                    'images.max' => 'Mỗi lần chỉ được gửi tối đa 5 ảnh.',

                    'images.*.file' => 'Tệp tải lên không hợp lệ.',

                    'images.*.mimes' => 'Ảnh chỉ được có định dạng JPG, JPEG, PNG hoặc WebP.',

                    'images.*.max' => 'Mỗi ảnh không được vượt quá 5MB.',
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | CHUẨN HÓA INPUT
        |--------------------------------------------------------------------------
        */

        $messageText =
            trim(
                (string) (
                    $validated['message']
                    ?? ''
                )
            );

        $uploadedImages =
            $request->file(
                'images',
                []
            );

        if (! is_array($uploadedImages)) {

            $uploadedImages = [
                $uploadedImages,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | PHẢI CÓ CHỮ HOẶC ẢNH
        |--------------------------------------------------------------------------
        */

        if (
            $messageText === ''
            && count($uploadedImages) === 0
        ) {

            return back()
                ->withErrors([
                    'message' => 'Vui lòng nhập tin nhắn hoặc chọn ít nhất một ảnh.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | FILE ĐÃ LƯU
        |--------------------------------------------------------------------------
        |
        | Nếu DB rollback thì xóa các file
        | đã ghi vào storage.
        |
        */

        $storedPaths = [];

        try {

            $message =
                DB::transaction(
                    function () use (
                        $conversation,
                        $staff,
                        $messageText,
                        $uploadedImages,
                        &$storedPaths
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | LOCK CONVERSATION
                        |--------------------------------------------------------------------------
                        */

                        $lockedConversation =
                            ChatConversation::query()
                                ->whereKey(
                                    $conversation->id
                                )
                                ->lockForUpdate()
                                ->firstOrFail();

                        /*
                        |--------------------------------------------------------------------------
                        | CHAT PHẢI CÒN MỞ
                        |--------------------------------------------------------------------------
                        */

                        if (
                            ! $lockedConversation
                                ->isOpen()
                        ) {

                            abort(
                                409,
                                'Cuộc trò chuyện đã kết thúc.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | STAFF PHẢI ĐANG PHỤ TRÁCH
                        |--------------------------------------------------------------------------
                        */

                        if (
                            (int) $lockedConversation
                                ->staff_id
                            !== (int) $staff->id
                        ) {

                            abort(
                                403,
                                'Bạn cần tiếp nhận cuộc trò chuyện trước khi trả lời.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | MESSAGE TYPE
                        |--------------------------------------------------------------------------
                        */

                        $messageType =
                            count(
                                $uploadedImages
                            ) > 0
                                ? ChatMessage::TYPE_IMAGE
                                : ChatMessage::TYPE_TEXT;

                        /*
                        |--------------------------------------------------------------------------
                        | MESSAGE CONTENT
                        |--------------------------------------------------------------------------
                        */

                        $storedMessageText =
                            $messageText !== ''
                                ? $messageText
                                : 'Đã gửi hình ảnh.';

                        /*
                        |--------------------------------------------------------------------------
                        | TẠO MESSAGE
                        |--------------------------------------------------------------------------
                        */

                        $message =
                            ChatMessage::create([
                                'chat_conversation_id' => $lockedConversation->id,

                                'sender_id' => $staff->id,

                                'message_type' => $messageType,

                                'message' => $storedMessageText,

                                'read_at' => null,
                            ]);

                        /*
                        |--------------------------------------------------------------------------
                        | LƯU ẢNH
                        |--------------------------------------------------------------------------
                        */

                        foreach (
                            $uploadedImages as $index => $file
                        ) {

                            $directory =
                                'chat/'
                                .$lockedConversation->id
                                .'/'
                                .now()->format('Y/m');

                            /*
                             * Laravel tự sinh tên file.
                             * Không dùng tên gốc làm tên file lưu.
                             */
                            $path =
                                $file->store(
                                    $directory,
                                    'public'
                                );

                            $storedPaths[] =
                                $path;

                            /*
                             * Lưu metadata attachment.
                             */
                            $message
                                ->attachments()
                                ->create([
                                    'attachment_type' => 'image',

                                    'disk' => 'public',

                                    'file_path' => $path,

                                    'original_name' => mb_substr(
                                        (string) $file
                                            ->getClientOriginalName(),
                                        0,
                                        250
                                    ),

                                    'mime_type' => $file->getMimeType(),

                                    'file_size' => $file->getSize(),

                                    'sort_order' => $index + 1,
                                ]);
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE CONVERSATION
                        |--------------------------------------------------------------------------
                        */

                        $lockedConversation
                            ->update([
                                'last_message_at' => $message->created_at,
                            ]);

                        return $message;
                    }
                );

        } catch (\Throwable $exception) {

            /*
            |--------------------------------------------------------------------------
            | XÓA FILE NẾU TRANSACTION THẤT BẠI
            |--------------------------------------------------------------------------
            */

            if (! empty($storedPaths)) {

                Storage::disk(
                    'public'
                )->delete(
                    $storedPaths
                );
            }

            throw $exception;
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD DỮ LIỆU CHO REALTIME
        |--------------------------------------------------------------------------
        */

        $message->load([
            'sender:id,name,role_id',
            'attachments',
        ]);

        /*
        |--------------------------------------------------------------------------
        | BROADCAST REALTIME
        |--------------------------------------------------------------------------
        */

        ChatMessageSent::dispatch(
            $message
        );

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'staff.chat.show',
                $conversation
            )
            ->with(
                'chat_success',
                'Đã gửi tin nhắn.'
            );
    }

    /**
     * Staff gửi danh sách sản phẩm gợi ý cho Customer.
     */
    public function storeProducts(
        Request $request,
        ChatConversation $conversation
    ) {
        $staff = $request->user();

        /*
        |--------------------------------------------------------------------------
        | CHỈ STAFF
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $staff
            && $staff->isStaff(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate(
                [
                    'product_ids' => [
                        'required',
                        'array',
                        'min:1',
                        'max:5',
                    ],

                    'product_ids.*' => [
                        'required',
                        'integer',
                        'distinct',
                        'exists:products,id',
                    ],
                ],
                [
                    'product_ids.required' => 'Vui lòng chọn ít nhất một sản phẩm.',

                    'product_ids.array' => 'Danh sách sản phẩm không hợp lệ.',

                    'product_ids.min' => 'Vui lòng chọn ít nhất một sản phẩm.',

                    'product_ids.max' => 'Chỉ được gợi ý tối đa 5 sản phẩm mỗi lần.',

                    'product_ids.*.distinct' => 'Danh sách sản phẩm không được trùng nhau.',

                    'product_ids.*.exists' => 'Có sản phẩm không tồn tại.',
                ]
            );

        $productIds =
            array_values(
                $validated['product_ids']
            );

        /*
        |--------------------------------------------------------------------------
        | CHỈ LẤY SẢN PHẨM ĐANG KINH DOANH
        |--------------------------------------------------------------------------
        */

        $products =
            Product::query()
                ->active()
                ->whereIn(
                    'id',
                    $productIds
                )
                ->get()
                ->keyBy('id');

        /*
         * Nếu có Product đã tắt hoạt động
         * hoặc không còn tồn tại thì không gửi.
         */
        if (
            $products->count()
            !== count($productIds)
        ) {

            return back()
                ->withErrors([
                    'product_ids' => 'Có sản phẩm hiện không còn được kinh doanh.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | TẠO PRODUCT LIST MESSAGE
        |--------------------------------------------------------------------------
        */

        $message =
            DB::transaction(
                function () use (
                    $conversation,
                    $staff,
                    $productIds
                ) {

                    /*
                     * Lock conversation để tránh thay đổi
                     * Staff phụ trách trong lúc gửi.
                     */
                    $lockedConversation =
                        ChatConversation::query()
                            ->whereKey(
                                $conversation->id
                            )
                            ->lockForUpdate()
                            ->firstOrFail();

                    /*
                    |--------------------------------------------------------------------------
                    | CHAT PHẢI CÒN MỞ
                    |--------------------------------------------------------------------------
                    */

                    if (
                        ! $lockedConversation->isOpen()
                    ) {

                        abort(
                            409,
                            'Cuộc trò chuyện đã kết thúc.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | STAFF PHẢI ĐANG PHỤ TRÁCH
                    |--------------------------------------------------------------------------
                    */

                    if (
                        (int) $lockedConversation->staff_id
                        !== (int) $staff->id
                    ) {

                        abort(
                            403,
                            'Bạn không phụ trách cuộc trò chuyện này.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | TẠO MESSAGE
                    |--------------------------------------------------------------------------
                    */

                    $message =
                        ChatMessage::create([
                            'chat_conversation_id' => $lockedConversation->id,

                            'sender_id' => $staff->id,

                            'message_type' => ChatMessage::TYPE_PRODUCT_LIST,

                            'message' => 'VELORA Eyes gợi ý một số sản phẩm phù hợp với bạn:',

                            'read_at' => null,
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | GẮN CÁC PRODUCT VÀO MESSAGE
                    |--------------------------------------------------------------------------
                    */

                    $attachData = [];

                    foreach (
                        $productIds as $index => $productId
                    ) {

                        $attachData[$productId] = [
                            'sort_order' => $index + 1,
                        ];

                    }

                    $message
                        ->products()
                        ->attach(
                            $attachData
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | CẬP NHẬT LAST MESSAGE
                    |--------------------------------------------------------------------------
                    */

                    $lockedConversation->update([
                        'last_message_at' => $message->created_at,
                    ]);

                    return $message;
                }
            );

        /*
        |--------------------------------------------------------------------------
        | LOAD DATA TRƯỚC KHI BROADCAST
        |--------------------------------------------------------------------------
        */

        $message->load([
            'sender:id,name',
            'products.primaryImage',
        ]);

        /*
        |--------------------------------------------------------------------------
        | BROADCAST REALTIME
        |--------------------------------------------------------------------------
        */

        ChatMessageSent::dispatch(
            $message
        );

        return redirect()
            ->route(
                'staff.chat.show',
                $conversation
            )
            ->with(
                'chat_success',
                'Đã gửi danh sách sản phẩm gợi ý cho khách hàng.'
            );
    }

    /**
     * Đánh dấu các tin nhắn của Customer
     * trong hội thoại là đã đọc.
     */
    public function markRead(
        Request $request,
        ChatConversation $conversation
    ) {
        $staff = $request->user();

        abort_unless(
            $staff && $staff->isStaff(),
            403
        );

        abort_unless(
            (int) $conversation->staff_id
                === (int) $staff->id,
            403
        );

        $updated = ChatMessage::query()
            ->where(
                'chat_conversation_id',
                $conversation->id
            )
            ->where(
                'sender_id',
                $conversation->customer_id
            )
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        if ($updated > 0) {
            ChatMessagesRead::dispatch(
                $conversation->id,
                $staff->id
            );
        }

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }

    /**
     * Staff kết thúc cuộc trò chuyện.
     */
    public function close(
        Request $request,
        ChatConversation $conversation
    ) {
        $staff = $request->user();

        /*
        |--------------------------------------------------------------------------
        | CHỈ STAFF
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $staff
            && $staff->isStaff(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | ĐÓNG HỘI THOẠI
        |--------------------------------------------------------------------------
        */

        $closedConversation =
            DB::transaction(
                function () use (
                    $conversation,
                    $staff
                ) {

                    $lockedConversation =
                        ChatConversation::query()
                            ->whereKey(
                                $conversation->id
                            )
                            ->lockForUpdate()
                            ->firstOrFail();

                    /*
                     * Staff phải là người
                     * đang phụ trách hội thoại.
                     */
                    abort_unless(
                        (int) $lockedConversation->staff_id
                            === (int) $staff->id,
                        403
                    );

                    /*
                     * Nếu đã đóng rồi
                     * thì không xử lý lại.
                     */
                    if (
                        ! $lockedConversation->isOpen()
                    ) {

                        return $lockedConversation;
                    }

                    $lockedConversation->update([
                        'status' => 'closed',

                        'closed_at' => now(),
                    ]);

                    return $lockedConversation;
                }
            );

        /*
        |--------------------------------------------------------------------------
        | BROADCAST REALTIME
        |--------------------------------------------------------------------------
        |
        | Phát sau khi transaction đã commit.
        |
        */

        ChatConversationClosed::dispatch(
            $closedConversation,
            $staff->id
        );

        return redirect()
            ->route(
                'staff.chat.index'
            )
            ->with(
                'chat_success',
                'Đã kết thúc cuộc trò chuyện.'
            );
    }
}
