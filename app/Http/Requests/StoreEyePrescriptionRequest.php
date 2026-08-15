<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEyePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }


    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | MẮT PHẢI
            |--------------------------------------------------------------------------
            */

            'right_sph' => [
                'nullable',
                'numeric',
                'between:-30,30',
            ],

            'right_cyl' => [
                'nullable',
                'numeric',
                'between:-15,15',
            ],

            'right_axis' => [
                'nullable',
                'integer',
                'between:0,180',
            ],


            /*
            |--------------------------------------------------------------------------
            | MẮT TRÁI
            |--------------------------------------------------------------------------
            */

            'left_sph' => [
                'nullable',
                'numeric',
                'between:-30,30',
            ],

            'left_cyl' => [
                'nullable',
                'numeric',
                'between:-15,15',
            ],

            'left_axis' => [
                'nullable',
                'integer',
                'between:0,180',
            ],


            /*
            |--------------------------------------------------------------------------
            | PD
            |--------------------------------------------------------------------------
            */

            'pd' => [
                'nullable',
                'numeric',
                'between:30,100',
            ],


            /*
            |--------------------------------------------------------------------------
            | NGÀY ĐO
            |--------------------------------------------------------------------------
            */

            'exam_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],


            /*
            |--------------------------------------------------------------------------
            | GHI CHÚ
            |--------------------------------------------------------------------------
            */

            'note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'right_sph.numeric' =>
                'SPH mắt phải phải là số.',

            'right_sph.between' =>
                'SPH mắt phải phải nằm trong khoảng -30 đến 30.',


            'right_cyl.numeric' =>
                'CYL mắt phải phải là số.',

            'right_cyl.between' =>
                'CYL mắt phải phải nằm trong khoảng -15 đến 15.',


            'right_axis.integer' =>
                'AXIS mắt phải phải là số nguyên.',

            'right_axis.between' =>
                'AXIS mắt phải phải nằm trong khoảng 0 đến 180.',


            'left_sph.numeric' =>
                'SPH mắt trái phải là số.',

            'left_sph.between' =>
                'SPH mắt trái phải nằm trong khoảng -30 đến 30.',


            'left_cyl.numeric' =>
                'CYL mắt trái phải là số.',

            'left_cyl.between' =>
                'CYL mắt trái phải nằm trong khoảng -15 đến 15.',


            'left_axis.integer' =>
                'AXIS mắt trái phải là số nguyên.',

            'left_axis.between' =>
                'AXIS mắt trái phải nằm trong khoảng 0 đến 180.',


            'pd.numeric' =>
                'PD phải là số.',

            'pd.between' =>
                'PD phải nằm trong khoảng 30 đến 100 mm.',


            'exam_date.required' =>
                'Vui lòng nhập ngày đo mắt.',

            'exam_date.date' =>
                'Ngày đo mắt không hợp lệ.',

            'exam_date.before_or_equal' =>
                'Ngày đo mắt không được lớn hơn ngày hiện tại.',


            'note.max' =>
                'Ghi chú không được vượt quá 2000 ký tự.',
        ];
    }


    /**
     * Phải nhập ít nhất một thông số đo.
     */
    public function withValidator(
        $validator
    ): void {
        $validator->after(
            function ($validator) {

                $fields = [
                    'right_sph',
                    'right_cyl',
                    'right_axis',

                    'left_sph',
                    'left_cyl',
                    'left_axis',

                    'pd',
                ];


                $hasValue = false;


                foreach ($fields as $field) {

                    if ($this->filled($field)) {

                        $hasValue = true;

                        break;
                    }
                }


                if (!$hasValue) {

                    $validator
                        ->errors()
                        ->add(
                            'prescription',
                            'Vui lòng nhập ít nhất một thông số đo mắt.'
                        );
                }
            }
        );
    }
}