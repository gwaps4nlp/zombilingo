<?php

namespace App\Http\Controllers;

use App\Repositories\ScoreRepository;
use App\Repositories\ChallengeRepository;
use App\Repositories\LanguageRepository;
use App\Repositories\CorpusRepository;
use App\Models\User;
use App\Models\Challenge;
use App\Http\Requests\ChallengeCreateRequest;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{

    /**
     * Instantiate a new GameController instance.
     *
     * @param  App\Repositories\ChallengeRepository $challenges
     * @return void     
     */
    public function __construct(ChallengeRepository $challenges)
    {
        $this->middleware('auth');
        $this->middleware('admin',['except' => ['getResults']]);
        $this->challenges = $challenges;

    }
    
    /**
     * Show the listing of passed challenges
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex()
    {
        $challenges = $this->challenges->getAll();
        return view('back.challenge.index',compact('challenges'));

    } 

    /**
     * Show the result of a given challenge
     *
     * @param  App\Repositories\ScoreRepository $scores
     * @param  Illuminate\Http\Request $request     
     * @return Illuminate\Http\Response
     */
    public function getResults(ScoreRepository $scores, Request $request)
    {
        $challenges = $this->challenges->getList();
        $scores_challenge = Array();
        if($request->has('challenge_id')){
            $user = Auth::user();
            $challenge = $this->challenges->getById($request->input('challenge_id'));
            $scores_challenge = $scores->neighborsByCorpus($user,'sup',14);
            $scores_challenge = $scores->neighborsByChallenge($user,'sup',$challenge);
        }
        return view('front.challenge.index',compact('challenge','scores_challenge'));

    }

    /**
     * Show the form to create a new challenge
     *
     * @param  App\Repositories\CorpusRepository $corpus 
     * @param  App\Repositories\LanguageRepository $language
     * @param  Illuminate\Http\Request $request  
     * @return Illuminate\Http\Response
     */
    public function getCreate(CorpusRepository $corpus, LanguageRepository $language, Request $request)
    {
        $types_challenge = ['points'=>'Points','annotations'=>'Annotations','duel'=>'Duels'];
        $languages = $language->getList();
        $corpora = $corpus->getList();
        return view('back.challenge.create',compact('languages','corpora','types_challenge'));

    }

    /**
     * Edit a challenge
     *
     * @param  App\Repositories\CorpusRepository $corpus 
     * @param  App\Repositories\LanguageRepository $language
     * @param  Illuminate\Http\Request $request       
     * @return Illuminate\Http\Response
     */
    public function getEdit($challenge_id, CorpusRepository $corpus, LanguageRepository $language, Request $request)
    {

        $challenge = $this->challenges->getById($challenge_id);
        $types_challenge = ['points'=>'Points','annotations'=>'Annotations','duel'=>'Duels'];
        $languages = $language->getList();
        $corpora = $corpus->getList();
        return view('back.challenge.edit',compact('languages','corpora','types_challenge','challenge'));

    }

    /**
     * Create a new challenge
     *
     * @param  App\Repositories\LanguageRepository $language
     * @param  Illuminate\Http\Request $request     
     * @return Illuminate\Http\Response
     */
    public function postCreate(ChallengeCreateRequest $request)
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = 'img/challenge';
            $image = str_replace([' ','.'],['-','-'],strtolower($request->name)).".".$request->file('image')->guessClientExtension();
            $request->file('image')->move($path,$image);
        }
        $start_date = date_create_from_format ( "d/m/Y H:i", $request->input('start_date')." 00:00");
        $end_date = date_create_from_format ( "d/m/Y H:i", $request->input('end_date')." 23:59");
        $this->challenges->create(array_merge($request->except('_token'),array('start_date'=>$start_date->format("Y-m-d H:i:s"),'end_date'=>$end_date->format("Y-m-d H:i:s"),'image'=>$path.'/'.$image)));
        return redirect()->action('ChallengeController@getIndex');

    }
    /**
     * Update a challenge
     *
     * @param  App\Repositories\LanguageRepository $language
     * @param  Illuminate\Http\Request $request     
     * @return Illuminate\Http\Response
     */
    public function postEdit($challenge_id, ChallengeCreateRequest $request)
    {
        $challenge = $this->challenges->getById($challenge_id);
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $path = 'img/challenge';
            $image = str_replace([' ','.'],['-','-'],strtolower($request->name)).".".$request->file('image')->guessClientExtension();
            $request->file('image')->move($path,$image);
            $challenge->image = $path.'/'.$image;
        }
        $start_date = date_create_from_format ( "d/m/Y H:i", $request->input('start_date')." 00:00");
        $end_date = date_create_from_format ( "d/m/Y H:i", $request->input('end_date')." 23:59");
        $challenge->save(array_merge($request->except('_token'),array('start_date'=>$start_date->format("Y-m-d H:i:s"),'end_date'=>$end_date->format("Y-m-d H:i:s"))));
        return redirect()->action('ChallengeController@getIndex');

    }
    
}
