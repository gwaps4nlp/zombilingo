<?php

namespace App\Repositories;

use App\Models\User, App\Models\Role, DB;

class UserRepository extends BaseRepository
{

	/**
	 * The Role instance.
	 *
	 * @var App\Models\Role
	 */	
	protected $role;

	/**
	 * Create a new UserRepository instance.
	 *
   	 * @param  App\Models\User $user
	 * @param  App\Models\Role $role
	 * @return void
	 */
	public function __construct(
		User $user, 
		Role $role)
	{
		$this->model = $user;
		$this->role = $role;
	}

	/**
	 * Save the User.
	 *
	 * @param  App\Models\User $user
	 * @param  Array  $inputs
	 * @return void
	 */
  	private function save($user, $inputs)
	{
		$user->username = $inputs['username'];
		$user->email = $inputs['email'];

		if(isset($inputs['role'])) {
			$user->role_id = $inputs['role'];	
		} else {
			$role_user = $this->role->where('slug', 'user')->first();
			$user->role_id = $role_user->id;
		}

		$user->save();
	}

	/**
	 * Get users collection paginate.
	 *
	 * @param  int  $n
	 * @param  string  $role
	 * @return Illuminate\Support\Collection
	 */
	public function index($n, $role)
	{
		if($role != 'total')
		{
			return $this->model
			->with('role')
			->whereHas('role', function($q) use($role) {
				$q->whereSlug($role);
			})		
			->oldest('seen')
			->latest()
			->paginate($n);			
		}

		return $this->model
		->with('role')		
		->oldest('seen')
		->latest()
		->paginate($n);
	}

	/**
	 * Count the users by Role.
	 *
	 * @param  string  $role
	 * @return int
	 */
	public function count($role = null)
	{
		if($role)
		{
			return $this->model
			->whereHas('role', function($q) use($role) {
				$q->whereSlug($role);
			})->count();			
		}

		return $this->model->count();
	}

	/**
	 * Count the number of connected users.
	 *
	 * @return int
	 */
	public function countConnected()
	{
		return $this->model->where('connected','=',1)->where('last_action_at', '>',DB::raw('DATE_SUB(NOW(), INTERVAL 2 HOUR)'))->count();
	}

	/**
	 * Return a list of connected users.
	 *
	 * @return Collection of User
	 */
	public function getConnected()
	{
		return $this->model->where('connected','=',1)->where('last_action_at', '>',DB::raw('DATE_SUB(NOW(), INTERVAL 2 HOUR)'))->get();
	}

	/**
	 * Return the last registered user.
	 *
	 * @return User
	 */
	public function getLastRegistered()
	{
		return $this->model->orderBy('id', 'desc')->first();
	}

	/**
	 * Return the list of all users.
	 *
	 * @return Collection of User
	 */
	public function getAll()
	{
		return $this->model->orderBy('score','desc')->get();
	}

	/**
	 * Return the list of all users.
	 *
	 * @return Collection of [username => id]
	 */
	public function getList()
	{
		return $this->model->orderBy('score','desc')->lists('username','id');
	}

	/**
	 * Return the count of new registrations by week.
	 *
	 * @return int
	 */
	public function countRegistrationsByWeek()
	{
		$this->model->setVisible(['count','week']);
		$list = $this->model
			->select(DB::raw('YEARWEEK(created_at) as week, count(*) as count'))
			->groupBy(DB::raw('YEARWEEK(created_at)'))->get();
		foreach($list as &$user)
			$user->setVisible(['count','week']);
		return $list;
	}
		
	/**
	 * Return the levels available for a user
	 *
	 * @return int
	 */
	public function getLevelsAvailable()
	{
		return $this->model->levels()->with('relation')->get();
	}
	
	/**
	 * Create a user.
	 *
	 * @param  array  $inputs
	 * @param  int    $confirmation_code
	 * @return App\Models\User 
	 */
	public function store($inputs, $confirmation_code = null)
	{
		$user = new $this->model;

		$user->password = bcrypt($inputs['password']);

		if($confirmation_code) {
			$user->confirmation_code = $confirmation_code;
		} else {
			$user->confirmed = true;
		}

		$this->save($user, $inputs);

		return $user;
	}

	/**
	 * Get statut of authenticated user.
	 *
	 * @return string
	 */
	public function getStatut()
	{
		return session('statut');
	}

	/**
	 * Confirm a user.
	 *
	 * @param  string  $confirmation_code
	 * @return App\Models\User
	 */
	public function confirm($confirmation_code)
	{
		$user = $this->model->whereConfirmationCode($confirmation_code)->firstOrFail();

		$user->confirmed = true;
		$user->confirmation_code = null;
		$user->save();
	}

}
