<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use DB;

class UserController extends Controller
{

	/**
	 * This function is user to fetch all users
	 * with specific data needed id|name|firm_name|status
	 * The status doesnt exist in database so its added as fake column
	 * status values are 0 - offline | 1 - online\
	 * @return $users
	 */
	public function getFilteredUsers(Request $request) {
		$users = DB::table('users')
      	->join('firms', 'users.company_id', '=', 'firms.id')
      	->select('users.id', 'users.name','firms.name as firmName',DB::raw("'0' as status"))
		->where('users.id', '<>', $request->id)->get();

		 return response()->json(['users' => $users , 
				'notification' => ['msg' => 'Успешно извличане на потребителите' , 'type' => 1, 'active' => 1] 
			],200);
	}

	public function getAllUsers(Request $request) {
      	$users = User::all();
      	return response()->json(['users' => $users , 
				'notification' => ['msg' => 'Успешно извличане на потребителите' , 'type' => 1, 'active' => 1] 
			],200);
	}

	public function registerUser(Request $request) {
		
	}

	public function getUser(Request $request) {
		$user = User::find($request->id); 

		if($user) {
	        return response()->json ([ 
                'status' =>'Успешно',
                'user' => $user 
            ], 200); 
	    } else {
	    	 return response()->json ([ 
                'status' =>'Не намерихме този потребител'
            ], 422); 
	    }
	}

	public function deleteUser(Request $request) {

	}

	public function updateUser(Request $request) {
		$rules = [
			// 'email' => ['required', 'email', 'unique:users'],
			'phone_number' => ['required','digits_between:10,10','numeric','unique:users'],
		];
		$customMessages = [
			// 'email.required' => 'Полето за имейл е задължително.',
			// 'email.unique' => 'Вече съществува потребител с този имейл.',
			// 'email.email' => 'Въведете валиден имейл адрес.',
			'phone_number.digits_between' => 'Телефонния номер трябва да съдържа 10 числа.',
			'phone_number.numeric' => 'Въведете само числа за телефонен номер.',
			'phone_number.unique' => 'Телефонният номер вече съществува.',
		];

		$validation = $this->validate($request,$rules,$customMessages);

		if($validation) {
			DB::table('users')->where('id',$request->id)->update(array('phone_number' => $request->phone_number));
			$user = DB::table('users')->where('id', $request->id)->first();
			return response()->json(['user' => $user , 
				'notification' => ['msg' => 'Успешно обновихте данните си' , 'type' => 1, 'active' => 1] 
			],200);
		} 

		return true;
	}


}
