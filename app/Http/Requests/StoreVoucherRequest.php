<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVoucherRequest extends FormRequest
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
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9_-]+$/',
                'unique:vouchers,code',
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
            }
        );
    }


    public function messages(): array
    {
        return [
            'code.required' =>
                'Vui lòng nhập mã Voucher.',

            'code.unique' =>
                'Mã Voucher đã tồn tại.',

            'code.regex' =>
                'Mã Voucher chỉ được chứa chữ in hoa, số, dấu gạch ngang hoặc gạch dưới.',

            'discount_type.required' =>
                'Vui lòng chọn loại giảm giá.',

            'discount_value.required' =>
                'Vui lòng nhập giá trị giảm.',

            'discount_value.gt' =>
                'Giá trị giảm phải lớn hơn 0.',

            'minimum_order_amount.required' =>
                'Vui lòng nhập giá trị đơn hàng tối thiểu.',

            'minimum_order_amount.min' =>
                'Giá trị đơn hàng tối thiểu không được âm.',

            'starts_at.required' =>
                'Vui lòng chọn thời gian bắt đầu.',

            'ends_at.required' =>
                'Vui lòng chọn thời gian kết thúc.',

            'ends_at.after' =>
                'Thời gian kết thúc phải sau thời gian bắt đầu.',

            'usage_limit.min' =>
                'Giới hạn lượt sử dụng phải từ 1 trở lên.',
        ];
    }
}