<?php

namespace App\Http\Controllers;

use App\Jobs\ChangeLocale;
use App\Repositories\ScoreRepository;
use App\Repositories\UserRepository;
use App\Repositories\ExportedCorpusRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\ChallengeRepository;
use Illuminate\Support\Facades\Request;

class HomeController extends Controller
{

	/**
	 * Display the informations page.
	 *
     * @param  App\Repositories\ExportedCorpusRepository $export
	 * @return Illuminate\Http\Response
	 */
	public function informations(ExportedCorpusRepository $export)
	{
		$last_exported_corpora = $export->getLast();
		$last_exported_mwe = $export->getLastMwe();
		return view('front.informations',compact('last_exported_corpora','last_exported_mwe'));
	}
	
	/**
	 * Display the home page.
	 *
     * @param  App\Repositories\UserRepository $user
     * @param  App\Repositories\ScoreRepository $score
     * @param  App\Repositories\AnnotationUserRepository $annotation_user
	 * @return Illuminate\Http\Response
	 */
	public function index(UserRepository $user,
		ScoreRepository $score,
		AnnotationUserRepository $annotation_user,
		ChallengeRepository $challenges
		)
	{
		$challenge = $challenges->getOngoing();
		$scores = $score->leaders(10,$challenge);
		$scores_annotations = $annotation_user->leaders(10,$challenge);
		$numberUsers = $user->count();
		$numberConnectedUsers = $user->countConnected();
		$connectedUsers = $user->getConnected();
		$lastRegisteredUser = $user->getLastRegistered();
		return view('front.home',compact('scores','challenge','numberUsers','numberConnectedUsers','lastRegisteredUser','connectedUsers','scores_annotations'));
	}

	/**
	 * Change language.
	 *
	 * @param  App\Jobs\ChangeLocale $changeLocale
	 * @return Illuminate\Http\Response
	 */
	public function language(
		ChangeLocale $changeLocale)
	{
		$this->dispatch($changeLocale);
		return redirect()->back();
	}

}
