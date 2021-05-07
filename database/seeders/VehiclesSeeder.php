<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class VehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $vehicles = [
            [
                'name' => "Хладилни",
            ],
            [
                'name' => "Цистерни",
            ],
            [
                'name' => "Гондоли",
            ],
            [
                'name' => "Мегатрейлъри",
            ],
            [
                'name' => "Контейнери",
            ]
        ];
        DB::table('vehicles')->insert($vehicles);
    }
}
