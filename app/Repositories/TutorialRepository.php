<?php

namespace App\Repositories;

use App\Models\Tutorial;

class TutorialRepository extends BaseRepository
{

	/**
	 * Create a new TutorialRepository instance.
	 *
	 * @param  App\Models\Tutorial $tutorial
	 * @return void
	 */
	public function __construct(
		Tutorial $tutorial)
	{
		$this->model = $tutorial;
	}

	/**
	 * Increment the number of good answers in the tutorials for a given user
	 *
	 * @param int $user_id
	 * @param int $relation_id id of the relation played
	 * @return void
	 */
	public function saveCorrectAnswer($user_id, $relation_id)
	{
		if(!$tutorial = $this->model
			->where('user_id',$user_id)
			->where('relation_id',$relation_id)
			->first()){
			$id = $this->model->insertGetId(['user_id'=>$user_id, 'relation_id'=>$relation_id]);
			$tutorial = $this->model->where('id',$id)->first();
		}	
		$tutorial->increment('number_success');
	}
	

	/**
	 * Count the number of tutorials done by a user.
	 *
	 * @param App\Models\User $user	 
	 * @return int
	 */
	public function countDone($user)
	{
		return $this->model
			->where('user_id',$user->id)
			->where('number_success','>=',10)
			->count();
	}	

}
