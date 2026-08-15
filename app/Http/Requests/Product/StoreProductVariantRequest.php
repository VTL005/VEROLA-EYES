<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'color' =>
                strtolower(
                    trim(
                        (string) $this->input(
                            'color'
                        )
                    )
                ),

            'size' =>
                strtoupper(
                    trim(
                        (string) $this->input(
                            'size'
                        )
                    )
                ),

            'sku' =>
                strtoupper(
                    trim(
                        (string) $this->input(
                            'sku'
                        )
                    )
                ),

            'is_active' =>
                $this->boolean(
                    'is_active'
                ),
        ]);
    }


    public function rules(): array
    {
        $product =
            $this->route('product');


        return [
            'color' => [
                'required',
                'string',
                'max:50',

                Rule::unique(
                    'product_variants',
                    'color'
                )->where(
                    fn ($query) =>
                        $query
                            ->where(
                                'product_id',
                                $product->id
                            )
                            ->where(
                                'size',
                                $this->input(
                                    'size'
                                )
                            )
                ),
            ],

            'size' => [
                'required',
                'string',
                'max:30',
            ],

            'sku' => [
                'required',
                'string',
                'max:120',
                'regex:/^[A-Za-z0-9\-]+$/',
                'unique:product_variants,sku',
            ],

            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'price_adjustment' => [
                'required',
                'numeric',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'color.required' =>
                'Vui lòng nhập màu sắc.',

            'color.max' =>
                'Màu sắc không được vượt quá 50 ký tự.',

            'color.unique' =>
                'Tổ hợp màu và size này đã tồn tại trong sản phẩm.',


            'size.required' =>
                'Vui lòng nhập size.',

            'size.max' =>
                'Size không được vượt quá 30 ký tự.',


            'sku.required' =>
                'Vui lòng nhập SKU biến thể.',

            'sku.regex' =>
                'SKU chỉ được chứa chữ, số và dấu gạch ngang.',

            'sku.unique' =>
                'SKU biến thể này đã tồn tại.',


            'stock_quantity.required' =>
                'Vui lòng nhập số lượng tồn kho.',

            'stock_quantity.integer' =>
                'Tồn kho phải là số nguyên.',

            'stock_quantity.min' =>
                'Tồn kho không được nhỏ hơn 0.',


            'price_adjustment.required' =>
                'Vui lòng nhập mức điều chỉnh giá.',

            'price_adjustment.numeric' =>
                'Mức điều chỉnh giá phải là số.',
        ];
    }
}