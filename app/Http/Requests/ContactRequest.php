<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            // 'name' => ['required','regex:/^[A-Za-z]{3,}+\s+[A-Za-z]{3,}$/','string','max:50'],
            'name' => ['required','string','max:50'],
            'email' => ['required', 'email', 'string','max:50'],
            'phone' => ['required', 'regex:/08[789]\d{7}/','digits_between:10,10'],
            'message' => ['required', 'string','max:500'],
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
            'name.required' => 'Полето за име е задължително.',
            'name.string' => 'Моля въведете правилни данни в полето за име',
            'name.max' => 'Надвишавате максималните допустими символи за име.',
            'email.email' => 'Въведете валиден имейл.',
            'email.max' => 'Надвишавате максималните допустими символи за имейл.',
            'email.string' => 'Въведете валиден имейл',
            'phone.required' => 'Полето за телефон е задължително.',
            'phone.digits_between' => 'Телефонният номер трябва да съдържа 10 числа.',
            'phone.regex' => 'Моля въведете телефонният номер в този формат 0885588111.',
            'message.required' => 'Полето за съобщение е задължително.',
            'message.string' => 'Въведете валидно съобщение.',
            'message.max' => 'Въведете по-късо съобщение.',
        ];
    }
}
