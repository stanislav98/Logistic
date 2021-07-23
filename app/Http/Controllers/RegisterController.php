<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;


class RegisterController extends Controller
{
    public function register(Request $request) 
    {
        DB::transaction(function() use ($request) {
            $company_id = DB::table('firms')->insertGetId([
                'name' => 'Име на фирма',
                'bulstat' => $request->bulstat,
                'manager' => "Тестов Мениджър",
                'address' => "Варна",
            ]);

            //Get last id inserted as single value and increment it by 1

            $user_id = DB::table('users')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'has_payed' => true,
                'company_id' => $company_id,
                'phone_number' => $request->phone_number,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            //insert with same id
            DB::table('users')->where('id',$user_id)->update(array('owner_id' => $user_id));

            foreach($request->vehicles as $k => $v) {
                if($v['disabled'] == 1) {
                    DB::table('company_vehicles')->insert([
                        'company_id' => $company_id,
                        'vehicles_id' => $v['id'],
                        'count' => $v['count'],
                    ]);
                }
            }


        });

        return true;
    }

}
