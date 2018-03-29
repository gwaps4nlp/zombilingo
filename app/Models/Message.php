<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['user_id','discussion_id','parent_message_id','content'];
    
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function discussion()
    {
        return $this->belongsTo('App\Models\Discussion');
    }

    public function deletion_reason()
    {
        return $this->belongsTo('App\Models\DeletionReason');
    }
	
}
