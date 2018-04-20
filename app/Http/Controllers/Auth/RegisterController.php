<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Auth\Events\Registered;
use App\Repositories\EmailFrequencyRepository;
use App\Http\Requests\FrequencyEmailRequest;
use Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $user->level_id = 1;
        $user->save();
        if($request->session()->has('demo.points_earned')){
            $user->score = session('demo.points_earned');
            $user->save();
        }

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => 'required|max:20',
            'email' => 'email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        $user->roles()->attach(Role::getUser()->id);
        return $user;
    }



    public function getUnsubscribe(Request $request,EmailFrequencyRepository $email_frequencies){
        $email_frequency = $email_frequencies->getAll();
        return view('auth.unsubscribe',['email'=>$request->input('email'),'email_frequency'=>$email_frequency]);
    }

	public function postUnsubscribe(FrequencyEmailRequest $request,EmailFrequencyRepository $email_frequencies){

        $email_frequency = $email_frequencies->getAll();
        $user = User::where('email', '=', $request->input('email'))->first();
        if($user){
            $user->email_frequency_id = $request->input('email_frequency_id');
            $user->save();
        }
        Session::flash('message', 'Ta demande a bien été prise en compte.');
		return view('auth.unsubscribe',['email'=>'','email_frequency'=>$email_frequency]);
	}

}
