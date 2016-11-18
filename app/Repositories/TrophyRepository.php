<?php

namespace App\Repositories;

use App\Models\Trophy;

class TrophyRepository extends BaseRepository
{

	/**
	 * Create a new TrophyRepository instance.
	 *
	 * @param  App\Models\Trophy $trophy
	 * @return void
	 */
	public function __construct(
		Trophy $trophy)
	{
		$this->model = $trophy;
	}

	/**
	 * Get all the trophies
	 *
	 * @return Collection of Trophy
	 */
	public function getAll()
	{
		return $this->model->get();
	}

}
