<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigAnnotator extends Model
{
    protected $fillable = ['user_id','config'];
}
