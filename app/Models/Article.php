<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public function scopeOfType($query,$type){
    	return $query->where('slug', $type)->first();
    }
	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongsToMany
	 */
	public function users()
	{
		return $this->belongsToMany('App\Models\User', 'users');
	}
}
