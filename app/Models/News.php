<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */	
    protected $dates = ['created_at', 'updated_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content','language_id','send_by_email','scheduled_at','title'];

	/**
	 * One to One relation
	 *
	 * @return Illuminate\Database\Eloquent\Relations\hasOne
	 */
	public function language()
	{
	  return $this->belongsTo('App\Models\Language');
	}

    /**
     * Many to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\belongToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User')->withPivot('seen');
    }

    /**
     * Many to Many relation
     *
     * @return Illuminate\Database\Eloquent\Relations\belongToMany
     */
    public function user($user)
    {
        return $this->users()->where('user_id',$user->id)->first();
    }	    
}