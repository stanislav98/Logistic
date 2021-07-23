<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UsersForAuthorize;
use DB;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

	/**
	 * This function is user to fetch all users
	 * with specific data needed id|name|firm_name|status
	 * The status doesnt exist in database so its added as fake column
	 * status values are 0 - offline | 1 - online
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
      	$users = DB::table('users')
      	->join('firms', 'users.company_id', '=', 'firms.id')
      	->join('roles','users.role_id','=','roles.id')
      	->select('users.*','firms.name as firmName','firms.id as firmId','firms.bulstat','roles.name as roleName')
		->get();
      	return response()->json(['users' => $users , 
				'notification' => ['msg' => 'Успешно извличане на потребителите' , 'type' => 1, 'active' => 1] 
			],200);
	}

	public function addUser(Request $request) {
		 $rules = [
                'name' => ['required','regex:/^[A-Za-z]{3,}+\s+[A-Za-z]{3,}+$/'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required','min:6','confirmed'],
                'phone_number' => ['required','digits_between:10,10','numeric','unique:users'],
                'file' => ['required','image']
        ];

        $customMessages = [
            'name.required' => 'Полето за име е задължително!',
            'name.regex' => 'Моля въведете име и фамилия!',
            'email.required' => 'Полето за имейл е задължително!',
            'email.unique' => 'Вече съществува потребител с този имейл!',
            'email.email' => 'Въведете валиден имейл адрес!',
            'password.required' => 'Полето за парола е задължително!',
            'password.min' => 'Използвайте минимум 6 символа за парола!',
            'password.confirmed' => 'Паролите не съвпадат!',
            'bulstat.required' => 'Полето за булстат е задължително!',
            'bulstat.digits_between' => 'Полето трябва да съдържа 9 числа!',
            'bulstat.numeric' => 'Въведете само числа за булстата!',
            'phone_number.required' => 'Полето за телефонен номер е задължително!',
            'phone_number.digits_between' => 'Телефонния номер трябва да съдържа 10 числа!',
            'phone_number.numeric' => 'Въведете само числа за телефонен номер!',
            'phone_number.unique' => 'Този телефонен номер съществува!',
            'file.required' => 'Задължително трябва да качите снимка на договора!',
            'file.image' => 'Каченият файл трябва да е снимка!',
        ];

        $validated = $this->validate($request,$rules,$customMessages);

        $fileName = '';
        $filePath = '';

        if($validated) {
	        $allUserData = $request->all();
	        unset($allUserData['file']);
	        $allFileData = [];

			$allFileData['fileName'] = time().'_'.$request->file->getClientOriginalName();
			$allFileData['filePath'] = $request->file('file')->storeAs('users_for_authorization', $allFileData['fileName'], 'public');
        }

        $allUserData['password'] = Hash::make($allUserData['password']);

        if($user = User::create($allUserData)) {
        	if(UsersForAuthorize::create(
        		['name' => $allFileData['fileName'], 'path' => $allFileData['filePath'], 'user_id' => $user->id]
        	)) {
	        	return response()->json(['notification' => 
			    		[
			    			'msg' => 'Успено добавихте потребителя!' ,
			    			'type' => 1, 
			    			'active' => 1
			    		],
			    		'user' => $user
			        ], 200); 
        	}

        	if(User::destroy($user->id)) {
        		return response()->json(['notification' => 
			    		[
			    			'msg' => 'Нещо се обърка при добавяне на потребителя!' ,
			    			'type' => 0, 
			    			'active' => 1
			    		]
			        ], 200); 
        	}
    	}

    	return response()->json(['notification' => 
    		[
    			'msg' => 'Потребителя не беше добавен!' ,
    			'type' => 0, 
    			'active' => 1
    		] 
        ], 422); 


	}

	public function getUnauthorizedUsers(Request $request) 
	{
		$unauthorizedUsers = DB::table('users')
		->join('users_for_authorize','users.id','=','users_for_authorize.user_id')
		->join('firms', 'users.company_id', '=', 'firms.id')
      	->join('roles','users.role_id','=','roles.id')
      	->select('users.*','firms.name as firmName','firms.id as firmId','firms.bulstat','roles.name as roleName','users_for_authorize.name as fileName','users_for_authorize.path','users_for_authorize.created_at as unAuthAdded')
		->get();

		if($unauthorizedUsers) {
	        return response()->json ([ 
                'status' =>'Успешно',
                'unauthorizedUsers' => $unauthorizedUsers 
            ], 200); 
	    } 

    	 return response()->json(['notification' => 
    		[
    			'msg' => 'Потребителя не беше намерен!' ,
    			'type' => 1, 
    			'active' => 1
    		] 
        ], 422); 
	}

	public function authorizeUser(Request $request) 
	{
		$user = $request->all();

		if($user['path']) {

    		Storage::delete('/public/users_for_authorization/'.$user['fileName']);
			
			$deleted = UsersForAuthorize::where('user_id',$user['id'])->delete();
			
			if($deleted) {
				$user = User::where('id',$user['id'])->update(['has_payed' => 1]);
				return response()->json(['notification' => 
	    		[
		    			'msg' => 'Потребителя беше одобрен!' ,
		    			'type' => 1, 
		    			'active' => 1
		    		] 
		        ], 200); 
			}
		}

	   	return response()->json(['notification' => 
    		[
    			'msg' => 'Потребителя не беше одобрен!' ,
    			'type' => 0, 
    			'active' => 1
    		] 
        ], 422); 
	}

	public function getRelatedUsers(Request $request) 
	{
		$firmId = $request->id;

		$relatedUsers = DB::table('users')
      	->join('firms', 'users.company_id', '=', 'firms.id')
      	->join('roles','users.role_id','=','roles.id')
      	->select('users.*','firms.name as firmName','firms.id as firmId','firms.bulstat','roles.name as roleName')
      	->where('firms.id',$firmId)
		->get();

		if($relatedUsers) {
	        return response()->json ([ 
                'status' =>'Успешно',
                'hasRel' => 1,
                'relatedUsers' => $relatedUsers 
            ], 200); 
	    } else {
	    	  return response()->json ([ 
                'status' =>'Успешно',
                'hasRel' => 0,
            ], 200); 
	    }
	}
	public function getUserById(Request $request) 
	{
		$userId = $request->id;

		$user = DB::table('users')
      	->join('firms', 'users.company_id', '=', 'firms.id')
      	->join('roles','users.role_id','=','roles.id')
      	->select('users.*','firms.name as firmName','firms.id as firmId','firms.bulstat','roles.name as roleName')
      	->where('users.id',$userId)
		->first();

		if($user) {
	        return response()->json ([ 
                'status' =>'Успешно',
                'user' => $user 
            ], 200); 
	    } 

    	 return response()->json(['notification' => 
    		[
    			'msg' => 'Потребителя не беше намерен!' ,
    			'type' => 1, 
    			'active' => 1
    		] 
        ], 422); 
	    
	}

	public function deleteUser(Request $request) 
	{

		$userId = $request->id;

		$user = User::find($userId);

		if($user->delete()) {
			return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Успешно изтрихте потребителя!' ,
	        			'type' => 1, 
	        			'active' => 1
	        		] 
	            ], 200);
		}  

		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Неуспешно изтриване на потребителя!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		] 
	            ], 422);
	}

	public function updateUser(Request $request) {

		$rules = [
			'email' => ['sometimes','required', 'email', 'unique:users'],
			'phone_number' => ['sometimes','required','digits_between:10,10','numeric','unique:users'],
		];

		$customMessages = [
			'email.required' => 'Полето за имейл е задължително.',
			'email.unique' => 'Вече съществува потребител с този имейл.',
			'email.email' => 'Въведете валиден имейл адрес.',
			'phone_number.required' => 'Телефонния номер е задължително поле.',
			'phone_number.digits_between' => 'Телефонния номер трябва да съдържа 10 числа.',
			'phone_number.numeric' => 'Въведете само числа за телефонен номер.',
			'phone_number.unique' => 'Телефонният номер вече съществува.',
		];

		$validation = $this->validate($request,$rules,$customMessages);

		$updateFields = $request->all();
		unset($updateFields['id']);

		if($validation) {
			DB::table('users')->where('id',$request->id)->update($updateFields);
			$user = DB::table('users')->where('id', $request->id)->first();
			return response()->json(['user' => $user , 
				'notification' => ['msg' => 'Успешно обновихте данните!' , 'type' => 1, 'active' => 1] 
			],200);
		} 
		
		return response()->json(
	        	['notification' => 
	        		[
	        			'msg' => 'Неуспешно обновяване на потребителя!' ,
	        			'type' => 0, 
	        			'active' => 1
	        		] 
	            ], 422);
	}


}
