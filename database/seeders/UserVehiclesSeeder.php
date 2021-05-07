<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class UserVehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $company_vehicles = [
    		[
	            'company_id' => 1,
	            'vehicles_id' => 1,
	            'count' => 4,
    	    ],
    	    [
	            'company_id' => 1,
	            'vehicles_id' => 3,
	            'count' => 2,
    	    ],
    	    [
	            'company_id' => 2,
	            'vehicles_id' => 2,
	            'count' => 1,
    	    ]
        ];
        DB::table('company_vehicles')->insert($company_vehicles);
    }
}
