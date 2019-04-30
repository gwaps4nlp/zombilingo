<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Relation extends Model
{
	
	protected $visible = ['name','id','slug','type','level_id'];
	protected $fillable = ['name','id','slug','type','level_id'];
	
	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasMany
	 */
	public function annotations()
	{
		return $this->hasMany('App\Models\Annotation');
	}
	
	/**
	 * One to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasMany
	 */
	public function annotations_user()
	{
		return $this->hasMany('App\Models\AnnotationUser');
	}

	public static function getIdBySlug($slug){
		try {
			return Cache::rememberForever(Config::get('app.name').'-'.$slug, function() use ($slug) {
				$relation = parent::select('id')->where('slug','=',$slug)->first();
				if($relation){
					return $relation->id;
				} else {
					return parent::select('id')->where('slug','=','UNK')->first()->id;
				}
			});
		} catch (Exception $Ex){
			$relation = parent::select('id')->where('slug','=',$slug)->first();
			if($relation){
				return $relation->id;
			} else {
				return parent::select('id')->where('slug','=','UNK')->first()->id;
			}			
		}
	}

	public static function getById($id){
		try {
			return Cache::rememberForever(Config::get('app.name').'-'.'relation_'.$id, function() use ($id) {
				$relation = parent::where('id','=',$id)->first();
				if($relation){
					return $relation;
				} else {
					return parent::where('slug','=','UNK')->first();
				}
			});
		} catch (Exception $Ex){
			$relation = parent::where('id','=',$id)->first();
			if($relation){
				return $relation->id;
			} else {
				return parent::where('slug','=','UNK')->first()->id;
			}			
		}
	}

	public static function getSlugById($id){
		if(!$id) return '_';
		try {
			return Cache::rememberForever($id, function() use ($id) {
				return parent::select('slug')->where('id','=',$id)->first()->slug;
			});
		} catch (Exception $Ex){
			return Cache::rememberForever($id, function() use ($id) {
				return parent::select('slug')->where('id','=',$id)->first()->slug;
			});			
		}
	}
	
}
