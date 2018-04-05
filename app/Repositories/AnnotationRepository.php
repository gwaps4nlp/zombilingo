<?php

namespace App\Repositories;

use App\Models\Annotation;
use App\Models\AnnotationUser;
use Gwaps4nlp\Core\Models\Source;
use Gwaps4nlp\Core\Repositories\BaseRepository;
use App\Models\Relation;
use App\Repositories\ChallengeRepository;
use DB;

class AnnotationRepository extends BaseRepository
{
	private $corpus;
	private $challenges;
	
	/**
	 * Create a new AnnotationRepository instance.
	 *
	 * @param  App\Models\Annotation $annotation
	 * @param  App\Repositories\ChallengeRepository $challenges
	 * @return void
	 */
	public function __construct(
		Annotation $annotation,
		ChallengeRepository $challenges)
	{
		$this->model = $annotation;
		$this->challenges = $challenges;
	}
	
	/**
	 * Get a random reference annotation
	 *
	 * @param  App\Models\Relation $relation 
	 * @param  App\Models\User $user
	 * @return App\Models\Annotation
	 */
	public function getRandomReference(Relation $relation, $user=null)
	{
		$source = Source::where('slug','reference')->first();
		return $this->getRandom($relation, $user, $source);
	}

	/**
	 * Get a random reference annotation
	 *
	 * @param  App\Models\Relation $relation 
	 * @param  App\Models\User $user
	 * @return App\Models\Annotation
	 */
	public function getRandomNegativeReference(Relation $relation, $user=null)
	{
		$source = Source::where('slug','reference')->first();
		return $this->getRandom($relation, $user, $source, null, true);
	}

	/**
	 * Get a random pre-annotated annotation
	 *
	 * @param  App\Models\Relation $relation
	 * @param  App\Models\User|null $user
	 * @return App\Models\Annotation
	 */
	public function getRandomPreAnnotated(Relation $relation, $user=null)
	{
		$source = Source::where('slug','preannotated')->first();
		return $this->getRandom($relation, $user, $source);
	}
	
	/**
	 * Get a random annotation
	 *
	 * @param  App\Models\Relation $relation
	 * @param  App\Models\User|null $user
	 * @param  Gwaps4nlp\Core\Models\Source $source
	 * @param  int|null $id
	 * @return App\Models\Annotation
	 */
	public function getRandom(Relation $relation, $user=null, $source, $id = null, $negative = false )
	{
		$query=$this->model->select('annotations.*')
			->where('annotations.relation_id','=',$relation->id)->where('playable','=',1)
			->orderBy(DB::raw('Rand()'));
		if($user)
		$query	->leftJoin('annotation_users',function($join) use ($user,$relation) {
				$join->on('annotations.sentence_id','=','annotation_users.sentence_id')
					->on('annotations.id','=','annotation_users.annotation_id')
					->on('annotations.relation_id','=','annotation_users.relation_id')
					->on('annotation_users.user_id','=',DB::raw($user->id));
				})
				->whereRaw('annotation_users.id is null');
		
		if($negative){
			$query->where(function ($query) {
                $query->where('annotations.word_position', '=', 99999)
                      ->orWhere('annotations.governor_position', '=', 99999);
            });
		} else {
			$query->where('annotations.word_position', '!=', 99999)->where('annotations.governor_position', '!=', 99999);
		}

		if(isset($this->corpus) && $source->id!=1){
			// if(!empty($this->corpus->evaluation_corpora->pluck('id')->toArray())){
				$corpora_ids = array_merge([$this->corpus->id],$this->corpus->subcorpora->pluck('id')->toArray());
				$query->whereIn('annotations.corpus_id', $corpora_ids);
			// } else 
			// 	$query->where('annotations.corpus_id', '=', $this->corpus->id);
			$query->whereIn('annotations.source_id', [Source::getPreAnnotated()->id]);
		}
		elseif(isset($this->corpus) && $source->id==1 && is_array($this->corpus->bound_corpora->pluck('id')->toArray())){
			if(!empty($this->corpus->bound_corpora->pluck('id')->toArray())){
				$query->whereIn('annotations.corpus_id', $this->corpus->bound_corpora->pluck('id')->toArray());
				$query->where('annotations.source_id', '=', $source->id);
			}
		}
		elseif($source->id==1){
			$query->where('annotations.source_id', '=', $source->id);	
		}

		if($id)	
			$query->where('annotations.id', '=', $id);
		
		if($relation->type=='trouverTete')
			$query->addSelect(DB::raw('"trouverTete" as relation_type'),'annotations.word_position as focus');
		else 
			$query->addSelect(DB::raw('"trouverDependant" as relation_type'),'annotations.governor_position as focus');

		$annotation = $query->with('sentence')->first();

		$this->attachExpectedAnswers($annotation, $relation);

		return $annotation;
	}
		
	/**
	 * Get a random reference annotation for duels
	 *
	 * @param  App\Models\Relation $relation
	 * @param  array $not_in
	 * @param  App\Models\User $user	 
	 * @return App\Models\Annotation
	 */
	public function getRandomReferenceDuel(Relation $relation, $not_in=array(), $user=null)
	{
		$source = Source::where('slug','reference')->first();
		return $this->getRandomDuel($relation, $not_in, $user, $source);
	}

	/**
	 * Get a random pre-annotated annotation for duels
	 *
	 * @param  App\Models\Relation $relation
	 * @param  array $not_in
	 * @param  App\Models\User $user	 
	 * @return App\Models\Annotation
	 */
	public function getRandomPreAnnotatedDuel(Relation $relation, $not_in=array(), $user=null)
	{
		$source = Source::where('slug','preannotated')->first();		
		/* ongoing challenge ? */
        $challenge = $this->challenges->getOngoing();
        if($challenge)
			return $this->getRandomDuel($relation, $not_in, $user, $source,$challenge->corpus_id);

		return $this->getRandomDuel($relation, $not_in, $user, $source);
	}
	
	/**
	 * Get a random annotation for duels
	 *
	 * @param  App\Models\Relation $relation
	 * @param  array $not_in
	 * @param  App\Models\User $user	 
	 * @param  Gwaps4nlp\Core\Models\Source $source	 
	 * @param  int $corpus_id	 
	 * @return App\Models\Annotation
	 */
	public function getRandomDuel(Relation $relation, $not_in=array(), $user=null, $source, $corpus_id = null)
	{
		$query=$this->model->select('annotations.*')
			->where('annotations.relation_id','=',$relation->id)
			->where('playable','=',1)
			->whereNotIn('annotations.id',$not_in)
			->orderBy(DB::raw('Rand()'));

		if($source)
			$query = $query->where('annotations.source_id', '=', $source->id);
		if($corpus_id)
			$query = $query->where('annotations.corpus_id', '=', $corpus_id);

		$annotation = $query->first();
		if(!$annotation && $corpus_id)
			return $this->getRandomDuel($relation, $not_in, $user, $source);
		return $annotation;
	}
	
	/**
	 * Get the annotation of a given id
	 *
	 * @param  int $id the id of the annotation
	 * @param  App\Models\Relation|null $relation
	 * @param  App\Models\User|null $user
	 * @return App\Models\Annotation
	 */
	public function get($id, Relation $relation=null, $user=null)
	{
		$query=$this->model->select('annotations.*')
			->where('annotations.id', '=', $id);
		if($user && $relation)
		$query	->leftJoin('annotation_users',function($join) use ($user,$relation) {
				$join->on('annotations.sentence_id','=','annotation_users.sentence_id')
					->on('annotations.relation_id','=','annotation_users.relation_id')
					->on('annotation_users.user_id','=',DB::raw($user->id));
					if($relation->type=='trouverTete')
						$join->on('annotations.word_position','=','annotation_users.word_position');
					else 
						$join->on('annotations.governor_position','=','annotation_users.governor_position');
				})
				->whereRaw('annotation_users.id is null');
				
		$annotation = $query->with('sentence')->first();

		if($annotation) {
			if($relation && $annotation->relation_id!=$relation->id){
				$annotation->relation_type="trouverTete";
				$annotation->focus=$annotation->word_position;
				$expected_answers = collect(['99999']);
			}
			elseif($annotation->relation->type=='trouverTete'){
				$annotation->relation_type="trouverTete";
				$annotation->focus=$annotation->word_position;
				$query=$this->model
					->where('annotations.relation_id','=',$annotation->relation->id)
					->where('annotations.sentence_id','=',$annotation->sentence_id)
					->where('annotations.source_id','=',$annotation->source_id)
					->where('annotations.word_position','=',$annotation->word_position);			
				$expected_answers = $query->pluck('annotations.governor_position');				
			}
			elseif($annotation->relation->type=='trouverDependant'){
				$annotation->relation_type="trouverDependant";
				$annotation->focus=$annotation->governor_position;
				$query=$this->model
						->where('annotations.relation_id','=',$annotation->relation->id)
						->where('annotations.sentence_id','=',$annotation->sentence_id)
						->where('annotations.source_id','=',$annotation->source_id)
						->where('annotations.governor_position','=',$annotation->governor_position);
				$expected_answers = $query->pluck('annotations.word_position');						
			}
		    foreach($expected_answers as $key=>$expected_answer)
				$expected_answers[$key] = strval($expected_answer);
			$annotation->expected_answers = $expected_answers;
		}

		return $annotation;
	}	
	
	/**
	 * Get the next annotation of a duel
	 *
	 * @param  App\Models\Duel $duel
	 * @param  App\Models\User $user
	 * @return App\Models\Annotation
	 */
	public function getNextAnnotationDuel($duel,$user)
	{
		$query = $this->model->select('annotations.*');
		
		$query = $query->join('annotation_duel', function ($query) use ($duel) {
            $query->on('annotation_duel.annotation_id', '=', 'annotations.id');
        });

		$query = $query->where('annotation_duel.duel_id',$duel->id);
		$query = $query->whereNotExists(function ($query) use ($user) {
            $query->select(DB::raw(1))
                  ->from('annotation_user_duel')
                  ->whereRaw('annotation_duel.annotation_id = annotation_user_duel.annotation_id')
                  ->whereRaw('annotation_duel.duel_id = annotation_user_duel.duel_id')
                  ->where('annotation_user_duel.user_id', $user->id);
        });
        $annotation = $query->with('sentence')->first();
		if($annotation) {
			if($annotation->relation->type=='trouverTete'){
				$annotation->relation_type="trouverTete";
				$annotation->focus=$annotation->word_position;
				$query=$this->model
					->where('annotations.relation_id','=',$annotation->relation->id)
					->where('annotations.sentence_id','=',$annotation->sentence_id)
					->where('annotations.source_id','=',$annotation->source_id)
					->where('annotations.word_position','=',$annotation->word_position);			
				$expected_answers = $query->pluck('annotations.governor_position');				
			}
			elseif($annotation->relation->type=='trouverDependant'){
				$annotation->relation_type="trouverDependant";
				$annotation->focus=$annotation->governor_position;
				$query=$this->model
						->where('annotations.relation_id','=',$annotation->relation->id)
						->where('annotations.sentence_id','=',$annotation->sentence_id)
						->where('annotations.source_id','=',$annotation->source_id)
						->where('annotations.governor_position','=',$annotation->governor_position);
				$expected_answers = $query->pluck('annotations.word_position');						
			}
		    foreach($expected_answers as $key=>$expected_answer)
				$expected_answers[$key] = strval($expected_answer);
			$annotation->expected_answers = $expected_answers;
		}
		return $annotation;
	}

	/**
	 * 
	 * @param  array $param
	 * @return App\Models\Annotation
	 */
	public function getStatistics($params=array('user_id'=>null,'corpus_id'=>null),$corpus)
	{
		$query=$this->model->select('annotations.*')
			->addSelect(DB::raw('case relations.type when "trouverTete" THEN annotations.word_position when "trouverDependant" then annotations.governor_position END as focus'))
			->join('relations','relations.id','=','annotations.relation_id');

		if(isset($params['user_id']))
			$query = $query->addSelect(DB::raw('case relations.type when "trouverTete" THEN annotation_users.governor_position when "trouverDependant" then annotation_users.word_position END as user_answer'))
					->join('annotation_users',function($join) use ($params) {
					$join->on('annotations.sentence_id','=','annotation_users.sentence_id')
						->on('annotations.relation_id','=','annotation_users.relation_id')
						->on('annotations.id','=','annotation_users.annotation_id')
						->on('annotation_users.user_id','=',DB::raw($params['user_id']));
					})
					->orderBy('annotation_users.created_at','DESC');
		
		if($params['playable'])
			$query = $query->where('playable',1);
		if(isset($params['undecided']))
			$query = $query->where('undecided',$params['undecided']);
		if($corpus->id){
			$corpora_ids = array_merge([$corpus->id],$corpus->subcorpora->pluck('id')->toArray());
			$query = $query->whereIn('corpus_id',$corpora_ids);	
		}
		if(isset($params['relation_id']))
			$query = $query->where('annotations.relation_id',$params['relation_id']);
		
		$annotations = $query->with('sentence')->with('relation')->with('source')->with('statistics')->paginate(100);

		return $annotations;
	}		
		
	/**
	 * Get a random negative annotation for the tutorial of a given relation 
	 *
	 * @param  App\Models\Relation $relation
	 * @param  App\Models\User|null $user
	 * @param  int|null $id 	 
	 * @return App\Models\Annotation
	 */
	public function getRandomNegative(Relation $relation, $user=null, $id = null)
	{
		$query=$this->model->select('annotations.*')
			->where('visible','=',1)
			->orderBy(DB::raw('Rand()'));
		$query->join('negative_annotations', 'annotations.id', '=', 'negative_annotations.annotation_id')
			->where('negative_annotations.relation_id','=',$relation->id);
		if($user)
			$query->leftJoin('annotation_users',function($join) use ($user,$relation) {
					$join->on('annotations.sentence_id','=','annotation_users.sentence_id')
						->on('annotations.relation_id','=','annotation_users.relation_id')
						->on('annotation_users.user_id','=',DB::raw($user->id));
						if($relation->type=='trouverTete')
							$join->on('annotations.word_position','=','annotation_users.word_position');
						else 
							$join->on('annotations.governor_position','=','annotation_users.governor_position');
					})
					->whereRaw('annotation_users.id is null');
		
		if($id)	
			$query->where('annotations.id', '=', $id);
		
		if($relation->type=='trouverDependant')
			$query->addSelect(DB::raw('"trouverDependant" as relation_type'),'annotations.word_position as focus');		
		else
			$query->addSelect(DB::raw('"trouverTete" as relation_type'),'annotations.word_position as focus');

		$annotation = $query->with('sentence')->with('relation')->first();

		if($annotation) {
			if($annotation->focus==""||$annotation->focus=="0") 
				$annotation->focus = $annotation->word_position;	
			$annotation->expected_answers = collect(['99999']);
		}
		return $annotation;
	}

	/**
	 * Get a random annotation for the tutorial of a given relation
	 *
	 * @param  App\Models\Relation $relation
	 * @param  int $level
	 * @param  array $already_played
	 * @param  App\Models\User|null $user 
	 * @param  int|null $id 
	 * @return App\Models\Annotation
	 */
	public function getRandomTutorial(Relation $relation, $level, $already_played = array(), $user=null, $id = null)
	{

		$query=$this->model->select('annotations.*','tutorial_annotations.explanation','tutorial_annotations.type')
			->where('visible','=',1)
			->where('tutorial_annotations.level','=',$level)
			->whereNotIn('annotations.id',$already_played)
			->orderBy(DB::raw('Rand()'));
		$query->join('tutorial_annotations', 'annotations.id', '=', 'tutorial_annotations.annotation_id')
			->where('tutorial_annotations.relation_id','=',$relation->id);
		if($user)
			$query->leftJoin('annotation_users',function($join) use ($user,$relation) {
					$join->on('annotations.sentence_id','=','annotation_users.sentence_id')
						->on('annotations.relation_id','=','annotation_users.relation_id')
						->on('annotation_users.user_id','=',DB::raw($user->id));
						if($relation->type=='trouverTete')
							$join->on('annotations.word_position','=','annotation_users.word_position');
						else 
							$join->on('annotations.governor_position','=','annotation_users.governor_position');
					})
					->whereRaw('annotation_users.id is null');
		
		if($id)	
			$query->where('annotations.id', '=', $id);
		
		if($relation->type=='trouverTete')
			$query->addSelect(DB::raw('"trouverTete" as relation_type'),'annotations.word_position as focus');
		else 
			$query->addSelect(DB::raw('"trouverDependant" as relation_type'),'annotations.governor_position as focus');

		$annotation = $query->with('sentence')->with('relation')->first();

		if($annotation) {
			//Search possible answers
			if($annotation->type==-1){
				$annotation->focus = $annotation->word_position;		
				$expected_answers = collect(['99999']);
				$annotation->expected_answers = $expected_answers;
			} else {
				$this->attachExpectedAnswers($annotation, $relation);
			}
			
		}

		return $annotation;
	}

	/**
	 * 
	 * @param  App\Models\Relation $relation
	 * @param  int|null $id
	 * @return \Illuminate\Database\Eloquent\Collection Collection of Annotations
	 */
	public function getNegatives(Relation $relation, $id = null)
	{
		$query=$this->model->select('annotations.*','negative_annotations.*');
		$query->join('negative_annotations', 'annotations.id', '=', 'negative_annotations.annotation_id')
			->where('negative_annotations.relation_id','=',$relation->id);
		
		if($id)	
			$query->where('annotations.id', '=', $id);
		
		if($relation->type=='trouverDependant')
			$query->addSelect(DB::raw('"trouverDependant" as relation_type'),'annotations.word_position as focus');		
		else
			$query->addSelect(DB::raw('"trouverTete" as relation_type'),'annotations.word_position as focus');

		$annotations = $query->with('sentence')->get();

		if($annotations) {
			foreach($annotations as &$annotation){
				if($annotation->focus==""||$annotation->focus=="0") 
					$annotation->focus = $annotation->word_position;	
				$annotation->expected_answers = collect(['99999']);
			}
		}
		return $annotations;
	}
	
	/**
	 * Get a collection of annotations for the tutorial of a given relation
	 * 
	 * @param  App\Models\Relation $relation
	 * @param  int|null $id
	 * @return \Illuminate\Database\Eloquent\Collection Collection of Annotations
	 */
	public function getTutorial(Relation $relation, $id = null)
	{
		$query=$this->model->select('annotations.*','tutorial_annotations.*');
		$query->join('tutorial_annotations', 'annotations.id', '=', 'tutorial_annotations.annotation_id')
			->where('tutorial_annotations.relation_id','=',$relation->id);
		
		if($id)	
			$query->where('annotations.id', '=', $id);
		
		if($relation->type=='trouverDependant')
			$query->addSelect(DB::raw('"trouverDependant" as relation_type'),'annotations.word_position as focus');		
		else
			$query->addSelect(DB::raw('"trouverTete" as relation_type'),'annotations.word_position as focus');

		$annotations = $query->with('sentence')->get();

		if($annotations) {
			foreach($annotations as &$annotation){
				if($annotation->focus==""||$annotation->focus=="0") 
					$annotation->focus = $annotation->word_position;		
				$annotation->expected_answers = collect(['99999']);
			}
		}
		return $annotations;
	}

	/**
	 * get a collection of annotations by relation
	 *
	 * @param  int $parser_id
	 * @param  int $parser_relation_id
	 * @param  int $control_relation_id
	 * @return \Illuminate\Database\Eloquent\Collection Collection of Annotations
	 */
	public function getByRelation($corpus, $parser1_id, $parser1_relation_id, $parser2_id, $parser2_relation_id)
	{
		$corpora_ids = array_merge([$corpus->id],$corpus->subcorpora->pluck('id')->toArray());
		
		$query=$this->model->select('annotations.*')
			->join('relations as relations1','relations1.id','=','annotations.relation_id')
			->join("annotations as a2", function($join) {
	            $join->on("annotations.sentence_id", "=", "a2.sentence_id")->on("annotations.word_position", "=", "a2.word_position");
	        })
	        ->join('relations as relations2','relations2.id','=','a2.relation_id')
	        ->whereIn('annotations.corpus_id', $corpora_ids)
	        ->where('relations1.slug', $parser1_relation_id)
	        ->where('relations2.slug', $parser2_relation_id);

        if($parser1_id=='best')
        	$query->where('annotations.best','=',1);        
        elseif($parser1_id=='ref'){
        	$query->where('annotations.source_id',1);
        } else {
            $query->join('annotation_parser as ap1','ap1.annotation_id','=','annotations.id')
            		->where('annotations.source_id','!=',2)
            		->where('ap1.parser_id',$parser1_id);
        }

        if($parser2_id=='best')
        	$query->where('a2.best','=',1);  
        elseif($parser2_id=='ref'){
        	$query->where('a2.source_id',1);
        } else {
            $query->join('annotation_parser as ap2','ap2.annotation_id','=','a2.id')
            		->where('a2.source_id','!=',2)
            		->where('ap2.parser_id',$parser2_id);
        }
		
		$annotations = $query->with('sentence')->get();

		return $annotations;
	}

	private function attachExpectedAnswers(&$annotation, $relation=null){

		if($annotation) {

			//Search possible answers
			if($relation->type=='trouverTete'){
				$query=$this->model
					->where('annotations.relation_id','=',$relation->id)
					->where('annotations.sentence_id','=',$annotation->sentence_id)
					->where('annotations.source_id','=',$annotation->source_id)
					->where('annotations.word_position','=',$annotation->word_position);			
				$expected_answers = $query->pluck('annotations.governor_position');
			} elseif($relation->type=='trouverDependant'){
				$query=$this->model
						->where('annotations.relation_id','=',$relation->id)
						->where('annotations.sentence_id','=',$annotation->sentence_id)
						->where('annotations.source_id','=',$annotation->source_id)
						->where('annotations.governor_position','=',$annotation->governor_position);				
				$expected_answers = $query->pluck('annotations.word_position');
			}
		    foreach($expected_answers as $key=>$expected_answer)
				$expected_answers[$key] = strval($expected_answer);
			$annotation->expected_answers = $expected_answers;
		}

	}

	/**
	 * Convert an Annotation to string
	 * 
	 * @param  int $sentence_id
	 * @param  int $word_position
	 * @return string
	 */
	public function toString($sentence_id,$word_position)
	{
		$string='';
		$annotation=$this->model->where('sentence_id','=',$sentence_id)
			->where('word_position','=',$word_position)
			->first();
		if(!$annotation)
			return '';
		if($annotation->relation->type=='trouverTete')
			$string = $annotation->governor_position." a pour ".$annotation->relation->name." ".$annotation->word;
		elseif($annotation->relation->type=='trouverDependant')
			$string = $annotation->word." est ".$annotation->relation->name." de ".$annotation->governor_position;		
		return $string;
	}

	/**
	 * Set the current corpus
	 *
	 * @return void
	 */	
	public function setCorpus($corpus){
		$this->corpus=$corpus;
	}	
}
