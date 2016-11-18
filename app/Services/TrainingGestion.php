<?php

namespace App\Services;


use Illuminate\Http\Request;
use App\Models\ConstantGame;
use App\Models\AnnotationInProgress;
use App\Services\Game;
use App\Repositories\AnnotationRepository;
use App\Repositories\RelationRepository;
use App\Repositories\TutorialRepository;
use App\Exceptions\GameException;	
use Response, View;

class TrainingGestion extends Game 
{
	
	public $relation_id;
	
	public $annotation_id;
	
	// public $relation;
	
	public $mode = 'training';
	
	// protected $in_progress = false;

	protected $fillable = ['turn', 'nb_turns', 'gain', 'in_progress','relation_id','annotation_id','already_played','rien'];

	protected $visible = ['turn', 'nb_turns','annotation','html'];
	
	public function __construct(Request $request, AnnotationRepository $annotations, RelationRepository $relations, TutorialRepository $tutorials){

		parent::__construct($request);

		$this->relations_repo=$relations;
		$this->annotations = $annotations;
		$this->tutorials = $tutorials;
		$this->annotation = null;
		$this->already_played=[];
		$this->user = auth()->user();

		if($this->relation_id){
			$this->relation=$this->relations_repo->getById($this->relation_id);
			if($this->annotation_id){
				$this->annotation=$this->annotations->get($this->annotation_id,$this->relation);
			}
		} 
	}
	
	public function begin($relation_id){

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
			$this->annotation = $this->annotations->getRandomNegative($this->relation);
		
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

	public function jsonAnswer(){
		$this->processAnswer();
        $reponse = array('answer' => $this->request->input('word_position'),
                 'explication' => $this->explication,
                 'expected_answers' => $this->annotation->expected_answers,
                 'nb_messages' => $this->annotation->messages()->count(),				 
                 'reference' => 1,
                 'nb_turns' => $this->nb_turns,
                 'turn' => $this->turn,
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

        if($this->annotation->expected_answers->contains($this->request->input('word_position'))){
            $this->incrementTurn();
            $this->tutorials->saveCorrectAnswer($this->user->id, $this->relation->id); 
        } else {
			$this->explication = $this->annotations->toString($this->annotation->sentence_id,$this->request->input('word_position'));
		}

		$this->set('annotation_id',null);
		
	}
	
}
