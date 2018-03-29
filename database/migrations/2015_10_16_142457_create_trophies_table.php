<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrophiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trophies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->string('slug',100);
            $table->string('key',30);
            $table->mediumInteger('required_value');
            $table->string('description',50);
            $table->mediumInteger('points');
            $table->boolean('is_secret');
            $table->string('image',50);
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
        Schema::drop('trophies');
    }
}