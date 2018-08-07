<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    //
    protected $fillable = ['id','name','slug','description','required_value','criterion'];
}
