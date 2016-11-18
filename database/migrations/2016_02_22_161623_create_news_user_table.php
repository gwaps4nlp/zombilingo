<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('news_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->boolean('seen')->default(0);
            $table->timestamps();
        });

        DB::insert('insert into news_user (news_id,user_id,seen)  
            select news.id as news_id, users.id as user_id ,1 as seen from news, users');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('news_user');
    }

}


