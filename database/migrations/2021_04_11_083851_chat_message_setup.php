<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChatMessageSetup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('messages', function (Blueprint $table) {
            // $table->increments('id');
            // $table->unsignedBigInteger('sender_id');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            // $table->unsignedBigInteger('reciever_id');
            $table->foreign('reciever_id')->references('id')->on('users')->onDelete('cascade');
            // $table->text('message')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         if (Schema::hasTable('messages')) {
            Schema::table('messages', function($table) {
                $table->dropForeign('messages_sender_id_foreign');
                $table->dropColumn('sender_id');

                $table->dropForeign('messages_reciever_id_foreign');
                $table->dropColumn('reciever_id');
            });

            Schema::dropIfExists('messages');
        }
    }
}
