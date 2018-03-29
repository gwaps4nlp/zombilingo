<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegativeAnnotation extends Model
{
    protected $fillable = ['relation_id','annotation_id','explanation'];
}
