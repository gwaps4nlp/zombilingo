<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ObjectUser extends Model
{
    protected $fillable = array('user_id', 'object_id', 'quantity');
}
