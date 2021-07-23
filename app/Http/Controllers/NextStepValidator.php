<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NextStepValidator extends Controller
{	
	public function nextStep(Request $request) {
        // this is for first step
        $step = $request->input('step');
        if($step == 0) {
      	 	 $rules = [
                'name' => ['required','regex:/^[A-Za-z]{3,}+\s+[A-Za-z]{3,}+$/'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required','min:6','confirmed'],
                'bulstat' => ['required','digits_between:9,9','numeric'],
                'phone_number' => ['required','digits_between:10,10','numeric','unique:users'],
            ];
            $customMessages = [
                'name.required' => 'Полето за име е задължително.',
                'name.regex' => 'Моля въведете име и фамилия.',
                'email.required' => 'Полето за имейл е задължително.',
                'email.unique' => 'Вече съществува потребител с този имейл.',
                'email.email' => 'Въведете валиден имейл адрес.',
                'password.required' => 'Полето за парола е задължително.',
                'password.min' => 'Използвайте минимум 6 символа за парола.',
                'password.confirmed' => 'Паролите не съвпадат.',
                'bulstat.required' => 'Полето за булстат е задължително.',
                'bulstat.digits_between' => 'Полето трябва да съдържа 9 числа.',
                'bulstat.numeric' => 'Въведете само числа за булстата.',
                'phone_number.required' => 'Полето за телефонен номер е задължително.',
                'phone_number.digits_between' => 'Телефонния номер трябва да съдържа 10 числа.',
                'phone_number.numeric' => 'Въведете само числа за телефонен номер.',
                'phone_number.unique' => 'Този телефонен номер съществува.',
            ];

            $this->validate($request,$rules,$customMessages);
            
        } else if($step == 1) {
            $atLeastOneChecked = 0;
            foreach($request->all() as $k => $v) {
                if($k == 'vehicles') {      
                    foreach($v as $kk => $vv) {              
                        if($vv['count'] != 0) {
                            print_r($vv);
                            print_r($vv['count']);
                            $atLeastOneChecked = 1;
                        }
                    }
                }
            }


            if(!$atLeastOneChecked) {
                 return response()->json(['errors'=> ['vehicles' => 'Моля въведете данни в поне едно от полетата'] ],422);
            } 
        }
        return true;
    }
}
