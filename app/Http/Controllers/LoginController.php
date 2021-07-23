<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Vehicle;
use App\Models\Firm;
use App\Models\Message;
use DB;

class LoginController extends Controller
{   

    // use AuthenticatesUsers;

    public function login(Request $request) {
    	$request->validate([
    		'email' => ['required', 'email'],
    		'password' => ['required'],
    	]);

        $credentials = $request->only('email', 'password'); 

        //here we fetch the whole data for the user
        if ($token = $this->guard()->attempt($credentials)) { 
            
            $user = Auth::user();
            
            $firm = Firm::find($user->company_id);

            $vehicles = DB::select(
                        'SELECT vehicles.name, vehicles.id, coalesce(vehicles_id,0) as vehicles_id, coalesce(count,0) as count, (CASE 
                                    WHEN company_vehicles.count = "0" OR ISNULL(company_vehicles.count) THEN 0 
                                    ELSE 1 
                                  END) AS "disabled" 
                          FROM `vehicles` 
                          LEFT JOIN company_vehicles ON company_vehicles.vehicles_id = vehicles.id AND company_vehicles.company_id = 1'
                        );

            $users = DB::table('users')
            ->join('firms', 'users.company_id', '=', 'firms.id')
            ->select('users.id', 'users.name','firms.name as firmName',DB::raw("'0' as status"))
            ->where('users.id', '<>', $user->id)->get();

            return response()->json (
                [
                    'status' => 'Успешен вход', 
                    'user' => Auth::user(),
                    'firm' => $firm,
                    'vehicles' => $vehicles, 
                    'token' => $token,
                    'users' => $users,
                    // 'realNotifications' => $realNotifications,
                    // 'allMessages' => $formatedMessages
                ], 
                200
            )
            ->header('Authorization','Bearer '.$token); 
        }

        return response()->json(['email' => 'Невалидни данни'], 401);
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
