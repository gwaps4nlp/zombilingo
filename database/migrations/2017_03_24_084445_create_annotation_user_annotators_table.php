<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationUserAnnotatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotation_user_annotators', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('corpus_id')->unsigned();
            $table->integer('sentence_id')->unsigned();
            $table->integer('relation_id')->unsigned();
            $table->integer('word_position')->unsigned();
            $table->string('word',100);
            $table->string('governor_word',100);
            $table->integer('governor_position')->unsigned();
            $table->string('lemma',100);
            $table->integer('category_id')->unsigned();
            $table->integer('pos_id')->unsigned();
            $table->text('features',1000);
            $table->integer('projective_governor_position')->unsigned();
            $table->integer('projective_relation_id')->unsigned();
            $table->integer('source_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('annotation_user_annotators', function(Blueprint $table) {
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

            // $table->foreign('pos_id')
            //             ->references('id')
            //             ->on('cat_pos');
        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('annotation_user_annotators');
    }
}
