<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnotationUserAnnotator extends Annotation
{

	protected $fillable = ['user_id','sentence_id','corpus_id','word_position','governor_position','relation_id','source_id','category_id','pos_id','features','lemma','word'];
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
