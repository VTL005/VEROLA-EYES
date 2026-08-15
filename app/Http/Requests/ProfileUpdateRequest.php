<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }


    public function rules(): array
    {
        $user = auth()->user();


        return [
            /*
             * Customer được sửa họ tên.
             */
            'name' => [
                'required',
                'string',
                'min:2',
                'max:50',
            ],


            /*
             * Customer được sửa số điện thoại.
             *
             * Số điện thoại phải duy nhất,
             * trừ chính số hiện tại của User.
             */
            'phone' => [
                'required',
                'regex:/^0[0-9]{9}$/',

                Rule::unique(
                    'users',
                    'phone'
                )->ignore($user->id),
            ],


            /*
             * Avatar không bắt buộc.
             */
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'name.required' =>
                'Vui lòng nhập họ tên.',

            'name.min' =>
                'Họ tên phải có ít nhất 2 ký tự.',

            'name.max' =>
                'Họ tên không được vượt quá 50 ký tự.',


            'phone.required' =>
                'Vui lòng nhập số điện thoại.',

            'phone.regex' =>
                'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0.',

            'phone.unique' =>
                'Số điện thoại này đã được sử dụng.',


            'avatar.image' =>
                'Avatar phải là hình ảnh.',

            'avatar.mimes' =>
                'Avatar chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',

            'avatar.max' =>
                'Avatar không được vượt quá 2MB.',
        ];
    }
}