<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $fillable = ['user_id','corpus_id','created_at','relation_id'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }	
}
