<?php

namespace App\Repositories;

use App\Models\AnnotationUser;
use App\Models\Annotation;
use App\Models\AnnotationInProgress;
use App\Models\Sentence;
use App\Models\Tutorial;
use App\Models\Stat;
use Gwaps4nlp\Core\Models\Source;
use Gwaps4nlp\Core\Repositories\BaseRepository;
use App\Events\BroadCastNewAnnotation;
use DB, Event;

class AnnotationUserRepository extends BaseRepository
{
	public $model;
	/**
	 * Create a new AnnotationUserRepository instance.
	 *
	 * @param  App\Models\AnnotationUser $annotation_user
	 * @param  App\Models\Annotation $annotation
	 * @param  App\Models\Tutorial $tutorial
	 * @param  App\Models\Stat $stat
	 * @param  Gwaps4nlp\Core\Models\Source $source
	 * @return void
	 */
	public function __construct(
		AnnotationUser $annotation_user,
		Annotation $annotation,
		Tutorial $tutorial,
		Stat $stat,
		Source $source
		)
	{
		$this->model = $annotation_user;
		$this->annotation = $annotation;
		$this->tutorial = $tutorial;
		$this->stat = $stat;
		$this->source = $source;
		$this->reference = 0;
		$this->error = 0;
	}

	/**
	 * Save the answer of a user.
	 *
	 * @param  App\Models\User $user
	 * @param  App\Models\Annotation $annotation
	 * @param  integer $user_response
	 * @param  boolean $save
	 * @return void
	 */
	public function save($user, $annotation, $user_response, $relation, $save=true)
	{

		if($annotation->relation->type=='trouverDependant'){
			$governor_position = $annotation->focus;
			$word_position = $user_response;
		} else {
			$governor_position = $user_response;
			$word_position = $annotation->focus;
		}

		if($annotation->sentence->source_id==$this->source->getReference()->id)
			return $this->saveReferenceAnnotation($user, $annotation, $user_response ,$relation, $save);
		else
			return $this->saveAnnotationUser($user, $annotation, $word_position, $governor_position,null,$save);

	}

	/**
	 * Save the answer of a user for the special mode game
	 *
	 * @return void
	 */
	public function saveByRelation($user, $annotation, $user_response)
	{
		
		// $annotation->relation_id = $user_response;
		
		if($annotation->sentence->source_id==$this->source->getReference()->id)
			return $this->saveReferenceAnnotationSpecialGame($user, $annotation , $user_response);
		else
			return $this->saveAnnotationUser($user, $annotation, $annotation->word_position, $annotation->governor_position, $user_response);

	}
	
	/**
	 * Save an answer.
	 *
	 * @return void
	 */
	private function saveAnnotationUser(
		$user, 
		$annotation_played,
		$word_position, 
		$governor_position,
		$relation_id=null,
		$save=true)
	{

		if($existing_annotation = Annotation::where(array(
			'word_position'=>$word_position,
			'governor_position'=>$governor_position,
			'relation_id'=>($relation_id)? $relation_id : $annotation_played->relation_id,
			'sentence_id'=>$annotation_played->sentence_id
			))->first()){

				$existing_annotation->increment('score',$user->level_id);
				$annotation_score_max = Annotation::where(array(
					'relation_id'=>($relation_id)? $relation_id : $annotation_played->relation_id,
					'sentence_id'=>$annotation_played->sentence_id
				));
				
				if($relation_id){
					$annotation_score_max->where('word_position','=',$word_position);
					$annotation_score_max->where('governor_position','=',$governor_position);
				}
				if($annotation_played->relation_type=="trouverTete")
					$annotation_score_max->where('word_position','=',$annotation_played->focus);
				else
					$annotation_score_max->where('governor_position','=',$annotation_played->focus);
				
				$score_max=$annotation_score_max->max('score');
				
				$this->score_multiplier = $existing_annotation->score/$score_max;

		} else {
			$new_annotation = Annotation::firstOrNew(['word_position'=>$word_position,'sentence_id'=>$annotation_played->sentence_id,'corpus_id'=>$annotation_played->corpus_id,'source_id'=>Source::getPreAnnotated()->id]);
			$annotation = $new_annotation->replicate($new_annotation->getGuarded());
			$annotation->score = $user->level_id;
			$annotation->word_position = $word_position;
			$annotation->source_id = $this->source->getUser()->id;
			$annotation->relation_id = ($relation_id)? $relation_id : $annotation_played->relation_id;
			$annotation->governor_position = $governor_position;
			
			$this->score_multiplier = 0;			
			
			if($save)
				$annotation->save();

		}

		$this->model->user_id = $user->id;
		$this->model->level_id = $user->level_id;
		$this->model->annotation_id = $annotation_played->id;
		$this->model->answer_id = (isset($annotation))? $annotation->id : $annotation_played->id;
		$this->model->sentence_id = $annotation_played->sentence_id;
		$this->model->word_position = $word_position;
		$this->model->governor_position = $governor_position;
		$this->model->relation_id = ($relation_id)? $relation_id : $annotation_played->relation_id;
		$this->model->source_id = (isset($annotation))? $annotation->source_id : $annotation_played->source_id;
		if($save)
			return $this->model->save();
	}
	
	/**
	 * Save an answer in training mode.
	 *
	 * @return void
	 */
	public function saveAnnotationTraining (
		$user, 
		$annotation,
		$user_response, 
		$relation_id=null,
		$save=true)
	{


		$source_training_id = Source::getTraining()->id;
		
		if($annotation->relation->type=='trouverDependant'){
			$governor_position = $annotation->focus;
			$word_position = $user_response;
		} else {
			$governor_position = $user_response;
			$word_position = $annotation->focus;
		}

		$user_annotation = $this->model->firstOrCreate(
			array(
				'annotation_id'=>$annotation->id,
				'relation_id'=> $annotation->relation_id,
				'sentence_id'=>$annotation->sentence_id,
				'user_id'=>$user->id,
				'source_id'=>$source_training_id,
			)
		);

		$user_annotation->word_position = $word_position;
		$user_annotation->governor_position = $governor_position;
		$user_annotation->save();

	}	
	/**
	 * Save an answer in training mode.
	 *
	 * @return void
	 */
	public function saveAnnotationExpert (
		$user, 
		$annotation_played,
		$user_response, 
		$relation_id,
		$save=true)
	{


		$source_expert_id = Source::getExpert()->id;

		if($annotation_played->relation->type=='trouverDependant'){
			$governor_position = $annotation_played->focus;
			$word_position = $user_response;
		} else {
			$governor_position = $user_response;
			$word_position = $annotation_played->focus;
		}

		$annotation = Annotation::where(array(
			'word_position'=>$word_position,
			'governor_position'=>$governor_position,
			'relation_id'=>$relation_id,
			'sentence_id'=>$annotation_played->sentence_id
			))->first();

		if(!$annotation){
			$annotation = Annotation::create(
				array(
					'word_position'=>$word_position,
					'governor_position'=>$governor_position,					
					'relation_id'=> $annotation_played->relation_id,
					'sentence_id'=>$annotation_played->sentence_id,
					'corpus_id'=>$annotation_played->corpus_id,
					'source_id'=>$source_expert_id,
				)
			);
		}

		$user_annotation = $this->model->firstOrCreate(
			array(
				'annotation_id'=>$annotation_played->id,
				'answer_id'=>$annotation->id,
				'relation_id'=> $annotation->relation_id,
				'sentence_id'=>$annotation->sentence_id,
				'source_id'=>$source_expert_id,
			)
		);
		$user_annotation->user_id = $user->id;
		$user_annotation->word_position = $word_position;
		$user_annotation->governor_position = $governor_position;
		$user_annotation->save();

	}

	/**
	 * Count the users.
	 *
	 * @return void
	 */
	private function saveReferenceAnnotation(
		$user, 
		$annotation, 
		$user_response,
		$relation=null,
		$save=true)
	{	
		$this->reference = 1;
		$user_id = $user->id;
		
		$relation_id = ($relation)? $relation->id : $annotation->relation_id;
		
		if($relation->type=='trouverDependant'){
			$governor_position = $annotation->focus;
			$word_position = $user_response;
		} else {
			$governor_position = $user_response;
			$word_position = $annotation->focus;
		}

		$stat = $this->stat->firstOrCreate(['user_id'=>$user_id,'relation_id'=>$relation_id]);

		$this->model->user_id = $user->id;
		$this->model->level_id = $user->level_id;		
		$this->model->annotation_id = $annotation->id;
		$this->model->sentence_id = $annotation->sentence_id;
		$this->model->word_position = $word_position;
		$this->model->governor_position = $governor_position;
		$this->model->relation_id = $relation_id;

		if($annotation->expected_answers->contains($user_response)){
			
			if($save){
				$stat->increment('correct');
				if($stat->percent<93)
					$stat->increment('percent',3);
			}

			$this->score_multiplier=1;
			$this->model->source_id = $annotation->source_id;

		} else {
			$this->score_multiplier=0;
			$this->model->source_id = $this->source->getUser()->id;
			if($save){
				if($stat->percent>=3)
					$stat->decrement('percent',3);
				if($stat->error<2){
					$stat->increment('error');
					$this->error = $stat->error;
				} else {
					$this->error = 3;
					$stat->error = 0;
					$stat->save();
					$game_in_progress = AnnotationInProgress::where('relation_id','=',$relation_id)->where('user_id','=',$user_id)->first();
					if($game_in_progress)
						$game_in_progress->delete();
					$tutorial = $this->tutorial->where('relation_id','=',$relation_id)->where('user_id','=',$user_id)->first();
					if($tutorial){
						$tutorial->number_success = 0;
						$tutorial->save();
					}
				}
			} else {
				$this->error = 1;
			}
			
		}
		if($save)
		return $this->model->save();

	}

	/**
	 * Save an answer of a corpus of control 
	 *
	 * @return void
	 */
	private function saveReferenceAnnotationSpecialGame(
		$user, 
		$annotation, 
		$relation_id,
		$save=true)
	{	
		$this->reference = 1;
		$user_id = $user->id;
		
		$stat = $this->stat->firstOrCreate(['user_id'=>$user_id,'relation_id'=>$annotation->relation_id]);

		$this->model->user_id = $user->id;
		$this->model->level_id = $user->level_id;		
		$this->model->annotation_id = $annotation->id;
		$this->model->sentence_id = $annotation->sentence_id;
		$this->model->word_position = $annotation->word_position;
		$this->model->governor_position = $annotation->governor_position;
		$this->model->relation_id = $relation_id;

		if($annotation->relation_id == $relation_id){
			
			if($save){
				$stat->increment('correct');
				if($stat->percent<93)
					$stat->increment('percent',3);
			}

			$this->score_multiplier=1;
			$this->model->source_id = $annotation->source_id;

		} else {
			$this->score_multiplier=0;
			$this->model->source_id = $this->source->getUser()->id;
			if($save){
				if($stat->percent>=3)
					$stat->decrement('percent',3);
				if($stat->error<2){
					$stat->increment('error');
					$this->error = $stat->error;
				} else {
					$this->error = 3;
					$stat->error = 0;
					$stat->save();
					$game_in_progress = AnnotationInProgress::where('relation_id','=',$relation_id)->where('user_id','=',$user_id)->first();
					if($game_in_progress)
						$game_in_progress->delete();
					$tutorial = $this->tutorial->where('relation_id','=',$relation_id)->where('user_id','=',$user_id)->first();
					$tutorial->number_success = 0;
					$tutorial->save();
				}
			} else {
				$this->error = 1;
			}
			
		}
		return $this->model->save();

	}

	/**
	 * Return the leaders.
	 *
	 * @return array
	 */
	public function leaders($take=10, $challenge=null)
	{
		$scores = [
			'week' => $this->leadersByPeriode('week',null,$take),
			'month' => $this->leadersByPeriode('month',null,$take),
			'total' => $this->leadersByPeriode(null,null,$take),
		];
		if($challenge)
			$scores['challenge'] = $this->leadersByPeriode(null,$challenge,$take);
		return $scores;
	}
	
	/**
	 * Return the leaders for a given period.
	 *
	 * @return Array
	 */
	public function leadersByPeriode($periode=null, $challenge=null,$take=10)
	{
		$list = $this->model->join('users','users.id','=','annotation_users.user_id')
			->join('annotations','annotations.id','=','annotation_users.annotation_id')
			->select('username','users.id as user_id')
			->selectRaw('count(*) as score')
			->whereNull('users.deleted_at')
			->groupBy('user_id') 
			->orderBy('score','desc');
			
		if($challenge){
			$corpora_ids = array_merge([$challenge->corpus_id], $challenge->corpus->subcorpora->pluck('id')->toArray());
			$list = $list->whereIn('annotations.corpus_id', $corpora_ids)
				->whereDate('annotation_users.created_at','>=',$challenge->start_date)
				->whereDate('annotation_users.created_at','<=',$challenge->end_date);
		}
		
		if($periode)
			$list = $list->whereRaw("annotation_users.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		return $list->paginate($take);
	}	
	/**
	 * Return the leaders for a given period.
	 *
	 * @return Array
	 */
	public function leadersByPeriodeOld($periode=null, $challenge=null,$take=10)
	{
		$list = $this->model->join('users','users.id','=','annotation_users.user_id')
			->join('annotations','annotations.id','=','annotation_users.annotation_id')
			->select('username','users.id as user_id')
			->selectRaw('count(*) as score')
			->whereNull('users.deleted_at')
			->groupBy('user_id') 
			->orderBy('score','desc');
			
		if($challenge){
			$corpora_ids = array_merge([$challenge->corpus_id], $challenge->corpus->subcorpora->pluck('id')->toArray());
			$list = $list->whereIn('annotations.corpus_id', $corpora_ids)
				->whereDate('annotation_users.created_at','>=',$challenge->start_date)
				->whereDate('annotation_users.created_at','<=',$challenge->end_date);
		}
		
		if($periode)
			$list = $list->whereRaw("annotation_users.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		return $list->paginate($take);
	}

	/**
	 * Return the leaders for a given corpus.
	 *
	 * @return array
	 */
	public function leadersChallenge($challenge,$take=10)
	{
		$corpora_ids = array_merge([$challenge->corpus_id], $challenge->corpus->subcorpora->pluck('id')->toArray());
		$scores = $this->model->join('users','annotation_users.user_id','=','users.id')
				->join('annotations','annotations.id','=','annotation_users.annotation_id')
				->select('username','user_id',DB::raw('count(*) as score'))
				->whereIn('annotations.corpus_id',$corpora_ids)
				->whereNull('users.deleted_at')
				->where('annotation_users.created_at','<=',$challenge->end_date)
				->where('annotation_users.created_at','>=',$challenge->start_date)
				->groupBy('annotation_users.user_id')
				->orderBy('score', 'desc');

		return $scores->paginate($take);
	}

	/**
	 * 
	 *
	 * @return array
	 */	
	public function neighbors($user, $take=3, $challenge=null)
	{

		$scores = [
			'week' => [
				'sup' => $this->neighborsByPeriode($user,'sup','week',null,$take),
				'inf' => $this->neighborsByPeriode($user,'inf','week',null,$take)
			],
			'month' => [
				'sup' => $this->neighborsByPeriode($user,'sup','month',null,$take),
				'inf' => $this->neighborsByPeriode($user,'inf','month',null,$take)
			],
			'total' => [
				'sup' => $this->neighborsByPeriode($user,'sup',false,null,$take),
				'inf' => $this->neighborsByPeriode($user,'inf',false,null,$take)
			],
		];
		$scores['challenge'] = [
			'sup' => $this->neighborsByPeriode($user,'sup',false,$challenge,$take),
			'inf' => $this->neighborsByPeriode($user,'inf',false,$challenge,$take)
		];
		return $scores;
	}

	/**
	 * 
	 *
	 * @return array
	 */
	public function neighborsByPeriode($user,$range,$periode=null,$challenge=null ,$take=10)
	{
		$score_user = $this->model
				->where('user_id','=',$user->id)
				->join('annotations','annotations.id','=','annotation_users.annotation_id')
				->groupBy('user_id');
				
		if($periode)
			$score_user = $score_user->whereRaw("annotation_users.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		if($challenge){
			$corpora_ids = array_merge([$challenge->corpus_id], $challenge->corpus->subcorpora->pluck('id')->toArray());
			$score_user = $score_user->whereIn('annotations.corpus_id',$corpora_ids)
				->where('annotation_users.created_at','>=', $challenge->start_date)
				->where('annotation_users.created_at','<=', $challenge->end_date);
		}

		$score_user = $score_user->count();
		$score_user = ($score_user)?$score_user:0;

		$scores = $this->model->join('users','annotation_users.user_id','=','users.id')
				->join('annotations','annotations.id','=','annotation_users.annotation_id')
				->select('annotations.id as annotation_id','username','annotation_users.user_id',DB::raw('count(*) as user_score'))
				->whereNull('users.deleted_at')
				->groupBy('annotation_users.user_id');

		if($range=='sup')
			$scores = $scores->havingRaw('user_score >'.$score_user)->orderBy('user_score', 'asc')->take($take);
		elseif($range=='inf')
			$scores =$scores->havingRaw('user_score <='.$score_user)
				->whereRaw('users.id!='.$user->id)
				->orderBy('user_score', 'desc')->take($take);
		
		if($periode)
			$scores = $scores->whereRaw("annotation_users.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		if($challenge){
			$scores = $scores->whereRaw('annotations.corpus_id in ('.join($corpora_ids,',').')')
					->whereRaw('annotation_users.created_at>="'.$challenge->start_date.'"')
					->whereRaw('annotation_users.created_at<="'.$challenge->end_date.'"');
		}

		$sqlRank = DB::table(DB::raw("({$scores->toSql()}) as users_scores"))->mergeBindings($scores->getQuery()) 
			->leftJoin('annotation_users as score_rank',function($join) use($periode,$challenge) {
				if($periode)
					$join->on('score_rank.created_at','>=',DB::raw("DATE_SUB(NOW(), interval 1 $periode )"));
				if($challenge)
					$join->on('score_rank.created_at','>=',DB::raw("'".$challenge->start_date."'"))
						 ->on('score_rank.created_at','<=',DB::raw("'".$challenge->end_date."'"));
				$join->on(DB::raw('1'),'>=',DB::raw('1'));
			})
			->select('score_rank.user_id as sup','users_scores.user_score as user_score','users_scores.username','users_scores.user_id as user_id')
			->havingRaw('count(*) >= users_scores.user_score')
			->groupBy('score_rank.user_id','users_scores.user_id');

		if($periode)
			$sqlRank = $sqlRank->whereRaw("score_rank.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		if($challenge)
			$sqlRank = $sqlRank->join('annotations','annotations.id','=','score_rank.annotation_id')
			->whereRaw('annotations.corpus_id in ('.join($corpora_ids,',').')')
			->whereRaw('score_rank.created_at >= "'.$challenge->start_date.'"');

		$scores_ranked = DB::table(DB::raw("({$sqlRank->toSql()}) as scores"))
			->select(DB::raw('count(distinct sup) as rank'), 'scores.user_score as score', 'scores.username', 'scores.user_id' )
			->groupBy('user_id')->orderBy('rank', 'asc');

		return $scores_ranked->get();

	}
	
	/**
	 * 
	 *
	 * @return array
	 */
	public function getByUser($user, $challenge=null)
	{
		$scores = [
			'week' => $this->getByUserAndPeriode($user,'week'),
			'challenge' => $this->getByUserAndPeriode($user,null,null,$challenge),
			'month' => $this->getByUserAndPeriode($user,'month'),
			'total' => $this->getByUserAndPeriode($user),
		];

		if($challenge)
			$scores['challenge'] = $this->getByUserAndPeriode($user,null,null,$challenge);
	
		return $scores;
	}	
	/**
	 * 
	 *
	 * @return array
	 */
	public function getByUserAndPeriode($user,$periode=null,$before=null,$challenge=null,$between=array())
	{
		if(is_object($user))
			$user_id = $user->id;
		else
			$user_id = $user;

		$scores = $this->model->join('users','annotation_users.user_id','=','users.id')
				->join('annotations','annotation_users.annotation_id','=','annotations.id')
				->select('username','user_id',DB::raw('count(*) as user_score'))
				->whereRaw('user_id='.$user_id)
				->groupBy('user_id');
		
		if($between)
			$scores = $scores->whereRaw("annotation_users.created_at BETWEEN '".$between['min']."' and '".$between['max']."'");
		elseif($before)
			$scores = $scores->whereRaw("annotation_users.created_at <= DATE_SUB(NOW(), interval $before )");	
		elseif($periode)
			$scores = $scores->whereRaw("annotation_users.created_at >= DATE_SUB(NOW(), interval 1 $periode )");
		
		if($challenge){
			$corpora_ids = array_merge([$challenge->corpus_id], $challenge->corpus->subcorpora->pluck('id')->toArray());
			$scores = $scores->whereRaw('annotations.corpus_id in ('.join($corpora_ids,',').')')
				->whereRaw("annotation_users.created_at>='".$challenge->start_date."'")
				->whereRaw("annotation_users.created_at<='".$challenge->end_date."'");
		}
		
		$sqlRank = DB::table(DB::raw("({$scores->toSql()}) as users_scores"))
			->mergeBindings($scores->getQuery())
			->leftJoin('annotation_users as score_rank',function($join) use($periode,$before,$challenge,$between) {
				if($between){
					$join->on('score_rank.created_at','>=',DB::raw("'".$between['min']."'"));
					$join->on('score_rank.created_at','<=',DB::raw("'".$between['max']."'"));
				} elseif($before)
					$join->on('score_rank.created_at','<=',DB::raw("DATE_SUB(NOW(), interval $before )"));
				elseif($periode)
					$join->on('score_rank.created_at','>=',DB::raw("DATE_SUB(NOW(), interval 1 $periode )"));
				if($challenge)
					$join->on('score_rank.created_at','>=',DB::raw("'".$challenge->start_date."'"))
					     ->on('score_rank.created_at','<=',DB::raw("'".$challenge->end_date."'"));
				$join->on(DB::raw('1'),'>=',DB::raw('1'));

			})
			->select('score_rank.user_id as sup','users_scores.user_score as user_score','users_scores.username','users_scores.user_id as user_id')

			->havingRaw('count(*) >= users_scores.user_score')
			->groupBy('score_rank.user_id','users_scores.user_id');

		if($between)
			$scores = $scores->whereRaw("score_rank.created_at BETWEEN '".$between['min']."' and '".$between['max']."'");				
		elseif($before)
			$sqlRank = $sqlRank->whereRaw("score_rank.created_at <= DATE_SUB(NOW(), interval $before )");	
		elseif($periode)
			$sqlRank = $sqlRank->whereRaw("score_rank.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		if($challenge)
			$sqlRank = $sqlRank->join('annotations','annotations.id','=','score_rank.annotation_id')
				->whereRaw('annotations.corpus_id in ('.join($corpora_ids,',').')')
				->whereRaw("score_rank.created_at>='".$challenge->start_date."'")
				->whereRaw("score_rank.created_at<='".$challenge->end_date."'");

		$scores_ranked = DB::table(DB::raw("({$sqlRank->toSql()}) as scores"))
			->select(DB::raw('count(distinct sup) as rank'), 'scores.user_score as score', 'scores.username', 'scores.user_id' )
			->groupBy('user_id')->orderBy('rank', 'asc');

		return $scores_ranked->first();

	}

	/**
	 * 
	 *
	 * @return Array
	 */
	public function countByUserAndCorpus($periode=null, $corpus_id=null,$take=10)
	{
		$list = $this->model->join('users','users.id','=','annotation_users.user_id')
			->join('annotations','annotations.id','=','annotation_users.annotation_id')
			->select('username')
			->selectRaw('count(*) as count')
			->whereNull('users.deleted_at')
			->groupBy('user_id') 
			->orderBy('count','desc');
			
		if($corpus_id)
			$list->where('annotations.corpus_id','=',$corpus_id);
		
		if($periode)
			$list = $list->whereRaw("annotation_users.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		return $list->paginate($take);
	}

	/**
	 * 
	 *
	 * @return Annotation
	 */
	public function countByUser($relation_id=null)
	{
		$list = $this->model->join('users','users.id','=','annotation_users.user_id')
			->select('username')
			->selectRaw('count(*) as count')
			->groupBy('user_id') 
			->orderBy('count','desc');
			
		if($relation_id)
			$list->where('relation_id','=',$relation_id);				
			
		return $list->get();
	}

	/**
	 * 
	 *
	 * @return Annotation
	 */
	public function countByWeek($relation_id=null)
	{
		$list = $this->model
			->selectRaw('YEARWEEK(created_at) as period')
			->selectRaw('count(*) as count')
			->where('user_id','!=',0)
			->groupBy(DB::Raw('YEARWEEK(created_at)'))
			->orderBy('period','asc');

		if($relation_id)
			$list->where('relation_id','=',$relation_id);			
			
		return $list->get();
	}
	/**
	 * 
	 *
	 * @return Annotation
	 */
	public function countByMonth($relation_id=null)
	{
		$list = $this->model
			->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period')
			->selectRaw('count(*) as count')
			->where('user_id','!=',0)
			->groupBy(DB::Raw('DATE_FORMAT(created_at, "%Y-%m")'))
			->orderBy('period','asc');

		if($relation_id)
			$list->where('relation_id','=',$relation_id);			
			
		return $list->get();
	}

	/**
	 * 
	 *
	 * @return int
	 */
	public function countByCorpus($corpus)
	{
		$corpora_ids = array_merge([$corpus->id], $corpus->subcorpora->pluck('id')->toArray());
		$count = $this->model->join('annotations','annotations.id','=','annotation_users.annotation_id')->where('user_id','!=',0);
		$count->whereIn('corpus_id',$corpora_ids);
		return $count->count();
	}

	/**
	 * 
	 *
	 * @return Annotation
	 */
	public function countByRelation($user_id=null)
	{
		$list = $this->model->join('relations','relations.id','=','annotation_users.relation_id')
			->select('relations.name as name')
			->selectRaw('count(*) as count')
			->where('user_id','!=',0)
			->groupBy('annotation_users.relation_id')
			->orderBy('count','desc');
		if($user_id)
			$list->where('user_id','=',$user_id);
		return $list->get();
	}

	/**
	 * Return the count of new registrations by week.
	 *
	 * @return int
	 */
	public function countDaysOfActivityByUser()
	{
		$list = $this->model->join('users','users.id','=','annotation_users.user_id')
			->select('username')
			->selectRaw('count(distinct date(annotation_users.created_at)) as count')
			->groupBy('user_id')
			->orderBy('count','desc')
			->get();
		return $list;
	}

}
	