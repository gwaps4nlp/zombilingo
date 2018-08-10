<?php

namespace App\Jobs;

use App\Jobs\Job;
use Request, Config;

class SetLocale extends Job
{
    /**
     * The availables languages.
     *
     * @array $languages
     */
    protected $languages = ['en','fr'];

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        if(!session()->has('locale'))
        {
            session()->put('locale', Config::get('app.locale'));
            // session()->put('locale', Request::getPreferredLanguage($this->languages));
        }

        app()->setLocale(session('locale'));
    }
}
