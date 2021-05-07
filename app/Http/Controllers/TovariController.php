<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TovariTransport; 
use DB;
class TovariController extends Controller
{
     public function getTovari(Request $request) {
  	 	$tovari = DB::table('tovari_transport')
      ->join('firms', 'tovari_transport.firm_id', '=', 'firms.id')
      ->join('vehicles', 'tovari_transport.vehicle_type', '=', 'vehicles.id')
      ->select('tovari_transport.*', 'firms.name as firmName','vehicles.name as vehicleName')
      ->where('type','=','0')
      ->orderBy('price', 'desc')
      ->get();
  		return json_encode(['msg' => 'succes call', 'tovari' => $tovari]);
    }
}
