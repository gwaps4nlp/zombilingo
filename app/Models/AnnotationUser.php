<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnotationUser extends Model
{

	protected $fillable = ['annotation_id','relation_id','sentence_id','user_id','source_id'];
	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasOne
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}

	/**
	 * 
	 *
	 * @return int
	 */
	public static function countByCorpus($corpus)
	{
		$corpora_ids = array_merge([$corpus->id], $corpus->subcorpora->pluck('id')->toArray());
		$count = AnnotationUser::join('annotations','annotations.id','=','annotation_users.annotation_id')->whereIn('corpus_id',$corpora_ids);
		return $count->count();
	}

}
