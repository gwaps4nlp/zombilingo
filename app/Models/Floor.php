<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $fillable=['id','trophy_id','score_to_reach','floor','image'];
}
