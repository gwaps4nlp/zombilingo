<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTrophies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trophies', function (Blueprint $table) {
            Schema::drop('trophies');
            Schema::create('trophies', function (Blueprint $table){
                $table->increments('id');
                $table->string('name',100);
                $table->string('slug',100);
                $table->string('key',30);
                $table->mediumInteger('maximum_floor');
                $table->string('description',50);
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
        Schema::table('trophies', function (Blueprint $table) {
            //
        });
    }
}
