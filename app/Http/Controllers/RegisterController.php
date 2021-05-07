<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use DB;

class RegisterController extends Controller
{
    public function register(Request $request) {


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
            ]);

            //insert with same id
            DB::table('users')->where('id',$user_id)->update(array('owner_id' => $user_id));

            $vehicles = [];
            foreach($request->vehicles as $k => $v) {
                $vehicle_id = 0;
                if($k == 'cist') {
                    $vehicle_id = 2;
                } else if ($k == 'gond') {
                    $vehicle_id = 3;
                } else if ($k == 'hlad') {
                    $vehicle_id = 1;
                } else if ($k == 'megat') {
                    $vehicle_id = 4;
                } else if ($k == 'vlek') {
                    $vehicle_id = 5;
                }
                if(!empty($v)) {
                    $vehicles[] = [
                        'company_id' => $company_id,
                        'vehicles_id' => $vehicle_id,
                        'count' => $v,
                    ];
                }
            }

            DB::table('company_vehicles')->insert($vehicles);

        });

        return true;
    }

}
