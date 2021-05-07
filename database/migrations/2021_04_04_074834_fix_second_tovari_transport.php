<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixSecondTovariTransport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('tovari_transport')) {
            Schema::table('tovari_transport', function($table) {
                $table->dropColumn('to_lat_lng');
                $table->dropColumn('from_lat_lng');
                $table->decimal('from_lat', 10, 8)->nullable(false);
                $table->decimal('from_lng', 11, 8)->nullable(false);
                $table->string('from_city')->nullable(false);
                $table->decimal('to_lat', 10, 8)->nullable(false);
                $table->decimal('to_lng', 11, 8)->nullable(false);
                $table->string('to_city')->nullable(false);
            });
        }
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('tovari_transport')) {
            Schema::table('tovari_transport', function($table) {
                $table->dropColumn('from_lat');
                $table->dropColumn('from_lng');
                $table->dropColumn('from_city');
                $table->dropColumn('to_lat');
                $table->dropColumn('to_lng');
                $table->dropColumn('to_city');
                $table->point('from_lat_lng')->nullable(false);
                $table->point('to_lat_lng')->nullable(false);
            });
        }
    }
}
