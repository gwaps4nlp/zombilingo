<?php

namespace App\Repositories;

use App\Models\TutorialAnnotation;

class TutorialAnnotationRepository extends BaseRepository
{

	/**
	 * Create a new TutorialAnnotationRepository instance.
	 *
	 * @param  App\Models\TutorialAnnotation $annotation
	 * @return void
	 */
	public function __construct(
		TutorialAnnotation $annotation)
	{
		$this->model = $annotation;
	}

	/**
	 * Retrieve all the annotations for tutorial
	 *
	 * @return int
	 */
	public function getAll()
	{
		return $this->model->get();
	}
	/**
	 * Count the annotations for tutorials by relation
	 *
	 * @return int
	 */
	public function getByRelation()
	{
		$list = $this->model->join('relations','relations.id','=','tutorial_annotations.relation_id')
			->select('relations.name as relation_name', 'relations.id as relation_id')
			->selectRaw('count(*) as count')
			->groupBy('tutorial_annotations.relation_id')
				->orderBy('count','desc');

		return $list->get();
	}

}
