<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Message;
use App\Models\Discussion;
use App\Models\Role;
use Mail, Log, Config;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $discussion;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(\App\Models\Message $message, \App\Models\Discussion $discussion)
    {
        $this->message = $message;
        $this->discussion = $discussion;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $message = $this->message;
        $discussion = $this->discussion;
        $data['content'] = $message->content;
        if(!$message->user){
           Log::info('Message sans user: '.$message->id);
           return;
        }
        $data['username_message'] = $message->user->username;
        $data['discussion_id'] = $discussion->id;
        $data['message_id'] = $message->id;
        $data['entity_id'] = $discussion->entity_id;
        $data['entity_type'] = $discussion->entity_type;

        $role_admin = Role::where('slug','=','super-admin')->first();

        $administrators = $role_admin->users()->get();

        $administrators_ids = [];
        foreach($administrators as $user){
            $administrators_ids[]=$user->id;
            $data['username'] = $user->username;
            $data['user'] = $user;
            // A notification is send if the administrator is not the author
            if($user->id != $message->user_id)
                Mail::send('emails.notification-message', $data , function ($m) use ($user) {
                    $m->from('contact@zombilingo.org', 'ZombiLingo');
                    $m->to($user->email, $user->username)->subject("Nouveau message dans le forum");
                });
        }

        //Users who follow the thread
        $users = $discussion->subscribers;

        foreach($users as $user){
            // A notification is send if the user is not the author
            if($user->id != $message->user_id && !in_array($user->id,$administrators_ids)){
                $data['username'] = $user->username;
                $data['user'] = $user;
                if($user->email)
                Mail::send('emails.notification-message', $data , function ($m) use ($user) {
                    $m->from('contact@zombilingo.org', 'ZombiLingo');
                    $m->to($user->email, $user->username)->subject("Quelqu'un a commenté une discussion que tu surveilles");
                });
            }
        }
    }
}
