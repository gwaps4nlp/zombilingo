<?php

namespace App\Http\Controllers;

use App\Repositories\RelationRepository;
use App\Repositories\CorpusRepository;
use App\Models\Corpus;
use Gwaps4nlp\Core\Models\ConstantGame;
use Gwaps4nlp\Core\Models\Source;
use Illuminate\Http\Request;
use Gwaps4nlp\Core\GameController as Gwaps4nlpGameController;
use Illuminate\Http\Response;
use View;

class GameController extends Gwaps4nlpGameController
{

    protected $game;

    /**
     * Instantiate a new GameController instance.
     *
     * @return void
     */
    public function __construct()
    {
		parent::__construct();
		if($this->game->mode=='demo')
			$this->middleware('guest');
        elseif($this->game->mode=='admin-game')
            $this->middleware('admin');
		else
			$this->middleware('auth');
    }

	/**
	 * Show the index of the game by level
	 *
     * @param  App\Repositories\RelationRepository $relation
     * @param  App\Repositories\CorpusRepository $corpuses
     * @param  Illuminate\Http\Request $request
	 * @return Illuminate\Http\Response
	 */
	public function index(RelationRepository $relation, CorpusRepository $corpuses, Request $request)
	{
        $user = auth()->user();
		if($user->isAdmin() || $user->level_id >= 2){
			if($request->has('corpus_id')){
				$corpus = Corpus::find($request->input('corpus_id'));
			} elseif(!$this->game->corpus_id){
                $corpus = Corpus::find(ConstantGame::get('default-corpus'));
            } else {
                $corpus = Corpus::find($this->game->corpus_id);
            }
		} else {
            $corpus = Corpus::find(ConstantGame::get('default-corpus'));
        }
        if($user->isAdmin()){
            if(!$corpus)
                $corpus = Corpus::where('playable','1')->orderBy('created_at','desc')->firstOrFail();
        }  else if(!$corpus || !$corpus->playable){
            $corpus = Corpus::where('playable','1')->orderBy('created_at','desc')->firstOrFail();
        }

        $this->game->set('corpus_id',$corpus->id);
        $relation->setCorpus($corpus);
		$relations = $relation->getByUser($user);
        if($user->isAdmin()){
		  $corpora = Corpus::where('source_id','=',Source::getPreannotated()->id)->pluck('name','id');
        } else {
            $corpora = Corpus::where('source_id','=',Source::getPreannotated()->id)->where('playable',1)->pluck('name','id');
        }
		return view('front.game.index',compact('user','relations','corpora'))->with('game',$this->game);
	}

	/**
	 * Display the index of the demo game
	 *
	 * @return Illuminate\Http\Response
	 */
	public function indexDemo()
	{
		return view('front.demo.index',['game'=>$this->game]);
	}

    /**
     * Begin a new game
     *
     * @param  Illuminate\Http\Request $request
     * @param  string $mode
     * @param  int $relation_id
     * @return Illuminate\Http\Response
     */
    public function begin(Request $request, $mode, $relation_id)
    {

        if($request->has('corpus_id') && (auth()->user()->isAdmin() || auth()->user()->level_id >= 2)){
            $corpus = Corpus::findOrFail($request->input('corpus_id'));
            if($corpus->source_id!=Source::getPreAnnotated()->id)
                throw new GameException('Unknown corpus');
            if(!$corpus->playable)
                throw new GameException('Unknown corpus');
            $this->game->set('corpus_id',$corpus->id);
        }

        $this->game->begin($request, $relation_id);
        if($this->game->request->ajax())
            return response()->json(array(
                'html' => View::make('partials.'.$this->game->mode.'.container',['game'=>$this->game])->render()
                ));
        else
            return view('front.game.container',['game'=>$this->game]);
    }

}
