<?php

namespace App\Http\Controllers;

use App\Repositories\RelationRepository;
use App\Repositories\DuelRepository;
use Gwaps4nlp\Core\Repositories\UserRepository;
use App\Repositories\AnnotationRepository;
use App\Repositories\ChallengeRepository;
use Gwaps4nlp\Core\Models\ConstantGame;
use App\Models\Corpus;
use Gwaps4nlp\Core\Models\Source;
use App\Models\Challenge;
use App\Models\Duel;
use App\Http\Requests\DuelCreateRequest;
use App\Http\Requests\DuelJoinRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Gwaps4nlp\Core\GameController as Gwaps4nlpGameController;
use Response, View, App, Auth, DB;

class DuelController extends Gwaps4nlpGameController
{

    /**
     * Instantiate a new GameController instance.
     *
     * @param  App\Repositories\DuelRepository $duels
     * @return void
     */
    public function __construct(DuelRepository $duels)
    {
        $this->middleware('ajax', ['only' => ['index']]);
        $this->duels = $duels;
        parent::__construct();
    }
    
    /**
     * Display the index of duel games
     *
     * @param  App\Repositories\RelationRepository $relations
     * @param  App\Repositories\ChallengeRepository $challenges
     * @param  Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getIndex(RelationRepository $relation, ChallengeRepository $challenges, Request $request)
    {
        $user = Auth::user();

        $tab = ($request->has('tab'))?$request->input('tab'):'available';

        $currentPageAvailable = ($request->has('page')&&$tab=='available')?$request->input('page'):1;
        Paginator::currentPageResolver(function() use ($currentPageAvailable) {
            return $currentPageAvailable;
        });
        $available_duels = $this->duels->getPendingAvailable($user)->appends(['tab'=>'available']);
        
        $currentPageInProgress = ($request->has('page')&&$tab=='in_progress')?$request->input('page'):1;
        Paginator::currentPageResolver(function() use ($currentPageInProgress) {
            return $currentPageInProgress;
        });
        $pending_duels = $this->duels->getInProgress($user)->appends(['tab'=>'in_progress']);
        foreach($pending_duels as $duel){
            if(!$duel->user(Auth::user())->seen)
                $duel->users()->updateExistingPivot(Auth::id(),['seen'=>1]);
        }

        $currentPageCompleted = ($request->has('page')&&$tab=='completed')?$request->input('page'):1;
        Paginator::currentPageResolver(function() use ($currentPageCompleted) {
            return $currentPageCompleted;
        });   
        $completed_duels = $this->duels->getCompleted($user)->appends(['tab'=>'completed']);

        $relations = $relation->getListPlayable($user);
        $enemies = $user->getListFriends();
        return view('front.duel.index',compact('user','available_duels','pending_duels','completed_duels','relations','enemies','request'))->with('game',$this->game)->with('duels',$this->duels);
    }

    /**
     * Show the form to launch a new duel
     *
     * @param  App\Repositories\RelationRepository $relation
     * @return Illuminate\Http\Response
     */
    public function getNew(RelationRepository $relation)
    {
        $user = Auth::user();
        $relations = $relation->getListPlayable($user);
        $enemies = $user->getListFriends();
        return view('front.duel.new',compact('user','relations','enemies'))->with('game',$this->game);
    }

    /**
     * Display a form in a modal to launch a new duel 
     *
     * @param  App\Repositories\RelationRepository $relation
     * @return Illuminate\Http\Response
     */
    public function getModalNew(RelationRepository $relation)
    {
        $user = Auth::user();
        $relations = $relation->getListPlayable($user);
        $enemies = $user->getListFriends();
        return view('partials.duel.modal-new',compact('user','relations','enemies'))->with('game',$this->game);
    }

    /**
     * Launch the revenge of a duel
     *
     * @param  App\Repositories\RelationRepository $relation
     * @param  App\Repositories\AnnotationRepository $annotations
     * @param  int $id the id of the duel which we want to revenge
     * @return Illuminate\Http\Response
     */
    public function getRevenge(RelationRepository $relation, AnnotationRepository $annotations, $id)
    {
        $user = Auth::user();
        $duel = $this->duels->getById($id, $user, 'completed');
        $new_duel = Duel::create(['relation_id'=>$duel->relation_id,'level_id'=>$duel->level_id,'nb_turns'=>$duel->nb_turns]);

        $new_duel->users()->save($user);
        $challenger = $duel->challenger($user);
        $new_duel->users()->save($challenger);
        $new_duel->users()->updateExistingPivot($user->id,['seen'=>1,'email'=>0]);
        $new_duel->state = "in_progress";
        $new_duel->expired_at = DB::raw('date_add(NOW(), INTERVAL 7 day)');
        $new_duel->save();
        $already_selected=[];
        
        for($i=0;$i<$duel->nb_turns;$i++){
            if(rand(0,100)>20)
                $annotation = $annotations->getRandomPreAnnotatedDuel($duel->relation,$already_selected);
            else
                $annotation = $annotations->getRandomReferenceDuel($duel->relation,$already_selected);

            $already_selected[]=$annotation->id;
            $new_duel->annotations()->save($annotation);
        }

        return redirect(url('game/duel/begin/'.$new_duel->id));
    }

    /**
     * Create and launch a new duel
     *
     * @param  App\Repositories\RelationRepository $relation
     * @param  App\Repositories\UserRepository $users
     * @param  App\Repositories\AnnotationRepository $annotations     
     * @param  App\Http\Requests\DuelCreateRequest $request
     * @return Illuminate\Http\Response
     */
    public function postNew(RelationRepository $relations, UserRepository $users, AnnotationRepository $annotations, DuelCreateRequest $request)
    {
        $user = Auth::user();
        
        $relation=null;

        if($request->has('relation_id') && $request->input('relation_id')){
            $relation = $relations->getById($request->input('relation_id'));
        }

        if($request->has('challenger_id') && $request->input('challenger_id')){
            $challenger = $users->getById($request->input('challenger_id'));
            if(!$relation){
                $relation = $relations->getRandom(min($challenger->level_id,$user->level_id));
            }
            $duel = Duel::create(['relation_id'=>$relation->id,'level_id'=>$relation->level_id,'nb_turns'=>$request->input('nb_turns')]);
            $duel->users()->save($user);
            $duel->users()->save($challenger);
            $duel->users()->updateExistingPivot($user->id,['seen'=>1,'email'=>0]);
            $duel->state = "in_progress";
            $duel->expired_at = DB::raw('date_add(NOW(), INTERVAL 7 day)');
            $duel->save();      
            $already_selected=[];
            for($i=0;$i<$duel->nb_turns;$i++){
                if(rand(0,100)>20)
                    $annotation = $annotations->getRandomPreAnnotatedDuel($duel->relation,$already_selected);
                else
                    $annotation = $annotations->getRandomReferenceDuel($duel->relation,$already_selected);
                
                if(!$annotation){
                    $duel->delete();
                    return Response::json(['errors'=>['nb_turns'=> ["pas assez de phrases"]]],422);
                }
                
                $already_selected[]=$annotation->id;
                $duel->annotations()->save($annotation);
            }
        } else {
            $duel = null;

            if($duel){
                $duel->users()->save($user);
                $duel->state = "in_progress";
                $duel->save();
            } else {
                if(!$relation){
                    $relation = $relations->getRandom($user->level_id);
                }
                $duel = Duel::create(['relation_id'=>$relation->id,'level_id'=>$relation->level_id,'nb_turns'=>$request->input('nb_turns')]);
                $duel->users()->save($user);
                $already_selected=[];
                for($i=0;$i<$duel->nb_turns;$i++){
                    if(rand(0,100)>20)
                        $annotation = $annotations->getRandomPreAnnotatedDuel($duel->relation,$already_selected);
                    else
                        $annotation = $annotations->getRandomReferenceDuel($duel->relation,$already_selected);

                    if(!$annotation){
                        $duel->delete();
                        return Response::json(['errors'=>['nb_turns'=> ["pas assez de phrases"]]],422);
                    }

                    $already_selected[]=$annotation->id;
                    $duel->annotations()->save($annotation);
                }              
            }
        }

        $relations = $relations->getListPlayable($user);
        $enemies = $user->getListFriends();
        
        if($request->ajax())
            return Response::json(['href'=>url('game/duel/begin/'.$duel->id)]);

        return redirect(url('game/duel/begin/'.$duel->id));
    }

    /**
     * Join an existent duel
     *
     * @param  App\Http\Requests\DuelJoinRequest $request
     * @return Illuminate\Http\Response
     */
    public function postJoin(DuelJoinRequest $request)
    {
        $user = Auth::user();
        $duel = $this->duels->getAvailableDuel($user, $request->input('duel_id'), null);
        $duel->users()->save($user);
        $duel->users()->updateExistingPivot($user->id,['seen'=>1]);
        $duel->state = "in_progress";
        $duel->save();
        return Response::json(['href'=>url('game/duel/begin/'.$duel->id)]);
    }

    /**
     * Show the differences between the answers of a duel 
     *
     * @param  int $id the identifier of the duel to compare the results
     * @return Illuminate\Http\Response
     */
    public function getCompareResults($id)
    {
        $user = Auth::user();
        $duel = $this->duels->getById($id, $user, 'completed');
        return view('partials.duel.compare',compact('duel'));
    }

    /**
     * Check a duel as seen
     *
     * @param  int $id the identifier of the duel to check as seen
     * @return Illuminate\Http\Response
     */
    public function getCheckAsSeen($id){
        $user = Auth::user();
        $duel = $this->duels->getById($id, $user);
        $duel->users()->updateExistingPivot($user->id,['seen'=>1]);
    }


    
}
