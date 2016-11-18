<?php

namespace App\Repositories;

use App\Models\Object;

class ObjectRepository extends BaseRepository
{

	/**
	 * Create a new ObjectRepository instance.
	 *
	 * @param  App\Models\Object $Object
	 * @return void
	 */
	public function __construct(
		Object $object)
	{
		$this->model = $object;
	}

	/**
	 * Retrieve all objects 
	 *
	 * @return Collection of Objects
	 */
	public function getAll()
	{
		return $this->model->get();
	}
	/**
	 * Get a ramdom id of object
	 *
	 * @return int
	 */
	public static function getRandomId()
	{
		$count = Object::count();
		$rang = rand(0,$count-1);
		return Object::skip($rang)->take(1)->first()->id;
	}

}
