<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNegativeAnnotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('negative_annotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('relation_id')->unsigned();
            $table->integer('annotation_id')->unsigned();
            $table->string('explanation',200);
            $table->boolean('visible')->default(1);
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
        Schema::drop('negative_annotations');
    }
}
