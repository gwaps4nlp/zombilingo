<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\EmailFrequency;
use Validator;
use Auth;
use Hash;
use Session, Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\FrequencyEmailRequest;
use App\Repositories\EmailFrequencyRepository;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['getLogout','getUnsubscribe','postUnsubscribe']]);
    }
    
    public function getLogin(Request $request){
        if($request->ajax())
            return view('partials.auth.modal-login');
        return view('auth.login');
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

	public function getRegister(Request $request){
		if($request->ajax())
			return view('partials.auth.modal-register');
		return view('auth.register');
	}
	
    public function postLogin(LoginRequest $request)
    {
        		
        if (Auth::attempt(['username' => $request->input('log'), 'password' => $request->input('password_log')],true)) {
            // Authentication passed...
            if($request->ajax())
                return Response::json(['href'=>Session::get('url.intended', url('/'))]);
            return redirect()->intended('/user/home')->withInputs(['new_log'=>1]);
        }

        if($request->ajax())
            return Response::json(['log'=> [$this->getFailedLoginMessage()]],422);

        return back()
            ->withInput($request->only('log'))
            ->withErrors([
                'message' => $this->getFailedLoginMessage(),
            ]);

    }

	/**
	 * Handle a registration request for the application.
	 *
	 * @param  App\Http\Requests\RegisterRequest  $request
	 * @param  App\Repositories\UserRepository $user_gestion
	 * @return Illuminate\Http\Response
	 */
	public function postRegister(
		RegisterRequest $request)
	{		
		
		$user = $this->create($request->all());
		if($request->session()->has('demo.points_earned')){
			$user->score = session('demo.points_earned');
			$user->save();
		}
        Auth::login($user);
		return redirect('user/home')->with('message', trans('site.welcome-register', ['name' => $user->username]));
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
            'username' => 'required|max:255|unique:users',
            'email' => 'email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
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
        return User::create([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role_id' => 1
        ]);
    }
}
