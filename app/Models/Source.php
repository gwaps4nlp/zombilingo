<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{

	protected $fillable = ['id','slug','description'];
	
	public $timestamps = false;

	public static function getReference(){
		return self::where('slug','reference')->first();		
	}

	public static function getUser(){
		return self::where('slug','user')->first();		
	}

	public static function getPreAnnotated(){
		return self::where('slug','preannotated')->first();		
	}

	public static function getExpert(){
		return self::where('slug','expert')->first();		
	}
	
	public static function getPreAnnotatedForEvaluation(){
		return self::where('slug','evaluation')->first();		
	}
}
