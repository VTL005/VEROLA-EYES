<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' =>
                trim((string) $this->name),

            'sku' =>
                strtoupper(
                    trim((string) $this->sku)
                ),

            'dimensions' =>
                $this->filled('dimensions')
                    ? trim((string) $this->dimensions)
                    : null,

            'description' =>
                $this->filled('description')
                    ? trim((string) $this->description)
                    : null,

            'highlights' =>
                $this->filled('highlights')
                    ? trim((string) $this->highlights)
                    : null,

            'is_active' =>
                $this->boolean('is_active'),
        ]);
    }


    public function rules(): array
    {
        $product =
            $this->route('product');


        return [
            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'name' => [
                'required',
                'string',
                'min:5',
                'max:150',
            ],

            'sku' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-z0-9\-]+$/',

                Rule::unique(
                    'products',
                    'sku'
                )->ignore(
                    $product?->id
                ),
            ],

            'price' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'sale_price' => [
                'nullable',
                'numeric',
                'gte:0',
                'lt:price',
            ],

            'material' => [
                'nullable',
                'in:acetate,tr90,metal,titanium',
            ],

            'shape' => [
                'nullable',
                'in:round,square,rectangle,oval,cat_eye,aviator,browline',
            ],

            'gender' => [
                'nullable',
                'in:male,female,unisex,kids',
            ],

            'dimensions' => [
                'nullable',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'highlights' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'recommended_face_shapes' => [
                'nullable',
                'array',
            ],

            'recommended_face_shapes.*' => [
                'in:round,square,oval,heart',
            ],

            'style_tags' => [
                'nullable',
                'array',
            ],

            'style_tags.*' => [
                'in:minimal,elegant,bold,vintage',
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
            'category_id.required' =>
                'Vui lòng chọn danh mục.',

            'category_id.exists' =>
                'Danh mục không tồn tại.',


            'name.required' =>
                'Vui lòng nhập tên sản phẩm.',

            'name.min' =>
                'Tên sản phẩm phải có ít nhất 5 ký tự.',

            'name.max' =>
                'Tên sản phẩm không được vượt quá 150 ký tự.',


            'sku.required' =>
                'Vui lòng nhập SKU.',

            'sku.regex' =>
                'SKU chỉ được chứa chữ, số và dấu gạch ngang.',

            'sku.unique' =>
                'SKU này đã tồn tại.',


            'price.required' =>
                'Vui lòng nhập giá sản phẩm.',

            'price.numeric' =>
                'Giá sản phẩm phải là số.',

            'price.gt' =>
                'Giá sản phẩm phải lớn hơn 0.',


            'sale_price.numeric' =>
                'Giá khuyến mãi phải là số.',

            'sale_price.gte' =>
                'Giá khuyến mãi không được nhỏ hơn 0.',

            'sale_price.lt' =>
                'Giá khuyến mãi phải nhỏ hơn giá niêm yết.',


            'dimensions.max' =>
                'Kích thước không được vượt quá 100 ký tự.',

            'description.max' =>
                'Mô tả không được vượt quá 5000 ký tự.',

            'highlights.max' =>
                'Thông tin nổi bật không được vượt quá 3000 ký tự.',
        ];
    }
}