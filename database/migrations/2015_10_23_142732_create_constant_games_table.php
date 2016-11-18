<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConstantGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('constant_games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key',50)->unique();
            $table->string('value',50);
            $table->string('description',200);
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
        Schema::drop('constant_games');
    }
}
