<?php

namespace App\Services;

use Illuminate\Http\Request;
use Gwaps4nlp\Core\Models\ConstantGame;
use Gwaps4nlp\Core\Game;
use Gwaps4nlp\Core\Models\Source;
use Gwaps4nlp\Core\GameGestionInterface;
use App\Models\AnnotationInProgress;
use App\Repositories\RelationRepository;
use App\Repositories\AnnotationRepository;

use Response, View, App;

class DemoGestion extends Game implements GameGestionInterface
{
	
	public $relation_id;
	
	public $annotation_id;

	public $type_gain = 'points';
	
	public $relation;
	
	public $mode = 'demo';

	protected $spell;

	protected $fillable = ['turn', 'nb_turns', 'type_gain', 'gain', 'relation_id','annotation_id','points_earned','already_played'];

	protected $visible = ['turn', 'nb_turns', 'annotation','html','gain'];
	
	public function __construct(Request $request, 
		AnnotationRepository $annotations, 
		RelationRepository $relations){

		parent::__construct($request);
	
		$this->relations_repo=$relations;		
		$this->annotations = $annotations;
		$this->annotation = null;
		$this->html = null;

	}
	
	public function begin(Request $request, $relation_id){

        $this->loadSession($request);
        $this->relation = $this->relations_repo->getBySlug(ConstantGame::get('relation-demo'));
		$this->set('relation_id',$this->relation->id);
		$this->set('annotation_id',null);
		$this->set('already_played',array());
		$this->set('turn',0);
		$this->set('points_earned',0);
		$this->set('nb_turns',ConstantGame::get("turns-".$this->mode));

	}

	public function loadContent(){

		$this->annotation = $this->annotations->getRandomTutorial($this->relation,(int)($this->turn/2)+1,$this->already_played);

		if(!$this->annotation){
			$this->annotation = $this->annotations->getRandomReference($this->relation);
		}

        if(!$this->annotation){
         	$this->set('html',View::make('partials.game.no-sentences')->render());
        }

        else {
			$this->set('annotation_id',$this->annotation->id);
			$this->pushAttr('already_played',$this->annotation->id);
			$this->computeGain();
    	}
	}

	public function jsonAnswer(Request $request){

		$this->loadSession($request);
		$this->processAnswer();

        $reponse = array('answer' => $this->request->input('word_position'),
                 'expected_answers' => $this->annotation->expected_answers,
                 'reference' => 1,
                 'nb_turns' => ConstantGame::get('turns-game'),
                 'turn' => $this->turn,
				 );

        return Response::json($reponse);

	}
	
    private function computeGain(){

        $difficulty = $this->annotation->sentence->difficulty;
        $gain = $difficulty * ConstantGame::get("multiplier-gain");

        if($this->turn == ($this->nb_turns - 1)){
            $gain *= ConstantGame::get("multiplier-boss");
        }
		$this->set('gain',$gain);
	}
	
    public function addGain(){
		$this->increment('points_earned',ConstantGame::get("gain-sentence"));
		$this->increment('points_earned', $this->gain);
    }
	
	public function end(){
		
		$this->set('annotation_id',0);
		$this->set('in_progress',0);

	}
	
	public function processAnswer(){
        
		if($this->annotation->expected_answers->contains($this->request->input('word_position'))){
			$this->addGain();
			$this->incrementTurn();			
		}
		$this->set('annotation_id',null);
		
	}
	public function loadSession(Request $request){
		parent::loadSession($request);
		if($this->relation_id){
			$this->relation = $this->relations_repo->getById($this->relation_id);
			if($this->annotation_id){
				$this->annotation=$this->annotations->get($this->annotation_id,$this->relation);
			}
		}

	}
}