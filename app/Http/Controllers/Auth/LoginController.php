<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Lang;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['username' => $request->input('log'), 'password' => $request->input('password_log')],true)) {
            if($request->ajax())
                return Response::json(['href'=>Session::get('url.intended', url('/'))]);
            return redirect()->intended($this->redirectPath())->withInputs(['new_log'=>1]);
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
     * Get the failed login message.
     *
     * @return string
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
                ? Lang::get('auth.failed')
                : 'These credentials do not match our records.';
    }

}
