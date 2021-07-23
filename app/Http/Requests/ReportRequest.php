<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'reason' => 'required|max:255|string',
            'body' => 'required|string',
            'file' => 'present|image',
        ];
    }

     /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'reason.required' => 'Полето за причина е задължително!',
            'reason.max' => 'Въведете по-малко текст за причина',
            'reason.string' => 'Полето за причина трябва да съдържа само букви',
            'body.required' => 'Полето за обяснение е задължително!',
            'body.string' => 'Въведете правилни данни в полето за обяснение!',
            'file.image' => 'Каченият файл трябва да е снимка',
        ];
    }
}
