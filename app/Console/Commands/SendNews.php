<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\User;
use App\Models\ScheduledEmailNews;
use DB;
use Mail;

class SendNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send emails of the news';

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
        $scheduled_email_news = ScheduledEmailNews::whereRaw('scheduled_at <= NOW()')
            ->whereNull('sent_at')
            ->take(15)->get();
        foreach($scheduled_email_news as $email){

            try {

                $email->sent_at = DB::raw('now()');
                $email->save();

                $user = User::findOrFail($email->user_id);
                $news = News::findOrFail($email->news_id);
                
                Mail::send('emails.news', ['user' => $user,'news'=>$news], function ($m) use ($user, $news) {
                    $m->from('contact@zombilingo.org', 'Bob le Zombie');

                    $m->to($user->email, $user->username)->subject($news->title);
                });
                $email->sent = 1;
                $email->save();

            } catch (Exception $Ex) {

            }

        }

    }
}
