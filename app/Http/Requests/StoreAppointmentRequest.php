<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }


    public function rules(): array
    {
        return [
            'customer_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'phone' => [
                'required',
                'regex:/^0[0-9]{9}$/',
            ],

            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'time_slot' => [
                'required',

                Rule::in([
                    '08:00-09:00',
                    '09:00-10:00',
                    '10:00-11:00',
                    '14:00-15:00',
                    '15:00-16:00',
                    '16:00-17:00',
                ]),
            ],

            'service_type' => [
                'required',

                Rule::in([
                    Appointment::SERVICE_EYE_EXAM,
                    Appointment::SERVICE_RECHECK,
                    Appointment::SERVICE_LENS_CONSULTATION,
                    Appointment::SERVICE_FRAME_CONSULTATION,
                ]),
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'customer_name.required' =>
                'Vui lòng nhập họ tên.',

            'customer_name.min' =>
                'Họ tên phải có ít nhất 2 ký tự.',

            'customer_name.max' =>
                'Họ tên không được vượt quá 100 ký tự.',


            'email.required' =>
                'Vui lòng nhập email.',

            'email.email' =>
                'Email không đúng định dạng.',

            'email.max' =>
                'Email không được vượt quá 255 ký tự.',


            'phone.required' =>
                'Vui lòng nhập số điện thoại.',

            'phone.regex' =>
                'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng số 0.',


            'appointment_date.required' =>
                'Vui lòng chọn ngày hẹn.',

            'appointment_date.date' =>
                'Ngày hẹn không hợp lệ.',

            'appointment_date.after_or_equal' =>
                'Không thể đặt lịch vào ngày trong quá khứ.',


            'time_slot.required' =>
                'Vui lòng chọn khung giờ.',

            'time_slot.in' =>
                'Khung giờ không hợp lệ.',


            'service_type.required' =>
                'Vui lòng chọn dịch vụ.',

            'service_type.in' =>
                'Loại dịch vụ không hợp lệ.',


            'note.max' =>
                'Ghi chú không được vượt quá 1000 ký tự.',
        ];
    }
}