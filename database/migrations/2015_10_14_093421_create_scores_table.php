<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('corpus_id')->unsigned();
            $table->integer('relation_id')->unsigned();
            $table->integer('points')->unsigned();
            $table->timestamps();
        });

        Schema::table('scores', function (Blueprint $table) {
            $table->foreign('corpus_id')->references('id')->on('corpuses');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('scores');
    }
}
