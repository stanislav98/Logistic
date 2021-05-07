<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTovariTransport extends Migration
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
                $table->unsignedBigInteger('tov_trans_types');
                $table->foreign('tov_trans_types')->references('id')->on('tov_trans_types')->onDelete('cascade');

                $table->unsignedBigInteger('firm_id');
                $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');

                 $table->unsignedBigInteger('vehicle_type');
                $table->foreign('vehicle_type')->references('id')->on('vehicles')->onDelete('cascade');
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
                $table->dropForeign('tovari_transport_tov_trans_types_foreign');
                $table->dropColumn('tov_trans_types');

                $table->dropForeign('tovari_transport_firm_id_foreign');
                $table->dropColumn('firm_id');

                $table->dropForeign('tovari_transport_vehicle_type_foreign');
                $table->dropColumn('vehicle_type');
            });
        }
    }
}
