<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDuelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('relation_id')->unsigned()->nullable();
            $table->integer('level_id')->unsigned()->nullable();
            $table->tinyInteger('nb_turns')->unsigned()->default(5);
            $table->tinyInteger('nb_users')->unsigned()->default(2);
            $table->string('state',20)->default("pending");
            $table->datetime('expired_at');
            $table->timestamps();
        });
        
        Schema::create('duel_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('duel_id')->unsigned();
            $table->integer('score')->unsigned()->default(0);
            $table->integer('final_score')->unsigned()->default(0);
            $table->tinyInteger('rank')->unsigned()->nullable();
            $table->tinyInteger('result')->nullable();
            $table->tinyInteger('turn')->unsigned()->default(0);
            $table->boolean('seen')->default(0);
            $table->boolean('email')->nullable()->default(null);
            $table->timestamps();
        });
        
        Schema::create('annotation_duel', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('duel_id')->unsigned();
            $table->integer('annotation_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('annotation_user_duel', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('duel_id')->unsigned();
            $table->integer('annotation_id')->unsigned();
            $table->integer('annotation_user_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->mediumInteger('answer')->unsigned();
            $table->integer('score')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('duels');
        Schema::drop('duel_user');
        Schema::drop('annotation_duel');
        Schema::drop('annotation_user_duel');
    }
}
