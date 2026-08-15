<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'address_id' => [
                'required',
                'integer',
                'exists:addresses,id',
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'cod',
                    'qr',
                    'vnpay',
                ]),
            ],

            'note' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'address_id.required' =>
                'Vui lòng chọn địa chỉ nhận hàng.',

            'address_id.exists' =>
                'Địa chỉ nhận hàng không hợp lệ.',


            'payment_method.required' =>
                'Vui lòng chọn phương thức thanh toán.',

            'payment_method.in' =>
                'Phương thức thanh toán không hợp lệ.',


            'note.max' =>
                'Ghi chú không được vượt quá 500 ký tự.',
        ];
    }
}