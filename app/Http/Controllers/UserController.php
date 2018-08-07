<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Relation;
use App\Repositories\FloorRepository;
use Gwaps4nlp\Core\Models\Language;
use App\Models\Corpus;
use Gwaps4nlp\Core\Repositories\UserRepository;
use App\Repositories\DuelRepository;
use App\Repositories\LevelRepository;
use Gwaps4nlp\Core\Repositories\TrophyRepository;
use App\Repositories\RelationRepository;
use App\Repositories\ScoreRepository;
use App\Repositories\EmailFrequencyRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\CorpusRepository;
use App\Repositories\ChallengeRepository;
use Gwaps4nlp\Core\Repositories\RoleRepository;
use App\Http\Requests\ChangeEmailRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Models\Challenge;
use App\Models\User;
use App\Models\Friend;
use Gwaps4nlp\NewsManager\Models\News;
use Gwaps4nlp\Core\Models\Role;
use Response, Auth;

class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct(UserRepository $users)
    {
        $this->middleware('auth');
        $this->middleware('admin',['only'=>'getIndexAdmin']);
        $this->users = $users;
    }

    /**
     * Show the user's home.
     *
     * @param  Gwaps4nlp\Core\Repositories\TrophyRepository $trophy
     * @param  App\Repositories\ScoreRepository $score
     * @param  App\Repositories\DuelRepository $duels
     * @param  App\Repositories\AnnotationUserRepository $annotation_user
     * @param  App\Repositories\EmailFrequencyRepository $email_frequencies
     * @param  App\Repositories\ChallengeRepository $challenges
     * @return Illuminate\Http\Response
     */
    public function getHome(LevelRepository $level,
        TrophyRepository $trophy,
        ScoreRepository $score,
        DuelRepository $duels,
        AnnotationUserRepository $annotation_user,
        EmailFrequencyRepository $email_frequencies,
        ChallengeRepository $challenges,
        FloorRepository $checkpoint
        )
    {
        $user=Auth::user();
        $trophyuser= App::make('Gwaps4nlp\Core\Repositories\TrophyUserRepository');
        $challenge = $challenges->getOngoing();
        $leaders = $score->leaders(11,'points',$challenge);
        $leaders_annotations = $score->leaders(11, 'number_annotations', $challenge);
        $neighbors = $score->neighbors($user,'points',3,$challenge);
        $neighbors_annotations = $score->neighbors($user,'number_annotations',3,$challenge);
        $scores_user = $score->getByUser($user,'points',$challenge);
        $scores_annotation_user = $score->getByUser($user,'number_annotations',$challenge);
        $language = Language::where('slug','=',app()->getLocale())->first();
        $email_frequency = $email_frequencies->getAll();
        $news = News::take(5)->where('language_id',$language->id)->orderBy('created_at','desc')->get();
        return view('front.user.home',compact('user','level','score','trophy','leaders','leaders_annotations','neighbors','neighbors_annotations','scores_user','scores_annotation_user','news','duels','trophyid','email_frequency','challenge','trophyuser'));
    }
    
    /**
     * Display a listing of the connected users.
     *
     * @return Illuminate\Http\Response
     */
    public function getConnected()
    {
        $users = $this->users->getConnected();
        return view('front.user.connected',compact('users'));
    }
    
    /**
     * Show the detail of a user.
     *
     * @param  App\Models\User $user
     * @return Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('front.user.show',compact('user'));
    }
    
    /**
     * Ask a friend.
     *
     * @param  App\Models\User $user
     * @return Illuminate\Http\Response
     */
    public function getAskFriend(User $user)
    {
        if($user->id==Auth::user()->id) return;
        $new_friend = Friend::firstOrCreate(['user_id'=>Auth::user()->id,'friend_id'=>$user->id]);
        $response = trans('site.message-ask-friend', ['username' => $user->username]);
        return Response::json(['html'=>$response]);
    }

    /**
     * Cancel an ask of friend.
     *
     * @param  int  $user_id
     * @return Illuminate\Http\Response
     */
    public function getCancelFriend(User $user)
    {
        $friend_relation = Friend::where(['user_id'=>Auth::user()->id,'friend_id'=>$user->id])->first();
        if($friend_relation)
            $friend_relation->delete();
        $friend_relation = Friend::where(['friend_id'=>Auth::user()->id,'user_id'=>$user->id])->first();
        if($friend_relation)  
            $friend_relation->delete();
        return Response::json($user);
    }
    
    /**
     * Accept a friend.
     *
     * @param  int  $user_id
     * @return Illuminate\Http\Response
     */
    public function getAcceptFriend(User $user)
    {
        $friend_relation = Friend::where(['user_id'=>$user->id,'friend_id'=>Auth::user()->id])->firstOrFail();
        $friend_relation->accepted = 1;
        $friend_relation->save();
        $new_friend = Friend::firstOrCreate(['user_id'=>Auth::user()->id,'friend_id'=>$user->id,'accepted'=>1]);
        return Response::json($user);
    } 

    /**
     * Change email and email frequency.
     *
     * @param  App\Http\Requests\ChangeEmailRequest $request
     * @return Illuminate\Http\Response
     */
    public function postChangeEmail(ChangeEmailRequest $request)
    {
        Auth::user()->email = $request->input('email');
        Auth::user()->email_frequency_id = $request->input('email_frequency_id');
        Auth::user()->save();
        return Response::json(Auth::user());
    }

    /**
     * Update the password of the user.
     *
     * @param  App\Http\Requests\ChangePasswordRequest $request
     * @return Illuminate\Http\Response
     */
    public function postChangePassword(ChangePasswordRequest $request)
    {
        Auth::user()->password = bcrypt($request->input('password'));
        Auth::user()->save();
        return Response::json(Auth::user());
    }

    /**
     * Delete the account of the logged user.
     *
     * @return Illuminate\Http\Response
     */
    public function getDelete()
    {
        Auth::user()->username = 'deleted_'.Auth::user()->id;
        Auth::user()->email = "";
        Auth::user()->save();
        Friend::where('user_id', Auth::user()->id)->delete();
        Friend::where('friend_id', Auth::user()->id)->delete();
        $this->users->destroy(Auth::user()->id);
        Auth::logout();
        return redirect('');
    }
    
    /**
     * Show a listing of players ordered by scores and relation
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Repositories\ScoreRepository $scores
     * @param  App\Repositories\RelationRepository $relation_repo
     * @param  App\Repositories\CorpusRepository $corpuses
     * @return Illuminate\Http\Response
     */
    public function getPlayers(Request $request, 
        ScoreRepository $scores, 
        RelationRepository $relation_repo, 
        CorpusRepository $corpuses, 
        ChallengeRepository $challenges_repo)
    {

        $relations = $relation_repo->getListPlayable();
        $corpora = $corpuses->getListPlayable();
        $challenges = $challenges_repo->getListWithScore();
        $count_input = count($request->all());
        $params = Array('relation_id'=>null,'corpus_id'=>null,'challenge_id'=>null,'sortby'=>'score','order'=>'desc');
        $relation = new Relation(['id'=>0]);
        $corpus = new Corpus(['id'=>0]);
        $challenge = new Challenge(['id'=>0]);
        $allowedAttributes = Array('username','score');
        $allowedOrders = Array('asc','desc');
        
        if($request->has('sortby') && in_array($request->input('sortby'),$allowedAttributes)){
            $params['sortby'] = $request->input('sortby');
        }

        if($request->has('order') && in_array($request->input('order'),$allowedOrders)){
            $params['order'] = $request->input('order');
        }

        if($request->filled('username')){
            $users = User::where('username','LIKE','%'.$request->input('username').'%')->orderBy('score','desc')->paginate(10);
        } elseif($request->filled('challenge_id')){

            if($challenges->has($request->input('challenge_id'))){
                $challenge = $challenges_repo->getById($request->input('challenge_id'));
                $params['challenge_id']=$request->input('challenge_id');
            }

            $users = $scores->leadersByChallenge($challenge,'points',10);
            $users->appends($params);

            $users->setPath('players');

        } elseif($request->filled('relation_id') || $request->filled('corpus_id')){

            if($request->filled('relation_id') && $relations->has($request->input('relation_id'))){
                $relation = $relation_repo->getById($request->input('relation_id'));
                $params['relation_id']=$request->input('relation_id');
            }

            if($request->filled('corpus_id') && $corpora->has($request->input('corpus_id'))){
                $corpus = $corpuses->getById($request->input('corpus_id'));
                $params['corpus_id']=$request->input('corpus_id');
            }

            if($request->filled('challenge_id') && $challenges->has($request->input('challenge_id'))){
                $challenge = $challenges_repo->getById($request->input('challenge_id'));
                $params['challenge_id']=$request->input('challenge_id');
            }

            $users = $scores->leadersRankedByPeriode(null,10,$params);
            $users->appends($params);

            $users->setPath('players');
        } else {
            $users = User::orderBy('score','desc')->paginate(10);
        }
        return view('front.user.index',compact('users','relations','corpora','corpus','challenges','challenge','relation','params'));
    }

    /**
     * Show a listing of all the users.
     *
     * @return Illuminate\Http\Response
     */
    public function getIndexAdmin(RoleRepository $roles_repo)
    {
        $users = $this->users->getAll();
        $roles = Role::get()->pluck('label', 'id');
        return view('back.user.index',compact('users','roles'));
    }

    /**
     * Show a listing of the connected users.
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex()
    {
        $users = $this->users->getConnected();
        return view('front.user.connected',compact('users'));
    }

}
