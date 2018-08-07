<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestUser extends Model
{
    //
    protected $fillable = ['id','quest_id','user_id','score','required_value','quest_finished'];

}
