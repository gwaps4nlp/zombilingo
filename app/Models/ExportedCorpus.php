<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportedCorpus extends Model
{
    protected $fillable = ['corpus_id','user_id','file'];

	
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
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasOne
	 */
	public function corpus() 
	{
		return $this->belongsTo('App\Models\Corpus');
	}    
}
