<?php

namespace App\Repositories;

use App\Models\Message;
use App\Models\Discussion;
use Gwaps4nlp\Repositories\BaseRepository;
use DB;

class DiscussionRepository extends BaseRepository
{

	/**
	 * Create a new DiscussionRepository instance.
	 *
	 * @param  App\Models\Discussion $discussion
	 * @return void
	 */
	public function __construct(
		Discussion $discussion)
	{
		$this->model = $discussion;
	}

	/**
	 * Get the list of messages for a given annotation
	 *
	 * @param  App\Models\Annotation $annotation	 
	 * @return Collection of Message
	 */
	public function getByAnnotation($annotation)
	{
		$query = $this->model->where('entity_id','=',$annotation->id)->where('entity_type','=',"App\\Models\\Annotation");

		return $query->first();
	}
	/**
	 * Get the list of messages for a given annotation
	 *
	 * @param  App\Models\Annotation $annotation	 
	 * @return Collection of Message
	 */
	public function getByEntiy($entity)
	{
		$query = $this->model->where('entity_id','=',$entity->id)->where('entity_type','=', get_class($entity) );

		return $query->first();
	}

}
