<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }


    public function rules(): array
    {
        return [
            /*
             * Laravel tự kiểm tra
             * mật khẩu hiện tại của User.
             */
            'current_password' => [
                'required',
                'current_password',
            ],


            /*
             * confirmed yêu cầu field:
             *
             * password_confirmation
             */
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'current_password.required' =>
                'Vui lòng nhập mật khẩu hiện tại.',

            'current_password.current_password' =>
                'Mật khẩu hiện tại không chính xác.',


            'password.required' =>
                'Vui lòng nhập mật khẩu mới.',

            'password.min' =>
                'Mật khẩu mới phải có ít nhất 8 ký tự.',

            'password.confirmed' =>
                'Xác nhận mật khẩu mới không khớp.',
        ];
    }
}