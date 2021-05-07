<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;


class LoginController extends Controller
{   

    // use AuthenticatesUsers;

    public function login(Request $request) {
    	$request->validate([
    		'email' => ['required', 'email'],
    		'password' => ['required'],
    	]);

    	// if(Auth::attempt($request->only('email','password'))) {
    	// 	return response()->json(Auth::user(),200);
    	// }

        $credentials = $request->only('email', 'password'); 

        if ($token = $this->guard()->attempt($credentials)) { 
            return response()->json (['status' => 'Успешен вход', 'user' => Auth::user(), 'token' => $token], 200)
                    ->header('Authorization','Bearer '.$token); 
        }

        return response()->json(['email' => 'Невалидни данни'], 401);

        // abort(401);
  //   	throw ValidationException::withMessages([
  //   		'email' => ['The provided credentials are incorrect.']
		// ]);
    }

    public function logout() {
        // Auth::logout();

        $this->guard()->logout(); 

        return response()->json ([ 
            'status' => 'Успешен изход', 
            'msg' => 'Успешно излязохте от системата.' 
        ], 200); 

    }

    public function user(Request $request ) 
    { 
        $user = User::find(Auth::user()->id); 

        return response()->json([ 
            'status' => 'success', 
            'data' => $user 
        ]);
    }

    public function refresh()
    {
        if ($token = $this->guard()->refresh()) {
            return response()
                ->json(['status' => 'successs'], 200)
                ->header('Authorization', $token);
        }

        return response()->json(['error' => 'refresh_token_error'], 401);
    }

    private function guard() 
    { 
        return Auth::guard(); 
    } 
    // /**
    //  * The user has been authenticated.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  mixed  $user
    //  * @return mixed
    //  */
    // protected function authenticated(Request $request, $user)
    // {
    //     if($request->isXmlHttpRequest()){
    //         return response(null,200);
    //     }
    // }
}
