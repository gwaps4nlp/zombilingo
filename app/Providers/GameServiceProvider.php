<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\GameModeRepository;
use App\Models\GameMode;
use Config;

class GameServiceProvider extends ServiceProvider
{

    protected $defer = true;
	
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      
    }    

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		$game_mode = $this->app['request']->segment(2);
		$game_modes = Config::get('game.modes');
		$className = (array_key_exists($game_mode,$game_modes))?Config::get("game.modes.".$game_mode.".class_name"):'Game';
		$serviceName = 'App\Services\\'.$className.'Gestion';
        $this->app->bind('App\Services\GameGestionInterface', $serviceName);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['App\Services\GameGestionInterface'];
    }    
 
}
