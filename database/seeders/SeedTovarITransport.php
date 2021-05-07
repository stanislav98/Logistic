<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class SeedTovarITransport extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	// $tovari_transport_types = [
     //    	[
	    //         'name' => "Групажен",
    	//     ],
    	//     [
	    //         'name' => "Комплектен",
    	//     ], 
    	//     [
	    //         'name' => "Насипен",
    	//     ], 
    	//     [
	    //         'name' => "Друг",
    	//     ],
    	//     [
	    //         'name' => "Групажен/Комплектен",
    	//     ]
     //    ];
     //    DB::table('tov_trans_types')->insert($tovari_transport_types);

        $tovari_transport = [
        	[
	            'from_lat' => 43.218199,
	            'from_lng' => 27.932893,
	            'to_lat' => 42.510784,
	            'to_lng' => 27.451005,
	            'from_city' => "Варна",
	            'to_city' => "Бургас",
	            'description' => '',
	            'price' => 32,
	            'to_date' => '2020-07-05 10:10:10',
	            'fast_payment' => 1,
	            'adr' => 0,
	            'type' => 1,
	            'tov_trans_types' => 1,
	            'firm_id' => 1,
	            'vehicle_type' => 1,
    	    ],
    	    [
	            'from_lat' => 43.218199,
	            'from_lng' => 27.932893,
	            'to_lat' => 42.510784,
	            'to_lng' => 27.451005,
	            'from_city' => "Варна",
	            'to_city' => "Бургас",
	            'description' => '',
	            'price' => 32,
	            'to_date' => '2020-07-05 10:10:10',
	            'fast_payment' => 1,
	            'adr' => 1,
	            'type' => 0,
	            'tov_trans_types' => 1,
	            'firm_id' => 1,
	            'vehicle_type' => 1,
    	    ],
    	    
        ];
        DB::table('tovari_transport')->insert($tovari_transport);

       
    }
}
