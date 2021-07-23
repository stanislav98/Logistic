<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDirectionColumnTovariTransport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tovari_transport', function ($table) {
            $table->boolean('both_directions')->default(true)->comment('0 for one direction | 1 for both directions');
            $table->smallInteger('number_vehicles')->default(1)->comment('number of vehicles needed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
