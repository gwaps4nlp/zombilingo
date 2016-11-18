<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['user_id','annotation_id','message_id','content'];
	
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }	
	
}
