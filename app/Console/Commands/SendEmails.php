<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ScheduledEmail;
use App\Repositories\ScoreRepository;
use App\Repositories\DuelRepository;
use App\Repositories\AnnotationUserRepository;
use DB,Mail;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled emails to users';

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
    public function handle(ScoreRepository $scores, AnnotationUserRepository $scores_annotation, DuelRepository $duels)
    {
        $scheduled_emails = ScheduledEmail::whereRaw('scheduled_at <= NOW()')
            ->whereNull('sent_at')
            ->take(15)->get();
        foreach($scheduled_emails as $email){

            try {

                $email->sent_at = DB::raw('now()'); 
                $email->save();

                $user = User::findOrFail($email->user_id);
                $enemies = $user->getAcceptedFriends();
                $neighbors = $scores->neighbors($user,3);   
                $neighbors_challenge = $scores_annotation->neighbors($user,3);
                $pending_enemies = $user->getAskFriendRequests();
                $scores_user = $scores->getByUser($user);
                $scores_annotation_user = $scores_annotation->getByUser($user);
                Mail::send('emails.daily-email', ['scores_annotation_user' => $scores_annotation_user , 'scores_user' => $scores_user , 'email' => $email, 'user' => $user, 'enemies' => $enemies, 'duels' => $duels, 'neighbors' => $neighbors, 'pending_enemies' => $pending_enemies, 'scores' => $scores, 'scores_annotation' => $scores_annotation, 'neighbors_challenge' => $neighbors_challenge], function ($m) use ($user) {
                    $m->from('contact@zombilingo.org', 'ZombiLingo');

                    $m->to($user->email, $user->username)->subject('Alerte ZombiLingo - Résultats du challenge');
                });

                $email->sent = 1;
                $email->save();

            } catch (Exception $Ex) {

            }
        }        
    }
}
