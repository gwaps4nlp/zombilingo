<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\DuelUser;
use App\Repositories\DuelRepository;
use App,Mail;

class SendEmailsDuel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send-duel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Plan automatic email when a new duel is created';

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
    public function handle(DuelRepository $duels)
    {
            
        $users_id = $duels->getUsersToSendEmail();

        foreach($users_id as $user_id){

            try {
                $user = User::findOrFail($user_id);

                if($user->email_duel){

                    $new_duels = $duels->getToSendEmail($user);

                    Mail::send('emails.duel-email', ['new_duels' => $new_duels,'user' => $user], function ($m) use ($user) {
                        $m->from('contact@zombilingo.org', 'ZombiLingo');

                        $m->to($user->email, $user->username)->subject('Alerte ZombiLingo - Nouveau duel');
                    });

                    DuelUser::where('user_id', '=', $user_id)->whereNull('email')->update(array('email' => 1));

                } else {
                    DuelUser::where('user_id', '=', $user_id)->whereNull('email')->update(array('email' => 0));
                }

            } catch (Exception $Ex){

            }
        }

    }
}
