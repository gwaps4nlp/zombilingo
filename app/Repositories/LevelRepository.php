<?php

namespace App\Repositories;

use App\Models\Level;
use Gwaps4nlp\Repositories\BaseRepository;

class LevelRepository extends BaseRepository
{

	/**
	 * Create a new LevelRepository instance.
	 *
	 * @param  App\Models\Level $level
	 * @return void
	 */
	public function __construct(
		Level $level)
	{
		$this->model = $level;
	}

	/**
	 * Get all the levels
	 *
	 * @return Collection of Level
	 */
	public function getAll()
	{
		return $this->model->get();
	}


}
