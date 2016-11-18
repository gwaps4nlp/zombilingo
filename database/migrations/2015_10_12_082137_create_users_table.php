<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 30)->unique();
            $table->string('email')->nullable();
            $table->string('password', 60);
            $table->integer('role_id')->unsigned();
            $table->tinyInteger('level_id')->unsigned()->default(1);
            $table->integer('score')->unsigned()->default(0);
            $table->integer('money')->unsigned()->default(0);
            $table->integer('won')->unsigned()->default(0);
            $table->integer('perfect')->unsigned()->default(0);
            $table->integer('last_mwe')->unsigned()->default(0);
            $table->integer('end_mwe')->unsigned()->default(0);
            $table->integer('number_mwes')->default(0);
            $table->integer('number_objects')->default(0);
            $table->boolean('connected')->default(false);
            $table->integer('email_frequency_id')->unsigned()->default(3);
            $table->boolean('email_duel')->default(1);
            $table->timestamp('last_action_at')->nullable();
            $table->string('session_id',100)->default("");
            $table->timestamps();
            $table->rememberToken();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
