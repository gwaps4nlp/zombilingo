<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePosGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pos_games', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pos1_id')->unsigned();
            $table->integer('pos2_id')->unsigned();
            $table->string('help_file',30);
            $table->timestamps();
        });

        Schema::create('cat_pos_pos_game', function (Blueprint $table) {
            $table->integer('cat_pos_id')->unsigned();
            $table->integer('pos_game_id')->unsigned();
            $table->foreign('cat_pos_id')->references('id')->on('cat_pos');   
            $table->foreign('pos_game_id')->references('id')->on('pos_games');   
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pos_games');
        Schema::drop('pos_games');
    }
}
