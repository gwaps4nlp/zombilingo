<?php

namespace App\Repositories;

use App\Models\Mwe;
use Gwaps4nlp\Core\Repositories\BaseRepository;
use DB;

class MweRepository extends BaseRepository 
{

	/**
	 * Create a new MweRepository instance.
	 *
	 * @param  App\Models\Mwe $mwe
	 * @return void
	 */
	public function __construct(
		Mwe $mwe)
	{
		$this->model = $mwe;
	}
	
	/**
	 * Get a random mwe for playing
	 *
	 * @return App\Models\Mwe
	 */
	public function getRandom()
	{
		$mwe=$this->model->orderBy(DB::raw('Rand()'))->first();
		return $mwe;

	}
}
