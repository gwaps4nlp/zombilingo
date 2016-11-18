<?php

namespace App\Repositories;

use App\Models\Score, App\Models\User, App\Models\Challenge;
use DB;

class ScoreRepository extends BaseRepository
{

	/**
	 * Create a new ScoreRepository instance.
	 *
	 * @param  App\Models\Score $score
	 * @return void
	 */
	public function __construct(
		Score $score)
	{
		$this->model = $score;
	}

	/**
	 * Retrieve the leaders of game for given periods : last week, last moth, total
	 *
	 * @return array
	 */
	public function leaders($take=10, $challenge=null)
	{
		$scores = [
			'week' => $this->leadersByPeriode('week',$take),
			'month' => $this->leadersByPeriode('month',$take),
			'total' => $this->leadersByPeriode(null,$take),	
		];
		if($challenge){
			$scores['challenge'] = $this->leadersByChallenge($challenge,$take);
	
		}
		return $scores;
	}
	
	/**
	 * Return the leaders for a given period.
	 *
	 * @param  string|null $periode : DAY, WEEK, MONTH...
	 * @param  int $take : the number of leaders to retrieve	 
	 * @return array
	 */
	public function leadersByPeriode($periode=null,$take=10)
	{
		$scores = $this->model->join('users','scores.user_id','=','users.id')
				->select('username','user_id',DB::raw('sum(points) as score'))
				->whereNull('users.deleted_at')
				->groupBy('user_id')
				->orderBy('score', 'desc');
		
		if($periode)
			$scores = $scores->whereRaw("scores.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		return $scores->paginate($take);
	}

	/**
	 * Return the leaders and their ranks for a given period.
	 * 
	 * @param  string|null $periode : DAY, WEEK, MONTH...
	 * @param  int $take : the number of leaders to retrieve	 	 
	 * @param  array $params : array of additionnal parameters	 	 
	 * @return Collection of Score
	 */
	public function leadersRankedByPeriode($periode=null,$take=10,$params = array('relation_id'=>0,'corpus_id'=>0,'sortby'=>'score','order'=>'desc'))
	{
		$scores = $this->model->join('users','scores.user_id','=','users.id')
				->select('username','user_id',DB::raw('sum(points) as user_score'))
				->whereNull('users.deleted_at')
				->groupBy('user_id')
				->orderBy($params['sortby'], $params['order']);
				
		if($params['relation_id'])
			$scores = $scores->whereRaw("scores.relation_id = {$params['relation_id']}");	
		if($params['corpus_id'])
			$scores = $scores->whereRaw("scores.corpus_id = {$params['corpus_id']}");
		if($periode)
			$scores = $scores->whereRaw("scores.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		$sqlRank = DB::table(DB::raw("({$scores->toSql()}) as users_scores"))->mergeBindings($scores->getQuery());
		$sqlRank = 	$sqlRank->leftJoin('scores as score_rank',function($join) use($periode) {
				if($periode)
					$join->on('score_rank.created_at','>=',DB::raw("DATE_SUB(NOW(), interval 1 $periode )"));
				$join->on(DB::raw('1'),'>=',DB::raw('1'));

			})
			->select('score_rank.user_id as sup','users_scores.user_score as user_score','users_scores.username','users_scores.user_id as user_id')
			->havingRaw('sum(score_rank.points) >= users_scores.user_score')
			->groupBy('score_rank.user_id','users_scores.user_id');

		if($periode)
			$sqlRank = $sqlRank->whereRaw("score_rank.created_at>=DATE_SUB(NOW(), interval 1 $periode )");
		if($params['relation_id'])
			$sqlRank = $sqlRank->whereRaw("score_rank.relation_id = {$params['relation_id']}");
		if($params['corpus_id'])
			$sqlRank = $sqlRank->whereRaw("score_rank.corpus_id = {$params['corpus_id']}");

		$scores_ranked = DB::table(DB::raw("({$sqlRank->toSql()}) as scores"))
			->select(DB::raw('count(distinct sup) as rank'), 'scores.user_score as score', 'scores.username', 'scores.user_id as id' )
			->groupBy('user_id')->orderBy($params['sortby'], $params['order']);

		return $scores_ranked->paginate($take);
	}

	/**
	 * Return the leaders of a given corpus.
	 *
	 * @param  int $corpus_id
	 * @param  int $take : the number of leaders to retrieve	
	 * @return Collection of Score
	 */
	public function leadersByCorpus($corpus_id,$take=10)
	{
		$scores = $this->model->join('users','scores.user_id','=','users.id')
				->select('username','user_id',DB::raw('sum(points) as score'))
				->where('scores.corpus_id',$corpus_id)
				->whereNull('users.deleted_at')
				->groupBy('user_id')
				->orderBy('score', 'desc');

		return $scores->paginate($take);
	}

	/**
	 * Return the leaders of a given challenge.
	 *
	 * @param  int $corpus_id
	 * @param  int $take : the number of leaders to retrieve	
	 * @return Collection of Score
	 */
	public function leadersByChallenge(Challenge $challenge=null, $take=10)
	{
		$scores = $this->model->join('users','scores.user_id','=','users.id')
				->select('username','user_id',DB::raw('sum(points) as score'))
				->where('scores.corpus_id',$challenge->corpus_id)
				->whereNull('users.deleted_at')
				->whereDate('scores.created_at','>=',$challenge->start_date)
				->whereDate('scores.created_at','<=',$challenge->end_date)
				->groupBy('user_id')
				->orderBy('score', 'desc');

		return $scores->paginate($take);

	}

	/**
	 * Return the neighbors in the leaderboard of a given user.
	 *
	 * @param  App\Models\User $user
	 * @param  int $take : the number of leaders to retrieve
	 * @param  App\Models\Challenge|null $challenge	 
	 * @return array
	 */	
	public function neighbors($user,$take=3, $challenge=null)
	{
		$scores = [
			'week' => [
				'sup' => $this->neighborsByPeriode($user,'sup','week',$take),
				'inf' => $this->neighborsByPeriode($user,'inf','week',$take)
			],
			'month' => [
				'sup' => $this->neighborsByPeriode($user,'sup','month',$take),
				'inf' => $this->neighborsByPeriode($user,'inf','month',$take)
			],
			'total' => [
				'sup' => $this->neighborsByPeriode($user,'sup',false,$take),
				'inf' => $this->neighborsByPeriode($user,'inf',false,$take)
			],
		];
		if($challenge)
			$scores['challenge'] = [
				'sup' => $this->neighborsByChallenge($user,'sup',$challenge,$take),
				'inf' => $this->neighborsByChallenge($user,'inf',$challenge,$take)
			];			

		return $scores;
	}
	/**
	 * Return the neighbors in the leaderboard of a given user for a given period.
	 *
	 * @param  App\Models\User $user
	 * @param  string $range : sup or inf
	 * @param  int $take : the number of leaders to retrieve	 
	 * @param  string $periode : day, week, month... 
	 * @return array
	 */
	public function neighborsByPeriode($user,$range,$periode=null,$take=10)
	{
		$score_user = $this->model
				->where('user_id','=',$user->id)
				->groupBy('user_id');
				
		if($periode)
			$score_user = $score_user->whereRaw("scores.created_at>=DATE_SUB(NOW(), interval 1 $periode )");
		
		$score_user = $score_user->sum('points');
		$score_user = ($score_user)?$score_user:0;


		$scores = $this->model->join('users','scores.user_id','=','users.id')
				->select('username','scores.user_id',DB::raw('sum(points) as user_score'))
				->whereNull('users.deleted_at')
				->groupBy('scores.user_id');

		if($range=='sup')
			$scores = $scores->havingRaw('user_score >'.$score_user)->orderBy('user_score', 'asc')->take($take);
		elseif($range=='inf')
			$scores =$scores->havingRaw('user_score <'.$score_user)->orderBy('user_score', 'desc')->take($take);
		
		if($periode)
			$scores = $scores->whereRaw("scores.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		$sqlRank = DB::table(DB::raw("({$scores->toSql()}) as users_scores"))->mergeBindings($scores->getQuery()) 
			->leftJoin('scores as score_rank',function($join) use($periode) {
				if($periode)
					$join->on('score_rank.created_at','>=',DB::raw("DATE_SUB(NOW(), interval 1 $periode )"));
				$join->on(DB::raw('1'),'>=',DB::raw('1'));

			})
			->select('score_rank.user_id as sup','users_scores.user_score as user_score','users_scores.username','users_scores.user_id as user_id')
			->havingRaw('sum(score_rank.points) >= users_scores.user_score')
			->groupBy('score_rank.user_id','users_scores.user_id');

		if($periode)
			$sqlRank = $sqlRank->whereRaw("score_rank.created_at>=DATE_SUB(NOW(), interval 1 $periode )");

		$scores_ranked = DB::table(DB::raw("({$sqlRank->toSql()}) as scores"))
			->select(DB::raw('count(distinct sup) as rank'), 'scores.user_score as score', 'scores.username', 'scores.user_id' )
			->groupBy('user_id')->orderBy('rank', 'asc');

		return $scores_ranked->get();

	}
		
	/**
	 * Return the neighbors in the leaderboard of a given user for a given corpus.
	 *
	 * @param  App\Models\User $user
	 * @param  string $range : sup or inf
	 * @param  int $corpus_id
	 * @param  int $take : the number of leaders to retrieve	 
	 * @return Collection of Score
	 */
	public function neighborsByCorpus($user,$range,$corpus_id,$take=10)
	{
		$score_user = $this->model
				->where('user_id','=',$user->id)
				->where('corpus_id','=',$corpus_id)
				->groupBy('user_id');				
		
		$score_user = $score_user->sum('points');
		$score_user = ($score_user)?$score_user:0;


		$scores = $this->model->join('users','scores.user_id','=','users.id')
				->select('username','scores.user_id',DB::raw('sum(points) as user_score'))
				->whereRaw("corpus_id=$corpus_id")
				->whereNull('users.deleted_at')
				->groupBy('scores.user_id');

		if($range=='sup')
			$scores = $scores->havingRaw('user_score >'.$score_user)->orderBy('user_score', 'asc')->take($take);
		elseif($range=='inf')
			$scores =$scores->havingRaw('user_score <'.$score_user)->orderBy('user_score', 'desc')->take($take);

		$sqlRank = DB::table(DB::raw("({$scores->toSql()}) as users_scores"))->mergeBindings($scores->getQuery()) 
			->leftJoin('scores as score_rank',function($join) use($corpus_id) {
				$join->on('score_rank.corpus_id','=',DB::raw($corpus_id));
				$join->on(DB::raw('1'),'>=',DB::raw('1'));
			})
			->select('score_rank.user_id as sup','users_scores.user_score as user_score','users_scores.username','users_scores.user_id as user_id')
			->havingRaw('sum(score_rank.points) >= users_scores.user_score')
			->groupBy('score_rank.user_id','users_scores.user_id');

		$sqlRank = $sqlRank->whereRaw("score_rank.corpus_id = $corpus_id");

		$scores_ranked = DB::table(DB::raw("({$sqlRank->toSql()}) as scores"))
			->select(DB::raw('count(distinct sup) as rank'), 'scores.user_score as score', 'scores.username', 'scores.user_id' )
			->groupBy('user_id')->orderBy('rank', 'asc');

		return $scores_ranked->get();

	}

	/**
	 * Return the neighbors in the leaderboard of a given user for a given challenge.
	 *
	 * @param  App\Models\User $user
	 * @param  string $range : sup or inf
	 * @param  App\Models\Challenge
	 * @param  int $take : the number of neighbors to retrieve	 
	 * @return Collection of Score
	 */
	public function neighborsByChallenge($user,$range, Challenge $challenge,$take=10)
	{
		$score_user = $this->model
				->where('user_id','=',$user->id)
				->where('corpus_id','=',$challenge->corpus_id)
				->whereDate('created_at','>=',$challenge->start_date)
				->whereDate('created_at','<=',$challenge->end_date)
				->groupBy('user_id');				
		
		$score_user = $score_user->sum('points');
		$score_user = ($score_user)?$score_user:0;


		$scores = $this->model->join('users','scores.user_id','=','users.id')
				->select('username','scores.user_id',DB::raw('sum(scores.points) as user_score'))
				->whereRaw("scores.corpus_id=$challenge->corpus_id")
				->whereRaw("scores.created_at>='$challenge->start_date'")
				->whereNull('users.deleted_at')
				->groupBy('scores.user_id');

		if($range=='sup')
			$scores = $scores->havingRaw('user_score >'.$score_user)->orderBy('user_score', 'asc')->take($take);
		elseif($range=='inf')
			$scores =$scores->havingRaw('user_score <'.$score_user)->orderBy('user_score', 'desc')->take($take);

		$sqlRank = DB::table(DB::raw("({$scores->toSql()}) as users_scores"))->mergeBindings($scores->getQuery()) 
			->leftJoin('scores as score_rank',function($join) use($challenge) {

				$join->on('score_rank.created_at','>=',DB::raw("'".$challenge->start_date."'"));
				$join->on('score_rank.created_at','<=',DB::raw("'".$challenge->end_date."'"));
				$join->on('score_rank.corpus_id','=',DB::raw($challenge->corpus_id));
				$join->on(DB::raw('1'),'>=',DB::raw('1'));

			})
			->whereRaw('score_rank.created_at>="'.$challenge->start_date.'"')
			->whereRaw('score_rank.created_at<="'.$challenge->end_date.'"')
			->select('score_rank.user_id as sup','users_scores.user_score as user_score','users_scores.username','users_scores.user_id as user_id')
			->havingRaw('sum(score_rank.points) >= users_scores.user_score')
			->groupBy('score_rank.user_id','users_scores.user_id');

		$sqlRank = $sqlRank->whereRaw("score_rank.corpus_id=$challenge->corpus_id");
		$scores_ranked = DB::table(DB::raw("({$sqlRank->toSql()}) as scores"))
			->select(DB::raw('count(distinct sup) as rank'), 'scores.user_score as score', 'scores.username', 'scores.user_id' )
			->groupBy('user_id')->orderBy('rank', 'asc');

		return $scores_ranked->get();

	}
	
	/**
	 * Get the scores of a given user for different periodes : last week, last month, total
	 *
	 * @param  App\Models\User $user
	 * @param  App\Models\Challenge|null $challenge
	 * @return array
	 */
	public function getByUser($user, $challenge=null)
	{
		$scores = [
			'week' => $this->getByUserAndPeriode($user,'week'),
			'month' => $this->getByUserAndPeriode($user,'month'),
			'total' => $this->getByUserAndPeriode($user),
		];
		if($challenge)
			$scores['challenge'] = $this->getByUserAndChallenge($user,$challenge);
		return $scores;

	}

	/**
	 * 
	 *
	 * @return array
	 */
	public function getByUserAndPeriode($user,$periode=null,$before=null,$corpus_id=null,$between=array() )
	{
		if(is_object($user))
			$user_id = $user->id;
		else
			$user_id = $user;
		
		$scores = $this->model->join('users','scores.user_id','=','users.id')
				->select('username','user_id',DB::raw('sum(points) as user_score'))
				->whereRaw('user_id='.$user_id)
				->groupBy('user_id');
		
		if($between)
			$scores = $scores->whereRaw("scores.created_at BETWEEN '".$between['min']."' and '".$between['max']."'");
		elseif($before)
			$scores = $scores->whereRaw("scores.created_at <= DATE_SUB(NOW(), interval $before )");	
		elseif($periode)
			$scores = $scores->whereRaw("scores.created_at>=DATE_SUB(NOW(), interval 1 $periode )");
		
		if($corpus_id)
			$scores = $scores->whereRaw('scores.corpus_id='.$corpus_id);
		
		$sqlRank = DB::table(DB::raw("({$scores->toSql()}) as users_scores"))
			->mergeBindings($scores->getQuery())
			->leftJoin('scores as score_rank',function($join) use($periode,$before,$corpus_id,$between) {
				if($between){
					$join->on('score_rank.created_at','>=',DB::raw("'".$between['min']."'"));
					$join->on('score_rank.created_at','<=',DB::raw("'".$between['max']."'"));
				} elseif($before)
					$join->on('score_rank.created_at','<=',DB::raw("DATE_SUB(NOW(), interval $before )"));
				elseif($periode)
					$join->on('score_rank.created_at','>=',DB::raw("DATE_SUB(NOW(), interval 1 $periode )"));

				if($corpus_id)
					$join->on('score_rank.corpus_id','=',DB::raw($corpus_id));
				$join->on(DB::raw('1'),'>=',DB::raw('1'));

			})
			->select('score_rank.user_id as sup','users_scores.user_score as user_score','users_scores.username','users_scores.user_id as user_id')

			->havingRaw('sum(score_rank.points) >= users_scores.user_score')
			->groupBy('score_rank.user_id','users_scores.user_id');
		
		if($between)
			$scores = $scores->whereRaw("score_rank.created_at BETWEEN '".$between['min']."' and '".$between['max']."'");				
		elseif($before)
			$sqlRank = $sqlRank->whereRaw("score_rank.created_at <= DATE_SUB(NOW(), interval $before )");	
		elseif($periode)
			$sqlRank = $sqlRank->whereRaw("score_rank.created_at>=DATE_SUB(NOW(), interval 1 $periode )");
 
		if($corpus_id)
			$sqlRank = $sqlRank->whereRaw('score_rank.corpus_id='.$corpus_id);

		$scores_ranked = DB::table(DB::raw("({$sqlRank->toSql()}) as scores"))
			->select(DB::raw('count(distinct sup) as rank'), 'scores.user_score as score', 'scores.username', 'scores.user_id' )
			->groupBy('user_id')->orderBy('rank', 'asc');

		return $scores_ranked->first();

	}

	/**
	 * 
	 *
	 * @return array
	 */
	public function getByUserAndChallenge($user,$challenge)
	{
		$user_id = $user->id;
		$scores = $this->model->join('users','scores.user_id','=','users.id')
				->select('username','user_id',DB::raw('sum(points) as user_score'))
				->whereRaw('user_id='.$user_id)
				->whereRaw("scores.created_at>='".$challenge->start_date."'")
				->whereRaw("scores.created_at<='".$challenge->end_date."'")
				->whereRaw('corpus_id='.$challenge->corpus_id)
				->groupBy('user_id');

		$sqlRank = DB::table(DB::raw("({$scores->toSql()}) as users_scores"))
			->mergeBindings($scores->getQuery())
			->leftJoin('scores as score_rank',function($join) use($challenge) {
				$join->on('score_rank.corpus_id','=',DB::raw($challenge->corpus_id));
				$join->on(DB::raw('1'),'>=',DB::raw('1'));

			})
			->select('score_rank.user_id as sup','users_scores.user_score as user_score','users_scores.username','users_scores.user_id as user_id')

			->havingRaw('sum(score_rank.points) >= users_scores.user_score')
			->groupBy('score_rank.user_id','users_scores.user_id');

		$sqlRank = $sqlRank->whereRaw("score_rank.corpus_id = $challenge->corpus_id")
				->whereRaw("score_rank.created_at>='$challenge->start_date'")
				->whereRaw("score_rank.created_at<='$challenge->end_date'");

		$scores_ranked = DB::table(DB::raw("({$sqlRank->toSql()}) as scores"))
			->select(DB::raw('count(distinct sup) as rank'), 'scores.user_score as score', 'scores.username', 'scores.user_id' )
			->groupBy('user_id')->orderBy('rank', 'asc');

		return $scores_ranked->first();

	}

	/**
	 * 
	 *
	 * @return array
	 */
	public function neighborsByRelation($user,$relation,$take=1)
	{
		$date = date('Y-m-d');
		$score_user = $this->model
			->select(DB::raw('ifnull(points,0) as points'))
			->where('relation_id','=',$relation->id)
			->where('user_id','=',$user->id)
			->where('created_at','=',$date)
			->first();
		$score_user = ($score_user)?$score_user->points:0;
		$scores = [
			'sup' => $this->neighborsRelationByRange('sup',$user,$relation,$score_user,$date,$take),
			'inf' => $this->neighborsRelationByRange('inf',$user,$relation,$score_user,$date,$take),
		];
		return $scores;
	}
	
	/**
	 * 
	 *
	 * @return array
	 */
	public function neighborsRelationByRange($range, $user,$relation=null,$score_user,$date,$take=1)
	{
		$neighbors = $this->model->join('users','scores.user_id','=','users.id')
				->select('username','points as score')
				->where('scores.created_at','=',$date)
				->whereNull('users.deleted_at')
				->where('relation_id','=',$relation->id)
				->havingRaw('points >0 ')
				->groupBy('user_id')
				->orderBy('points', 'asc');
				
		if($range=='sup')
			$neighbors = $neighbors->selectRaw(DB::raw('cast(points-'.$score_user.'.0 as unsigned) as points'));
		else
			$neighbors = $neighbors->selectRaw('cast('.DB::raw($score_user.'.0-points as unsigned) as points'));
		
		return $neighbors->take($take)->get();
	}

	
}
