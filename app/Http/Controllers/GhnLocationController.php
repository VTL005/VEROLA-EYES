<?php

namespace App\Http\Controllers;

use App\Services\GhnShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;

class GhnLocationController extends Controller
{
    /**
     * Danh sách Tỉnh/Thành phố GHN.
     */
    public function provinces(
        GhnShippingService $ghn
    ): JsonResponse {
        return $this->respond(
            fn () => $ghn->getProvinces()
        );
    }

    /**
     * Danh sách Quận/Huyện theo Tỉnh/Thành phố.
     */
    public function districts(
        Request $request,
        GhnShippingService $ghn
    ): JsonResponse {
        $validated = $request->validate([
            'province_id' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        return $this->respond(
            fn () => $ghn->getDistricts(
                (int) $validated['province_id']
            )
        );
    }

    /**
     * Danh sách Phường/Xã theo Quận/Huyện.
     */
    public function wards(
        Request $request,
        GhnShippingService $ghn
    ): JsonResponse {
        $validated = $request->validate([
            'district_id' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        return $this->respond(
            fn () => $ghn->getWards(
                (int) $validated['district_id']
            )
        );
    }

    /**
     * Trả dữ liệu GHN theo một cấu trúc thống nhất.
     */
    private function respond(
        callable $callback
    ): JsonResponse {
        try {
            return response()->json([
                'data' => $callback(),
            ]);
        } catch (RuntimeException $exception) {
            report($exception);

            return response()->json([
                'message' => $exception->getMessage(),
                'data' => [],
            ], 502);
        }
    }
}