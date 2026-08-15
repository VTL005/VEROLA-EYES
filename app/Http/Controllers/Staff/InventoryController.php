<?php

namespace App\Http\Controllers\Staff;

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
            $request->query('stock_status');


        if (
            $stockStatus
            && !in_array(
                $stockStatus,
                [
                    'out_of_stock',
                    'low_stock',
                ],
                true
            )
        ) {
            $stockStatus = null;
        }


        $lowStockThreshold =
            InventoryService::LOW_STOCK_THRESHOLD;


        $variants = ProductVariant::query()

            ->with([
                'product.category',
            ])

            /*
             * Chỉ lấy Variant đang hoạt động.
             */
            ->where(
                'is_active',
                true
            )

            /*
             * Chỉ lấy Product đang kinh doanh.
             */
            ->whereHas(
                'product',
                function ($query) {

                    $query->where(
                        'is_active',
                        true
                    );
                }
            )

            /*
             * Trang Inventory chỉ quan tâm
             * Variant có tồn kho thấp.
             */
            ->where(
                'stock_quantity',
                '<=',
                $lowStockThreshold
            )

            /*
             * Search Product / SKU Variant.
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
             * Filter hết hàng.
             */
            ->when(
                $stockStatus === 'out_of_stock',
                function ($query) {

                    $query->where(
                        'stock_quantity',
                        0
                    );
                }
            )

            /*
             * Filter sắp hết.
             */
            ->when(
                $stockStatus === 'low_stock',
                function ($query) use ($lowStockThreshold) {

                    $query
                        ->where(
                            'stock_quantity',
                            '>',
                            0
                        )

                        ->where(
                            'stock_quantity',
                            '<=',
                            $lowStockThreshold
                        );
                }
            )

            /*
             * Hết hàng lên trước.
             */
            ->orderBy('stock_quantity')

            ->orderBy('id')

            ->paginate(20)

            ->withQueryString();


        /*
         * Thống kê toàn bộ kho cảnh báo,
         * không phụ thuộc filter hiện tại.
         */
        $baseWarningQuery =
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


        $outOfStockCount =
            (clone $baseWarningQuery)
                ->where(
                    'stock_quantity',
                    0
                )
                ->count();


        $lowStockCount =
            (clone $baseWarningQuery)
                ->where(
                    'stock_quantity',
                    '>',
                    0
                )
                ->where(
                    'stock_quantity',
                    '<=',
                    $lowStockThreshold
                )
                ->count();


        return view(
            'staff.inventory.index',
            compact(
                'variants',
                'inventoryService',
                'keyword',
                'stockStatus',
                'lowStockThreshold',
                'outOfStockCount',
                'lowStockCount'
            )
        );
    }
}