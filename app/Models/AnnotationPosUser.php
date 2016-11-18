<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnotationPosUser extends Model
{
    protected $fillable = ['user_id','sentence_id','pos_game_id','word_position','cat_pos_id','confidence','is_user_tag'];

}
