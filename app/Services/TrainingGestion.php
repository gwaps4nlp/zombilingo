<?php

namespace App\Services;

use Gwaps4nlp\Core\Game;
use Illuminate\Http\Request;
use Gwaps4nlp\Core\Models\ConstantGame;
use App\Models\AnnotationInProgress;
use App\Repositories\AnnotationRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\RelationRepository;
use App\Repositories\TutorialRepository;
use Gwaps4nlp\Core\Exceptions\GameException;
use Response, View;

class TrainingGestion extends Game 
{
	
	public $relation_id;
	
	public $annotation_id;
	
	// public $relation;
	
	public $mode = 'training';
	
	// protected $in_progress = false;

	protected $fillable = ['turn', 'nb_turns', 'gain', 'in_progress','relation_id','annotation_id','already_played'];

	protected $visible = ['turn', 'nb_turns','annotation','html'];
	
	public function __construct(Request $request, 
			AnnotationRepository $annotations, 
			AnnotationUserRepository $annotation_users,
			RelationRepository $relations, 
			TutorialRepository $tutorials){

		parent::__construct($request);

		$this->relations_repo=$relations;
		$this->annotations = $annotations;
		$this->annotation_users = $annotation_users;
		$this->tutorials = $tutorials;
		$this->annotation = null;

		$this->user = auth()->user();

		if($this->relation_id){
			$this->relation=$this->relations_repo->getById($this->relation_id);
			if($this->annotation_id){
				$this->annotation=$this->annotations->get($this->annotation_id,$this->relation);
			}			
		}

	}
	
	public function begin(Request $request, $relation_id){
		$this->loadSession($request);
        $this->relation = $this->relations_repo->getByUser($this->user,$relation_id);

		if($this->relation->level_id > $this->user->level_id)
			throw new GameException(trans('game.you-havent-the-required-level'));

		$this->set('relation_id',$this->relation->id);
		$this->set('annotation_id',null);
		$this->set('already_played',array());		
		$this->set('turn',0);
		$this->set('attempts',0);
		$this->set('perfect',1);
		$this->set('in_progress',1);
		$this->set('nb_turns',ConstantGame::get("turns-".$this->mode));

	}

	public function loadContent(){

		$this->annotation = $this->annotations->getRandomTutorial($this->relation,(int)($this->turn/2)+1,$this->already_played);

		if(!$this->annotation && rand(0,100)<=ConstantGame::get("proba-negative-item-training"))
			$this->annotation = $this->annotations->getRandomNegativeReference($this->relation);
		
		if(!$this->annotation){
			$this->annotation = $this->annotations->getRandomReference($this->relation);
		}
		
        if(!$this->annotation){
         	$this->set('html',View::make('partials.game.no-sentences')->render());
        }

        else {
			$this->set('annotation_id',$this->annotation->id);
			$this->pushAttr('already_played',$this->annotation->id);
    	}
		return $this->annotation;
	}

	public function loadSession(Request $request){
		parent::loadSession($request);

		if($this->relation_id){
			$this->relation=$this->relations_repo->getById($this->relation_id);
			if($this->annotation_id){
				$this->annotation=$this->annotations->get($this->annotation_id,$this->relation);
			}			
		}

	}

	public function jsonAnswer(Request $request){
		$this->loadSession($request);
		$this->processAnswer();
		$nb_messages = ($this->annotation->discussion)? $this->annotation->discussion->messages()->count():0;
        $reponse = array (
			'answer' => $this->request->input('word_position'),
			'explication' => $this->explication,
			'expected_answers' => $this->annotation->expected_answers,
			'reference' => 1,
			'nb_turns' => $this->nb_turns,
			'turn' => $this->turn,
			'nb_messages' => $nb_messages,
			'annotation' => $this->annotation,
		);
        return Response::json($reponse);
	}
	
	public function end(){
		$tutorials_done = $this->tutorials->countDone($this->user);
		$this->checkTrophy('training', $tutorials_done);
		$this->set('annotation_id',0);
		$this->set('in_progress',0);
	}
	
	public function processAnswer(){

		$this->annotation_users->saveAnnotationTraining($this->user, $this->annotation, $this->request->input('word_position'));

        if($this->annotation->expected_answers->contains($this->request->input('word_position'))){
            $this->incrementTurn();
            $this->tutorials->saveCorrectAnswer($this->user->id, $this->relation->id); 
        } else {
			$this->explication = $this->annotations->toString($this->annotation->sentence_id,$this->request->input('word_position'));
		}
		
		$this->set('annotation_id',null);
		
	}
	
}
