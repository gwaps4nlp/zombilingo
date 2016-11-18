<?php

namespace App\Repositories;

use App\Models\News;
use Config;

class NewsRepository extends BaseRepository
{

	/**
	 * Create a new NewsRepository instance.
	 *
	 * @param  App\Models\News $news
	 * @return void
	 */
	public function __construct(
		News $news)
	{
		$this->model = $news;
	}

	/**
	 * Retrieve all the news
	 *
	 * @return Collection of News
	 */
	public function getAll()
	{
		return $this->model->orderBy('created_at','desc')->get();
	}

	/**
	 * Create a new news in database
	 *
	 * @param array $inputs
	 * @return Collection
	 */
	public function create($inputs)
	{
		return $this->model->create($inputs);
	}

	/**
	 * Count the news not seen for a given user
	 * 
	 * @param App\Models\User $user
	 * @return int
	 */
	public function countNotSeen($user)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
           	$query->where('seen', '=', 0);
        });

		return $query->count();
	}
	
	/**
	 * Get the news not seen for a given user
	 * 
	 * @param App\Models\User $user
	 * @return Collection of News
	 */
	public function getNotSeen($user)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
           	$query->where('seen', '=', 0);
        });

		return $query->get();
	}

}
