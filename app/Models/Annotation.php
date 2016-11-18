<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Relation;
use App\Models\Statistic;
use App\Models\Source;
use DB;

class Annotation extends Model
{


	protected $fillable = ['corpus_id', 'sentence_id','word_position','word','lemma','category_id','pos_id','governor_position','relation_id','projective_governor','projective_relation_id','source_id'];
	protected $guarded_insert = ['id','focus','expected_answer','created_at','updated_at','relation_type','expected_answers'];
	
	protected $visible = ['id', 'focus', 'explanation', 'sentence'];
	
    protected $appends = ['category', 'pos', 'relation_name', 'focus_position','projective_relation_name','projective_governor'];
	
	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function sentence()
	{
		return $this->belongsTo('App\Models\Sentence');
	}
	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function relation()
	{
		return $this->belongsTo('App\Models\Relation');
	}
	
	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function source()
	{
		return $this->belongsTo('App\Models\Source');
	}

	/**
	 * Many to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongToMany
	 */
	public function duels()
	{
		return $this->belongsToMany('App\Models\Duel');
	}

	/**
	 * Many to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongToMany
	 */
	public function parsers()
	{
		return $this->belongsToMany('App\Models\Parser');
	}

	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasMany
	 */
	public function messages()
	{
		return $this->hasMany('App\Models\Message');
	}
	
	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasMany
	 */
	public function statistics()
	{
		return $this->hasMany('App\Models\Statistic')->orderBy('score','desc');

	}	
	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function statisticsFiltered()
	{
		return $this->statistics()->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('statistics as s2')
                      ->whereRaw('s2.source_id = 2')
                      ->whereRaw('s2.source_id = 3')
                      ->whereRaw('s2.score >= statistics.score')
                      ->whereRaw('s2.annotation_id = statistics.annotation_id');
            });
	}
	
	public function getGuarded(){
		return $this->guarded_insert;
	}
	
    public function getRelationNameAttribute()
    {
    	$relation_ids = explode('|',$this->relation_id);
    	$results = [];
    	foreach($relation_ids as $id){
    		$relation_id = explode(':',$id,2);
    		$relation_name = Relation::getSlugById($relation_id[0]);
    		if(count($relation_id)>1) $relation_name .=":".$relation_id[1];
			$results[]=$relation_name;
    	}
        return join('|',$results);
    }

    public function getFocusPositionAttribute()
    {
    	if($this->relation->type=="trouverTete")
    		return $this->word_position;

        return $this->governor_position;
    }

    public function getProjectiveGovernorAttribute()
    {
		if(!$this->projective_governor_position)
			return "_";
        return $this->projective_governor_position;
    }
    	
    public function getProjectiveRelationNameAttribute()
    {
        return Relation::getSlugById($this->projective_relation_id);
    }

    public function getCategoryAttribute()
    {
        return CatPos::getSlugById($this->category_id);
    }

    public function getPosAttribute()
    {
        return CatPos::getSlugById($this->pos_id);
    }

    public function isUser()
    {
        return $this->source_id==Source::getUser()->id;
    }

    public static function setAnswerId(){

    	DB::update('update annotation_users, annotations 
			set answer_id = annotations.id
			where 1=1 
			and annotation_users.sentence_id = annotations.sentence_id
			and annotation_users.word_position = annotations.word_position
			and annotation_users.governor_position = annotations.governor_position
			and annotation_users.relation_id = annotations.relation_id
			and annotations.source_id in (2,3)
			and answer_id is null');
    }

    public static function computeScore($score_init=5, $weight_level=1, $weight_confidence_user=0)
    {
    	self::setAnswerId();
        DB::statement('delete from annotation_users where user_id=0');
        DB::insert('insert into annotation_users 
			( `user_id`,
			  `level_id`,
			  `annotation_id`,
			  `sentence_id`,
			  `relation_id`,
			  `word_position`,
			  `governor_position`,
			  `source_id`) select 0,?,annotations.id,annotations.sentence_id,annotations.relation_id,annotations.word_position,annotations.governor_position,3 
			  from annotations where source_id = 3 and not exists (select 1 from annotation_parser 
			    where annotation_parser.annotation_id=annotations.id)', [$score_init]);
        DB::insert('insert into annotation_users 
			( `user_id`,
			  `level_id`,
			  `annotation_id`,
			  `sentence_id`,
			  `relation_id`,
			  `word_position`,
			  `governor_position`,
			  `source_id`) select 0,?,annotations.id,annotations.sentence_id,annotations.relation_id,annotations.word_position,annotations.governor_position,3 
			  from annotations, annotation_parser where source_id = 3 and annotation_parser.annotation_id=annotations.id', [$score_init]);
        DB::statement('drop table if exists confidence_users');
        DB::statement('create table confidence_users  
			select totaux.relation_id, totaux.user_id, username, totaux.total as total, ifnull(success_annotations.total,0)/totaux.total as success_rate, ifnull(error_annotations.total,0)/totaux.total as error_rate from 
			(SELECT count(*) as total, users.id user_id,users.username, annotations.relation_id  FROM annotation_users, annotations, users 
			WHERE 
			users.id=annotation_users.user_id 
			and annotation_users.annotation_id = annotations.id 
			and annotations.source_id =1
			group by users.id, annotations.relation_id ) as totaux 
			left join (
			    SELECT count(*) as total, users.id as user_id, annotations.relation_id FROM users, annotation_users, annotations WHERE 
			    users.id=annotation_users.user_id 
			    and annotation_users.annotation_id = annotations.id
			    and annotations.source_id =1 
			    and annotation_users.source_id =1 
			    group by users.id, annotations.relation_id 
			) as success_annotations on totaux.user_id=success_annotations.user_id and totaux.relation_id = success_annotations.relation_id
			left join (
			    SELECT count(*) as total, users.id as user_id, annotations.relation_id FROM users, annotation_users, annotations WHERE 
			    users.id=annotation_users.user_id 
			    and annotation_users.annotation_id = annotations.id
			    and annotations.source_id =1 
			    and annotation_users.source_id !=1 
			    group by users.id, annotations.relation_id 
			) as error_annotations on totaux.user_id=error_annotations.user_id and totaux.relation_id = error_annotations.relation_id
			order by user_id asc');
        DB::statement('ALTER TABLE `confidence_users` ADD INDEX(`user_id`,`relation_id`)');
        DB::statement('drop table if exists annotations_temp');
        DB::insert('create table annotations_temp 
			SELECT ifnull(sum(
			  IF(annotation_users.word_position=99999 or annotation_users.governor_position=99999,-1,1)
			  *(?*annotation_users.level_id+?*ifnull(confidence_users.success_rate,0))),0) AS computed_score,count(annotation_users.id), score, annotations.source_id , annotations.id annotation_id 
			from annotations LEFT JOIN annotation_users 
			on (annotations.sentence_id = annotation_users.sentence_id 
			  and annotations.relation_id = annotation_users.relation_id 
			  and (annotations.word_position = annotation_users.word_position or annotation_users.word_position=99999)
			  and (annotations.governor_position = annotation_users.governor_position or annotation_users.governor_position=99999) )
			LEFT JOIN confidence_users on annotation_users.user_id=confidence_users.user_id and annotation_users.relation_id=confidence_users.relation_id 
			where 1=1 
			and annotations.source_id = 3 
			group by annotations.sentence_id,annotations.word_position,annotations.governor_position,annotations.relation_id', [$weight_level, $weight_confidence_user]);
        DB::insert('insert into annotations_temp 
				SELECT ifnull(sum(
				  -1 
				  *(?*annotation_users.level_id+?*ifnull(confidence_users.success_rate,0))),0) AS computed_score,count(annotation_users.id), score, annotations.source_id , annotations.id annotation_id 
				from annotations , annotation_users 
				LEFT JOIN confidence_users on annotation_users.user_id=confidence_users.user_id and annotation_users.relation_id=confidence_users.relation_id 
				where 1=1 
				and annotations.id = annotation_users.annotation_id 
				and annotation_users.annotation_id != annotation_users.answer_id
				and annotations.source_id = 3 
				group by annotations.id', [$weight_level, $weight_confidence_user]);
        DB::insert('insert into annotations_temp 
			SELECT sum(
			?*annotation_users.level_id+?*ifnull(confidence_users.success_rate,0)) AS computed_score,count(annotation_users.id), score, annotations.source_id , annotations.id annotation_id 
			from annotations LEFT JOIN annotation_users 
			on (annotations.sentence_id = annotation_users.sentence_id 
			  and annotations.relation_id = annotation_users.relation_id 
			  and annotations.word_position = annotation_users.word_position 
			  and annotations.governor_position = annotation_users.governor_position ) 
			LEFT JOIN confidence_users 
			on (annotation_users.user_id=confidence_users.user_id 
			    and annotation_users.relation_id=confidence_users.relation_id ) 
			where 1=1 
			and annotations.source_id = 2 
			and annotation_users.word_position!=99999
			and annotation_users.governor_position!=99999
			group by annotations.sentence_id,annotations.word_position,annotations.governor_position,annotations.relation_id', [$weight_level, $weight_confidence_user]);
        DB::statement('ALTER TABLE `annotations_temp` ADD INDEX(`annotation_id`)');
        DB::update('update annotations set custom_score=(select sum(annotations_temp.computed_score) from annotations_temp
  where annotations.id = annotations_temp.annotation_id)');
        self::setBestAnnotations();
        DB::statement('drop table if exists annotations_temp');
    }

    public static function computeScoreAtDate($corpus, $date=null, $score_init=5, $weight_level=1, $weight_confidence_user=0)
    {
    	if(!$date)
    		$date = date("Y-m-d");
    	self::setAnswerId();
        DB::statement('delete from annotation_users where user_id=0');

        DB::statement('drop table if exists confidence_users');
        DB::statement('create table confidence_users  
			select totaux.relation_id, totaux.user_id, username, totaux.total as total, ifnull(success_annotations.total,0)/totaux.total as success_rate, ifnull(error_annotations.total,0)/totaux.total as error_rate from 
			(SELECT count(*) as total, users.id user_id,users.username, annotations.relation_id  FROM annotation_users, annotations, users 
			WHERE 
			users.id=annotation_users.user_id 
			and annotation_users.annotation_id = annotations.id 
			and annotations.source_id =1 
			group by users.id, annotations.relation_id ) as totaux 
			left join (
			    SELECT count(*) as total, users.id as user_id, annotations.relation_id FROM users, annotation_users, annotations WHERE 
			    users.id=annotation_users.user_id 
			    and annotation_users.annotation_id = annotations.id
			    and annotations.source_id =1 
			    and annotation_users.source_id =1 
			    group by users.id, annotations.relation_id 
			) as success_annotations on totaux.user_id=success_annotations.user_id and totaux.relation_id = success_annotations.relation_id
			left join (
			    SELECT count(*) as total, users.id as user_id, annotations.relation_id FROM users, annotation_users, annotations WHERE 
			    users.id=annotation_users.user_id 
			    and annotation_users.annotation_id = annotations.id
			    and annotations.source_id =1 
			    and annotation_users.source_id !=1 
			    group by users.id, annotations.relation_id 
			) as error_annotations on totaux.user_id=error_annotations.user_id and totaux.relation_id = error_annotations.relation_id
			order by user_id asc');
        
        DB::statement('ALTER TABLE `confidence_users` ADD INDEX(`user_id`,`relation_id`)');
        
        DB::statement('drop table if exists annotations_temp');

 		/*
 		If answer = pre-annotated annotation : we add the user's level to the score of the annotation 
		If answer = refused (99999) : we substract the user's level to the score of the annotation 
 		 */
        DB::insert('create table annotations_temp 
			SELECT ifnull(sum(
			  IF(annotation_users.word_position=99999 or annotation_users.governor_position=99999,-1,1)
			  *(?*annotation_users.level_id+?*ifnull(confidence_users.success_rate,0))),0) AS computed_score,count(annotation_users.id), score, annotations.source_id , annotations.id annotation_id 
			from annotations LEFT JOIN annotation_users 
			on (annotations.sentence_id = annotation_users.sentence_id 
			  and annotations.relation_id = annotation_users.relation_id 
			  and (annotations.word_position = annotation_users.word_position or annotation_users.word_position=99999)
			  and (annotations.governor_position = annotation_users.governor_position or annotation_users.governor_position=99999) 
			  and annotation_users.created_at < DATE_ADD(?,INTERVAL 1 DAY) )
			LEFT JOIN confidence_users on annotation_users.user_id=confidence_users.user_id and annotation_users.relation_id=confidence_users.relation_id 
			where 1=1 
			and annotations.source_id in (3,5) 
			group by annotations.sentence_id,annotations.word_position,annotations.governor_position,annotations.relation_id', [$weight_level, $weight_confidence_user, $date]);
 		/*
 		If answer != pre-annotated annotation : we substract the user's level to the score of the annotation 
 		 */
        DB::insert('insert into annotations_temp 
				SELECT ifnull(sum(
				  -1 
				  *(?*annotation_users.level_id+?*ifnull(confidence_users.success_rate,0))),0) AS computed_score,count(annotation_users.id), score, annotations.source_id , annotations.id annotation_id 
				from annotations , annotation_users 
				LEFT JOIN confidence_users on annotation_users.user_id=confidence_users.user_id and annotation_users.relation_id=confidence_users.relation_id 
				where 1=1 
				and annotations.id = annotation_users.annotation_id 
				and annotation_users.annotation_id != annotation_users.answer_id
				and annotations.source_id in (3,5)
				and annotation_users.created_at < DATE_ADD(?,INTERVAL 1 DAY) 
				group by annotations.id', [$weight_level, $weight_confidence_user, $date]);
        
        DB::insert('insert into annotations_temp 
			SELECT sum(
			?*annotation_users.level_id+?*ifnull(confidence_users.success_rate,0)) AS computed_score,count(annotation_users.id), score, annotations.source_id , annotations.id annotation_id 
			from annotations LEFT JOIN annotation_users 
			on (annotations.sentence_id = annotation_users.sentence_id 
			  and annotations.relation_id = annotation_users.relation_id 
			  and annotations.word_position = annotation_users.word_position 
			  and annotations.governor_position = annotation_users.governor_position ) 
			LEFT JOIN confidence_users 
			on (annotation_users.user_id=confidence_users.user_id 
			    and annotation_users.relation_id=confidence_users.relation_id ) 
			where 1=1 
			and annotations.source_id = 2 
			and annotation_users.word_position!=99999
			and annotation_users.governor_position!=99999
			and annotation_users.created_at < DATE_ADD(?,INTERVAL 1 DAY) 
			group by annotations.sentence_id,annotations.word_position,annotations.governor_position,annotations.relation_id', [$weight_level, $weight_confidence_user, $date]);
        
        // parsed annotations with a single parser
        DB::insert('insert into annotations_temp 
			  select ?,1, score, annotations.source_id,annotations.id  
			  from annotations where source_id = 3 and not exists (select 1 from annotation_parser 
			    where annotation_parser.annotation_id=annotations.id)', [$score_init]);
        // parsed annotations with multiple parser
        DB::insert('insert into annotations_temp 
			  select ?,1 , score, annotations.source_id,annotations.id 
			  from annotations, annotation_parser where source_id = 3 and annotation_parser.annotation_id=annotations.id', [$score_init]);        
        
        DB::statement('ALTER TABLE `annotations_temp` ADD INDEX(`annotation_id`)');
        
        DB::update('update annotations set custom_score=(select sum(annotations_temp.computed_score) from annotations_temp
            where annotations.id = annotations_temp.annotation_id)');
        self::setBestAnnotationsByDate($date);
        DB::statement('drop table if exists annotations_temp');
    }
 
    public static function setBestAnnotations(){
        DB::statement('DROP TABLE if exists stats_temp');
    	DB::insert('create table stats_temp
			SELECT  a2.id as annotation_id, max(a2.custom_score) as score_max  FROM `annotations` AS `a2` 
			WHERE  
			  a2.source_id in  (2,3,5)
			  and not exists (select 1 from `annotations` as `a1` where a2.sentence_id=a1.sentence_id
			    and a2.word_position = a1.word_position
			    and a2.id != a1.id
			  and a1.corpus_id = a2.corpus_id 
			    and a1.source_id in (2,3,5) 
			    and a1.custom_score >= a2.custom_score)
			  group by a2.sentence_id, a2.word_position');
		DB::statement('ALTER TABLE `stats_temp` ADD INDEX(`annotation_id`)');
		DB::update('update annotations set best = 0');
		DB::update('update annotations set best = 1
			WHERE exists (select 1 from stats_temp where annotations.id=stats_temp.annotation_id)');
    }
    public static function setBestAnnotationsByDate($date){
        DB::statement('DROP TABLE if exists stats_temp');
    	DB::insert('create table stats_temp
			SELECT  a2.id as annotation_id, max(a2.custom_score) as score_max  FROM `annotations` AS `a2` 
			WHERE  
			  a2.source_id in (2,3,5)
			  and not exists (select 1 from `annotations` as `a1` where a2.sentence_id=a1.sentence_id
			    and a2.word_position = a1.word_position
			    and a2.id != a1.id
			  and a1.corpus_id = a2.corpus_id     
			    and a1.source_id in (2,3,5)
			    and a1.custom_score >= a2.custom_score)
			  group by a2.sentence_id, a2.word_position');
		DB::statement('ALTER TABLE `stats_temp` ADD INDEX(`annotation_id`)');
		DB::insert('insert ignore into score_annotations select id, custom_score, ?, 0 from annotations where source_id in (2,3,5) on duplicate key update score_annotations.custom_score = annotations.custom_score',[$date]);
		DB::update('update score_annotations set best = 0 where date = ?',[$date]);
		DB::update('update score_annotations set best = 1 
			WHERE date = ? and exists (select 1 from stats_temp where score_annotations.annotation_id=stats_temp.annotation_id)',[$date]);

    }
    public static function getConfidenceByUser(){

    	$sql = 'select totaux.user_id, username, totaux.total as total, totaux_annotations.total as total_annotations, ifnull(success_annotations.total,0)/totaux.total as success_rate, ifnull(error_annotations.total,0)/totaux.total as error_rate from 
			(SELECT count(*) as total, users.id user_id,users.username FROM annotation_users, annotations, users 
			WHERE 
			users.id=annotation_users.user_id 
			and annotation_users.annotation_id = annotations.id 
			and annotations.source_id =1
			group by users.id) as totaux 
			left join (
			    SELECT count(*) as total, users.id as user_id FROM users, annotation_users, annotations WHERE 
			    users.id=annotation_users.user_id 
			    and annotation_users.annotation_id = annotations.id
			    and annotations.source_id =1 
			    and annotation_users.source_id =1 
			    group by users.id
			) as success_annotations on totaux.user_id=success_annotations.user_id
			left join (
			    SELECT count(*) as total, users.id as user_id FROM users, annotation_users WHERE 
			    users.id=annotation_users.user_id 
			    group by users.id
			) as totaux_annotations on totaux_annotations.user_id=totaux.user_id
			left join (
			    SELECT count(*) as total, users.id as user_id FROM users, annotation_users, annotations WHERE 
			    users.id=annotation_users.user_id 
			    and annotation_users.annotation_id = annotations.id
			    and annotations.source_id =1 
			    and annotation_users.source_id !=1 
			    group by users.id
			) as error_annotations on totaux.user_id=error_annotations.user_id
			order by user_id asc';
    	return DB::select($sql);

    }
}
