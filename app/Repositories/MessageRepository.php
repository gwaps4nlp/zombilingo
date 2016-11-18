<?php

namespace App\Repositories;

use App\Models\Message;
use DB;

class MessageRepository extends BaseRepository
{

	/**
	 * Create a new MessageRepository instance.
	 *
	 * @param  App\Models\Message $message
	 * @return void
	 */
	public function __construct(
		Message $message)
	{
		$this->model = $message;
	}

	/**
	 * Get the list of messages for a given annotation
	 *
	 * @param  App\Models\Annotation $annotation	 
	 * @return Collection of Message
	 */
	public function getByAnnotation($annotation)
	{
		$query = $this->model->where('annotation_id','=',$annotation->id);

		return $query->with('user')->orderBy('created_at')->get();
	}

}
