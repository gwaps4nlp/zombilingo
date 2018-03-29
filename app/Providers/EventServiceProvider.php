<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Login' => ['App\Services\Statut@setLoginStatut'],
        'Illuminate\Auth\Events\Logout' => ['App\Services\Statut@setVisitorStatut'],
        'user.access' => ['App\Services\Statut@setStatut'],
        'App\Events\MessagePosted' => ['App\Listeners\SendMessageNotification'],
        'App\Events\ScoreUpdated' => ['App\Listeners\UpdateScore'],
    ];

    /**
     * Register any other events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
