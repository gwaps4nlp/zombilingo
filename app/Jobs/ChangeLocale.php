<?php

namespace App\Jobs;

use App\Jobs\Job;

class ChangeLocale extends Job
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        session()->put('locale', session('locale') == 'fr' ? 'en' : 'fr');
    }
}
