<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class ConstantGame extends Model
{
	protected $fillable = ['key','value','description'];
	
	public static function get($key){
		try {
			return Cache::rememberForever($key, function() use ($key) {
				return parent::select('value')->where('key','=',$key)->first()->value;
			});
		} catch (Exception $Ex){
			return parent::select('value')->where('key','=',$key)->first()->value;
		}
	}
}
