<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'friend_id', 'accepted'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function friend()
    {
        return $this->belongsTo('App\Models\User','friend_id');
    }        
}
