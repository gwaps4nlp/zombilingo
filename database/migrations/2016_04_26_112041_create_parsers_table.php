<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parsers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',300);
            $table->timestamps();
        });
        
        Schema::create('annotation_parser', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('annotation_id');
            $table->integer('parser_id');
        });
        // Schema::table('annotation_parser', function(Blueprint $table) {
        //     $table->foreign('annotation_id')
        //                 ->references('id')
        //                 ->on('annotations')
        //                 ->onDelete('cascade')
        //                 ->onUpdate('restrict');        
        // });

        // Schema::table('annotation_parser', function(Blueprint $table) {
        //     $table->foreign('parser_id')
        //                 ->references('id')
        //                 ->on('parsers')
        //                 ->onDelete('restrict')
        //                 ->onUpdate('restrict');        
        // });
     
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('parsers');
        Schema::drop('annotation_parser');
    }
}
