<?php namespace App\Services;

use Session;

class Statut  {

	/**
	 * Set the login user statut
	 * 
	 * @param  App\Models\User $user
	 * @return void
	 */
	public function setLoginStatut($login)
	{
		session()->put('statut', $login->user->role->slug);
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
		session()->put('statut', 'visitor');
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

		if(!session()->has('statut')) 
		{
			session()->put('statut', auth()->check() ?  auth()->user()->role->slug : 'visitor');
		}
		if(auth()->check()){
			auth()->user()->last_action_at = date('Y-m-d H:i:s');
			auth()->user()->save();
		}
	}

}