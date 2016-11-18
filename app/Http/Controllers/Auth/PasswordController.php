<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\ChangePasswordRequest;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use Response, Hash;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest',['except' => ['postChange']]);
    }
	
    /**
     * Send a reset link to the given user.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function postEmail(Request $request)
    {
        $this->validate($request, ['email_reset' => 'required|email']);

        $response = Password::sendResetLink(['email'=>$request->input('email_reset')], function (Message $message) {
            $message->subject($this->getEmailSubject());
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return redirect()->back()->with('status', trans($response));

            case Password::INVALID_USER:
                return redirect()->back()->withErrors(['email_reset' => trans($response)]);
        }
    }

    /**
     * Reset the given user's password.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function postReset(ResetPasswordRequest $request)
    {

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
        
        Password::validator(function($credentials) {
            return $this->passwordValidator($credentials);
        });

        $response = Password::reset($credentials, function ($user, $password) {

            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return redirect('/user/home')->with('status', trans($response));

            default:
                return redirect()->back()
                            ->withInput($request->only('email'))
                            ->withErrors(['email' => trans($response)]);
        }
    }

    /**
     * Modify the given user's password.
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function postChange(ChangePasswordRequest $request)
    {

        $credentials = $request->only(
            'password', 'password_confirmation'
        );

        if (! $this->passwordValidator($credentials)) {
            $response = Password::INVALID_PASSWORD;
        } else {
            auth()->user()->password = Hash::make($request->input('password'));
            auth()->user()->save();  
            $response = Password::PASSWORD_RESET;          
        }

        return Response::json(trans($response));

    }

    /**
     * Determine if the passwords are valid for the request.
     *
     * @param  array  $credentials
     * @return bool
     */
    protected function passwordValidator(array $credentials)
    {
        list($password, $confirm) = [
            $credentials['password'],
            $credentials['password_confirmation'],
        ];

        return $password === $confirm && mb_strlen($password) >= 4;
    }	
}
