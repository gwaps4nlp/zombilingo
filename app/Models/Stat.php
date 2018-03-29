<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stat extends Model
{
    protected $fillable = array('user_id', 'relation_id');

    public function scopeOfRelation($query,$relation){
    	return $query->where('relation_id', $relation->id);
    }


}
