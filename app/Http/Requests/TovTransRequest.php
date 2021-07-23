<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TovTransRequest extends FormRequest
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
            'from_city' => 'required|max:255|string',
            'to_city' => 'required|max:255|string',
            'from_lat' => ['required','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'to_lat' => ['required','regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'from_lng' => ['required','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'to_lng' => ['required','regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'fast_payment' => 'required|boolean',
            'adr' => 'required|boolean',
            'type' => 'required|boolean',
            'vehicle_type' => 'required|integer',
            'price' => ['required','regex:/^([\d]{1,6}[.][\d]{0,2})?([\d]{1,6})$/'],
            'weight' => ['required','regex:/^([\d]{1,6}[.][\d]{0,2})?([\d]{1,6})$/'],
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'both_directions' => 'required|boolean',
            'number_vehicles' => 'required|integer',
            'user_id' => 'required|integer',
            'firm_id' => 'required|integer',
            'description' => 'string|max:1000',
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
            'from_date.required' => 'Полето за дата на товарене е задължително!',
            'from_date.date' => 'Полето за дата е невалидно!',
            'to_date.required' => 'Полето до дата за разтоварване е задължително!',
            'to_date.date' => 'Полето до дата е невалидно!',
            'to_date.after_or_equal' => 'Полето до дата не може да е задна дата спрямо полето за дата!',
            'from_city.required' => 'Полето от град е задължително!',
            'from_city.max' => 'Полето от град не трябва да надвишава 255 символа!',
            'from_city.string' => 'Полето от град трябва да съдържа валиден текст!',
            'to_city.required' => 'Полето към град е задължително!',
            'to_city.max' => 'Полето към град не трябва да надвишава 255 символа!',
            'to_city.string' => 'Полето към град трябва да съдържа валиден текст!',
            'from_lat.required' => 'Полето към град е задължително!',
            'from_lat.regex' => 'Координатите за полето от град не са валидни!',
            'to_lat.required' => 'Полето за координати е задължително!',
            'to_lat.regex' => 'Координатите за полето към град не са валидни!',
            'from_lng.required' => 'Полето за координати е задължително!',
            'from_lng.regex' => 'Координатите за полето от град не са валидни!',
            'to_lng.required' => 'Полето за координати е задължително!',
            'to_lng.regex' => 'Координатите за полето към град не са валидни!',
            'fast_payment.required' => 'Полето за бързо плащане е задължително!',
            'fast_payment.boolean' => 'Полето за бързо плащане е с невалидни данни!',
            'adr.required' => 'Полето за АДР е задължително!',
            'adr.boolean' => 'Полето за АДР е с невалидни данни!',
            'type.required' => 'Полето за тип е задължително!',
            'adr.boolean' => 'Полето за тип е с невалидни данни!',
            'vehicle_type.required' => 'Полето за тип на превозното средство е задължително!',
            'vehicle_type.integer' => 'Полето за тип на превозното средство трябва да е число!',
            'price.required' => 'Полето за цена е задължително!',
            'price.regex' => 'Полето за цена не е валидно!',
            'weight.required' => 'Полето за тегло е задължително!',
            'weight.regex' => 'Полето за тегло не е валидно!',
            'user_id.required' => 'Полето за идентификатор на потребителя е задължително!',
            'user_id.required' => 'Полето за потребител е невалидно!',
            'firm_id.required' => 'Полето за идентификатор на фирмата е задължително!',
            'firm_id.required' => 'Полето за фирма е невалидно!',
            'vehicle_type.integer' => 'Полето за потребител трябва да е число!',
            'both_directions.required' => 'Полето за посока е задължително',
            'both_directions.boolean' => 'Полето за посока не е валидно',
            'number_vehicles.required' => 'Полето за брой на превозните не е валидно!',
            'number_vehicles.integer' => 'Полето за брой на превозните средства не е валидно!',
            'description.string' => 'Полето за описание не е валидно!',
            'description.max' => 'Полето за описание е твърде дълго!',
        ];
    }
}
