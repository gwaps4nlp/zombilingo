<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\User;
use App\Models\ScheduledEmailNews;
use DB,App;

class PlanEmailNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:plan-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Plan emails of the news';

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
        $news = News::where('send_by_email',1)
            ->whereRaw('scheduled_at <= NOW()')
            ->whereNull('sent_at')
            ->first();
        if($news){

            $news->sent_at = DB::raw('now()');
            $news->save();

                if(App::environment('local')){
                    $users = User::where('email_frequency_id','!=',1)->where('role_id','=',2)
                        ->where('email','!=','')->get();
                } else {
                    $users = User::where('email_frequency_id','!=',1)
                        ->where('email','!=','')->get();
                }

                foreach($users as $user){
                    try {
                        ScheduledEmailNews::create(['scheduled_at'=>$news->scheduled_at,'user_id'=>$user->id,'news_id'=>$news->id]);
                    } catch (Exception $Ex){

                    }        
                }

        }

    }
}
