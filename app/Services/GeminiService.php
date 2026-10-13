<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

class GeminiService
{
    /**
     * Gửi một tin nhắn đến model Gemini qua APIZ và trả về câu trả lời dạng text.
     *
     * @return array{text: string, raw: array<string, mixed>}
     */
    public function chat(
        string $message,
        array $history = [],
        array $productCatalog = []
    ): array
    {
        $message = trim($message);

        if ($message === '') {
            throw new InvalidArgumentException(
                'Tin nhắn gửi đến AI không được để trống.'
            );
        }

        try {
            $response = Http::withToken($this->apiKey())
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout(30)
                ->post(
                    $this->endpoint(),
                    [
                        'model' => $this->model(),
                        'messages' => array_merge(
                            [
                                [
                                    'role' => 'system',
                                    'content' => $this->systemPrompt(
                                        $productCatalog
                                    ),
                                ],
                            ],
                            $this->historyMessages($history),
                            [
                                [
                                    'role' => 'user',
                                    'content' => $this->latestCustomerMessage(
                                        $message
                                    ),
                                ],
                            ]
                        ),
                        // Vừa đủ linh hoạt để lời tư vấn tự nhiên, nhưng vẫn bám sát ý khách.
                        'temperature' => 0.45,
                        'max_tokens' => 512,
                    ]
                );

            $response->throw();
        } catch (ConnectionException $exception) {
            throw new RuntimeException(
                'Không thể kết nối đến APIZ. Vui lòng thử lại sau.',
                previous: $exception
            );
        } catch (RequestException $exception) {
            throw new RuntimeException(
                $this->requestErrorMessage($exception),
                previous: $exception
            );
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new RuntimeException(
                'APIZ trả về dữ liệu không hợp lệ.'
            );
        }

        $text = $this->extractText($data);

        if ($text === '') {
            throw new RuntimeException(
                'AI không trả về nội dung tư vấn. Yêu cầu có thể đã bị chặn bởi bộ lọc an toàn.'
            );
        }

        $recommendations = $this->extractProductIds(
            $text,
            $productCatalog
        );

        return [
            'text' => $recommendations['text'],
            'product_ids' => $recommendations['product_ids'],
            'raw' => $data,
        ];
    }

    /**
     * Lấy text từ phản hồi Chat Completions tương thích OpenAI của APIZ.
     *
     * @param array<string, mixed> $data
     */
    private function extractText(array $data): string
    {
        $content = data_get($data, 'choices.0.message.content');

        if (is_string($content)) {
            return trim($content);
        }

        if (! is_array($content)) {
            return '';
        }

        return trim(
            collect($content)
                ->map(function (mixed $part): string {
                    if (is_string($part)) {
                        return $part;
                    }

                    return is_array($part)
                        ? (string) ($part['text'] ?? '')
                        : '';
                })
                ->implode('')
        );
    }

    /**
     * Chuẩn hóa lịch sử theo format messages của API OpenAI-compatible.
     *
     * @param array<int, array{role?: mixed, text?: mixed}> $history
     * @return array<int, array{role: string, content: string}>
     */
    private function historyMessages(array $history): array
    {
        $contents = [];

        foreach (array_slice($history, -6) as $turn) {
            $role = $turn['role'] ?? null;
            $text = $turn['text'] ?? null;

            if (
                ! in_array($role, ['user', 'model'], true)
                || ! is_string($text)
                || trim($text) === ''
            ) {
                continue;
            }

            $contents[] = [
                'role' => $role === 'model' ? 'assistant' : 'user',
                'content' => mb_substr(trim($text), 0, 400),
            ];
        }

        return $contents;
    }

    private function latestCustomerMessage(string $message): string
    {
        return "TIN NHẮN MỚI NHẤT CỦA KHÁCH (cần trả lời ngay):\n"
            . $message
            . "\n\nHãy xử lý đúng ý định của tin mới nhất. Nếu tin này ngắn như “ok”, “được”, “gửi đi”, “kiểu này” hoặc “lấy mẫu đó”, hãy dùng lượt ngay trước đó để hiểu khách đang đồng ý với điều gì. Không được tự quay lại giải quyết một yêu cầu cũ nếu khách đã đổi chủ đề hoặc chỉ chào hỏi.";
    }

    private function apiKey(): string
    {
        $apiKey = trim(
            (string) config('services.apiz.api_key')
        );

        if ($apiKey === '') {
            throw new RuntimeException(
                'Chưa cấu hình APIZ_API_KEY.'
            );
        }

        return $apiKey;
    }

    private function endpoint(): string
    {
        $baseUrl = $this->baseUrl();

        if ($baseUrl === '' || $this->model() === '') {
            throw new RuntimeException(
                'Cấu hình APIZ chưa đầy đủ.'
            );
        }

        return $baseUrl
            . '/chat/completions';
    }

    private function baseUrl(): string
    {
        $baseUrl = trim(
            (string) config('services.apiz.base_url')
        );

        // Chấp nhận cả URL được dán từ Markdown: [https://...](https://...).
        if (preg_match('/^\[(https?:\/\/[^\]]+)\]\(https?:\/\/[^)]+\)$/', $baseUrl, $matches)) {
            $baseUrl = $matches[1];
        }

        return rtrim($baseUrl, '/');
    }

    private function model(): string
    {
        return trim((string) config('services.apiz.model'));
    }

    private function requestErrorMessage(
        RequestException $exception
    ): string {
        $response = $exception->response;

        $message = $response?->json('error.message');

        if (! is_string($message) || trim($message) === '') {
            $message = 'APIZ không thể xử lý yêu cầu.';
        }

        return 'APIZ: ' . trim($message);
    }

    /**
     * @param array<int, array{id: int, name: string, price: string, details: string}> $productCatalog
     */
    private function systemPrompt(array $productCatalog = []): string
    {
        $productInstructions = '';

        if ($productCatalog !== []) {
            $productList = collect($productCatalog)
                ->map(function (array $product): string {
                    return sprintf(
                        'ID %d | %s | %s | %s',
                        $product['id'],
                        $product['name'],
                        $product['price'],
                        $product['details']
                    );
                })
                ->implode("\n");

            $productInstructions = <<<PROMPT

Danh sách sản phẩm đang được hệ thống VELORA cung cấp:
{$productList}

Chỉ khi có sản phẩm phù hợp hoặc khách xin link/mẫu, hãy chọn tối đa 3 ID trong danh sách trên. Cuối câu trả lời, thêm đúng một dòng kỹ thuật theo mẫu [[PRODUCT_IDS: 12, 34]]. Không nhắc đến dòng kỹ thuật này với khách. Nếu không cần gợi ý sản phẩm, dùng [[PRODUCT_IDS:]].
PROMPT;
        }

        return <<<PROMPT
Bạn là VELORA AI, trợ lý tư vấn khách hàng của thương hiệu kính mắt VELORA Eyes.

Quy tắc làm việc:
- Nói tiếng Việt tự nhiên, gần gũi như đang nhắn với một người bạn: ưu tiên xưng “mình” và gọi khách là “bạn”. Có thể dùng “nha/nhé” vừa phải, không lạm dụng.
- Trước khi trả lời, tự xác định thầm 3 điều: khách đang muốn gì; chi tiết nào khách đã nói rõ; chỉ còn đúng một thông tin nào cần hỏi thêm. Không viết ba bước này ra cho khách.
- Tin nhắn mới nhất là ưu tiên số một. Lịch sử chỉ để hiểu ngữ cảnh, nhất là các câu xác nhận ngắn như “ok”, “được”, “ừ”, “gửi đi”, “mẫu đó”. Nếu khách đổi chủ đề hoặc chỉ chào hỏi thì không kéo lại yêu cầu cũ.
- Bám vào ít nhất một chi tiết thực sự khách vừa nói (ví dụ: dịp dùng, kiểu dáng, mức độ nổi bật, khuôn mặt) khi chi tiết đó có ích. Không nhắc lại nguyên văn câu khách và không tự bịa gu, khuôn mặt, giá hay tình trạng hàng.
- Trả lời gọn, thường 1–2 câu. Chỉ hỏi một câu tiếp theo khi câu hỏi đó thật sự giúp chọn kính; tuyệt đối không hỏi lại điều khách đã trả lời trong lịch sử gần nhất.
- Khi khách nói chung chung như “muốn mua kính” hoặc “muốn mua kính thời trang”, chưa đủ dữ liệu để đoán kiểu phù hợp: hỏi một câu ngắn, có 2–3 lựa chọn dễ trả lời. Khi khách đã nêu rõ dịp dùng hoặc gu, hãy đưa ra gợi ý cụ thể trước rồi mới hỏi một chi tiết còn thiếu nếu cần.
- Với lời chào, cảm ơn hoặc câu xã giao, phản hồi đúng lời chào/câu xã giao trước; không tự quay về nhu cầu cũ.
- Khi khách đã yêu cầu xem mẫu, xem sản phẩm hoặc xin link, hãy nói ngắn gọn rằng mình gửi ngay vài mẫu phù hợp. Hệ thống VELORA sẽ tự đính kèm thẻ sản phẩm thật; không bảo khách chờ, không nói cần hỏi nhân viên và không tự bịa tên sản phẩm.
- Không trả lời bằng một từ hoặc mệnh đề cụt. Mỗi câu trả lời phải đủ ý, trừ khi khách chỉ yêu cầu xác nhận có/không.
- Không mở đầu bằng “Tuyệt vời”, “Hay quá”, “Dạ được” một cách máy móc hoặc lời khen chung chung. Vào thẳng việc hỗ trợ khách; thay đổi cách mở đầu theo ngữ cảnh.

        Mẫu phản hồi cần học theo:
        Khách: “Mình muốn mua kính thời trang.”
        Bạn: “Dạ được bạn nha. Bạn đang tìm kính để đeo hằng ngày, đi chơi hay chụp ảnh; thích gọng mảnh thanh lịch, gọng dày cá tính hay dáng mắt mèo để mình chọn kiểu sát gu hơn?”

        Khách: “Mình cần kính đi chơi tối với người yêu.”
        Bạn: “Đi chơi tối thì gọng kim loại mảnh hoặc dáng mắt mèo sẽ lên đồ và chụp ảnh khá xinh đó bạn. Bạn thích vẻ thanh lịch nhẹ nhàng hay nổi bật cá tính hơn?”

        Khách: “Mình thích gọng kim loại cá tính, dáng vừa vặn.”
        Bạn: “Vậy mình nghiêng về gọng đa giác hoặc vuông bo nhẹ bằng kim loại, nhìn cá tính mà vẫn dễ đeo hằng ngày đó bạn. Mình gửi vài mẫu đúng kiểu này nhé.”

        Khách: “Ok bạn gửi đi.”
        Bạn: “Mình gửi bạn vài mẫu gọng kim loại dáng vừa vặn để xem ngay nhé.”

        Khách: “Kính nào hợp mặt tròn?”
        Bạn: “Mặt tròn thường hợp gọng vuông, chữ nhật hoặc đa giác để khuôn mặt trông cân đối hơn đó bạn. Bạn thích gọng mảnh thanh lịch hay kiểu cá tính nổi bật hơn?”

        Khách: “Chào bạn.”
        Bạn: “Chào bạn nha, mình đây. Bạn đang cần mình tư vấn chọn kính hay hỗ trợ thông tin nào vậy?”
- Không dùng Markdown, ký tự **, tiêu đề, hoặc danh sách dài trừ khi khách chủ động yêu cầu.
- Không tự bịa giá bán, tồn kho, ưu đãi, thời gian giao hàng, chính sách hoặc thông tin sản phẩm.
- Chỉ giới thiệu sản phẩm có trong danh sách hệ thống được cung cấp. Khi đã chọn sản phẩm, hệ thống sẽ tự gửi thẻ có link thật cho khách.
- Nếu thiếu dữ liệu thực tế, nói ngắn gọn là mình cần kiểm tra lại với nhân viên VELORA; không nói hệ thống không hỗ trợ gửi link.
- Có thể trò chuyện ngắn về các chủ đề đời sống, phong cách, thời trang hoặc câu hỏi xã giao. Với chủ đề quá xa, chỉ phản hồi thân thiện 1–2 câu rồi khéo gợi lại nhu cầu về kính hoặc VELORA.
- Không đưa tư vấn y tế/chẩn đoán; với vấn đề về mắt, khuyên khách đi khám chuyên môn.
- Không tự nhận mình là con người, không gây áp lực mua hàng, và không dùng giọng quá trang trọng.
- Nếu khách yêu cầu gặp nhân viên, khiếu nại, hoàn tiền, có thanh toán bất thường hoặc vấn đề cần kiểm tra thủ công, hãy đề nghị chuyển cho nhân viên VELORA.
{$productInstructions}
PROMPT;
    }

    /**
     * Tách danh sách ID kỹ thuật để hệ thống tạo thẻ sản phẩm thật.
     *
     * @param array<int, array{id: int, name: string, price: string, details: string}> $productCatalog
     * @return array{text: string, product_ids: array<int, int>}
     */
    private function extractProductIds(
        string $text,
        array $productCatalog
    ): array {
        if ($productCatalog === []) {
            return [
                'text' => trim($text),
                'product_ids' => [],
            ];
        }

        preg_match(
            '/\[\[PRODUCT_IDS:\s*([0-9,\s]*)\]\]/u',
            $text,
            $matches
        );

        $availableIds = collect($productCatalog)
            ->pluck('id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        $ids = collect(explode(',', $matches[1] ?? ''))
            ->map(fn (string $id): int => (int) trim($id))
            ->filter(fn (int $id): bool => in_array($id, $availableIds, true))
            ->unique()
            ->take(3)
            ->values()
            ->all();

        return [
            'text' => trim(
                preg_replace(
                    '/\s*\[\[PRODUCT_IDS:\s*[0-9,\s]*\]\]\s*/u',
                    '',
                    $text
                )
            ),
            'product_ids' => $ids,
        ];
    }
}
