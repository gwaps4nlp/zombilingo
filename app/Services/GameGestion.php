<?php

namespace App\Services;

use Gwaps4nlp\Core\Game;
use Illuminate\Http\Request;
use App\Models\AnnotationInProgress;
use App\Repositories\RelationRepository;
use App\Repositories\AnnotationRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\ScoreRepository;
use App\Repositories\ObjectRepository;
use App\Repositories\CorpusRepository;
use App\Repositories\ChallengeRepository;
use Gwaps4nlp\Core\Models\Source;
use Gwaps4nlp\Core\Models\ConstantGame;
use App\Models\Relation;
use App\Models\Score;
use App\Models\User;
use App\Models\Object;
use App\Models\Corpus;
use Gwaps4nlp\Core\Exceptions\GameException;
use Response, View, App;
use App\Events\BroadCastNewAnnotation;
use App\Events\ScoreUpdated;
use Gwaps4nlp\Core\GameGestionInterface;
use Event, Auth;

class GameGestion extends Game implements GameGestionInterface
{
	
	public $relation_id;
	
	public $annotation_id;

	public $type_gain = 'points';

	public $effect;
	
	public $relation;

	public $mode = 'game';

	protected $spell;

	protected $fillable = ['turn', 'nb_turns', 'gain', 'type_gain', 'spell','in_progress','relation_id','current_relation_id','annotation_id','money_spent','money_earned','points_earned','effect','nb_successes','reference_again','next_level','corpus_id','trophies','bonuses','already_spell','already_played','default_corpus_id','challenge_id'];

	protected $visible = ['turn', 'nb_turns', 'gain', 'spell', 'user','annotation','loot','attempts','html','neighbors','trophy','bonus','next_level'];
	
	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['neighbors'];
	
	public function __construct(Request $request, 
		AnnotationRepository $annotations,
		ChallengeRepository $challenges,
		AnnotationUserRepository $annotation_users, 
		ScoreRepository $scores, 
		RelationRepository $relations,
		CorpusRepository $corpora
		){

		parent::__construct($request);

		$this->scores=$scores;
		$this->relations_repo=$relations;
		$this->annotations = $annotations;
		$this->challenges = $challenges;
		$this->annotation_users = $annotation_users;
		$this->corpora = $corpora;
		$this->annotation = null;
		$this->html = null;
		$this->loot = null;

		$this->user = Auth::user();
	}
	
	public function begin(Request $request, $relation_id){
        $this->loadSession($request);
		$this->loadRelation($relation_id);

		$this->set('annotation_id',null);
		$this->set('already_played',array());
		$this->set('turn',0);
		$this->set('points_earned',0);
		$this->set('trophy',null);
		$this->set('bonus',null);
		$this->set('trophies',array());
		$this->set('bonuses',array());
		$this->set('money_earned',0);
		$this->set('money_spent',0);
		$this->set('errors',0);
		$this->set('nb_successes',0);
		$this->set('effect',0);
		$this->set('next_level',0);
		$this->set('mwe',0);
		$this->set('already_spell',false);
		$challenge=$this->challenges->getOngoing();
		if($challenge && $challenge->corpus_id == $this->corpus_id)
			$this->set('challenge_id',$challenge->id);
		else
			$this->set('challenge_id',null);
		$this->set('nb_turns',ConstantGame::get("turns-".$this->mode));
		$this->set('default_corpus_id',ConstantGame::get("default-corpus"));

		if($inProgress = $this->getInProgressCurrentRelation()){
			$this->set('annotation_id',$inProgress->annotation_id );
			$this->set('turn',$inProgress->turn);
			if($inProgress->annotation_id)
				$this->annotation = $this->annotations->getById($inProgress->annotation_id);
		}
	}
	
	public function loadRelation($relation_id){
		$this->relation = $this->relations_repo->getByUser(Auth::user(),$relation_id);
		if(!in_array($this->relation->type,['trouverTete','trouverDependant']))
			throw new GameException(trans('game.unknown-playing-mode'));
		if( $this->relation->level_id > $this->user->level_id )
			throw new GameException(trans('game.you-havent-the-required-level'));
		if( $this->relation->tutorial < ConstantGame::get("turns-game") )
			throw new GameException(trans('game.do-the-training',['name'=>$this->relation->name]));
		if( $this->relation->todo == 0 )
			throw new GameException(trans('game.play-all-sentences'));
			
		$this->set('current_relation_id',$this->relation->id);
		$this->set('relation_id',$this->relation->id);
	}

	public function loadSession(Request $request){
		parent::loadSession($request);

		if($this->relation_id){
			$this->relation=Relation::findOrFail($this->relation_id);
		}

		if($this->corpus_id){
            $this->corpus = Corpus::find($this->corpus_id);
            if($this->corpus){
                $this->annotations->setCorpus($this->corpus);
                $this->relations_repo->setCorpus($this->corpus);
            }
		}

		if($this->current_relation_id && $this->user){
			$this->current_relation=$this->relations_repo->getByUser($this->user,$this->current_relation_id);
			if($this->annotation_id){
				$this->annotation=$this->annotations->get($this->annotation_id,$this->current_relation);
			}
		}

	}
	
	public function loadContent(){
		
		if($this->annotation_id){
			$this->annotation=$this->annotations->get($this->annotation_id,$this->current_relation,$this->user);
		}

		if(!$this->annotation){

			if($this->reference_again || rand(0,100)>=$this->user->stat($this->current_relation->id)->percent){
				
				if(rand(0,100)<=ConstantGame::get("proba-negative-item-game"))
					$this->annotation = $this->annotations->getRandomNegativeReference($this->current_relation,$this->user);

				if(!$this->annotation)
					$this->annotation = $this->annotations->getRandomReference($this->current_relation,$this->user);


				if(!$this->annotation)
					$this->annotation = $this->annotations->getRandomReference($this->current_relation);

				if(!$this->annotation){
					$this->annotations->setCorpus(null);
					$this->annotation = $this->annotations->getRandomReference($this->current_relation);
				}

			} else {
				// if(!$this->corpus->is_playable){
				// 	$this->annotation = $this->annotations->getRandomPreAnnotated($this->current_relation);	
				// } else {
					$this->annotation = $this->annotations->getRandomPreAnnotated($this->current_relation,$this->user);
				// }
			}
        }
			
		$this->set('reference_again',0);
		
        if(!$this->annotation){
			$this->deleteInProgress();	
        	$this->set('html',View::make('partials.game.no-sentences')->render());
        } else {
			$this->set('annotation_id',$this->annotation->id);
			$this->pushAttr('already_played',$this->annotation->id);
			$this->computeGain();
			$this->addSpell();
	        $this->inProgress();
    	}

	}

	public function addSpell(){

        if (!$this->already_spell && $this->user->level->id > 1) {
            if(rand(0,100) < ConstantGame::get("proba-vanishing")){
                $this->set('spell',"vanish");
                $this->set('already_spell',true);
            } else {
                if(rand(0,100) < ConstantGame::get("proba-shrink") && $this->user->level->id > 2){
                	$this->set('spell',"shrink");
                	$this->set('already_spell',true);
                 }
            }
        }
	}

	public function addLoot(){

		$proba_loot = ConstantGame::get("proba-object");

		if($bonus = $this->user->bonuses()->where('slug','=','increase-proba-object')->first())
			$proba_loot *= $bonus->multiplier;
		if(rand(0,100) <= $proba_loot){

			$object_id = ObjectRepository::getRandomId();

			$object = $this->user->inventaire()->find($object_id);

			if($object->object_user_id)
				$this->user->objects()->updateExistingPivot($object->id, ['quantity'=>$object->quantity+1]);
			else
				$this->user->objects()->save($object, ['quantity'=>1]);

			$this->user->increment('number_objects');

			$this->checkTrophy('number_objects', $this->user->number_objects);

			$this->loot = $object;
		}	

	}

	public function jsonAnswer(Request $request){
		$this->loadSession($request);
		$this->processAnswer();
		$nb_messages = ($this->annotation->discussion)? $this->annotation->discussion->messages()->count():0;
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
				'errors'=> $this->errors,
				'nb_messages' => $nb_messages,
				'annotation' => $this->annotation,
				 );

        return Response::json($reponse);

	}

	public function inProgress(){
		
		$inProgress = AnnotationInProgress::firstOrCreate(array('user_id'=>$this->user->id,'relation_id'=>$this->relation_id,'corpus_id'=>$this->corpus_id));		
		
		if($this->annotation){
			$this->set('annotation_id',$this->annotation->id);
			$this->set('in_progress',1);
			$this->set('expected_answer',$this->annotation->expected_answer);
			$this->set('focus',$this->annotation->focus);
			$inProgress->annotation_id = $this->annotation->id;
			$inProgress->turn = $this->turn;
			$inProgress->save();
		} else {
			$inProgress->delete();
		}
		
	}
	
	public function isInProgress(){
		
		return AnnotationInProgress::where('user_id','=',$this->user->id)->exists();
		
	}
	
	public function getInProgress(){
		
		return AnnotationInProgress::join('corpuses','corpuses.id','=','annotation_in_progress.corpus_id')
			->where('user_id','=',$this->user->id)
			->where('corpuses.playable','=',1)
			->get();
		
	}

	public function getInProgressCurrentRelation(){
		
		return AnnotationInProgress::where('user_id','=',$this->user->id)->where('relation_id','=',$this->relation_id)->where('corpus_id','=',$this->corpus_id)->first();
		
	}
	
	public function deleteInProgress(){
		
		if($inProgress = $this->getInProgressCurrentRelation()){
			$inProgress->delete();
		}
		
	}
	
    protected function computeGain(){

        $difficulty = $this->annotation->sentence->difficulty;
		$coeff=$this->current_relation->level_id;
        $gain = $difficulty * ConstantGame::get("multiplier-gain") * $coeff;

        if($this->turn == ($this->nb_turns - 1)){
            $gain *= ConstantGame::get("multiplier-boss");
        }
        $gain = intval($gain);
		$this->set('gain',$gain);

	}
	
    public function decrementMoney($money){

		$this->user->decrement('money',$money);
		$this->increment('money_spent',$money);

	}
	
    public function addGain(){
    	if($this->errors) return;
    	// if($this->errors || !$this->corpus->is_playable) return;
		$this->user->increment('money',ConstantGame::get("gain-sentence"));
		$this->increment('money_earned',ConstantGame::get("gain-sentence"));
		$this->user->increment('score',ConstantGame::get("gain-sentence"));
		$this->increment('points_earned',ConstantGame::get("gain-sentence"));
      
		if($this->type_gain == 'money' ){
			$this->user->increment('money', $this->gain);
			$this->increment('money_earned', $this->gain);
			$this->set('type_gain', 'points');
		} else {
			$nextLevel = $this->user->level->getNext();
			if($nextLevel && ($this->user->score+$this->gain)>=$nextLevel->required_score){
				$this->set('next_level',1);
				$this->user->level_id = $nextLevel->id;
				$this->user->save();
				$this->checkBonus('level-'.$this->user->level_id);
			}

			$this->user->increment('score', $this->gain);
			$this->increment('points_earned', $this->gain);
			$date = date('Y-m-d');

			$score = Score::firstorCreate(array(
				'user_id'=>$this->user->id,
				'corpus_id'=>$this->corpus_id,
				'created_at'=>"$date",
				'relation_id'=>$this->relation_id
				));
				
			$score->increment('points',$this->gain);

		}

    }

	public function isOver(){
		return ($this->turn>=$this->nb_turns || $this->current_relation->todo == 0);
	}

	public function end(){
		
		$this->deleteInProgress();

		if($this->current_relation->todo == 0){
			$this->checkTrophy($this->corpus->name, $this->user->perfect);
		}

		$this->user->increment('won');
		$this->checkTrophy('won', $this->user->won);
		
		if($this->nb_successes==$this->nb_turns)
			$this->user->increment('perfect');
		
		$this->checkTrophy('perfect', $this->user->perfect);
		
		$this->relation = $this->relations_repo->getByUser($this->user,$this->relation_id);
		
		if(rand(0,100)<=ConstantGame::get("proba-mwe")){
			$this->mwe=1;
			session()->put('mwe.enabled',1);
		}
		
		$this->set('annotation_id',0);
		$this->set('in_progress',0);

	}
	
	public function processAnswer(){
		// if($this->corpus->is_playable){
			$this->annotation_users->save($this->user, $this->annotation, $this->request->input('word_position'),$this->current_relation);
			
			$this->corpus->increment('number_answers',1);
			if($this->corpus_id == $this->default_corpus_id)
				Event::fire(new BroadCastNewAnnotation($this->corpus->number_answers));
			
			$score_multiplier = $this->annotation_users->score_multiplier;
			
			if($score_multiplier==1)
				$this->increment('nb_successes');

			$this->gain*=$score_multiplier;

			$this->gain = intval($this->gain);

			Event::fire(new ScoreUpdated($this->user, $this->gain,1,$this->challenge_id));

			if($this->annotation_users->error)
				$this->set('reference_again',1);
			if($this->annotation_users->error==3)
				$this->deleteInProgress();
			
			$this->set('errors',$this->annotation_users->error);
			
		
			if($this->in_progress && $inProgress = AnnotationInProgress::where('user_id','=',$this->user->id)
				->where('relation_id','=',$this->relation_id)->where('corpus_id','=',$this->corpus_id)->first()
			){
				$inProgress->annotation_id=null;
				$inProgress->save();
			}
		// }
		$this->addGain();
		$this->addLoot();

		$this->incrementTurn();

		$this->set('annotation_id',null);
		$this->set('spell',null);
		$this->set('effect',0);
		
	}

	public function hasSpell($spell){
		return ($this->spell==$spell);
	}
	
	public function cancelSpell(){
        $this->set('spell',null);
	}
	
	public function inventaire(){
		return Auth::user()->inventaire()->get();
	}
	
	public function getNeighborsAttribute(){
		return $this->scores->neighborsByRelation($this->user,$this->relation);
	}

}
