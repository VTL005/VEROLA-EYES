<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(
        Request $request,
        InventoryService $inventoryService
    ) {
        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );


        $stockStatus =
            $request->query(
                'stock_status'
            );


        if (
            $stockStatus
            && !in_array(
                $stockStatus,
                [
                    'low_stock',
                    'out_of_stock',
                ],
                true
            )
        ) {
            $stockStatus = null;
        }


        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        |
        | Chỉ quản lý cảnh báo của:
        |
        | - Variant đang Active
        | - Product đang Active
        | - Tồn kho <= ngưỡng cảnh báo
        |
        */

        $baseQuery =
            ProductVariant::query()

                ->where(
                    'is_active',
                    true
                )

                ->whereHas(
                    'product',
                    function ($query) {

                        $query->where(
                            'is_active',
                            true
                        );
                    }
                );


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $outOfStockCount =
            (clone $baseQuery)
                ->where(
                    'stock_quantity',
                    '<=',
                    0
                )
                ->count();


        $lowStockCount =
            (clone $baseQuery)
                ->where(
                    'stock_quantity',
                    '>',
                    0
                )
                ->where(
                    'stock_quantity',
                    '<=',
                    InventoryService::LOW_STOCK_THRESHOLD
                )
                ->count();


        $totalAlerts =
            $outOfStockCount
            + $lowStockCount;


        /*
        |--------------------------------------------------------------------------
        | LIST
        |--------------------------------------------------------------------------
        */

        $variants =
            (clone $baseQuery)

                ->with([
                    'product.category',
                ])

                /*
                 * Chỉ lấy Variant cần cảnh báo.
                 */
                ->where(
                    'stock_quantity',
                    '<=',
                    InventoryService::LOW_STOCK_THRESHOLD
                )

                /*
                 * Search Product / SKU / Color / Size.
                 */
                ->when(
                    $keyword !== '',
                    function ($query) use ($keyword) {

                        $query->where(
                            function ($subQuery) use ($keyword) {

                                $subQuery
                                    ->where(
                                        'sku',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhere(
                                        'color',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhere(
                                        'size',
                                        'like',
                                        "%{$keyword}%"
                                    )

                                    ->orWhereHas(
                                        'product',
                                        function ($productQuery) use ($keyword) {

                                            $productQuery
                                                ->where(
                                                    'name',
                                                    'like',
                                                    "%{$keyword}%"
                                                )

                                                ->orWhere(
                                                    'sku',
                                                    'like',
                                                    "%{$keyword}%"
                                                );
                                        }
                                    );
                            }
                        );
                    }
                )

                /*
                 * Hết hàng.
                 */
                ->when(
                    $stockStatus
                    === 'out_of_stock',
                    function ($query) {

                        $query->where(
                            'stock_quantity',
                            '<=',
                            0
                        );
                    }
                )

                /*
                 * Sắp hết.
                 */
                ->when(
                    $stockStatus
                    === 'low_stock',
                    function ($query) {

                        $query
                            ->where(
                                'stock_quantity',
                                '>',
                                0
                            )
                            ->where(
                                'stock_quantity',
                                '<=',
                                InventoryService::LOW_STOCK_THRESHOLD
                            );
                    }
                )

                ->orderBy(
                    'stock_quantity'
                )

                ->orderBy('sku')

                ->paginate(20)

                ->withQueryString();


        return view(
            'admin.inventory.index',
            compact(
                'variants',
                'inventoryService',
                'keyword',
                'stockStatus',
                'outOfStockCount',
                'lowStockCount',
                'totalAlerts'
            )
        );
    }
}