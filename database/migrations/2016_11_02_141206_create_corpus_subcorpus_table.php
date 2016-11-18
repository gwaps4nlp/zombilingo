<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorpusSubcorpusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corpus_subcorpus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('corpus_id')->unsigned();
            $table->integer('subcorpus_id')->unsigned();
            $table->timestamps();
        });
        Schema::table('corpus_subcorpus', function ($table) {
            $table->foreign('corpus_id')->references('id')->on('corpuses');
            $table->foreign('subcorpus_id')->references('id')->on('corpuses');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('corpus_subcorpus');
    }
}