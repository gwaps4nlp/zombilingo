<?php namespace App\Services;

use Session;
use App\Models\LogDB;

class Statut  {

	/**
	 * Set the login user statut
	 * 
	 * @param  App\Models\User $user
	 * @return void
	 */
	public function setLoginStatut($login)
	{
		$login->user->connected = 1;
		$login->user->session_id = Session::getId();
		$login->user->save();
	}

	/**
	 * Set the visitor user statut
	 * 
	 * @return void
	 */
	public function setVisitorStatut()
	{
		if(auth()->check()){
			auth()->user()->connected = 0;
			auth()->user()->save();
		}
	}

	/**
	 * Set the statut
	 * 
	 * @return void
	 */
	public function setStatut()
	{
		if(auth()->check()){
			auth()->user()->last_action_at = date('Y-m-d H:i:s');
			auth()->user()->save();
		}

		LogDB::create([
			'session_id' => Session::getId(),
			'referer' => request()->headers->get('referer'),
			'url' => request()->fullUrl(),
		]);
	}

}