<?php

namespace App\Events;

use App\Events\Event;
use App\Services\ConllParser;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ExportCorpus extends Event
{
    use SerializesModels;

	public $parser;
    /**
     * Create a new event instance.
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
        return ['import-corpus.'.$this->parser->corpus->id];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['done' => $this->parser::nb_setences];
    }    
}
