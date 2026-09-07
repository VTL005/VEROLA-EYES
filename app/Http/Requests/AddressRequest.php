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

            'ghn_province_id' => [
                'required',
                'integer',
                'min:1',
            ],

            /*
             * Giữ lại để tương thích dữ liệu cũ.
             */
            'province_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            /*
            |--------------------------------------------------------------------------
            | Quận / Huyện
            |--------------------------------------------------------------------------
            */

            'district' => [
                'required',
                'string',
                'max:100',
            ],

            'ghn_district_id' => [
                'required',
                'integer',
                'min:1',
            ],

            /*
            |--------------------------------------------------------------------------
            | Phường / Xã
            |--------------------------------------------------------------------------
            */

            'ward' => [
                'required',
                'string',
                'max:100',
            ],

            'ghn_ward_code' => [
                'required',
                'string',
                'max:20',
            ],

            /*
             * Giữ lại để tương thích dữ liệu cũ.
             */
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

            'ghn_province_id.required' =>
                'Vui lòng chọn Tỉnh/Thành phố trong danh sách GHN.',

            'ghn_province_id.integer' =>
                'Mã Tỉnh/Thành phố GHN không hợp lệ.',

            'district.required' =>
                'Vui lòng chọn Quận/Huyện.',

            'district.max' =>
                'Quận/Huyện không được vượt quá 100 ký tự.',

            'ghn_district_id.required' =>
                'Vui lòng chọn Quận/Huyện trong danh sách GHN.',

            'ghn_district_id.integer' =>
                'Mã Quận/Huyện GHN không hợp lệ.',

            'ward.required' =>
                'Vui lòng chọn Phường/Xã.',

            'ward.max' =>
                'Phường/Xã không được vượt quá 100 ký tự.',

            'ghn_ward_code.required' =>
                'Vui lòng chọn Phường/Xã trong danh sách GHN.',

            'ghn_ward_code.max' =>
                'Mã Phường/Xã GHN không hợp lệ.',

            'detail_address.required' =>
                'Vui lòng nhập địa chỉ chi tiết.',

            'detail_address.max' =>
                'Địa chỉ chi tiết không được vượt quá 255 ký tự.',

            'label.max' =>
                'Tên gợi nhớ địa chỉ không được vượt quá 50 ký tự.',
        ];
    }
}