<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
            && auth()->user()->isAdmin();
    }


    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' =>
                trim(
                    (string) $this->name
                ),

            'email' =>
                strtolower(
                    trim(
                        (string) $this->email
                    )
                ),

            'phone' =>
                trim(
                    (string) $this->phone
                ),

            'position' =>
                $this->filled('position')
                    ? trim(
                        (string) $this->position
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
        $staff =
            $this->route('staff');


        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',

                Rule::unique(
                    'users',
                    'email'
                )->ignore($staff),
            ],

            'phone' => [
                'required',
                'regex:/^0\d{9}$/',

                Rule::unique(
                    'users',
                    'phone'
                )->ignore($staff),
            ],

            'position' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
             * Để trống = giữ Password cũ.
             */
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
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
                'Vui lòng nhập tên nhân viên.',

            'name.min' =>
                'Tên nhân viên phải có ít nhất 2 ký tự.',

            'name.max' =>
                'Tên nhân viên không được vượt quá 100 ký tự.',


            'email.required' =>
                'Vui lòng nhập email.',

            'email.email' =>
                'Email không đúng định dạng.',

            'email.unique' =>
                'Email này đã được sử dụng.',


            'phone.required' =>
                'Vui lòng nhập số điện thoại.',

            'phone.regex' =>
                'Số điện thoại phải gồm 10 số và bắt đầu bằng 0.',

            'phone.unique' =>
                'Số điện thoại này đã được sử dụng.',


            'position.max' =>
                'Chức vụ không được vượt quá 100 ký tự.',


            'password.min' =>
                'Mật khẩu mới phải có ít nhất 8 ký tự.',

            'password.confirmed' =>
                'Xác nhận mật khẩu mới không khớp.',
        ];
    }
}