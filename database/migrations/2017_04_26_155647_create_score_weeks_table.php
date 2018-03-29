<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreWeeksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_weeks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('yearweek')->unsigned()->index();
            $table->integer('points')->default(0)->unsigned();
            $table->integer('number_annotations')->default(0)->unsigned();
            $table->timestamps();
            $table->unique(['user_id','yearweek']);
        });

        DB::update("insert into score_weeks (user_id,yearweek,points)
            select user_id , YEARWEEK(created_at), sum(points) as points from 
            scores group by user_id, YEARWEEK(created_at) ");

        DB::update("create table tmp_score_weeks 
        select `users`.`id` as `user_id`,YEARWEEK(annotation_users.created_at) as yearweek, count(*) as number_annotations from `annotation_users` inner join `users` on `users`.`id` = `annotation_users`.`user_id` inner join `annotations` on `annotations`.`id` = `annotation_users`.`annotation_id` where `users`.`deleted_at` is null group by `user_id`, YEARWEEK(annotation_users.created_at)");

        DB::update("update score_weeks, tmp_score_weeks set score_weeks.number_annotations = tmp_score_weeks.number_annotations where 
            score_weeks.user_id = tmp_score_weeks.user_id and score_weeks.yearweek = tmp_score_weeks.yearweek ");

        DB::update("drop table tmp_score_weeks");
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('score_weeks');
    }
}
