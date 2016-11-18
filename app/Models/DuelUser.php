<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DuelUser extends Model
{

	protected $table = 'duel_user';

	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasOne
	 */
	public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
	


}
