<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreChallengesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_challenges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('challenge_id')->unsigned()->index();
            $table->integer('points')->default(0)->unsigned();
            $table->integer('number_annotations')->default(0)->unsigned();            
            $table->timestamps();
            $table->unique(['user_id', 'challenge_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('score_challenges');
    }
}
