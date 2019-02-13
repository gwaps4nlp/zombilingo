<?php

namespace App\Repositories;

use App\Models\Article;
use Gwaps4nlp\Core\Repositories\BaseRepository;

class ObjectRepository extends BaseRepository
{

	/**
	 * Create a new ArticleRepository instance.
	 *
	 * @param  App\Models\Article $article
	 * @return void
	 */
	public function __construct(
		Article $article)
	{
		$this->model = $article;
	}

	/**
	 * Retrieve all articles
	 *
	 * @return Collection of Articles
	 */
	public function getAll()
	{
		return $this->model->get();
	}
	/**
	 * Get a ramdom id of article
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
