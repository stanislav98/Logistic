<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserToTovtrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tovari_transport', function ($table) {
            $table->dropForeign('tovari_transport_firm_id_foreign');
            $table->dropColumn('firm_id');

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            // Schema::table('tovari_transport', function($table) {
            //     $table->dropForeign('tovari_transport_firm_id_foreign');
            //     $table->dropColumn('firm_id');
            // });
    }
}
