<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\ConllParser;

class BroadCastImport extends Event implements ShouldBroadcast
{
    use InteractsWithQueue, SerializesModels;

    public $parser;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(ConllParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['import-corpus'];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['parser' => $this->parser];
    } 
		
}
