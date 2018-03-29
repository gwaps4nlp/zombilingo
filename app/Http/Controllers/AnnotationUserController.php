<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\AnnotationRepository;
use Gwaps4nlp\Repositories\UserRepository;
use App\Repositories\RelationRepository;
use App\Repositories\CorpusRepository;
use App\Models\Corpus;
use App\Models\User;
use App\Models\Relation;

class AnnotationUserController extends Controller
{
    /**
     * Create a new AnnotationUserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin', ['except' => ['getIndex']]);
    }

    /**
     * Show a listing of the answers given by a user by relation and corpus.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Repositories\AnnotationRepository $annotations
     * @param  App\Repositories\RelationRepository $relation
     * @param  App\Repositories\CorpusRepository $corpuses     
     * @param  App\Repositories\UserRepository $user_repo     
     * @return Illuminate\Http\Response
     */
    public function getAdminIndex(Request $request, AnnotationRepository $annotations, RelationRepository $relation, CorpusRepository $corpuses, UserRepository $user_repo) 
    {
        $relations = $relation->getListPlayable();
        $corpora = $corpuses->getListPreAnnotated();
        $users = $user_repo->getList();

        $params = Array('relation_id'=>null,'corpus_id'=>null,'user_id'=>null,'undecided'=>null,'playable'=>null,'sortby'=>'score','order'=>'desc');
        
        if($request->has('undecided')&&$request->input('undecided')==1){
            $params['undecided']=1;
        }

        if($request->has('relation_id') && $relations->has($request->input('relation_id'))){
            $relation = $relation->getById($request->input('relation_id'));
            $params['relation_id']=$request->input('relation_id');
        } else {
            $relation = new Relation(['id'=>0]);
        }

        if($request->has('corpus_id') && $corpora->has($request->input('corpus_id'))){
            $corpus = $corpuses->getById($request->input('corpus_id'));
            $params['corpus_id']=$request->input('corpus_id');
        } else {
            $corpus = new Corpus(['id'=>0]);
        }

        if($request->has('user_id') && $users->has($request->input('user_id'))){
            $user = $user_repo->getById($request->input('user_id'));
            $params['user_id']=$request->input('user_id');
        } else {
            $user = new User(['id'=>0]);
        }

        $annotations_user =  $annotations->getStatistics($params,$corpus);
        $annotations_user->appends($params);
        
        return view('back.annotation-user.index',compact('annotations_user','corpora','relations','params','users'));
    }

    /**
     * Show a listing of the answers given by a user by relation and corpus.
     *
     * @param  Illuminate\Http\Request $request
     * @param  App\Repositories\AnnotationRepository $annotations
     * @param  App\Repositories\RelationRepository $relation
     * @param  App\Repositories\CorpusRepository $corpuses     
     * @return Illuminate\Http\Response
     */
    public function getIndex(Request $request, AnnotationRepository $annotations, RelationRepository $relation, CorpusRepository $corpuses) 
    {
        $relations = $relation->getListPlayable();
        $corpora = $corpuses->getListPreAnnotated();
        $user = Auth::user();
        $params = Array('relation_id'=>null,'corpus_id'=>null,'user_id'=>null,'undecided'=>null,'sortby'=>'score','order'=>'desc','playable'=>1);

        if($request->has('relation_id') && $relations->has($request->input('relation_id'))){
            $relation = $relation->getById($request->input('relation_id'));
            $params['relation_id']=$request->input('relation_id');
        } else {
            $relation = new Relation(['id'=>0]);
        }

        if($request->has('corpus_id') && $corpora->has($request->input('corpus_id'))){
            $corpus = $corpuses->getById($request->input('corpus_id'));
            $params['corpus_id']=$request->input('corpus_id');
        } else {
            $corpus = new Corpus(['id'=>0]);
        }

        $params['user_id']=$user->id;

        $annotations_user =  $annotations->getStatistics($params,$corpus);
        $annotations_user->appends($params);
        
        return view('front.annotation-user.index',compact('annotations_user','corpora','relations','params','users'));
    }

}
