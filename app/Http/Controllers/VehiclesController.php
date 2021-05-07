<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Firm;
use App\Models\UserVehicle;
use DB;

class VehiclesController extends Controller
{
    //GET 
    public function getVehicles(Request $request) {
  	 	$vehicles = Vehicle::all();
  		return json_encode(['msg' => 'succes call', 'vehicles' => $vehicles]);
    }

    //POST method

    //PUT method

    //DELETE method


    public function getVehiclesByCompany(Request $request) {
    	$firm = Firm::find($request->id);
      $vehicles = DB::select(
                        'SELECT vehicles.name, vehicles.id, coalesce(vehicles_id,0) as vehicles_id, coalesce(count,0) as count, (CASE 
                                    WHEN company_vehicles.count = "0" OR ISNULL(company_vehicles.count) THEN 0 
                                    ELSE 1 
                                  END) AS "disabled" 
                          FROM `vehicles` 
                          LEFT JOIN company_vehicles ON company_vehicles.vehicles_id = vehicles.id AND company_vehicles.company_id = 1'

                  );
      return json_encode(['msg' => 'succes call', 'firm' => $firm,'vehicles' => $vehicles]);
    }

    public function updateVehiclesByCompany(Request $request) {
      $firmId = $request->firm['id'];
      $atLeastOneChecked = 0;

        foreach($request->vehicles as $k => $v) {
          if($v['count'] != 0 && $v['disabled'] == 1) {
              $atLeastOneChecked = 1;
              // dd($firmId);
              // dd($v['id'])
              // dd($v['count'])
              UserVehicle::updateOrCreate(
                ['company_id' => $firmId, 'vehicles_id' => $v['id']],
                ['count' => $v['count']]
             );
          } 
        } 

         foreach($request->vehicles as $k => $v) {
          if($v['disabled'] == 0 && $atLeastOneChecked) {
            $deletedRows = UserVehicle::where('company_id', $firmId)->where('vehicles_id',$v['id'])->delete();
          }
        } 

      
        $vehicles = UserVehicle::where('company_id','=',$firmId)->get();
        
        if(!$atLeastOneChecked) {
            return response()->json(['vehicles' => $vehicles , 
              'notification' => ['msg' => 'Моля оставете поне едно от полетата активно' , 'type' => 0, 'active' => 1] 
            ],200);
        } else {
            return response()->json(['vehicles' => $vehicles , 
              'notification' => ['msg' => 'Успешно обновихте данните си' , 'type' => 1, 'active' => 1] 
            ],200);
        }
    }
}
