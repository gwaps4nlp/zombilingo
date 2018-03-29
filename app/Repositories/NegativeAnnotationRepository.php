<?php

namespace App\Repositories;

use App\Models\NegativeAnnotation;
use Gwaps4nlp\Repositories\BaseRepository;

class NegativeAnnotationRepository extends BaseRepository
{

	/**
	 * Create a new NegativeAnnotationRepository instance.
	 *
	 * @param  App\Models\NegativeAnnotation $level
	 * @return void
	 */
	public function __construct(
		NegativeAnnotation $negative_annotation)
	{
		$this->model = $negative_annotation;
	}

	/**
	 * Get all the negative annotations
	 *
	 * @return Collection of NegativeAnnotation
	 */
	public function getAll()
	{
		return $this->model->get();
	}
	/**
	 * Count the negative annotations group by relation.
	 *
	 * @return Collection 
	 */
	public function getByRelation()
	{
		$list = $this->model->join('relations','relations.id','=','negative_annotations.relation_id')
			->select('relations.name as relation_name', 'relations.id as relation_id')
			->selectRaw('count(*) as count')
			->groupBy('negative_annotations.relation_id')
				->orderBy('count','desc');

		return $list->get();		
	}

}
