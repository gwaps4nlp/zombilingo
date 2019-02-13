<?php

namespace App\Repositories;

use App\Models\Article;
use Gwaps4nlp\Core\Repositories\BaseRepository;

class ObjectRepository extends BaseRepository
{

	/**
	 * Create a new ObjectRepository instance.
	 *
	 * @param  App\Models\Article $object
	 * @return void
	 */
	public function __construct(
		Article $object)
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
		$count = Article::count();
		$rang = rand(0,$count-1);
		return Article::skip($rang)->take(1)->first()->id;
	}

}
