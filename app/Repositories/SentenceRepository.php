<?php

namespace App\Repositories;

use App\Models\Sentence, App\Models\User, App\Models\Source;
use DB;

class SentenceRepository extends BaseRepository
{

	/**
	 * Create a new SentenceRepository instance.
	 *
	 * @param  App\Models\Sentence $sentence
	 * @return void
	 */
	public function __construct(
		Sentence $sentence)
	{
		$this->model = $sentence;
	}

	/**
	 * Get a random reference sentence
	 *
	 * @param \App\Models\Relation $relation
	 * @param \App\Models\User $user
	 * @return App\Models\Sentence
	 */
	public function getRandomReference($relation, $user)
	{
		$source = Source::where('slug','reference')->first();
		return $this->getRandom($relation, $user, $source);
	}

	/**
	 * Get a random pre-annotated sentence
	 *
	 * @param \App\Models\Relation $relation
	 * @param \App\Models\User $user
	 * @return App\Models\Sentence
	 */
	public function getRandomPreAnnotated($relation, $user)
	{
		$source = Source::where('slug','preannotated')->first();
		return $this->getRandom($relation, $user, $source);
	}
	
	/**
	 * Get a random sentence
	 *
	 * @param \App\Models\Relation $relation
	 * @param \App\Models\User $user
	 * @param \App\Models\Source $source
	 * @return App\Models\Sentence
	 */
	public function getRandom($relation, $user, $source)
	{
		$query=$this->model->select('sentences.id as id','sentences.content as content',
				'annotations.word_position as word_position',
				'annotations.governor_position as governor_position',
				'annotations.id as annotation_id')
			->join('annotations',function($join){
				$join->on('sentences.id','=','annotations.sentence_id')
					->on('sentences.source_id','=','annotations.source_id');				
			})
			->leftJoin('annotation_users',function($join) use ($user,$relation) {
				$join->on('sentences.id','=','annotation_users.sentence_id')
					->on('annotations.relation_id','=','annotation_users.relation_id')
					->on('annotation_users.user_id','=',DB::raw($user->id));
					if($relation->type=='trouverTete')
						$join->on('annotations.word_position','=','annotation_users.word_position');
					else 
						$join->on('annotations.governor_position','=','annotation_users.governor_position');
							})			
			->where('annotations.relation_id','=',$relation->id)		
			->whereRaw('annotation_users.id is null')
			->orderBy(DB::raw('Rand()'));
		if($source)	
			$query->where('sentences.source_id', '=', $source->id);
		if($relation->type=='trouverTete')
			$query->addSelect('annotations.word_position as focus', 'annotations.governor_position as expected_answer');
		else 
			$query->addSelect('annotations.governor_position as focus','annotations.word_position as expected_answer');

		return $query->first();


	}

}
