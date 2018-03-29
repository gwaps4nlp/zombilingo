<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMwesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mwes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content',100);
            $table->integer('frozen');
            $table->integer('unfrozen');
            $table->integer('skipped');
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
        Schema::drop('mwes');
    }
}
