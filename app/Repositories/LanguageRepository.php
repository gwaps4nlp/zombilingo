<?php

namespace App\Repositories;

use App\Models\Language;
use DB;
use Config;

class LanguageRepository extends BaseRepository
{

	/**
	 * Create a new LanguageRepository instance.
	 *
	 * @param  App\Models\Language $language
	 * @return void
	 */
	public function __construct(
		Language $language)
	{
		$this->model = $language;
	}

	/**
	 * Retrieve the list of languages
	 *
	 * @return Collection of Language
	 */
	public function getAll()
	{
		return $this->model->get();
	}
	/**
	 * retrieve the list of available languages
	 *
	 * @return Collection [label => id]
	 */
	public function getList()
	{
		return $this->model->pluck('label','id');
	}	

}
