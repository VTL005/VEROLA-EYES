<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarrantyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }


    protected function prepareForValidation(): void
    {
        if ($this->filled('warranty_content')) {
            $this->merge([
                'warranty_content' =>
                    trim(
                        (string) $this->warranty_content
                    ),
            ]);
        }
    }


    public function rules(): array
    {
        return [
            'warranty_content' => [
                'nullable',
                'string',
                'max:3000',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'warranty_content.string' =>
                'Nội dung bảo hành không hợp lệ.',

            'warranty_content.max' =>
                'Nội dung bảo hành không được vượt quá 3000 ký tự.',
        ];
    }
}