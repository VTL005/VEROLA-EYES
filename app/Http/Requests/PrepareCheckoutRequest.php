<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PrepareCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'selected_items' => [
                'required',
                'array',
                'min:1',
            ],

            'selected_items.*' => [
                'required',
                'integer',
                'distinct',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'selected_items.required' =>
                'Vui lòng chọn ít nhất một sản phẩm để thanh toán.',

            'selected_items.array' =>
                'Danh sách sản phẩm thanh toán không hợp lệ.',

            'selected_items.min' =>
                'Vui lòng chọn ít nhất một sản phẩm để thanh toán.',

            'selected_items.*.integer' =>
                'Sản phẩm được chọn không hợp lệ.',

            'selected_items.*.distinct' =>
                'Danh sách sản phẩm được chọn bị trùng.',
        ];
    }
}