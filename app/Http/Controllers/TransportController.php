<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TovariTransport; 
use App\Http\Requests\TovTransRequest; 
use App\Events\NewTransport;
use DB;

class TransportController extends Controller
{
    public function getTransport(Request $request) {
      $transport = DB::select(
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
            where `type` = 0 and `to_date` > CURDATE() 
            group by `id`,`firms`.`id`,`users`.id 
            order by `created_at` desc,`price` desc'
        )
      );

      if(!empty($transport)) {
        return json_encode(['msg' => 'Успешно зареждане', 'transport' => $transport]);
      }

      return response()->json(
        ['notification' => 
          [
            'msg' => 'Няма намерен транспорт!' ,
            'type' => 1, 
            'active' => 1
          ] 
        ], 422);
    }

    public function getTransportByFirm(Request $request) 
    {
      $firmId = $request->id;

      $relatedTransport = DB::select(
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
            where `type` = 0 and `tovari_transport`.`firm_id` = :firm_id
            group by `id`,`firms`.`id`
            order by `created_at` desc,`price` desc'
        ),['firm_id'=>$firmId]
      );

      if($relatedTransport) {
        return json_encode(['msg' => 'Успешно зареждане', 'transport' => $relatedTransport]);
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

  public function deleteTransport(Request $request) 
  {

    $tovTransId = $request->id;

    $tovariTransport = TovariTransport::find($tovTransId);

    if($tovariTransport->delete()) {
      return response()->json(
            ['notification' => 
              [
                'msg' => 'Успешно изтрихте транспорта!' ,
                'type' => 1, 
                'active' => 1
              ] 
              ], 200);
    }  

    return response()->json(
            ['notification' => 
              [
                'msg' => 'Неуспешно изтриване на транспорта!' ,
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
        $transport = DB::table('tovari_transport')->where('id', $request->id)->first();
        return response()->json(['transport' => $transport , 
          'notification' => ['msg' => 'Успешно обновихте транспорта!' , 'type' => 1, 'active' => 1] 
        ],200);
      } 
      
      return response()->json(
        ['notification' => 
          [
            'msg' => 'Неуспешно обновяване на транспорта!' ,
            'type' => 0, 
            'active' => 1
          ] 
        ], 422
      );
    }

  public function insertTransport(TovTransRequest $request) {

    $data = $request->validated();

    if($transport = TovariTransport::create($data)) {
       $transport = DB::select(
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
                        where `type` = 0 and `to_date` > CURDATE() and `tovari_transport`.id = :tov_trans_id
                        group by `id`,`firms`.`id`,`users`.id 
                        order by `created_at` desc,`price` desc'
                      ),
                      ['tov_trans_id' => $transport->id]
                    );
      try {
          broadcast(new NewTransport($transport))->toOthers();
      } catch (Exception $e) {
          return response(['error' => 'failed']);            
      }
      return response()->json(['notification' => 
              [
                'msg' => 'Успено добавихте транспорта!' ,
                'type' => 1, 
                'active' => 1
              ],
              'transport' => $transport
              ], 200); 
    } 
    
    return response()->json(
            ['notification' => 
              [
                'msg' => 'Неуспешно добавяне на транспорта!' ,
                'type' => 0, 
                'active' => 1
              ] 
              ], 422);
  }
}
