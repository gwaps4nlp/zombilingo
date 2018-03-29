<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Duel extends Model
{
    protected $fillable = ['relation_id','level_id','nb_turns'];
	
	/**
	 * Many to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongToMany
	 */
	public function users()
	{
		return $this->belongsToMany('App\Models\User')->withPivot('score', 'turn', 'rank', 'final_score', 'seen', 'result');
	}

	/**
	 * 
	 *
	 * 
	 */
	public function user($user)
	{
		return $this->users()->where('user_id',$user->id)->first();
	}
	/**
	 * 
	 *
	 * 
	 */
	public function challenger($user)
	{
		return $this->users()->where('user_id','!=',$user->id)->first();
	}

	/**
	 * Many to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongToMany
	 */
	public function annotations()
	{
		return $this->belongsToMany('App\Models\Annotation','annotation_duel');
	}

	/**
	 * Many to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongToMany
	 */
	public function annotation_users()
	{
		return $this->belongsToMany('App\Models\AnnotationUser','annotation_user_duel')->withPivot('annotation_id','answer');
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
	public function isOver()
	{
		if(count($this->users)<$this->nb_users)
			return false;
		foreach($this->users as $user){
			if ($user->pivot->turn<$this->nb_turns)
				return false;
		}
		return true;
	}

	/**
	 * Harmonize the scores of a duel
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function harmonizeScores()
	{
		DB::update('update annotation_user_duel as a1 inner join (select max(score) score_max, annotation_id, answer,duel_id from annotation_user_duel where annotation_user_duel.duel_id = :duel_id group by annotation_id,answer) a2
			on a1.annotation_id=a2.annotation_id and a1.duel_id = a2.duel_id and a1.answer = a2.answer 
			set a1.score = a2.score_max',['duel_id'=>$this->id]);
	}

	/**
	 * Harmonize the scores of a duel
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function computeScores()
	{
		DB::update('update duel_user inner join (select sum(score) score_sum, user_id, duel_id from annotation_user_duel where annotation_user_duel.duel_id = :duel_id group by user_id) aud
			on duel_user.user_id=aud.user_id and duel_user.duel_id = aud.duel_id 
			set duel_user.score = aud.score_sum',['duel_id'=>$this->id]);
	}


}