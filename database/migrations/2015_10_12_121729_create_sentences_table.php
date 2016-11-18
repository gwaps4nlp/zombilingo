<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSentencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sentences', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->integer('corpus_id')->unsigned();
            $table->decimal('difficulty',4,2);
            $table->string('sentid',255);
            $table->integer('source_id')->unsigned();
            $table->text('conll');
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
        Schema::drop('sentences');
    }
}
