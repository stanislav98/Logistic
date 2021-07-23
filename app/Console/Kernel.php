<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use App\Models\TovariTransport;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // remove has_payed for the user !
        $schedule->call(function () {
            $allUnpaidFirms = DB::select(
                DB::raw('
                    SELECT `users`.`id`,COUNT(subscriptions.id) as countSubs
                    FROM `users` 
                    JOIN firms on `users`.`company_id` = `firms`.`id` 
                    LEFT JOIN `subscriptions` on `subscriptions`.`firm_id` = `firms`.`id` 
                    WHERE owner_id = users.id AND has_payed = 1 AND DATEDIFF(users.created_at, NOW()) = -1
                    GROUP BY users.id,firms.id
                ')
            );

            foreach($allUnpaidFirms as $v) {
                DB::table('users')
                ->where('id',$v->id)
                ->update('has_payed',0);
            }
        })->daily()->emailOutputOnFailure('staskata1998@gmail.com');

        //remove old tovari | transport 
          $schedule->call(function () {
            $allOldTovTrans = TovariTransport::where('to_date','<',Carbon::now());

            foreach($allOldTovTrans as $v) {
                TovariTransport::where('id',$v->id)->delete();
            }

        })->daily()->emailOutputOnFailure('staskata1998@gmail.com');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
