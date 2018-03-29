<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogDB extends Model
{
	protected $table = 'logs';

    protected $fillable = ['session_id','referer','ip','url'];
  
}
