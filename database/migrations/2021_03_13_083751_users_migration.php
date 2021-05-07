<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UsersMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
         if (!Schema::hasTable('firms')) {
            Schema::create('firms', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable(false);
                $table->integer('bulstat')->unique()->nullable(false);
                $table->string('manager')->nullable(false);
                $table->string('address')->nullable(false);
                $table->timestamp('created_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
            });

            DB::statement('ALTER TABLE firms ADD CONSTRAINT chk_bulstat_numbers CHECK (bulstat >= 100000000 and bulstat <= 999999999);');
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function($table) {
                $table->dropColumn('email_verified_at');
                $table->integer('owner_id')->nullable(true);
                $table->string('phone_number')->unique();
                $table->unsignedBigInteger('company_id');
                $table->foreign('company_id')->references('id')->on('firms')->onDelete('cascade');
                $table->boolean('has_payed');
            });

        }

       
        if (!Schema::hasTable('vehicles')) {
            Schema::create('vehicles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable(false);
            });
        }

        if (!Schema::hasTable('company_vehicles')) {
                Schema::create('company_vehicles', function (Blueprint $table) {
                    $table->bigIncrements('id');        
                    $table->unsignedBigInteger('company_id');
                    $table->foreign('company_id')
                          ->references('id')
                          ->on('firms')->onDelete('cascade');
                    $table->unsignedBigInteger('vehicles_id');
                    $table->foreign('vehicles_id')
                          ->references('id')
                          ->on('vehicles')->onDelete('cascade');
                    $table->integer('count')->nullable(false);

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
        //
         if (Schema::hasTable('users')) {
            Schema::table('users', function($table) {
                $table->timestamp('email_verified_at')->nullable();
                $table->dropColumn('owner_id');
                $table->dropColumn('company_id');
            });
        }

        Schema::dropIfExists('firms');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('user_vehicles');

    }
}
