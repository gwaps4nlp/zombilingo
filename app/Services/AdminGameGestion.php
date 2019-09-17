<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Models\AnnotationInProgress;
use App\Repositories\RelationRepository;
use App\Repositories\AnnotationRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\ScoreRepository;
use App\Repositories\ObjectRepository;
use Gwaps4nlp\Core\Models\Source;
use Gwaps4nlp\Core\Models\ConstantGame;
use Gwaps4nlp\Core\GameGestionInterface;
use App\Models\Relation;
use App\Models\Score;
use App\Models\User;
use App\Models\Article;
use App\Models\AnnotationUser;
use App\Exceptions\GameException;
use App\Services\GameGestion;
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

	protected $fillable = ['turn', 'mode', 'user_playing', 'save_mode', 'user_id', 'nb_turns', 'gain', 'type_gain', 'spell','in_progress','relation_id','current_relation_id','annotation_id','money_spent','money_earned','points_earned','effect','nb_successes','reference_again'];

	protected $visible = ['turn', 'expert', 'nb_turns', 'gain', 'spell', 'user','annotation','loot','attempts','html','neighbors','trophy','bonus'];

	/**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['neighbors'];

	public function begin(Request $request, $relation_id){

        $this->loadSession($request);
		$this->loadRelation($relation_id);

		if(!$this->request->ajax() && $this->request->has('annotation_id')){
			$this->set('annotation_id',$this->request->input('annotation_id'));
		}
		if(!$this->request->ajax()){
			if($this->request->has('save-mode')){
				$this->set('save_mode',$this->request->input('save-mode'));
				if($this->save_mode == 'user'){
					$user = User::findOrFail($this->request->input('user_id'));
					$this->set('user_playing',$user);
				}
			} else {
				$this->set('save_mode',null);
			}
		}

		$this->set('turn',0);
		$this->set('points_earned',0);
		$this->set('money_earned',0);
		$this->set('money_spent',0);
		$this->set('errors',0);
		$this->set('nb_successes',0);
		$this->set('effect',0);

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

    }

	public function end(){

	}

	public function isOver(){
		return false;
	}

	public function processAnswer(){

        if($this->save_mode == 'expert'){
			$this->annotation_users->saveAnnotationExpert($this->user, $this->annotation, $this->request->input('word_position'),$this->current_relation);
        }
        elseif($this->save_mode == 'user'){
        	// Delete the previous annotation of the user
        	AnnotationUser::where('user_id',$this->user_playing->id)->where('annotation_id',$this->annotation->id)->delete();
        	// Save the modified annotation
			$this->annotation_users->save($this->user_playing, $this->annotation, $this->request->input('word_position'),$this->current_relation);

        } else {
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


}
