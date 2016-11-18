<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosGame extends Model
{
	/**
	 * Many to Many relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\belongToMany
	 */
	public function cat_pos()
	{
		return $this->belongsToMany('App\Models\CatPos');
	}

	public function pos1()
	{
		return $this->hasOne('App\Models\CatPos','id','pos1_id');
	}

	public function pos2()
	{
		return $this->hasOne('App\Models\CatPos','id','pos2_id');
	}
        
        public function getComplementaryPostag($postag_id)
        {
                if ($postag_id==$this->pos1_id){
                    return $this->pos2_id;
                }else{
                    return $this->pos1_id;
                }
        }
}
