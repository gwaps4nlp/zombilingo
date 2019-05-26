<?php

namespace App\Repositories;

use App\Models\Relation;
use Gwaps4nlp\Core\Models\Source;
use Gwaps4nlp\Core\Repositories\BaseRepository;
use DB;
use Config;

class RelationRepository extends BaseRepository
{

	private $corpus;
	/**
	 * Create a new RelationRepository instance.
	 *
	 * @param  App\Models\Relation $relation
	 * @param  Gwaps4nlp\Core\Models\Source $source
	 * @return void
	 */
	public function __construct(
		Relation $relation,
		Source $source)
	{
		$this->model = $relation;
		$this->source = $source;
	}

	/**
	 * List of playable relations.
	 *
	 * @param  App\Models\User|null $user
	 * @return Collection of Relation
	 */
	public function getListPlayable($user=null)
	{
		$query = $this->model->select(DB::raw('concat(name," - '.trans('game.level').' ",level_id) as name'),'id')->whereIn('type',['trouverTete','TrouverDependant']);
		
		if($user)
			$query = $query->where('level_id','<=',$user->level_id);

		return $query->where('level_id','<=',7)->orderBy('level_id')->pluck('name','id');
	}

	/**
	 * List of all relations.
	 *
	 * @return Collection of [slug => id]
	 */
	public function getList()
	{
		return $this->model->where('level_id','<=',10)->where('type','!=','special')->orderBy('slug')->pluck('slug','id');
	}

	/**
	 * Get a random relation.
	 * 
	 * @param int $level_id
	 * @return App\Models\Relation
	 */
	public function getRandom($level_id)
	{
		$query = $this->model->orderBy(DB::raw('Rand()'))->whereIn('type',['trouverTete','TrouverDependant']);
		$query = $query->where('level_id','<=',$level_id);
		return $query->first();
	}

	/**
	 * Get the list of relations for a given user
	 *
	 * @param  App\Models\User $user
	 * @param  int|null $relation_id
	 * @param  string|null $slug
	 * @param  int|null $level_id
	 * @return int
	 */
	public function getByUser($user,$relation_id=null,$slug=null,$level_id=null)
	{
		$sources_ids = [Source::getPreAnnotated()->id];
		if(isset($this->corpus)){
			$corpora_ids = array_merge([$this->corpus->id],$this->corpus->subcorpora->pluck('id')->toArray(),$this->corpus->evaluation_corpora->pluck('id')->toArray());
		}	

		$sqlTotal = $this->model->join('annotations','annotations.relation_id','=','relations.id')
			->select('annotations.relation_id',
				DB::raw('count(distinct annotations.sentence_id,if(type="trouverTete",annotations.word_position,annotations.governor_position)) as total'))
			->whereIn('annotations.source_id',$sources_ids)
			->where('annotations.playable','=',1)
			->groupBy('annotations.relation_id');
		if(isset($this->corpus)){
			$sqlTotal->whereIn('corpus_id',$corpora_ids);
		}				
		$sqlTodo = $this->model->join('annotations','annotations.relation_id','=','relations.id')
			->join('annotation_users','annotation_users.annotation_id','=','annotations.id')
			->select('annotations.relation_id',
				DB::raw('count(distinct annotations.sentence_id,if(type="trouverTete",annotations.word_position,annotations.governor_position)) as total'))
			->whereIn('annotations.source_id',$sources_ids)
			->where('user_id','=',$user->id)
			->where('annotations.playable','=',1)
			->groupBy('annotations.relation_id');
		if(isset($this->corpus)){
			$sqlTodo->whereIn('corpus_id',$corpora_ids);
		}
		$sqlTutorials = $this->model->join('tutorials','tutorials.relation_id','=','relations.id')
			->select('relation_id','number_success')
			->where('user_id','=',$user->id)
			->groupBy('relation_id')
			->havingRaw('number_success >='.Config::get('constants.NB_PHASES_MAX_TUTORIEL'));
		
		$sqlScores = $this->model->join('scores','scores.relation_id','=','relations.id')
			->select('relation_id',DB::raw('sum(points) as score'))
			->where('user_id','=',$user->id)
			->groupBy('relation_id');
		if(isset($this->corpus)){
			$sqlScores->whereIn('corpus_id',$corpora_ids);
		}
		$query = $this->model->leftJoin(DB::raw("({$sqlTotal->toSql()}) as annotations"),'annotations.relation_id','=','relations.id')
				->mergeBindings($sqlTotal->getQuery())
			->leftJoin(DB::raw("({$sqlTodo->toSql()}) as annotation_users"),'annotation_users.relation_id','=','relations.id')
				->mergeBindings($sqlTodo->getQuery()) 
			->leftJoin(DB::raw("({$sqlTutorials->toSql()}) as tutorials"),'tutorials.relation_id','=','relations.id')
				->mergeBindings($sqlTutorials->getQuery())
			->leftJoin(DB::raw("({$sqlScores->toSql()}) as scores"),'scores.relation_id','=','relations.id')
				->mergeBindings($sqlScores->getQuery()) 
			->select('name','level_id','slug','id','description','help_file','type',
				DB::raw('ifnull(annotations.total,0) as total'),
				DB::raw('ifnull(annotation_users.total,0) as done'),
				DB::raw('ifnull(annotations.total,0)-ifnull(annotation_users.total,0) as todo'),
				DB::raw('ifnull(scores.score,0) as score'),
				DB::raw('ifnull(tutorials.number_success,0) as tutorial')
				)
			->where('level_id','<=',7)
			->having('total','>',0)
			->orderBy('level_id','asc');
		
		if($level_id){
			$query->where('level_id','=',$level_id);
		}

		if($relation_id)
			return $query->where('relations.id','=',$relation_id)->firstOrFail();
		if($slug)
			return $query->where('relations.slug','=',$slug)->firstOrFail();

		$relations = $query->get();
		foreach($relations as &$relation){
			if($relation->type=='special'){
				foreach(explode('/',$relation->slug) as $name){
					$sous_relation = $this->getByUser($user,null,$name);
					if(!$sous_relation->tutorial){
							$relation->tutorial=0;
							break;
					} else 
						$relation->tutorial=1;
				}
			}
		}
		return $relations;
	}

	/**
	 * Get the relations done by corpus for a user.
	 *
	 * @param  App\Models\User $user
	 * @return Collection of Relation
	 */
	public function countRelationDoneByUser($user)
	{
		$source_id = $this->source->where('slug','preannotated')->first()->id;

		$sqlTotal = $this->model->join('annotations','annotations.relation_id','=','relations.id')
			->select('annotations.relation_id',
				DB::raw('count(distinct annotations.sentence_id,if(type="trouverTete",annotations.word_position,annotations.governor_position)) as total'))
			->where('annotations.source_id','=',$source_id)
			->groupBy('annotations.relation_id');
		if(isset($this->corpus)){
			$sqlTotal->where('corpus_id','=',$this->corpus->id);
		}
		$sqlTodo = $this->model->join('annotations','annotations.relation_id','=','relations.id')
			->join('annotation_users','annotation_users.annotation_id','=','annotations.id')
			->select('annotations.relation_id',
				DB::raw('count(distinct annotations.sentence_id,if(type="trouverTete",annotations.word_position,annotations.governor_position)) as total'))
			->where('annotations.source_id','=',$source_id)->where('user_id','=',$user->id)
			->groupBy('annotations.relation_id');
		if(isset($this->corpus)){
			$sqlTodo->where('corpus_id','=',$this->corpus->id);
		}
		
		$query = $this->model->leftJoin(DB::raw("({$sqlTotal->toSql()}) as annotations"),'annotations.relation_id','=','relations.id')
				->mergeBindings($sqlTotal->getQuery())
			->leftJoin(DB::raw("({$sqlTodo->toSql()}) as annotation_users"),'annotation_users.relation_id','=','relations.id')
				->mergeBindings($sqlTodo->getQuery()) 
			->select('name','level_id','slug','id','description','help_file','type',
				DB::raw('ifnull(annotations.total,0) as total'),
				DB::raw('ifnull(annotation_users.total,0) as done'),
				DB::raw('ifnull(annotations.total,0)-ifnull(annotation_users.total,0) as todo'),
				DB::raw('ifnull(scores.score,0) as score'),
				DB::raw('ifnull(tutorials.number_success,0) as tutorial')
				)
			->where('level_id','<=',$user->level->id)
			->orderBy('level_id','asc');

		$relations = $query->get();
		return $relations;
	}
	/**
	 * Set the current corpus
	 *
	 * @param  App\Models\Corpus $corpus
	 * @return void
	 */	
	public function setCorpus($corpus){
		$this->corpus=$corpus;
	}
}
