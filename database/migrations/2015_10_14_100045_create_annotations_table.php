<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('corpus_id')->unsigned();
            $table->integer('sentence_id')->unsigned();
            $table->integer('relation_id')->unsigned();
            $table->float('score');
            $table->float('custom_score');
            $table->integer('word_position')->unsigned();
            $table->string('word',100);
            $table->string('governor_word',100);			
            $table->string('lemma',100);
            // $table->integer('category_id')->unsigned();
            // $table->integer('pos_id')->unsigned();
            $table->string('category_id',10);
            $table->string('pos_id',10);
            $table->string('features',100);
            $table->integer('governor_position')->unsigned();
            $table->integer('projective_governor_position')->unsigned();
            $table->integer('projective_relation_id')->unsigned();
            $table->integer('source_id')->unsigned();
            $table->boolean('undecided')->default(0);
            $table->boolean('best')->nullable()->default(null);
            $table->boolean('playable')->default(1);
            $table->timestamps();
        });

        Schema::table('annotations', function(Blueprint $table) {
            $table->foreign('sentence_id')
                        ->references('id')
                        ->on('sentences')
                        ->onDelete('restrict')
                        ->onUpdate('restrict');

            $table->foreign('relation_id')
                        ->references('id')
                        ->on('relations')
                        ->onDelete('restrict')
                        ->onUpdate('restrict');

            $table->foreign('corpus_id')
                        ->references('id')
                        ->on('corpuses');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('annotations');
    }
}
