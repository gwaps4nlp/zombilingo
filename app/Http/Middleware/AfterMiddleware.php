<?php

namespace App\Http\Middleware;

use Closure;

use App\Services\GameGestionInterface as Game;

class AfterMiddleware
{

    /**
     * Create a new AfterMiddleware instance.
     *
     * @param  App\Services\GameGestionInterface $game
     * @return void
     */
    public function __construct(Game $game)
    {
		$this->game=$game;
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

        $response = $next($request);

        return $response;
    }
}
