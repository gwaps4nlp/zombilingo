<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnotationUser extends Model
{
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
	public static function countByCorpus($corpus_id)
	{
		$count = AnnotationUser::join('annotations','annotations.id','=','annotation_users.annotation_id')->where('annotation_users.user_id','!=',0);
		if($corpus_id)
			$count->where('corpus_id','=',$corpus_id);
		return $count->count();
	}

}
