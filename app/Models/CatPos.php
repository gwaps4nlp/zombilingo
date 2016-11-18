<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class CatPos extends Model
{
	/**
	 * Many to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongToMany
	 */
	public function pos_games()
	{
		return $this->belongsToMany('App\Models\PosGame');
	}

	public static function getBySlug($slug)
	{
		return CatPos::where('slug','=',$slug)->first();
	}

	public static function getIdBySlug($slug)
	{
		return Cache::rememberForever($slug, function() use ($slug) {
			$cat_pos = CatPos::select('id')->where('slug','=',$slug)->first();
			if($cat_pos){
				return $cat_pos->id;
			} else {
				return CatPos::select('id')->where('slug','=','UNK')->first()->id;
			}
		});
	}

	public static function getSlugById($id)
	{
		return Cache::rememberForever('catpos'.$id, function() use ($id) {
			$cat_pos = CatPos::select('slug')->where('id','=',$id)->first();
			if($cat_pos){
				return str_replace('_pos','',$cat_pos->slug);
			} else {
				return 'UNK';
			}
		});
	}

}
