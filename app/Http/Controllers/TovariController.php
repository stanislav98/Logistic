<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TovariTransport; 
use App\Http\Requests\TovTransRequest; 
use App\Events\NewTovar;
use DB;

class TovariController extends Controller
{
    public function getTovari(Request $request) {
      $tovari = DB::select(
        DB::raw(
          'select 
          `tovari_transport`.*, 
          `firms`.`name` as `firmName`, 
          `vehicles`.`name` as `vehicleName`, 
          `users`.`name` as `userName`,
          COUNT(DISTINCT `reports`.`id`) as countReports,
          FLOOR(SUM(`rating`.`rate`)/COUNT(`rating`.`id`)) as rating
          from `tovari_transport` 
          inner join `users` on `tovari_transport`.`user_id` = `users`.`id` 
          inner join `firms` on `users`.`company_id` = `firms`.`id` 
          inner join `vehicles` on `tovari_transport`.`vehicle_type` = `vehicles`.`id`
          left join `reports` on `firms`.`id` = `reports`.`firm_id` and `reports`.`active` = 1 
          left join `rating` on `firms`.`id` = `rating`.`firm_id`
          where `type` = 1 and `to_date` > CURDATE()
          group by `id`,`firms`.`id`,`users`.id 
          order by `created_at` desc,`price` desc'
        )
      );

      if(!empty($tovari)) {
        return json_encode(['msg' => 'Успешно зареждане', 'tovari' => $tovari]);
      }

      return response()->json(
        ['notification' => 
          [
            'msg' => 'Няма намерени товари!' ,
            'type' => 1, 
            'active' => 1
          ] 
        ], 422);
    }

    public function getTovariByFirm(Request $request) 
    {
      $firmId = $request->id;

      $relatedTovari = DB::select(
        DB::raw(
            'select 
            `tovari_transport`.*, 
            `firms`.`name` as `firmName`, 
            `vehicles`.`name` as `vehicleName`, 
            `users`.`name` as `userName`,
            COUNT(DISTINCT `reports`.`id`) as countReports,
            FLOOR(SUM(`rating`.`rate`)/COUNT(`rating`.`id`)) as rating
            from `tovari_transport` 
            inner join `firms` on `tovari_transport`.`firm_id` = `firms`.`id` 
            inner join `users` on `tovari_transport`.`user_id` = `users`.`id` 
            inner join `vehicles` on `tovari_transport`.`vehicle_type` = `vehicles`.`id`
            left join `reports` on `firms`.`id` = `reports`.`firm_id` and `reports`.active = 1
            left join `rating` on `firms`.`id` = `rating`.`firm_id`
            where `type` = 1 and `tovari_transport`.`firm_id` = :firm_id
            group by `id`,`firms`.`id`
            order by `created_at` desc,`price` desc'
        ),['firm_id'=>$firmId]
      );

      if($relatedTovari) {
        return json_encode(['msg' => 'Успешно зареждане', 'tovari' => $relatedTovari]);
      }

      return response()->json(
        ['notification' => 
          [
            'msg' => 'Няма намерени товари!' ,
            'type' => 1, 
            'active' => 1
          ] 
        ], 422);

    }

  public function deleteTovar(Request $request) 
  {

    $tovTransId = $request->id;

    $tovariTransport = TovariTransport::find($tovTransId);

    if($tovariTransport->delete()) {
      return response()->json(
            ['notification' => 
              [
                'msg' => 'Успешно изтрихте товара!' ,
                'type' => 1, 
                'active' => 1
              ] 
              ], 200);
    }  

    return response()->json(
            ['notification' => 
              [
                'msg' => 'Неуспешно изтриване на товара!' ,
                'type' => 0, 
                'active' => 1
              ] 
              ], 422);
  }

  public function updateTovarTransport(TovTransRequest $request) 
  {
    $data = $request->validated();

      //unset data because we dont need to update the firm or user id
      unset($data['user_id']);
      unset($data['firm_id']);

      if($data) {
        DB::table('tovari_transport')->where('id',$request->id)->update($data);
        $tovar = DB::table('tovari_transport')->where('id', $request->id)->first();
        return response()->json(['tovar' => $tovar , 
          'notification' => ['msg' => 'Успешно обновихте товара!' , 'type' => 1, 'active' => 1] 
        ],200);
      } 
      
      return response()->json(
        ['notification' => 
          [
            'msg' => 'Неуспешно обновяване на товара!' ,
            'type' => 0, 
            'active' => 1
          ] 
        ], 422
      );
    }

    //old request is tovtransrequest
  public function insertTovariTransport(TovTransRequest $request) 
  {
    //adding
    $data = $request->validated();

    if($tovar = TovariTransport::create($data)) {
      $tovar = DB::select(
        DB::raw(
          'select 
          `tovari_transport`.*, 
          `firms`.`name` as `firmName`, 
          `vehicles`.`name` as `vehicleName`, 
          `users`.`name` as `userName`,
          COUNT(DISTINCT `reports`.`id`) as countReports,
          FLOOR(SUM(`rating`.`rate`)/COUNT(`rating`.`id`)) as rating
          from `tovari_transport` 
          inner join `users` on `tovari_transport`.`user_id` = `users`.`id` 
          inner join `firms` on `users`.`company_id` = `firms`.`id` 
          inner join `vehicles` on `tovari_transport`.`vehicle_type` = `vehicles`.`id`
          left join `reports` on `firms`.`id` = `reports`.`firm_id` and `reports`.`active` = 1 
          left join `rating` on `firms`.`id` = `rating`.`firm_id`
          where `type` = 1 and `to_date` > CURDATE() and `tovari_transport`.id = :tov_trans_id
          group by `id`,`firms`.`id`,`users`.id 
          order by `created_at` desc,`price` desc'
        ),
        ['tov_trans_id' => $tovar->id]
      );
        try {
            broadcast(new NewTovar($tovar))->toOthers();
        } catch (Exception $e) {
            return response(['error' => 'failed']);            
        }
        
      return response()->json(['notification' => 
              [
                'msg' => 'Успено добавихте товара!' ,
                'type' => 1, 
                'active' => 1
              ],
              'tovar' => $tovar
              ], 200); 
    } 
    
    return response()->json(
            ['notification' => 
              [
                'msg' => 'Неуспешно добавяне на товара!' ,
                'type' => 0, 
                'active' => 1
              ] 
              ], 422);
  }

}
