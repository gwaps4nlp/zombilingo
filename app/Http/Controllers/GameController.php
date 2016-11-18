<?php

namespace App\Http\Controllers;

use App\Repositories\RelationRepository;
use App\Repositories\CorpusRepository;
use App\Models\ConstantGame;
use App\Models\Corpus;
use App\Models\Source;
use App\Services\GameGestionInterface;
use App\Exceptions\GameException;
use Illuminate\Http\Request;

use Response, View, App, Auth;

class GameController extends Controller
{

    protected $game;

    /**
     * Instantiate a new GameController instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->game = App::make('App\Services\GameGestionInterface');
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
        $user = Auth::user();
		if($user->isAdmin() || $user->level_id >= 2){
			if($request->has('corpus_id')){
				$corpus = $corpuses->getById($request->input('corpus_id'));
				$this->game->set('corpus_id',$corpus->id);
				$relation->setCorpus($corpus);
			} elseif(!$this->game->corpus_id){
                $corpus = $corpuses->getById(ConstantGame::get('default-corpus'));
                $this->game->set('corpus_id',$corpus->id);
            }
		} else {
            $corpus = $corpuses->getById(ConstantGame::get('default-corpus'));
            $this->game->set('corpus_id',$corpus->id);
        }
        $corpus = $corpuses->getById($this->game->corpus_id);
        $relation->setCorpus($corpus);
		$relations = $relation->getByUser($user);
		$corpora = Corpus::where('source_id','=',Source::getPreannotated()->id)->where('playable',1)->lists('name','id');
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
     * @param  App\Repositories\CorpusRepository $corpuses
     * @param  Illuminate\Http\Request $request
     * @param  string $mode
     * @param  int $relation_id
     * @return Illuminate\Http\Response
     */
    public function begin(CorpusRepository $corpuses, Request $request, $mode, $relation_id)
    {

        if($request->has('corpus_id') && (Auth::user()->isAdmin() || Auth::user()->level_id >= 2)){
            $corpus = $corpuses->getById($request->input('corpus_id'));
            if($corpus->source_id!=Source::getPreAnnotated()->id)
                throw new GameException('Unknown corpus');
            if(!$corpus->playable)
                throw new GameException('Unknown corpus');
            $this->game->set('corpus_id',$corpus->id);
        }

        $this->game->begin($relation_id);
        if($this->game->request->ajax())
            return Response::json(array(
                'html' => View::make('partials.'.$this->game->mode.'.container',['game'=>$this->game])->render()
                ));
        else
            return view('front.game.container',['game'=>$this->game]);
    }
    
    /**
     * Send the content of the game in JSON format
     *
     * @return Illuminate\Http\Response
     */
    public function jsonContent()
    {
        return $this->game->jsonContent();

    }

    /**
     * Receive and process the answer of the player.
     *
     * @return Illuminate\Http\Response
     */
    public function answer()
    {
        return $this->game->jsonAnswer();

    }
	
}
