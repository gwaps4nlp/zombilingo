<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePreannotatedEvaluationCorpus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preannotated_evaluation_corpus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('corpus_id')->unsigned();
            $table->integer('evaluation_corpus_id')->unsigned();
            $table->timestamps();
        });
        Schema::table('preannotated_evaluation_corpus', function ($table) {
            $table->foreign('corpus_id')->references('id')->on('corpuses');
            $table->foreign('evaluation_corpus_id')->references('id')->on('corpuses');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('preannotated_evaluation_corpus');
    }
}