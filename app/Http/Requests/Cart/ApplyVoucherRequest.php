<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class ApplyVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'voucher_code' => [
                'required',
                'string',
                'max:50',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'voucher_code.required' =>
                'Vui lòng nhập mã giảm giá.',

            'voucher_code.string' =>
                'Mã giảm giá không hợp lệ.',

            'voucher_code.max' =>
                'Mã giảm giá không được vượt quá 50 ký tự.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('voucher_code')) {
            $this->merge([
                'voucher_code' => strtoupper(
                    trim($this->voucher_code)
                ),
            ]);
        }
    }
}