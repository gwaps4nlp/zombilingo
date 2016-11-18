<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\AnnotationInProgress;
use App\Repositories\RelationRepository;
use App\Repositories\AnnotationRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\ScoreRepository;
use App\Repositories\ObjectRepository;
use App\Repositories\CorpusRepository;
use App\Repositories\DuelRepository;
use App\Models\Source;
use App\Models\ConstantGame;
use App\Models\Relation;
use App\Models\Score;
use App\Models\User;
use App\Models\Duel;
use App\Exceptions\GameException;
use Response, View, App;

class DuelGestion extends Game implements GameGestionInterface
{
	
	public $relation_id;
	
	public $annotation_id;

	public $type_gain = 'points';

	public $effect;
	
	public $relation;
	
	public $mode = 'duel';

	protected $fillable = ['turn', 'nb_turns', 'gain', 'type_gain', 'in_progress','relation_id','current_relation_id','annotation_id','points_earned','duel_id'];

	protected $visible = ['turn', 'nb_turns', 'gain', 'user','annotation','html'];
	
	public function __construct(Request $request, 
		AnnotationRepository $annotations,
		DuelRepository $duels,
		AnnotationUserRepository $annotation_users, 
		ScoreRepository $scores, 
		RelationRepository $relations
		){

		parent::__construct($request);

		$this->scores=$scores;
		$this->duels=$duels;
		$this->relations_repo=$relations;
		$this->annotations = $annotations;
		$this->annotation_users = $annotation_users;
		$this->annotation = null;
		$this->html = null;
		$this->user = auth()->user();

		if($this->duel_id){
			$this->duel=Duel::find($this->duel_id);
		}

		if($this->annotation_id){
			$this->annotation=$this->annotations->get($this->annotation_id);
		}

	}
	
	public function begin($duel_id){
        
		$this->loadDuel($duel_id);
		$turn = $this->duel->annotation_users()->where('annotation_user_duel.user_id',$this->user->id)->count();
		$this->set('annotation_id',null);
		$this->set('turn',$turn);
		$this->set('points_earned',0);
		$this->set('trophy',null);
		$this->set('bonus',null);
		$this->set('trophies',array());
		$this->set('bonuses',array());
		$this->set('errors',0);
		$this->set('nb_successes',0);
		$this->set('next_level',0);
		$this->set('nb_turns',$this->duel->nb_turns);

	}
	
	public function loadDuel($duel_id){
		$this->duel = $this->duels->getById($duel_id,$this->user);
		$this->relation = $this->relations_repo->getByUser($this->user, $this->duel->relation_id);
		$this->set('duel_id',$this->duel->id);
		$this->set('relation_id',$this->duel->relation_id);
		if( $this->relation->tutorial < ConstantGame::get("turns-game") )
			throw new GameException(trans('game.do-the-training',['name'=>$this->relation->name]).'<br/><a style="text-decoration:underline;" href="'.url('game/training/begin',[$this->relation->id]).'"">Faire la formation maintenant.</a>');
	}
	
	public function loadContent(){

		$this->annotation = $this->annotations->getNextAnnotationDuel($this->duel,$this->user);

        if(!$this->annotation){
        	$this->set('html',View::make('partials.game.no-sentences')->render());
        } else {
			$this->set('annotation_id',$this->annotation->id);
			$this->computeGain();
    	}

	}

	public function jsonAnswer(){

		$this->processAnswer();

        $reponse = array('answer' => $this->request->input('word_position'),
                 'expected_answers' => $this->annotation->expected_answers,
                 'reference' => ($this->annotation->source_id==Source::getReference()->id)?1:0,
                 'nb_turns' => $this->nb_turns,
                 'turn' => $this->turn,
                 'points_earned' => $this->points_earned,
                 'gain' => $this->gain,
                 'level_user' => $this->user->level->id,
                 'image_level'=> $this->user->level->image,
				 'loot'=> $this->loot,
				 'trophy'=> $this->trophy,
				 'bonus'=> $this->bonus,
				 'errors'=> $this->errors
				 );

        return Response::json($reponse);

	}
	
    protected function computeGain(){

        $difficulty = $this->annotation->sentence->difficulty;
		$coeff=$this->duel->relation->level_id;
        $gain = $difficulty * ConstantGame::get("multiplier-gain") * $coeff;
        $gain = intval($gain);
		$this->set('gain',$gain);

	}
	
	public function isOver(){
		return ($this->turn>=$this->nb_turns);
	}

	public function end(){
		
		$this->relation = $this->relations_repo->getByUser($this->user,$this->relation_id);
		if($this->duel->isOver() && !$this->duel->state!='completed'){

			$this->duel->harmonizeScores();
			$this->duel->computeScores();

			$this->duel->state='completed';
			$this->duel->save();
			$players = $this->duel->users()->orderBy('pivot_score','desc')->get();
			$final_score = 0;
			$rank=1;
			
			foreach($players as $key=>$user){
				$final_score += $user->pivot->score;
				if($key>0 && $user->pivot->score==$players->get($key-1)->pivot->score){
					$user->pivot->rank = $players->get($key-1)->pivot->rank;
				} else
					$user->pivot->rank = $key+1;
				$this->duel->users()->updateExistingPivot($user->id, ['rank'=>$user->pivot->rank]);

				if($user->id != $this->user->id)
					$this->duel->users()->updateExistingPivot($user->id, ['rank'=>$user->pivot->rank]);
			}

			$winners = $this->duel->users()->where('rank',1)->get();
			$losers = $this->duel->users()->where('rank','>',1)->get();
			$score_winners = $this->relation->level_id * $final_score/count($winners);
			foreach($winners as $winner){

				$this->duel->users()->updateExistingPivot($winner->id, ['final_score'=>$score_winners]);
				$winner->score = $winner->score + $score_winners;
				$winner->save();
				if(count($winners)>1)
					$this->duel->users()->updateExistingPivot($winner->id, ['result'=>0]);
				else
					$this->duel->users()->updateExistingPivot($winner->id, ['result'=>1]);
			}
			foreach($losers as $loser){
				$this->duel->users()->updateExistingPivot($loser->id, ['final_score'=>0,'result'=>-1]);
			}
		}

		$this->set('duel_id',null);
		$this->set('annotation_id',null);
		$this->set('in_progress',0);

	}

	public function incrementTurn(){
		
		$turn = $this->duel->annotation_users()->where('annotation_user_duel.user_id',$this->user->id)->count();
		$score = $this->gain + $this->duel->user($this->user)->pivot->score;
		$this->duel->users()->updateExistingPivot($this->user->id, ['turn'=>$turn,'score'=>$score]);
		$this->set('annotation_id',0);
		$this->set('turn',$turn);

	}
	
	public function processAnswer(){
        
		$annotation_user = $this->annotation_users->save($this->user, $this->annotation, $this->request->input('word_position'),$this->duel->relation);
		
		$score_multiplier = $this->annotation_users->score_multiplier;

		$this->gain*=$score_multiplier;

		$this->gain = intval($this->gain);
		$date = date('Y-m-d');

		$this->duel->annotation_users()->save($this->annotation_users->model, ['user_id' => $this->user->id,'answer' => $this->request->input('word_position'),'annotation_id' => $this->annotation->id,'score'=>$this->gain]);

		// $score = AnnotationUserDuel::firstorCreate(array(
		// 	'user_id'=>$this->user->id,
		// 	'annotation_user_id'=>$this->annotation_users->id,
		// 	'annotation_id'=>$this->annotation->id,
		// 	'score'=>$this->gain,
		// 	'created_at'=>"$date"
		// 	));		

		$this->incrementTurn();
		
		$this->set('annotation_id',null);
		
	}

}
