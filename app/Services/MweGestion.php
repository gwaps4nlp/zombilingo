<?php

namespace App\Services;


use Illuminate\Http\Request;
use Gwaps4nlp\Core\Models\ConstantGame;
use App\Models\AnnotationInProgress;

use App\Exceptions\GameException;
use Gwaps4nlp\Core\Game;
use App\Repositories\MweRepository;
use Response, View;

class MweGestion extends Game implements GameGestionInterface
{

	public $mwe_id;
	
	public $mode = 'mwe';

	protected $fillable = ['turn', 'nb_turns', 'mwe_id', 'enabled' ];

	protected $visible = ['turn', 'nb_turns','mwe','html'];
	
	public function __construct(Request $request, MweRepository $mwes){

		parent::__construct($request);
		$this->mwes=$mwes;
	}
	
	public function begin(Request $request, $mwe_id){
        $this->loadSession($request);
		if($this->user->last_mwe+ConstantGame::get('time-mwe') > time()|| !$this->enabled)
			throw new GameException("Tu ne peux pas accéder à ce jeu pour l'instant.");
		if($this->request->ajax()){
			$this->user->end_mwe = time() + ConstantGame::get('length-mwe');
			$this->user->last_mwe = time();
			$this->user->save();
			$this->set('enabled',0);
		}
		$this->set('mwe_id',null);
		$this->set('turn',0);
		$this->set('attempts',0);

	}

	public function loadContent(){
		$this->mwe = $this->mwes->getRandom();
        if(!$this->mwe){
         	$this->set('html',View::make('partials.game.no-sentences')->render());
        }
        else {
			$this->set('mwe_id',$this->mwe->id);
    	}
		return $this->annotation;
	}

	public function jsonAnswer(Request $request){
		$this->loadSession($request);
		$this->processAnswer();
        $reponse = array(
			'nb_turns' => $this->nb_turns,
			'turn' => $this->turn,
		);

        return Response::json($reponse);
	}
	
	public function end(){
		$this->user->increment('number_mwes');
		$this->checkTrophy('mwe', $this->user->number_mwes);
		$this->set('mwe_id',0);
		$this->set('turn',0);
		$this->set('in_progress',0);
	}
	
	public function processAnswer(){
		if($this->request->input('mwe_id')!=$this->mwe_id)
			throw new GameException("Tu as une autre partie en cours");
		if($this->request->input('frozen')=="1")
			$this->mwe->increment('frozen'); 
		elseif($this->request->input('frozen')=="0")
			$this->mwe->increment('unfrozen'); 
		elseif($this->request->input('frozen')=="skip")
			$this->mwe->increment('skipped');
		else
			throw new GameException("Réponse incorrecte");
		
		$this->incrementTurn();
		$this->set('mwe_id',null);
		
	}
	
	public function isOver(){
		return ($this->user->end_mwe<time());
	}

	public function loadSession(Request $request){
		parent::loadSession($request);
		if($this->mwe_id){
			$this->mwe=$this->mwes->getById($this->mwe_id);
		}
	}
}
