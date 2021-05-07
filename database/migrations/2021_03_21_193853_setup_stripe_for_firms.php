<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetupStripeForFirms extends Migration
{

    public function up()
    {
        Schema::table('firms', function (Blueprint $table) {
            $table->string('stripe_id')->nullable()->index();
            $table->string('card_brand')->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->timestamp('trial_ends_at')->nullable();
        });

        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropColumn([
        //         'stripe_id',
        //         'card_brand',
        //         'card_last_four',
        //         'trial_ends_at',
        //     ]);
        // });

        Schema::table('subscriptions', function (Blueprint $table) {
            // $table->dropColumn(['user_id']);
            // $table->unsignedBigInteger('firm_id')->after('id');
            // $table->index(['firm_id', 'stripe_status']);
        });
      
    }

    public function down()
    {
       // Schema::table('firms', function (Blueprint $table) {
       //      $table->dropColumn([
       //          'stripe_id',
       //          'card_brand',
       //          'card_last_four',
       //          'trial_ends_at',
       //      ]);
       //  });


    }
}
