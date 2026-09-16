<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim(
                (string) $this->name
            ),

            'description' => $this->filled('description')
                    ? trim(
                        (string) $this->description
                    )
                    : null,

            'is_active' => $this->boolean(
                'is_active'
            ),

            'is_home_featured' => $this->boolean(
                'is_home_featured'
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

            'is_home_featured' => [
                'required',
                'boolean',
            ],

            'homepage_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if (! $value instanceof UploadedFile) {
                        return;
                    }
                    $size = @getimagesize($value->getRealPath());
                    if (! $size) {
                        $fail('File ảnh trang chủ không hợp lệ.');

                        return;
                    }
                    $width = $size[0];
                    $height = $size[1];
                    $ratio = $width / $height;
                    $isFeatured = $this->boolean('is_home_featured');

                    if ($isFeatured) {
                        $targetRatio = 9 / 16;
                        $diff = abs($ratio - $targetRatio) / $targetRatio;
                        if ($diff > 0.01) {
                            $fail('Ảnh của danh mục chủ đạo phải có tỷ lệ 9:16.');
                        }
                    } else {
                        $targetRatio = 1 / 1;
                        $diff = abs($ratio - $targetRatio) / $targetRatio;
                        if ($diff > 0.01) {
                            $fail('Ảnh của danh mục thường phải có tỷ lệ 1:1.');
                        }
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',

            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',

            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',

            'image.image' => 'File tải lên phải là hình ảnh.',

            'image.mimes' => 'Ảnh phải có định dạng JPG, JPEG, PNG hoặc WEBP.',

            'image.max' => 'Dung lượng ảnh không được vượt quá 2MB.',

            'homepage_image.image' => 'File tải lên phải là hình ảnh.',

            'homepage_image.mimes' => 'Ảnh trang chủ phải có định dạng JPG, JPEG, PNG hoặc WEBP.',

            'homepage_image.max' => 'Dung lượng ảnh trang chủ không được vượt quá 5MB.',
        ];
    }
}
