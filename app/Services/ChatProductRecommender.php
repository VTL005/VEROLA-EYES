<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\Product;

class ChatProductRecommender
{
    /**
     * Chỉ gửi thẻ sản phẩm khi khách chủ động yêu cầu xem mẫu/link.
     * Việc tìm sản phẩm diễn ra hoàn toàn trong database VELORA.
     *
     * @return array<int, int>
     */
    public function recommendIdsFor(ChatMessage $customerMessage): array
    {
        if (! $this->isProductRequest($customerMessage)) {
            return [];
        }

        $context = $this->conversationContext($customerMessage);

        $rankedProducts = Product::query()
            ->active()
            ->with('category:id,name')
            ->get()
            ->map(function (Product $product) use ($context): array {
                return [
                    'id' => $product->id,
                    'score' => $this->score($product, $context),
                ];
            })
            ->sort(function (array $left, array $right): int {
                return $right['score'] <=> $left['score']
                    ?: $left['id'] <=> $right['id'];
            })
            ->take(3)
            ->pluck('id')
            ->values()
            ->all();

        return array_map('intval', $rankedProducts);
    }

    private function isProductRequest(ChatMessage $customerMessage): bool
    {
        $message = $this->normalize($customerMessage->message);

        $isDirectRequest = str_contains($message, 'san pham')
            || str_contains($message, 'xem mau')
            || str_contains($message, 'gui mau')
            || str_contains($message, 'cho minh xem')
            || str_contains($message, 'cho xem')
            || str_contains($message, 'gui link')
            || str_contains($message, 'link san pham')
            || str_contains($message, 'xem kinh');

        if ($isDirectRequest) {
            return true;
        }

        $previousMessage = ChatMessage::query()
            ->where('chat_conversation_id', $customerMessage->chat_conversation_id)
            ->where('id', '<', $customerMessage->id)
            ->latest('id')
            ->first();

        if (! $previousMessage) {
            return false;
        }

        return $this->isConfirmation($message)
            && $this->isProductOffer($previousMessage->message);
    }

    private function isConfirmation(string $message): bool
    {
        return str_contains($message, 'ok')
            || str_contains($message, 'duoc')
            || str_contains($message, 'dong y')
            || str_contains($message, 'gui di')
            || str_contains($message, 'gui nhe')
            || str_contains($message, 'van gui');
    }

    private function isProductOffer(string $message): bool
    {
        $message = $this->normalize($message);

        return str_contains($message, 'gui')
            && (str_contains($message, 'mau')
                || str_contains($message, 'san pham')
                || str_contains($message, 'kinh'));
    }

    private function conversationContext(ChatMessage $customerMessage): string
    {
        return ChatMessage::query()
            ->where('chat_conversation_id', $customerMessage->chat_conversation_id)
            ->where('id', '<=', $customerMessage->id)
            ->latest('id')
            ->limit(6)
            ->pluck('message')
            ->reverse()
            ->implode(' ');
    }

    private function score(Product $product, string $context): int
    {
        $haystack = $this->normalize(implode(' ', [
            $product->name,
            $product->material,
            $product->shape,
            $product->gender,
            $product->description,
            $product->highlights,
            $product->category?->name,
            implode(' ', (array) $product->style_tags),
        ]));

        $score = 0;

        foreach ($this->intentKeywords($context) as $keyword) {
            if (str_contains($haystack, $keyword)) {
                $score += mb_strlen($keyword) > 4 ? 4 : 2;
            }
        }

        return $score;
    }

    /**
     * @return array<int, string>
     */
    private function intentKeywords(string $context): array
    {
        $context = $this->normalize($context);

        $keywordGroups = [
            'kim loai' => ['kim loai', 'metal', 'titan', 'thep'],
            'gong nhua' => ['gong nhua', 'acetate', 'nhua'],
            'mat meo' => ['mat meo', 'cat eye'],
            'phi cong' => ['phi cong', 'aviator'],
            'da giac' => ['da giac', 'goc canh'],
            'oversized' => ['oversized', 'ban to'],
            'oval' => ['oval', 'bau duc'],
            'vuong' => ['vuong'],
            'tron' => ['tron'],
            'ca tinh' => ['ca tinh', 'retro', 'vintage', 'noi bat'],
            'thanh lich' => ['thanh lich', 'toi gian', 'nhe nhang'],
            'kinh ram' => ['kinh ram', 'chong nang'],
        ];

        $keywords = [];

        foreach ($keywordGroups as $signal => $matches) {
            if (str_contains($context, $signal)) {
                $keywords = array_merge($keywords, $matches);
            }
        }

        return array_values(array_unique($keywords));
    }

    private function normalize(mixed $value): string
    {
        $value = mb_strtolower(trim((string) $value));

        return strtr($value, [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
        ]);
    }
}
