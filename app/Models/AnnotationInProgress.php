<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Relation;

class AnnotationInProgress extends Model
{
    protected $table = 'annotation_in_progress';
	
	protected $fillable = array('user_id', 'annotation_id', 'relation_id', 'corpus_id');

	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasOne
	 */
	public function relation() 
	{
		return $this->belongsTo('App\Models\Relation');
	}
	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasOne
	 */
	public function corpus() 
	{
		return $this->belongsTo('App\Models\Corpus');
	}

}
