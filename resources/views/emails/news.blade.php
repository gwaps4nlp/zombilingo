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

@stop