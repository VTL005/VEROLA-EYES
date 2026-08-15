<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => strtoupper(
                trim((string) $this->code)
            ),

            'is_active' =>
                $this->boolean('is_active'),
        ]);
    }


    public function rules(): array
    {
        $voucher = $this->route('voucher');


        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9_-]+$/',

                Rule::unique(
                    'vouchers',
                    'code'
                )->ignore($voucher),
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
                'min:0',
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

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }


    public function withValidator($validator): void
    {
        $validator->after(
            function ($validator) {

                $voucher =
                    $this->route('voucher');


                /*
                 * Percentage không vượt 100%.
                 */
                if (
                    $this->discount_type === 'percentage'
                    && is_numeric($this->discount_value)
                    && (float) $this->discount_value > 100
                ) {
                    $validator->errors()->add(
                        'discount_value',
                        'Voucher phần trăm không được giảm quá 100%.'
                    );
                }


                /*
                 * Không cho usage_limit mới
                 * nhỏ hơn số lượt đã dùng.
                 */
                if (
                    $voucher
                    && $this->filled('usage_limit')
                    && (int) $this->usage_limit
                        < (int) $voucher->usage_count
                ) {
                    $validator->errors()->add(
                        'usage_limit',
                        'Giới hạn lượt sử dụng không được nhỏ hơn số lượt đã sử dụng hiện tại.'
                    );
                }
            }
        );
    }
}