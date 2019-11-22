@extends('emails.template')

@section('content')
    <p style="font-size:18px;font-weight:700;color:#404040;line-height:24px;margin:0 0 15px 0">
        Salut <?php echo $user->username ?>, il y a des nouveautés sur {{ ucfirst(config('app.name')) }} !
    </p>
    <p style="color:#888;font-size:15px;font-weight:normal;line-height:24px;margin:0">
        <?php echo $news->content ?>
    </p>
    <p style="color:#404040;font-size:15px;font-weight:normal;line-height:24px;margin:15px 0 0 0;">
        À bientôt sur <a href="{{ config('app.url') }}" style="text-decoration:none;color:#888;" target="_blank">{{ ucfirst(config('app.name')) }}</a>,<br/>
        Bob le Zombie.
    </p>
 
    <div style="padding:20px;margin:0;text-align:center;font-size:12px;color:#bbbbbb">Si tu ne souhaites pas recevoir de notifications, tu peux te désabonner <a href="{{ config('app.url') }}/auth/unsubscribe?email=<?php echo $user->email ?>" style="color:#bbbbbb;font-weight:bold;text-decoration:none;font-size:12px!important" target="_blank">ici</a>
    </div> 
@stop