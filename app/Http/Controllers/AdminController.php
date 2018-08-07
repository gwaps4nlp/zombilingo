<?php

namespace App\Http\Controllers;

use Auth;
use App;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Gwaps4nlp\Core\Repositories\UserRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\RelationRepository;
use App\Repositories\LevelRepository;
use App\Repositories\CorpusRepository;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
	/**
     * Create a new AdminController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin');
    }
    
    /**
     * Show the admin panel.
     *
     * @return Illuminate\Http\Response
     */
    public function getIndex()
    {
        return view('back.index');
    }

    /**
     * Allow to play Rigor Mortis.
     *
     * @return Illuminate\Http\Response
     */
    public function getMwe()
    {
        session()->put('mwe.enabled',1);
        return new RedirectResponse(url('/game/mwe/begin/0'));
    }
    
    /**
     * Show the reporting page.
     *
     * @param  App\Repositories\AnnotationUserRepository $annotations_user
     * @param  App\Repositories\UserRepository $users_repo
     * @param  App\Repositories\RelationRepository $relations_repo
     * @return Illuminate\Http\Response
     */
    public function getReporting(Request $request, AnnotationUserRepository $annotations_user, UserRepository $users_repo, RelationRepository $relations_repo)
    {

        if($request->has('period') && $request->input('period')=='week'){
            $annotations = $annotations_user->countByWeek($request->input('relation_id'));
            $registrations = $users_repo->countRegistrationsByWeek();
            $label_period = "semaine";
        } else {
            $annotations = $annotations_user->countByMonth($request->input('relation_id'));    
            $registrations = $users_repo->countRegistrationsByMonth();
            $label_period = "mois";
        }
        $relations = $relations_repo->getListPlayable();
        $users = $users_repo->getList();
        $annotationsByUser = $annotations_user->countByUser($request->input('relation_id'));
        
        // $registrationsByWeek = $users_repo->countRegistrationsByWeek();
        // $registrationsByMonth = $users_repo->countRegistrationsByMonth();

        $annotationsByRelation = $annotations_user->countByRelation($request->input('user_id'));
        $daysOfActivityByUser = $annotations_user->countDaysOfActivityByUser();
        return view('back.reporting',compact('request','relations','users','annotationsByUser','annotations','daysOfActivityByUser','registrations','annotationsByRelation','label_period'));
    }

    public function givenewquest(){
        $questuser=App::make('App\Repositories\QuestUserRepository');
        $questuser->givequest(Auth::user());
        return view('back.index');
    }

}
