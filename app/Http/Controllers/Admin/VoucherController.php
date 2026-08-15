<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    /**
     * Danh sách Voucher.
     */
    public function index(Request $request)
    {
        $keyword = trim(
            (string) $request->query(
                'keyword',
                ''
            )
        );


        $discountType =
            $request->query(
                'discount_type'
            );


        $status =
            $request->query(
                'status'
            );


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE FILTER
        |--------------------------------------------------------------------------
        */

        if (
            $discountType
            && !in_array(
                $discountType,
                [
                    'percentage',
                    'fixed',
                ],
                true
            )
        ) {
            $discountType = null;
        }


        if (
            $status
            && !in_array(
                $status,
                [
                    'active',
                    'inactive',
                    'upcoming',
                    'expired',
                    'exhausted',
                ],
                true
            )
        ) {
            $status = null;
        }


        /*
        |--------------------------------------------------------------------------
        | LIST
        |--------------------------------------------------------------------------
        */

        $vouchers =
            Voucher::query()

                ->withCount('usages')

                ->when(
                    $keyword !== '',
                    fn ($query) =>
                        $query->where(
                            'code',
                            'like',
                            "%{$keyword}%"
                        )
                )

                ->when(
                    $discountType,
                    fn ($query) =>
                        $query->where(
                            'discount_type',
                            $discountType
                        )
                )


                /*
                 * Đang sử dụng được thực tế.
                 */
                ->when(
                    $status === 'active',
                    function ($query) {

                        $query
                            ->where(
                                'is_active',
                                true
                            )

                            ->where(
                                'starts_at',
                                '<=',
                                now()
                            )

                            ->where(
                                'ends_at',
                                '>=',
                                now()
                            )

                            ->where(
                                function ($usageQuery) {

                                    $usageQuery
                                        ->whereNull(
                                            'usage_limit'
                                        )

                                        ->orWhereColumn(
                                            'usage_count',
                                            '<',
                                            'usage_limit'
                                        );
                                }
                            );
                    }
                )


                /*
                 * Admin đã khóa.
                 */
                ->when(
                    $status === 'inactive',
                    fn ($query) =>
                        $query->where(
                            'is_active',
                            false
                        )
                )


                /*
                 * Chưa đến thời gian sử dụng.
                 */
                ->when(
                    $status === 'upcoming',
                    fn ($query) =>
                        $query
                            ->where(
                                'is_active',
                                true
                            )
                            ->where(
                                'starts_at',
                                '>',
                                now()
                            )
                )


                /*
                 * Đã hết hạn.
                 */
                ->when(
                    $status === 'expired',
                    fn ($query) =>
                        $query->where(
                            'ends_at',
                            '<',
                            now()
                        )
                )


                /*
                 * Đã hết lượt.
                 */
                ->when(
                    $status === 'exhausted',
                    fn ($query) =>
                        $query
                            ->whereNotNull(
                                'usage_limit'
                            )
                            ->whereColumn(
                                'usage_count',
                                '>=',
                                'usage_limit'
                            )
                )

                ->latest()

                ->paginate(15)

                ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | STATS
        |--------------------------------------------------------------------------
        */

        $totalVouchers =
            Voucher::query()
                ->count();


        $activeVouchers =
            Voucher::query()
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'starts_at',
                    '<=',
                    now()
                )
                ->where(
                    'ends_at',
                    '>=',
                    now()
                )
                ->where(
                    function ($query) {

                        $query
                            ->whereNull(
                                'usage_limit'
                            )
                            ->orWhereColumn(
                                'usage_count',
                                '<',
                                'usage_limit'
                            );
                    }
                )
                ->count();


        $expiredVouchers =
            Voucher::query()
                ->where(
                    'ends_at',
                    '<',
                    now()
                )
                ->count();


        $exhaustedVouchers =
            Voucher::query()
                ->whereNotNull(
                    'usage_limit'
                )
                ->whereColumn(
                    'usage_count',
                    '>=',
                    'usage_limit'
                )
                ->count();


        return view(
            'admin.vouchers.index',
            compact(
                'vouchers',
                'keyword',
                'discountType',
                'status',
                'totalVouchers',
                'activeVouchers',
                'expiredVouchers',
                'exhaustedVouchers'
            )
        );
    }


    /**
     * Form tạo.
     */
    public function create()
    {
        return view(
            'admin.vouchers.create'
        );
    }


    /**
     * Lưu Voucher.
     */
    public function store(Request $request)
    {
        $validated =
            $this->validateVoucher(
                $request
            );


        Voucher::create([
            'code' =>
                strtoupper(
                    trim(
                        $validated['code']
                    )
                ),

            'discount_type' =>
                $validated['discount_type'],

            'discount_value' =>
                $validated['discount_value'],

            'minimum_order_amount' =>
                $validated[
                    'minimum_order_amount'
                ],

            'starts_at' =>
                Carbon::parse(
                    $validated['starts_at']
                ),

            'ends_at' =>
                Carbon::parse(
                    $validated['ends_at']
                ),

            'usage_limit' =>
                $validated[
                    'usage_limit'
                ] ?? null,

            /*
             * Voucher mới chưa được dùng.
             */
            'usage_count' => 0,

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),
        ]);


        return redirect()
            ->route(
                'admin.vouchers.index'
            )
            ->with(
                'success',
                'Thêm Voucher thành công.'
            );
    }


    /**
     * Form sửa.
     */
    public function edit(
        Voucher $voucher
    ) {
        return view(
            'admin.vouchers.edit',
            compact('voucher')
        );
    }


    /**
     * Cập nhật Voucher.
     */
    public function update(
        Request $request,
        Voucher $voucher
    ) {
        $validated =
            $this->validateVoucher(
                $request,
                $voucher
            );


        /*
         * Không nhận usage_count từ Browser.
         *
         * usage_count phải do Checkout
         * và VoucherUsage quản lý.
         */
        $voucher->update([
            'code' =>
                strtoupper(
                    trim(
                        $validated['code']
                    )
                ),

            'discount_type' =>
                $validated['discount_type'],

            'discount_value' =>
                $validated['discount_value'],

            'minimum_order_amount' =>
                $validated[
                    'minimum_order_amount'
                ],

            'starts_at' =>
                Carbon::parse(
                    $validated['starts_at']
                ),

            'ends_at' =>
                Carbon::parse(
                    $validated['ends_at']
                ),

            'usage_limit' =>
                $validated[
                    'usage_limit'
                ] ?? null,

            'is_active' =>
                $request->boolean(
                    'is_active'
                ),
        ]);


        return redirect()
            ->route(
                'admin.vouchers.index'
            )
            ->with(
                'success',
                'Cập nhật Voucher thành công.'
            );
    }


    /**
     * Khóa / mở Voucher.
     */
    public function toggleActive(
        Voucher $voucher
    ) {
        DB::transaction(
            function () use ($voucher) {

                $lockedVoucher =
                    Voucher::query()
                        ->where(
                            'id',
                            $voucher->id
                        )
                        ->lockForUpdate()
                        ->firstOrFail();


                $lockedVoucher->update([
                    'is_active' =>
                        !$lockedVoucher->is_active,
                ]);
            }
        );


        $voucher->refresh();


        return back()->with(
            'success',
            $voucher->is_active
                ? 'Đã mở lại Voucher.'
                : 'Đã khóa Voucher.'
        );
    }


    /**
     * Validation dùng chung
     * Create / Update.
     */
    private function validateVoucher(
        Request $request,
        ?Voucher $voucher = null
    ): array {
        $request->merge([
            'code' =>
                strtoupper(
                    trim(
                        (string) $request->input(
                            'code'
                        )
                    )
                ),
        ]);


        $validated =
            $request->validate(
                [
                    'code' => [
                        'required',
                        'string',
                        'min:3',
                        'max:50',
                        'regex:/^[A-Z0-9\-]+$/',

                        Rule::unique(
                            'vouchers',
                            'code'
                        )->ignore(
                            $voucher?->id
                        ),
                    ],

                    'discount_type' => [
                        'required',
                        Rule::in([
                            'percentage',
                            'fixed',
                        ]),
                    ],

                    'discount_value' => [
                        'required',
                        'numeric',
                        'gt:0',
                    ],

                    'minimum_order_amount' => [
                        'required',
                        'numeric',
                        'gte:0',
                    ],

                    'starts_at' => [
                        'required',
                        'date',
                    ],

                    'ends_at' => [
                        'required',
                        'date',
                        'after:starts_at',
                    ],

                    'usage_limit' => [
                        'nullable',
                        'integer',
                        'min:1',
                    ],
                ],
                [
                    'code.required' =>
                        'Vui lòng nhập mã Voucher.',

                    'code.min' =>
                        'Mã Voucher phải có ít nhất 3 ký tự.',

                    'code.max' =>
                        'Mã Voucher không được vượt quá 50 ký tự.',

                    'code.regex' =>
                        'Mã Voucher chỉ được chứa chữ in hoa, số và dấu gạch ngang.',

                    'code.unique' =>
                        'Mã Voucher này đã tồn tại.',


                    'discount_type.required' =>
                        'Vui lòng chọn loại giảm giá.',

                    'discount_type.in' =>
                        'Loại giảm giá không hợp lệ.',


                    'discount_value.required' =>
                        'Vui lòng nhập giá trị giảm.',

                    'discount_value.numeric' =>
                        'Giá trị giảm phải là số.',

                    'discount_value.gt' =>
                        'Giá trị giảm phải lớn hơn 0.',


                    'minimum_order_amount.required' =>
                        'Vui lòng nhập giá trị đơn tối thiểu.',

                    'minimum_order_amount.numeric' =>
                        'Giá trị đơn tối thiểu phải là số.',

                    'minimum_order_amount.gte' =>
                        'Giá trị đơn tối thiểu không được âm.',


                    'starts_at.required' =>
                        'Vui lòng chọn thời gian bắt đầu.',

                    'starts_at.date' =>
                        'Thời gian bắt đầu không hợp lệ.',


                    'ends_at.required' =>
                        'Vui lòng chọn thời gian kết thúc.',

                    'ends_at.date' =>
                        'Thời gian kết thúc không hợp lệ.',

                    'ends_at.after' =>
                        'Thời gian kết thúc phải sau thời gian bắt đầu.',


                    'usage_limit.integer' =>
                        'Giới hạn lượt sử dụng phải là số nguyên.',

                    'usage_limit.min' =>
                        'Giới hạn lượt sử dụng phải từ 1 trở lên.',
                ]
            );


        /*
         * Percentage không vượt 100%.
         */
        if (
    $validated['discount_type']
        === 'percentage'

    && (float) $validated[
        'discount_value'
    ] > 100
) {
    throw \Illuminate\Validation\ValidationException::withMessages([
        'discount_value' =>
            'Voucher giảm theo phần trăm không được vượt quá 100%.',
    ]);
}


        return $validated;
    }
}