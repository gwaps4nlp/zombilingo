<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Repositories\ScoreRepository;
use App\Repositories\AnnotationUserRepository;
use App\Repositories\DuelRepository;
use App\Models\ScheduledEmail;
use DB,App;

class PlanMonthlyEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:plan-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Plan automatic monthly emails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ScoreRepository $scores, 
        AnnotationUserRepository $scores_annotation,
        DuelRepository $duels)
    {
        if(App::environment('local')){
            $users = User::where('email_frequency_id','=',4)->where('role_id','=',2)
                ->where('email','!=','')->get();
        } else {
            $users = User::where('email_frequency_id','=',4)->where('updated_at','>=','2015-12-01')
                ->where('email','!=','')->get();
        }

        $now = "0 day";
        $previous_period = "1 month";

        foreach($users as $user){

            $send_email = false;

            $score = $scores_annotation->getByUserAndPeriode($user,null,$now,12);
            $previous_score = $scores_annotation->getByUserAndPeriode($user,null,$previous_period,12);      
            if($score && $previous_score)
                $diff_rank =  $previous_score->rank - $score->rank ;
            else 
                $diff_rank = 0;

            if($diff_rank!=0)
                $send_email=true;

            // Is there new duels ?
            $new_duels = $duels->getPendingNotSeen($user,$previous_period);
            
            if(count($new_duels)>0)
                $send_email=true;            
            
            try {
                ScheduledEmail::create(['scheduled_at'=>DB::raw('now()'),'user_id'=>$user->id,'type'=>'monthly']);
            } catch (Exception $Ex){

            }
        }

    }
}
