<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;


class FirmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $firms = [
            [
                'name' => "ИКТ ООД",
                'bulstat' => 102898356,
                'manager' => "Станислав Стойчев",
                'address' => "Поморие",
            ],
            [
                'name' => "МД-Експрес ЕООД",
                'bulstat' => 200446102,
                'manager' => "Радостин Иванов",
                'address' => "Бургас",
            ]
        ];
          DB::table('firms')->insert($firms);
    }
}
