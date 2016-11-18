<?php

namespace App\Repositories;

use App\Models\License;
use DB;
use Config;

class LicenseRepository extends BaseRepository
{

	/**
	 * Create a new LicenseRepository instance.
	 *
	 * @param  App\Models\License $license
	 * @return void
	 */
	public function __construct(
		License $license)
	{
		$this->model = $license;
	}

	/**
	 * Retrieve all Licenses
	 *
	 * @return Collection of License
	 */
	public function getAll()
	{
		return $this->model->get();
	}
	/**
	 * Get a list of the Licenses
	 *
	 * @return Collection [label=>id]
	 */
	public function getList()
	{
		return $this->model->orderBy('label')->lists('label','id');
	}	

}
