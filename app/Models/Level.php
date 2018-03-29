<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasMany
	 */
	public function relations()
	{
		return $this->hasMany('App\Models\Relation');
	}

	public function getNext()
	{
		return $this->where('id','>',$this->id)->orderBy('id', 'asc')->first();
	}
	
}
