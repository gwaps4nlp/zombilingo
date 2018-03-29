<?php

namespace App\Listeners;

use App\Events\ImportCorpus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BroadCastImport extends Event implements ShouldBroadcast
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ConllParser $parser)
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ImportCorpus  $event
     * @return void
     */
    public function handle(BroadCastImport $event)
    {
        $event->parser->parse();
    }
    
}
