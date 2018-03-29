<?php

namespace App\Http\Middleware;

use Closure, Session, Response;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        }
        if(auth()->user()->session_id != Session::getId()){
            $this->auth->logout();
            
            if($request->ajax()) return Response::json(['error'=>"Ta session a expiré, merci de t'authentifier à nouveau."],401);

            return redirect()->guest('login')->withErrors(['message' => "Ta session a expiré, merci de t'authentifier à nouveau."]);
        }

        return $next($request);
    }
}
