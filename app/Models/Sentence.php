<?php

namespace App\Models;

use App\Models\Corpus;
use App\Models\Annotation;
use Illuminate\Database\Eloquent\Model;

class Sentence extends Model
{
	protected $fillable = ['corpus_id', 'sentid', 'source_id'];
	protected $visible = ['id', 'content', 'sentid'];
	
	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasOne
	 */
	public function corpus() 
	{
		return $this->belongsTo('App\Models\Corpus');
	}	
	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasMany
	 */
	public function annotations()
	{
	  return $this->hasMany('App\Models\Annotation')->orderBy('word_position');
	}	
}
