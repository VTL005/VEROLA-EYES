<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' =>
                trim(
                    (string) $this->name
                ),

            'description' =>
                $this->filled('description')
                    ? trim(
                        (string) $this->description
                    )
                    : null,

            'is_active' =>
                $this->boolean(
                    'is_active'
                ),
        ]);
    }


    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
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
            'name.required' =>
                'Vui lòng nhập tên danh mục.',

            'name.max' =>
                'Tên danh mục không được vượt quá 100 ký tự.',

            'description.max' =>
                'Mô tả không được vượt quá 1000 ký tự.',

            'image.image' =>
                'File tải lên phải là hình ảnh.',

            'image.mimes' =>
                'Ảnh phải có định dạng JPG, JPEG, PNG hoặc WEBP.',

            'image.max' =>
                'Dung lượng ảnh không được vượt quá 2MB.',
        ];
    }
}