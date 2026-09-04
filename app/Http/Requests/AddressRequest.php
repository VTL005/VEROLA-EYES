<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'recipient_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'phone' => [
                'required',
                'regex:/^0[0-9]{9}$/',
            ],


            /*
            |--------------------------------------------------------------------------
            | Tỉnh / Thành phố
            |--------------------------------------------------------------------------
            */

            'province' => [
                'required',
                'string',
                'max:100',
            ],

            'province_code' => [
                'nullable',
                'string',
                'max:20',
            ],


            /*
            |--------------------------------------------------------------------------
            | Quận / Huyện
            |--------------------------------------------------------------------------
            |
            | Chỉ giữ để tương thích với địa chỉ cũ.
            | Địa chỉ theo cấu trúc mới không bắt buộc trường này.
            |
            */

            'district' => [
                'nullable',
                'string',
                'max:100',
            ],


            /*
            |--------------------------------------------------------------------------
            | Phường / Xã / Đặc khu
            |--------------------------------------------------------------------------
            */

            'ward' => [
                'required',
                'string',
                'max:100',
            ],

            'ward_code' => [
                'nullable',
                'string',
                'max:20',
            ],


            'detail_address' => [
                'required',
                'string',
                'max:255',
            ],

            'label' => [
                'nullable',
                'string',
                'max:50',
            ],

            'is_default' => [
                'nullable',
                'boolean',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'recipient_name.required' =>
                'Vui lòng nhập tên người nhận.',

            'recipient_name.min' =>
                'Tên người nhận phải có ít nhất 2 ký tự.',

            'recipient_name.max' =>
                'Tên người nhận không được vượt quá 100 ký tự.',


            'phone.required' =>
                'Vui lòng nhập số điện thoại.',

            'phone.regex' =>
                'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0.',


            'province.required' =>
                'Vui lòng chọn Tỉnh/Thành phố.',

            'province.max' =>
                'Tỉnh/Thành phố không được vượt quá 100 ký tự.',

            'province_code.max' =>
                'Mã Tỉnh/Thành phố không hợp lệ.',


            /*
             * Không còn district.required
             * vì Quận/Huyện không bắt buộc với địa chỉ mới.
             */

            'district.max' =>
                'Quận/Huyện không được vượt quá 100 ký tự.',


            'ward.required' =>
                'Vui lòng chọn Phường/Xã/Đặc khu.',

            'ward.max' =>
                'Phường/Xã/Đặc khu không được vượt quá 100 ký tự.',

            'ward_code.max' =>
                'Mã Phường/Xã/Đặc khu không hợp lệ.',


            'detail_address.required' =>
                'Vui lòng nhập địa chỉ chi tiết.',

            'detail_address.max' =>
                'Địa chỉ chi tiết không được vượt quá 255 ký tự.',


            'label.max' =>
                'Tên gợi nhớ địa chỉ không được vượt quá 50 ký tự.',
        ];
    }
}