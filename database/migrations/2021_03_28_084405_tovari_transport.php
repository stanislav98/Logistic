<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TovariTransport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
       if (!Schema::hasTable('tov_trans_types')) {
            Schema::create('tov_trans_types', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable(false);
            });
        }

        if (!Schema::hasTable('tovari_transport')) {
            Schema::create('tovari_transport', function (Blueprint $table) {
                $table->id();
                // $table->unsignedBigInteger('firm_id');
                // $table->foreign('firm_id')->references('id')->on('firms')->onDelete('cascade');
                $table->point('from_lat_lng')->nullable(false);
                $table->point('to_lat_lng')->nullable(false);
                $table->mediumText('description')->nullable(true);
                // $table->unsignedBigInteger('vehicle_type');
                // $table->foreign('vehicle_type')->references('id')->on('vehicles')->onDelete('cascade');
                $table->decimal('price',8,2)->nullable(false);
                $table->timestamp('from_date')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->timestamp('to_date')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP')) ;
                $table->boolean('fast_payment')->nullable(false)->comment('1 - can pay fast tovar / search fast payment transport');
                $table->boolean('adr')->nullable(false)->comment('0 - not adr , 1 - is adr');
                $table->boolean('type')->nullable(false)->comment('0 - tovar , 1 - transport');
                // $table->unsignedBigInteger('tov_trans_types');
                // $table->foreign('tov_trans_types')->references('id')->on('tov_trans_types')->onDelete('cascade');
                $table->timestamp('created_at')->nullable(false)->default(DB::raw(DB::raw('CURRENT_TIMESTAMP')));
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
        Schema::dropIfExists('tov_trans_types');
        Schema::dropIfExists('tovari_transport');
    }
}
