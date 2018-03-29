<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationInProgressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotation_in_progress', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('relation_id')->unsigned();
            $table->integer('annotation_id')->unsigned();
            $table->integer('corpus_id')->unsigned();
            $table->integer('turn')->unsigned();
            $table->timestamps();
			$table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('annotation_in_progress', function (Blueprint $table) {
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
        Schema::table('annotation_in_progress', function ($table) {
            $table->dropForeign(['corpus_id']);   
        });        
        Schema::drop('annotation_in_progress');
    }
}
