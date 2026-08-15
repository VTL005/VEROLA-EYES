<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    /**
     * Customer đã được kiểm tra
     * bởi middleware ở Route.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Validate dữ liệu thêm vào Cart.
     */
    public function rules(): array
    {
        return [
            'variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'variant_id.required' =>
                'Vui lòng chọn biến thể sản phẩm.',

            'variant_id.integer' =>
                'Biến thể sản phẩm không hợp lệ.',

            'variant_id.exists' =>
                'Biến thể sản phẩm không tồn tại.',

            'quantity.required' =>
                'Vui lòng nhập số lượng.',

            'quantity.integer' =>
                'Số lượng phải là số nguyên.',

            'quantity.min' =>
                'Số lượng phải lớn hơn hoặc bằng 1.',
        ];
    }
}