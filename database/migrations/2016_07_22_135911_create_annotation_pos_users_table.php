<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationPosUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotation_pos_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('sentence_id')->unsigned();
            $table->integer('pos_game_id')->unsigned();
            $table->integer('word_position')->unsigned();
            $table->integer('cat_pos_id')->unsigned();
            $table->tinyInteger('confidence');
            $table->boolean('is_user_tag');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');            
            $table->foreign('sentence_id')->references('id')->on('sentences');            
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
        Schema::drop('annotation_pos_users');
    }
}
