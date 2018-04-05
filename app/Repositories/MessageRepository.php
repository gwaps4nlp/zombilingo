<?php

namespace App\Repositories;

use App\Models\Message;
use Gwaps4nlp\Core\Repositories\BaseRepository;
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
	public function getByDiscussion($discussion)
	{
		$query = $this->model->withTrashed()->where('discussion_id',$discussion->id);

		return $query->with('user')->orderBy('created_at')->get();
	}

}
