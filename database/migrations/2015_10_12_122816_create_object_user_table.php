<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObjectUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('object_id')->unsigned();
            $table->mediumInteger('quantity')->unsigned()->default(0);
            $table->boolean('help_seen')->default(0);
            $table->boolean('seen')->default(0);
            $table->timestamps();
        });

        Schema::table('object_user', function(Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')
                        ->onDelete('restrict')
                        ->onUpdate('restrict');
        });

        Schema::table('object_user', function(Blueprint $table) {
            $table->foreign('object_id')->references('id')->on('objects')
                        ->onDelete('restrict')
                        ->onUpdate('restrict');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('object_user');
    }
}
