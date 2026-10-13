<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class GeminiTestController extends Controller
{
    /**
     * Xác nhận ứng dụng có thể gọi Gemini với cấu hình hiện tại.
     */
    public function __invoke(
        GeminiService $gemini
    ): JsonResponse {
        try {
            $result = $gemini->chat(
                'Xin chào, bạn là ai? Hãy trả lời ngắn gọn.'
            );

            return response()->json([
                'success' => true,
                'message' => $result['text'],
            ]);
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 502);
        }
    }
}
