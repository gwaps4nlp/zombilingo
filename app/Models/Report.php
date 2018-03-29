<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['user_id', 'annotation_id', 'relation_id', 'mode', 'message', 'user_answer'];
}
