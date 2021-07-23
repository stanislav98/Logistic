<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Firm; 
use DB;

class FirmController extends Controller
{
    public function getFirms() {
    	$firms = DB::select(
    		DB::raw(
	    		'SELECT f.`id`, f.`bulstat`,f.`name`,f.`manager`,f.`address`,GROUP_CONCAT(DISTINCT(v.name) ORDER BY v.id) as vehicle_names,GROUP_CONCAT(DISTINCT(cv.count) ORDER BY cv.vehicles_id) as vehicle_counts, GROUP_CONCAT(DISTINCT(u.name) ORDER BY u.id) as users, GROUP_CONCAT(DISTINCT(u.id) ORDER BY u.id) as user_ids, GROUP_CONCAT(DISTINCT(u.email) ORDER BY u.id) as emails, GROUP_CONCAT(DISTINCT(u.phone_number) ORDER BY u.id) as phones, f.`created_at`
	    		FROM `firms` AS `f`
				LEFT JOIN company_vehicles AS cv ON f.id = cv.company_id
				LEFT JOIN vehicles AS v ON cv.vehicles_id = v.id
				LEFT JOIN users AS u ON f.id = u.company_id
				GROUP BY f.id'
			)
		);

		if(!empty($firms)) {
			return response()->json(['msg'=>'Успешно извличане на фирмите', 'firms' => $firms]);
		} else {
			return response()->json(
        	['notification' => 
        		[
        			'msg' => 'Нещо се обърка! Моля презаредете страницата' ,
        			'type' => 0, 
        			'active' => 1
        		] 
            ], 422);
		}


    }

    public function getFirmById(Request $request) {
    	
    	$firmId = $request->id;

    	$firm = DB::select(
    		DB::raw(
	    		'SELECT f.`id`, f.`bulstat`,f.`name`,f.`manager`,f.`address`,GROUP_CONCAT(DISTINCT(v.name) ORDER BY v.id) as vehicle_names,GROUP_CONCAT(DISTINCT(cv.count) ORDER BY cv.vehicles_id) as vehicle_counts, GROUP_CONCAT(DISTINCT(u.name) ORDER BY u.id) as users, GROUP_CONCAT(DISTINCT(u.id) ORDER BY u.id) as user_ids, GROUP_CONCAT(DISTINCT(u.email) ORDER BY u.id) as emails, GROUP_CONCAT(DISTINCT(u.phone_number) ORDER BY u.id) as phones, f.`created_at` FROM `firms` AS `f` LEFT JOIN company_vehicles AS cv ON f.id = cv.company_id LEFT JOIN vehicles AS v ON cv.vehicles_id = v.id LEFT JOIN users AS u ON f.id = u.company_id WHERE f.id = ? GROUP BY f.id'
			), [$firmId]
		);

    	if(!empty($firm)) {
			return response()->json(['msg'=>'Успешно извличане на фирмите', 'firm' => $firm[0]]);
    	} else {
    		return response()->json(
        	['notification' => 
        		[
        			'msg' => 'Нещо се обърка! Моля презаредете страницата' ,
        			'type' => 0, 
        			'active' => 1
        		] 
            ], 422);	
    	}

    }
}
