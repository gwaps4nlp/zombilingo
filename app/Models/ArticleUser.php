<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleUser extends Model
{
    protected $fillable = array('user_id', 'article_id', 'quantity');
}
