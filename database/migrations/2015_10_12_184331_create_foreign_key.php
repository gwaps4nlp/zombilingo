<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('users', function(Blueprint $table) {
        //     $table->foreign('role_id')
        //                 ->references('id')
        //                 ->on('roles')
        //                 ->onDelete('restrict')
        //                 ->onUpdate('restrict');
        // });

        // Schema::table('corpuses', function(Blueprint $table) {
        //     $table->foreign('language_id')
        //                 ->references('id')
        //                 ->on('languages')
        //                 ->onDelete('restrict')
        //                 ->onUpdate('restrict');
           
        // });

        // Schema::table('sentences', function(Blueprint $table) {
        //     $table->foreign('corpus_id')
        //                 ->references('id')
        //                 ->on('corpuses')
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
        // Schema::table('users', function(Blueprint $table) {
        //     $table->dropForeign('users_role_id_foreign');
        // });
    }
}
