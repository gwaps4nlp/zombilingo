<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\AnnotationInProgress;
use App\Repositories\RelationRepository;
use App\Repositories\AnnotationRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\ScoreRepository;
use App\Repositories\ObjectRepository;
use App\Models\Source;
use App\Models\ConstantGame;
use App\Models\Relation;
use App\Models\Score;
use App\Models\User;
use App\Models\Object;
use App\Exceptions\GameException;
use Response, View, App;

class AdminGameGestion extends GameGestion implements GameGestionInterface
{
	
	public $relation_id;
	
	public $annotation_id;

	public $type_gain = 'points';

	public $effect;
	
	public $relation;
	
	public $mode = 'admin-game';

	protected $spell;

	protected $fillable = ['turn', 'nb_turns', 'gain', 'type_gain', 'spell','in_progress','relation_id','current_relation_id','annotation_id','money_spent','money_earned','points_earned','effect','nb_successes','reference_again'];

	protected $visible = ['turn', 'nb_turns', 'gain', 'spell', 'user','annotation','loot','attempts','html','neighbors','trophy','bonus'];
	
	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['neighbors'];

	public function begin($relation_id){
        
		$this->loadRelation($relation_id);

		if(!$this->request->ajax() && $this->request->has('annotation_id')){
			$this->set('annotation_id',$this->request->input('annotation_id'));
		}
		// else
		// 	$this->set('annotation_id',null);
		$this->set('turn',0);
		$this->set('points_earned',0);
		$this->set('money_earned',0);
		$this->set('money_spent',0);
		$this->set('errors',0);
		$this->set('nb_successes',0);
		$this->set('effect',0);
		$this->set('nb_turns',ConstantGame::get("turns-".$this->mode));

	}

	public function loadRelation($relation_id){
		$this->relation = $this->relations_repo->getByUser($this->user,$relation_id);
		if(!in_array($this->relation->type,['trouverTete','trouverDependant']))
			throw new GameException("Mode de jeu inconnu");
			
		$this->set('current_relation_id',$this->relation->id);
		$this->set('relation_id',$this->relation->id);
	}

	public function loadContent(){

		if($this->annotation_id){
			$this->annotation=$this->annotations->get($this->annotation_id,$this->current_relation);
		}

		if(!$this->annotation){

			if($this->reference_again || rand(0,100)>=$this->user->stat($this->current_relation)->percent)
				// $this->annotation = $this->annotations->get(32009,$this->relation);
				$this->annotation = $this->annotations->getRandomReference($this->current_relation,$this->user);
			else
				// $this->annotation = $this->annotations->get(167601,$this->relation);
				$this->annotation = $this->annotations->getRandomPreAnnotated($this->current_relation,$this->user);
        }
			
		$this->set('reference_again',0);
		
        if(!$this->annotation){	
        	$this->set('html',View::make('partials.game.no-sentences')->render());
        } else {
			$this->set('annotation_id',$this->annotation->id);
			$this->computeGain();
    	}

	}
	
    protected function computeGain(){

        $difficulty = $this->annotation->sentence->difficulty;
		$coeff=1;
        $gain = $difficulty * ConstantGame::get("multiplier-gain") * $coeff;

        if($this->turn == ($this->nb_turns - 1)){
            $gain *= ConstantGame::get("multiplier-boss");
        }
        $gain = intval($gain);
		$this->set('gain',$gain);

	}

	public function getInProgressCurrentRelation(){
		
		return null;
		
	}
	
    public function addGain(){
    	if($this->errors) return;

		$this->increment('money_earned',ConstantGame::get("gain-sentence"));

		$this->increment('points_earned',ConstantGame::get("gain-sentence"));
      
		if($this->type_gain == 'money' ){
			$this->increment('money_earned', $this->gain);
			$this->set('type_gain', 'points');
		} else {
			$this->increment('points_earned', $this->gain);
		}

    }
	
	public function end(){

		$this->relation = $this->relations_repo->getByUser($this->user,$this->relation_id);
		
		$this->set('annotation_id',0);
		$this->set('turn',0);
		$this->set('in_progress',0);

	}
	
	public function processAnswer(){
        
		$this->annotation_users->save($this->user, $this->annotation, $this->request->input('word_position'),$this->current_relation, false);
		
		$score_multiplier = $this->annotation_users->score_multiplier;
		
		if($score_multiplier==1)
			$this->increment('nb_successes');

		$this->gain*=$score_multiplier;

		$this->gain = intval($this->gain);

		$this->set('errors',$this->annotation_users->error);
		$this->addGain();
		
	}

	
}
