<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Corpus;

class BroadCastNewAnnotation extends Event implements ShouldBroadcast
{

    use InteractsWithQueue, SerializesModels;

    public $number_annotations;
    
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct($number_annotations)
    {
        $this->number_annotations = $number_annotations;
    }    
    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['new-annotation'];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['number' => $this->number_annotations];
    } 
        
}
