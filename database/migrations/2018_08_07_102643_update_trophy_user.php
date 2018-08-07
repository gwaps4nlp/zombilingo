<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTrophyUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trophy_user', function (Blueprint $table) {
            Schema::drop('trophy_user');
            Schema::create('trophy_user', function (Blueprint $table){
                $table->increments('id');
                $table->integer('user_id')->unsigned();
                $table->integer('trophy_id')->unsigned();
                $table->integer('score');
                $table->integer('actual_floor');
                $table->integer('number_maximum_floor');
                $table->string('image',50);
                $table->timestamps();});
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trophy_user', function (Blueprint $table) {
            //
        });
    }
}
