<?php

namespace App\Repositories;

use App\Models\Challenge;
use DB;
use Gwaps4nlp\Core\Repositories\BaseRepository;

class ChallengeRepository extends BaseRepository
{

	/**
	 * Create a new ChallengeRepository instance.
	 *
	 * @param  App\Models\Challenge $challenge
	 * @return void
	 */
	public function __construct(
		Challenge $challenge)
	{
		$this->model = $challenge;
	}

	/**
	 * Get all the challenges.
	 *
	 * @return Challenge Collection
	 */
	public function getAll()
	{
		return $this->model->get();
	}

	/**
	 * retrieve the list of challenges
	 *
	 * @return Illuminate\Support\Collection [name => id]
	 */
	public function getList()
	{
		return $this->model->pluck('name','id');
	}

	/**
	 * retrieve the list of challenges
	 *
	 * @return Illuminate\Support\Collection [name => id]
	 */
	public function getListWithScore()
	{
		return $this->model->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('score_challenges')
                      ->whereRaw('score_challenges.challenge_id = challenges.id');
            })->pluck('name','id');
	}

	/**
	 * Get ongoing challenge
	 *
	 * @return App\Models\Challenge
	 */
    public function getOngoing(){
    	return $this->model->whereRaw("start_date <= NOW()")->whereRaw("end_date >= NOW()")->first();
    }

	/**
    * create a challenge
    * 
    * @param  array  $inputs
    * @return Challenge
    */
	public function create($inputs)
	{
		return $this->model->create($inputs);
	}
}
