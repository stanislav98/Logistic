<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixTovari extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // Бруто тегло (kg): * 
        // Обем (m3):  
        // Размери на товара (m):  
        // Линейни метри (m):  
        if (Schema::hasTable('tovari_transport')) {
            Schema::table('tovari_transport', function($table) {
                $table->dropForeign('tovari_transport_tov_trans_types_foreign');
                $table->dropColumn('tov_trans_types');

                 $table->decimal('weight', 8, 2)->nullable(false);
            });
        }
        Schema::dropIfExists('tov_trans_types');
        
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
