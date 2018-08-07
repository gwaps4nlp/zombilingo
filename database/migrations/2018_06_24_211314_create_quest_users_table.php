<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quest_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('quest_id');
            $table->integer('user_id');
            $table->integer('score');
            $table->integer('required_value');
            $table->string('key',100);
            $table->boolean('quest_finished')->default(0);
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
        Schema::dropIfExists('quest_users');
    }
}
