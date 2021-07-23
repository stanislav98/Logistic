<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPassword extends Controller
{

    use ResetsPasswords;

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        
        $rules = [
            'token' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255' ],
            'password' => ['required', 'confirmed', 'min:6'],
        ];
        $customMessages = [
            'token.required' => 'Полето за име е задължително.',
            'token.max' => 'Невалиден токън.',
            'email.required' => 'Полето за имейл е задължително.',
            'email.unique' => 'Вече съществува потребител с този имейл.',
            'email.email' => 'Въведете валиден имейл адрес.',
            'email.max' => 'Въведете валиден имейл адрес.',
            'password.required' => 'Полето за парола е задължително.',
            'password.confirmed' => 'Паролите не съвпадат.',
            'password.min' => 'Използвайте минимум 6 символа за парола.',
        ];

        $this->validate($request,$rules,$customMessages);

        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);
    }
    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $password
     * @return void
     */
    protected function resetPassword($user, $password) 
    { 
        $this->setUserPassword($user, $password); 
        //Here Larvel tries to set the "Remember me" cookie 
        //$user->setRememberToken(Str::random(60)); 

        $user->save(); 
        event(new PasswordReset($user)); 
        //By default, Laravel will attempt to automatically log in the user 
        //$this->guard()->login($user); 
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
           return response()->json(
            ['notification' => 
                [
                    'msg' => "Успешно обновихте паролата си" ,
                    'type' => 1, 
                    'active' => 1
                ] 
            ], 200);                  
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        $custom_message = "Неуспешен опит за смяна на паролата";
        $transResponse = trans($response);
        if($transResponse == "This password reset token is invalid.") {
            $custom_message = "Токънът ви е изтекъл върнете се и генерирайте нов линк за подновяване!";
        }
        
        return response()->json(
            ['notification' => 
                [
                    'msg' => $custom_message ,
                    'type' => 0, 
                    'active' => 1
                ] 
            ], 422);           
    }
}