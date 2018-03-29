<?php

namespace App\Listeners;

use App\Events\ScoreUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\ScoreMonth;
use App\Models\ScoreWeek;
use App\Models\ScoreChallenge;
use DB;
use Mail, Log;
class UpdateScore
{
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
     * @param  ScoreUpdated  $event
     * @return void
     */
    public function handle(ScoreUpdated $event)
    {

        $user = $event->user;
        $points = $event->points;
        $nb_annotations = $event->nb_annotations;
        $challenge_id = $event->challenge_id;

        $score_month = ScoreMonth::firstorCreate(array(
            'user_id'=>$user->id,
            'yearmonth'=>DB::raw("DATE_FORMAT(NOW(), '%Y%m')"),
            ));
        $score_month->increment('points',$points);
        $score_month->increment('number_annotations',$nb_annotations);

        $score_week = ScoreWeek::firstorCreate(array(
            'user_id'=>$user->id,
            'yearweek'=>DB::raw("YEARWEEK(NOW())"),
            ));    
        $score_week->increment('points',$points);
        $score_week->increment('number_annotations',$nb_annotations);

        if($challenge_id){
            $score_challenge = ScoreChallenge::firstorCreate(array(
                'user_id'=>$user->id,
                'challenge_id'=>$challenge_id,
                ));
                
            $score_challenge->increment('points',$points);
            $score_challenge->increment('number_annotations',$nb_annotations);
        }
    }
}
