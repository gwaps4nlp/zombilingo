<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreMonthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_months', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('yearmonth')->unsigned()->index();
            $table->integer('points')->default(0)->unsigned();
            $table->integer('number_annotations')->default(0)->unsigned();            
            $table->timestamps();
            $table->unique(['user_id', 'yearmonth']);
        });

        DB::update("insert into score_months (user_id,yearmonth,points)
            select user_id , DATE_FORMAT(created_at , '%Y%m'), sum(points) as points from 
            scores group by user_id, DATE_FORMAT(created_at , '%Y%m')");

        DB::update("create table tmp_score_months 
        select `users`.`id` as `user_id`,DATE_FORMAT(annotation_users.created_at,'%Y%m') as yearmonth, count(*) as number_annotations from `annotation_users` inner join `users` on `users`.`id` = `annotation_users`.`user_id` inner join `annotations` on `annotations`.`id` = `annotation_users`.`annotation_id` where `users`.`deleted_at` is null group by `user_id`, DATE_FORMAT(annotation_users.created_at,'%Y%m')");

        DB::update("update score_months, tmp_score_months set score_months.number_annotations = tmp_score_months.number_annotations where 
            score_months.user_id = tmp_score_months.user_id and score_months.yearmonth = tmp_score_months.yearmonth ");
        
        DB::update("drop table tmp_score_months");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('score_months');
    }
}
