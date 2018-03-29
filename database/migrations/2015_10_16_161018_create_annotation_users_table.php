<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnnotationUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotation_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('level_id')->unsigned();
            $table->integer('annotation_id')->unsigned();
            $table->integer('answer_id')->unsigned();
            $table->integer('sentence_id')->unsigned();
            $table->integer('relation_id')->unsigned();
            $table->integer('word_position')->unsigned();
            $table->integer('governor_position')->unsigned();
            $table->integer('source_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('annotation_users', function(Blueprint $table) {
			
						
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


            $table->index(array('user_id'));

        });        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('annotation_users');
    }
}
