<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'images' => [
                'required',
                'array',
                'min:1',
                'max:5',
            ],

            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'images.required' =>
                'Vui lòng chọn ít nhất một hình ảnh.',

            'images.array' =>
                'Dữ liệu hình ảnh không hợp lệ.',

            'images.min' =>
                'Vui lòng chọn ít nhất một hình ảnh.',

            'images.max' =>
                'Mỗi lần chỉ được tải tối đa 5 hình ảnh.',


            'images.*.required' =>
                'Hình ảnh không được để trống.',

            'images.*.image' =>
                'File tải lên phải là hình ảnh.',

            'images.*.mimes' =>
                'Ảnh phải có định dạng JPG, JPEG, PNG hoặc WEBP.',

            'images.*.max' =>
                'Mỗi ảnh không được vượt quá 2MB.',
        ];
    }
}