<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ScoreUpdated extends Event
{
    use SerializesModels;

    public $points;
    public $nb_annotations;
    public $challenge_id;
    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user,$points,$nb_annotations,$challenge_id)
    {
        $this->points= $points;
        $this->nb_annotations = $nb_annotations;
        $this->challenge_id = $challenge_id;
        $this->user = $user;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
