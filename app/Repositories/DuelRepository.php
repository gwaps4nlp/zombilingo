<?php

namespace App\Repositories;

use App\Models\Duel;
use App\Models\DuelUser;
use Gwaps4nlp\Repositories\BaseRepository;
use DB;

class DuelRepository extends BaseRepository
{

	/**
	 * Create a new DuelRepository instance.
	 *
	 * @param  App\Models\Duel $duel
	 * @return void
	 */
	public function __construct(
		Duel $duel)
	{
		$this->model = $duel;
	}

	/**
	 * Get the duels of a user
	 * 
	 * @param  App\Models\User $user
	 * @param  string $state
	 * @return Collection of Duel
	 */
	public function get($user,$state=null)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        });

        if($state)
        	$query = $query->where('state','=',$state);
        if($state=='completed')
        	$query = $query->orderBy('updated_at','desc');
		return $query->with('users')->with('relation')->paginate(10);
	}

	/**
	 * 
	 *
	 * @return Collection
	 */
	public function getUsersToSendEmail()
	{
		return DuelUser::whereNull('duel_user.email')->where('duel_user.seen','=','0')->groupBy('user_id')
				->join('users',function($join){
					$join->on('users.id','=','duel_user.user_id');
				})->whereRaw("last_action_at<DATE_SUB(NOW(), interval 5 minute)")
				->where('users.email','!=','')
				->where('email_frequency_id','!=','1')
				->where('email_duel','=','1')
				->pluck('user_id');

	}

	/**
	 * Retrieve a list of duels to send by email to the challenger
	 *
	 * @return Collection
	 */
	public function getToSendEmail($user)
	{

		$query = $this->model->select('duels.*')->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        });

       	$query = $query->join('duel_user',function($join) use ($user) {
       		$join->on('duels.id','=','duel_user.duel_id')
       			->where('duel_user.user_id', '=', $user->id)
       			->whereNull('duel_user.email')->where('duel_user.seen','=','0');
       	});

		return $query->with('users')->with('relation')->get();

	}

	/**
	 * Get the list of duels in progress of a given user
	 *
	 * @param  App\Models\User $user
	 * @return Collection of Duel
	 */
	public function getInProgress($user)
	{
		$query = $this->model->select('duels.*')->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        });

       	$query = $query->where('state','!=','completed');
       	$query = $query->join('duel_user',function($join) use ($user) {
       		$join->on('duels.id','=','duel_user.duel_id')
       			->where('duel_user.user_id', '=', $user->id);
       	});

		return $query->with('users')->with('relation')->orderBy('duel_user.turn')->paginate(10);
	}

	/**
	 * Get the list of pending duels available for a given user
	 *
	 * @param  App\Models\User $user
	 * @return Collection of Duel
	 */
	public function getPendingAvailable($user)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '!=', $user->id);
        },'<',DB::raw('nb_users'));

		$query = $query->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        },'=',0);

       	$query = $query->where('level_id','<=',$user->level_id);

       	$query = $query->where('state','=','pending');

		return $query->with('users')->with('relation')->paginate(10);
	}
	/**
	 * 
	 * @param  App\Models\User $user
	 * @param  string $before 
	 * @return Collection
	 */
	public function getPendingNotSeen($user, $before)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
           	$query->where('seen', '=', '0');
        });

      	$query = $query->where('state','!=','completed')
      		->where('created_at','>=',DB::raw("DATE_SUB(NOW(), interval $before )"));

		return $query->with('users')->with('relation')->paginate(10);
	}

	/**
	 * Get the list of completed duels of a given user
	 *
	 * @param  App\Models\User $user
	 * @return Collection
	 */
	public function getCompleted($user)
	{
		return $this->get($user,'completed');
	}

	/**
	 * Count the completed duels of a given user
	 *
	 * @param  App\Models\User $user	 
	 * @return int
	 */
	public function countCompleted($user)
	{
		return $this->count($user,'completed');
	}

	/**
	 * Count the won duels of a given user
	 *
	 * @param  App\Models\User $user
	 * @return int
	 */
	public function countWon($user)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
            $query->where('result', '=', 1);
        });
       	$query = $query->where('state','=','completed');

		return $query->count();
	}
	/**
	 * Count the draw duels of a given user
	 *
	 * @param  App\Models\User $user
	 * @return int
	 */
	public function countDraw($user)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
            $query->where('result', '=', 0);
        });
       	$query = $query->where('state','=','completed');

		return $query->count();
	}

	/**
	 * Count the lost duels of a given user
	 *
	 * @param  App\Models\User $user
	 * @return int
	 */
	public function countLost($user)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
            $query->where('result', '=', -1);
        });
       	$query = $query->where('state','=','completed');

		return $query->count();
	}

	/**
	 * Count the pending available duels of a given user
	 *
	 * @param  App\Models\User $user
	 * @return int
	 */
	public function countPendingAvailable($user)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '!=', $user->id);
        },'<',DB::raw('nb_users'));
		$query = $query->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        },'=',0);
       	$query = $query->where('level_id','<=',$user->level_id);        
       	$query = $query->where('state','=','pending');

		return $query->count();
	}

	/**
	 * Count the duels in progress not seen of a given user
	 *
	 * @param  App\Models\User $user
	 * @return int
	 */
	public function countInProgressNotSeen($user)
	{
		return $this->count($user,'in_progress',false);
	}
	/**
	 * Count the completed duels not seen of a given user
	 *
	 * @param  App\Models\User $user
	 * @return int
	 */
	public function countCompletedNotSeen($user)
	{
		return $this->count($user,'completed',false);
	}

	/**
	 * Count the uncompleted duels of a given user
	 *
	 * @param  App\Models\User $user
	 * @return int
	 */
	public function countNotCompleted($user)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        });

		$query = $query->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
            $query->whereRaw('turn < nb_turns');
        },'=',1);

       	$query = $query->where('state','!=','completed');

		return $query->count();
	}
	/**
	 * Count the duels in progress of a given user
	 *
	 * @param  App\Models\User $user
	 * @return int
	 */
	public function countInProgress($user)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        });

       	$query = $query->where('state','!=','completed');

		return $query->count();
	}

	/**
	 * Count the duels in progress of a given user
	 *
	 * @param  App\Models\User $user
	 * @param  string|null $state state of the duel : pending, in_progress or completed
	 * @param  boolean|null $seen (0 or 1) is the duel seen by the user
	 * @return int
	 */
	public function count($user,$state=null,$seen=null)
	{
		$query = $this->model->whereHas('users', function ($query) use ($user, $seen) {
            $query->where('user_id', '=', $user->id);
            if(isset($seen))
            	$query->where('seen', '=', $seen);
        });

        if($state)
        	$query = $query->where('state','=',$state);

		return $query->count();
	}
	
	/**
	 * get a duel by it id
	 *
	 * @param  int $id id of the duel
	 * @param  App\Models\User $user
	 * @param  string|null $state state of the duel : pending, in_progress or completed	 
	 * @return App\Models\Duel
	 */
	public function getById($id,$user=null,$state=null)
	{
		$query = $this->model->where('id', $id);

		if($user)
			$query = $query->whereHas('users', function ($query) use ($user) {
	            $query->where('user_id', '=', $user->id);
	        });

        if($state)
        	$query = $query->where('state','=',$state);

		return $query->with('users')->with('relation')->firstOrFail();
	}
	
	/**
	 * Retrieve the available duels for a given user
	 * 
	 * @param  int $id id of the duel
	 * @param  App\Models\User $user
	 * @param  string|null $state state of the duel : pending, in_progress or completed	
	 * @return App\Models\Duel
	 */
	public function getAvailableDuel($user,$duel_id=null,$relation=null)
	{
		$query = $this->model->where('state','pending');
		
		$query = $query->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '!=', $user->id);
        },'<',DB::raw('duels.nb_users'));
		
		$query = $query->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', '=', $user->id);
        },'=',0);

 		if($duel_id)
 			$query = $query->where('id',$duel_id);

 		if($relation)
 			$query = $query->where('relation_id',$relation->id);
 		else
 			$query = $query->where('level_id','<=',$user->level_id);

		return $query->orderBy('level_id','desc')->first();
	}

}
