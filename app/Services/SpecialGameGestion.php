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
use Gwaps4nlp\Core\Models\Source;
use Gwaps4nlp\Core\Models\ConstantGame;
use App\Models\Relation;
use App\Models\Score;
use App\Models\User;
use App\Models\Article;
use App\Exceptions\GameException;
use Response, View, App;

class SpecialGameGestion extends GameGestion
{

	public $relation_id;

	public $relation_special_id;

	public $annotation_id;

	public $type_gain = 'points';

	public $effect;

	public $relation;

	public $mode = 'special';

	protected $spell;

	protected $fillable = ['turn', 'nb_turns', 'gain', 'type_gain', 'spell','in_progress','current_relation_id','relation_id','annotation_id','money_spent','money_earned','points_earned','effect','nb_successes','relations','corpus_id','trophies','bonuses'];

	protected $visible = ['turn', 'nb_turns', 'gain', 'spell', 'user','annotation','loot','attempts','html','neighbors','trophy','bonus'];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['neighbors'];

	public function __construct(Request $request,
		AnnotationRepository $annotations,
		AnnotationUserRepository $annotation_users,
		ScoreRepository $scores,
		RelationRepository $relations,
		CorpusRepository $corpora){

		$this->request = $request;

		if($this->request->hasSession()) {
			if($this->request->session()->has("game.corpus_id")){
				$this->set("corpus_id", $this->request->session()->get("game.corpus_id"));
			} else {
				$this->set("corpus_id", ConstantGame::get('default-corpus'));
			}
		}

		parent::__construct($request,$annotations,$annotation_users,$scores,$relations,$corpora);

	}

	/**
	 * set relation
	 *
	 * @param integer $relation_id
	 *
	 * @throws GameException[if relation isn't found]
	 * @throws GameException[if relation isn't a special mode]
	 * @throws GameException[if the user didn't do the training]
	 *
	 * @return void
	 */
	public function loadRelation($relation_id){
		$this->relation = $this->relations_repo->getById($relation_id);

		if($this->relation->type!='special')
			throw new GameException("Mode de jeu inconnu");

		$relations_slug = explode('/',$this->relation->slug);
		$relations=[];
		foreach($relations_slug as $relation_slug){

			$relation = $this->relations_repo->getByUser($this->user, null, $relation_slug);

			if( $relation->tutorial < ConstantGame::get("turns-training") )
				throw new GameException("Tu dois faire la formation");

			$relations[]=$relation;
		}

		$this->set('relation_id',$this->relation->id);
		$this->set('relations',$relations);
	}

	public function loadContent(){

		if($this->annotation_id&&$this->current_relation){
			$this->annotation=$this->annotations->get($this->annotation_id,$this->current_relation,$this->user);
		}

		if(!$this->annotation){
			$index = rand(0,count($this->relations)-1);
			$relation = new Relation($this->relations[$index]);

			$this->set('current_relation_id',$relation->id);
			if(rand(0,100)>=$this->user->stat($relation)->percent)
			// if(0>=$this->user->stat($relation)->percent)
				$this->annotation = $this->annotations->getRandomReference($relation,$this->user);
			else
				$this->annotation = $this->annotations->getRandomPreAnnotated($relation,$this->user);
        }

        if(!$this->annotation){
        	$this->deleteInProgress();
        	$this->set('html',View::make('partials.game.no-sentences')->render());
        }
        else {
			$this->set('annotation_id',$this->annotation->id);
			$this->computeGain();
			$this->addSpell();
	        $this->inProgress();
	        $this->annotation->addVisible(array('word_position','governor_position'));
    	}

	}

	public function jsonAnswer(){

		$this->processAnswer();

        $reponse = array(
                 'answer' => $this->request->input('relation_id'),
                 'expected_answers' => [strval($this->annotation->relation_id)],
                 'reference' => ($this->annotation->source_id==Source::getReference()->id)?1:0,
                 'nb_turns' => $this->nb_turns,
                 'turn' => $this->turn,
                 'level_user' => $this->user->level->id,
				 'next_level'=> $this->next_level,
                 'image_level'=> $this->user->level->image,
				 'loot'=> $this->loot,
				 'trophy'=> $this->trophy,
				 'bonus'=> $this->bonus,
				 'errors'=> $this->errors,
				 'mode'=> $this->mode,
				 );

        return Response::json($reponse);

	}

	public function addSpell(){

	}

	public function isOver(){
		return ($this->turn>=$this->nb_turns);
	}

	public function processAnswer(){

		$this->annotation_users->saveByRelation($this->user, $this->annotation, $this->request->input('relation_id'));

		$score_multiplier = $this->annotation_users->score_multiplier;

		if($score_multiplier==1)
			$this->increment('nb_successes');

		$this->gain*=$score_multiplier;

		$this->set('errors',$this->annotation_users->error);

		$this->addGain();
		$this->addLoot();

		$this->incrementTurn();


		if($this->in_progress && $inProgress = AnnotationInProgress::where('user_id','=',$this->user->id)
			->where('relation_id','=',$this->relation_id)->first()
		){
			$inProgress->annotation_id=null;
			$inProgress->save();
		}

		$this->set('annotation_id',null);
		$this->set('spell',null);
		$this->set('effect',0);

	}

}
