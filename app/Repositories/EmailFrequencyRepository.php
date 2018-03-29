<?php

namespace App\Repositories;

use App\Models\EmailFrequency;
use Gwaps4nlp\Repositories\BaseRepository;

class EmailFrequencyRepository extends BaseRepository
{

	/**
	 * Create a new EmailFrequencyRepository instance.
	 *
	 * @param  App\Models\EmailFrequency $email_frequency
	 * @return void
	 */
	public function __construct(
		EmailFrequency $email_frequency)
	{
		$this->model = $email_frequency;
	}
	
	/**
	 * retrieve all EmailFrequency
	 *
	 * @return Collection of EmailFrequency
	 */
	public function getAll()
	{
		$list = $this->model->get();
		return $list;
	}

}
