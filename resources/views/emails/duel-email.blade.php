@extends('emails.template')

@section('content')

    <h1 style="font-size:18px;font-weight:500;color:#505050;line-height:24px;margin:0 0 15px 0">
        Salut <strong>{{ $user->username }}</strong>,
    </h1>

    @foreach($new_duels as $duel)
    <p style="color:#505050;font-size:15px;font-weight:normal;line-height:24px;margin:0">
        {{ $duel->challenger($user)->username }} vient de te défier en duel !<br/>
        <a class="btn btn-success" href="{{ config('app.url') }}/duel/index?tab=in_progress">Jouer maintenant</a><br/>
    </p>    
    @endforeach
    <br/>
    <p style="color:#888;font-size:15px;font-weight:normal;line-height:24px;margin:0">
        A bientôt sur ZombiLingo.
    </p>      
@stop
