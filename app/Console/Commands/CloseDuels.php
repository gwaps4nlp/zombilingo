<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Duel;
use DB;

class CloseDuels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'duels:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Free the duels not completed of more than 14 days';

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
    public function handle()
    {
        $duels_to_close = Duel::where('state','in_progress')->whereRaw('DATEDIFF(NOW(), updated_at) > 14')->get();
        foreach($duels_to_close as $duel){
            $users_completed = array();
            $users_in_progress = array();
            foreach($duel->users as $user){
                if($user->pivot->turn==$duel->nb_turns){
                    $users_completed[]=$user;
                } else {
                    $users_in_progress[]=$user;
                }
            }
            if($users_completed){
                foreach($users_in_progress as $user){
                    $duel->users()->detach($user->id);
                }
                $duel->state = 'pending';
                $duel->save();
            }
        }
    }
}
