<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorialAnnotation extends Model
{
    protected $fillable = ['relation_id','level','annotation_id','explanation','type'];
}
