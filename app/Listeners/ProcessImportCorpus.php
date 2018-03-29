<?php

namespace App\Listeners;

use App\Events\ImportCorpus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessImportCorpus implements ShouldQueue
{
	use InteractsWithQueue;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ImportCorpus  $event
     * @return void
     */
    public function handle(ImportCorpus $event)
    {
        $event->parser->parse();
    }
}
