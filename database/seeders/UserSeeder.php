<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Str;
use Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $users = [
        	[
	            'name' => "Stanislav Stoychev",
	            'email' => 'staskata_1998@abv.bg',
	            'password' => Hash::make('password'),
	            'owner_id' => 1,
	            'has_payed' => true,
	            'company_id' => 1,
	            'phone_number' => "0885588319",
    	    ],
    	    [
	            'name' => "Radostin Ivanov",
	            'email' => 'staskata1998@gmail.com',
	            'password' => Hash::make('password'),
	            'owner_id' => null,
	            'has_payed' => false,
	            'company_id' => 1,
	            'phone_number' => "0885588318",
    	    ],
    	    [
	            'name' => "Iliqn Stoqnov",
	            'email' => Str::random(10).'@gmail.com',
	            'password' => Hash::make('password'),
	            'owner_id' => 3,
	            'has_payed' => false,
	            'company_id' => 2,
	            'phone_number' => "0885588317",
    	    ]
        ];
        DB::table('users')->insert($users);
    }
}
