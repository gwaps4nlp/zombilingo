<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    protected $fillable = ['corpus_id','slug','name','description','image','type_score','start_date','end_date'];

    protected $dates = ['start_date','end_date'];

	/**
	 * belongsTo relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function corpus()
	{
		return $this->belongsTo('App\Models\Corpus');
	}

	/**
	 * belongsTo relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongsTo
	 */
	public function count_annotations_produced()
	{
		$annotations_done = \App\Models\AnnotationUser::countByCorpus($this->corpus);
		return $annotations_done;
	}    

}
