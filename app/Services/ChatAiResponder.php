<?php

namespace App\Services;

use App\Events\AdminChatInboxUpdated;
use App\Events\ChatAiSuggestionCreated;
use App\Events\ChatMessageSent;
use App\Models\ChatAiSuggestion;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Throwable;

class ChatAiResponder
{
    public function __construct(
        private GeminiService $gemini,
        private ChatProductRecommender $productRecommender
    ) {
    }

    /**
     * Trả lời Customer khi AI tự động, hoặc tạo nháp riêng cho Admin khi AI hỗ trợ.
     *
     * Nếu Admin đã tiếp quản hoặc khách gửi tin mới trong lúc Gemini xử lý,
     * kết quả cũ sẽ bị bỏ qua để tránh gửi sai ngữ cảnh.
     */
    public function respondTo(
        ChatMessage $customerMessage
    ): ChatMessage|ChatAiSuggestion|null {
        $conversation = ChatConversation::query()
            ->find($customerMessage->chat_conversation_id);

        if (
            ! $conversation
            || ! $conversation->isOpen()
            || (! $conversation->isAutoMode() && ! $conversation->isAssistMode())
            || $customerMessage->sender_type !== ChatMessage::SENDER_CUSTOMER
        ) {
            return null;
        }

        try {
            $result = $this->gemini->chat(
                $customerMessage->message,
                $this->historyBefore($customerMessage)
            );
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }

        $productIds = $conversation->isAutoMode()
            ? $this->productRecommender->recommendIdsFor($customerMessage)
            : [];

        try {
            $aiResult = DB::transaction(
                function () use ($customerMessage, $result, $productIds) {
                    $conversation = ChatConversation::query()
                        ->whereKey($customerMessage->chat_conversation_id)
                        ->lockForUpdate()
                        ->first();

                    if (
                        ! $conversation
                        || ! $conversation->isOpen()
                        || (! $conversation->isAutoMode() && ! $conversation->isAssistMode())
                    ) {
                        return null;
                    }

                    $latestMessageId = ChatMessage::query()
                        ->where(
                            'chat_conversation_id',
                            $conversation->id
                        )
                        ->latest('id')
                        ->value('id');

                    if ((int) $latestMessageId !== (int) $customerMessage->id) {
                        return null;
                    }

                    if ($conversation->isAssistMode()) {
                        return ChatAiSuggestion::create([
                            'chat_conversation_id' => $conversation->id,
                            'source_chat_message_id' => $customerMessage->id,
                            'content' => $result['text'],
                            'status' => ChatAiSuggestion::STATUS_PENDING,
                        ]);
                    }

                    $availableProductIds = Product::query()
                        ->active()
                        ->whereKey($productIds)
                        ->pluck('id')
                        ->map(fn (mixed $id): int => (int) $id)
                        ->all();

                    $aiMessage = ChatMessage::create([
                        'chat_conversation_id' => $conversation->id,
                        'sender_id' => null,
                        'sender_type' => ChatMessage::SENDER_AI,
                        'message_type' => $availableProductIds === []
                            ? ChatMessage::TYPE_TEXT
                            : ChatMessage::TYPE_PRODUCT_LIST,
                        'message' => $result['text'],
                        'read_at' => null,
                    ]);

                    if ($availableProductIds !== []) {
                        $aiMessage->products()->attach(
                            collect($availableProductIds)
                                ->values()
                                ->mapWithKeys(
                                    fn (int $productId, int $index): array => [
                                        $productId => [
                                            'sort_order' => $index + 1,
                                        ],
                                    ]
                                )
                                ->all()
                        );
                    }

                    $conversation->update([
                        'last_message_at' => $aiMessage->created_at,
                        'ai_last_reply_at' => $aiMessage->created_at,
                    ]);

                    return $aiMessage;
                }
            );
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }

        if (! $aiResult) {
            return null;
        }

        if ($aiResult instanceof ChatAiSuggestion) {
            $adminId = (int) ChatConversation::query()
                ->whereKey($aiResult->chat_conversation_id)
                ->value('admin_id');

            if ($adminId > 0) {
                ChatAiSuggestionCreated::dispatch($aiResult, $adminId);
            }

            return $aiResult;
        }

        ChatMessageSent::dispatch($aiResult);
        AdminChatInboxUpdated::dispatch($aiResult->conversation);

        return $aiResult;
    }

    public function respondToMessageId(
        int $messageId
    ): ChatMessage|ChatAiSuggestion|null
    {
        $message = ChatMessage::query()->find($messageId);

        return $message ? $this->respondTo($message) : null;
    }

    /**
     * @return array<int, array{role: string, text: string}>
     */
    private function historyBefore(
        ChatMessage $customerMessage
    ): array {
        return ChatMessage::query()
            ->where(
                'chat_conversation_id',
                $customerMessage->chat_conversation_id
            )
            ->where('id', '<', $customerMessage->id)
            ->latest('id')
            ->limit(6)
            ->get()
            ->reverse()
            ->map(function (ChatMessage $message): array {
                return [
                    'role' => $message->sender_type
                        === ChatMessage::SENDER_CUSTOMER
                        ? 'user'
                        : 'model',
                    'text' => $message->message,
                ];
            })
            ->values()
            ->all();
    }
}
