<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Events\ChatMessagesRead;
use App\Events\StaffChatInboxUpdated;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user && $user->isCustomer(),
            403
        );

        $conversation = ChatConversation::query()
            ->where('customer_id', $user->id)
            ->where('status', 'open')
            ->latest('id')
            ->first();

        if ($conversation) {
            $updated = ChatMessage::query()
                ->where(
                    'chat_conversation_id',
                    $conversation->id
                )
                ->where(
                    'sender_id',
                    '!=',
                    $user->id
                )
                ->whereNull('read_at')
                ->update([
                    'read_at' => now(),
                ]);

            if ($updated > 0) {
                ChatMessagesRead::dispatch(
                    $conversation->id,
                    $user->id
                );
            }
        }

        return view(
            'chat.customer.index',
            [
                'conversation' => $conversation,
            ]
        );
    }

    /**
     * Customer gửi tin nhắn.
     */
    /**
     * Customer gửi tin nhắn.
     *
     * Hỗ trợ:
     * - Tin nhắn chữ
     * - Ảnh
     * - Chữ + ảnh
     */
    public function store(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | CHỈ CUSTOMER
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $user && $user->isCustomer(),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | VALIDATE
        |--------------------------------------------------------------------------
        |
        | Tối đa:
        | - 2000 ký tự
        | - 5 ảnh
        | - 5MB / ảnh
        |
        | Chỉ cho:
        | jpg, jpeg, png, webp
        |
        */

        $validated = $request->validate(
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
        | PHẢI CÓ TEXT HOẶC ẢNH
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
        | DANH SÁCH FILE ĐÃ LƯU
        |--------------------------------------------------------------------------
        |
        | Nếu database transaction lỗi,
        | các file đã ghi vào Storage sẽ được xóa.
        |
        */

        $storedPaths = [];

        try {

            /*
            |--------------------------------------------------------------------------
            | TẠO / LẤY HỘI THOẠI + MESSAGE
            |--------------------------------------------------------------------------
            */

            $message = DB::transaction(
                function () use (
                    $user,
                    $messageText,
                    $uploadedImages,
                    &$storedPaths
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | LẤY HỘI THOẠI OPEN
                    |--------------------------------------------------------------------------
                    */

                    $conversation =
                        ChatConversation::query()
                            ->where(
                                'customer_id',
                                $user->id
                            )
                            ->where(
                                'status',
                                'open'
                            )
                            ->latest('id')
                            ->lockForUpdate()
                            ->first();

                    /*
                    |--------------------------------------------------------------------------
                    | CHƯA CÓ HỘI THOẠI → TẠO MỚI
                    |--------------------------------------------------------------------------
                    */

                    if (! $conversation) {

                        $conversation =
                            ChatConversation::create([
                                'customer_id' => $user->id,

                                'staff_id' => null,

                                'status' => 'open',

                                'last_message_at' => now(),

                                'closed_at' => null,
                            ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | XÁC ĐỊNH MESSAGE TYPE
                    |--------------------------------------------------------------------------
                    */

                    $messageType =
                        count($uploadedImages) > 0
                            ? ChatMessage::TYPE_IMAGE
                            : ChatMessage::TYPE_TEXT;

                    /*
                    |--------------------------------------------------------------------------
                    | NỘI DUNG MESSAGE
                    |--------------------------------------------------------------------------
                    |
                    | Cột message hiện tại vẫn cần nội dung.
                    |
                    | Nếu Customer chỉ gửi ảnh:
                    | dùng nội dung mô tả mặc định.
                    |
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
                            'chat_conversation_id' => $conversation->id,

                            'sender_id' => $user->id,

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

                        /*
                         * Laravel tự sinh tên file hash.
                         * Không sử dụng tên file gốc
                         * làm tên file lưu trên server.
                         */

                        $directory =
                            'chat/'
                            .$conversation->id
                            .'/'
                            .now()->format('Y/m');

                        $path =
                            $file->store(
                                $directory,
                                'public'
                            );

                        /*
                         * Ghi nhớ để có thể xóa
                         * nếu transaction gặp lỗi.
                         */
                        $storedPaths[] =
                            $path;

                        /*
                         * Lưu metadata vào database.
                         */
                        $message
                            ->attachments()
                            ->create([
                                'attachment_type' => 'image',

                                'disk' => 'public',

                                'file_path' => $path,

                                /*
                                 * Chỉ lưu tên gốc làm metadata.
                                 * Không dùng để tạo đường dẫn.
                                 */
                                'original_name' => mb_substr(
                                    (string) $file->getClientOriginalName(),
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
                    | CẬP NHẬT HỘI THOẠI
                    |--------------------------------------------------------------------------
                    */

                    $conversation->update([
                        'last_message_at' => $message->created_at,
                    ]);

                    return $message;
                }
            );

        } catch (\Throwable $exception) {

            /*
            |--------------------------------------------------------------------------
            | XÓA FILE NẾU DATABASE ROLLBACK
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
        | LOAD QUAN HỆ
        |--------------------------------------------------------------------------
        */

        $message->load([
            'sender:id,name,role_id',
            'attachments',
        ]);

        /*
        |--------------------------------------------------------------------------
        | BROADCAST REALTIME HỘI THOẠI
        |--------------------------------------------------------------------------
        */

        ChatMessageSent::dispatch(
            $message
        );

        /*
        |--------------------------------------------------------------------------
        | BROADCAST STAFF INBOX
        |--------------------------------------------------------------------------
        */

        StaffChatInboxUpdated::dispatch(
            $message->conversation
        );

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'customer.chat.index'
            );
    }

    public function markRead(
        Request $request,
        ChatConversation $conversation
    ) {
        $customer = $request->user();

        abort_unless(
            $customer && $customer->isCustomer(),
            403
        );

        abort_unless(
            (int) $conversation->customer_id
                === (int) $customer->id,
            403
        );

        $updated = ChatMessage::query()
            ->where(
                'chat_conversation_id',
                $conversation->id
            )
            ->whereNotNull('sender_id')
            ->where(
                'sender_id',
                '!=',
                $customer->id
            )
            ->whereNull('read_at')
            ->update([
                'read_at' => now(),
            ]);

        if ($updated > 0) {
            ChatMessagesRead::dispatch(
                $conversation->id,
                $customer->id
            );
        }

        return response()->json([
            'success' => true,
            'updated' => $updated,
        ]);
    }
}
