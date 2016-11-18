@if($game->trophy)
    <h2>Vous avez débloqué le trophée {{ $game->trophy->name }}</h2>
@elseif($game->bonus)
    <h2>Vous avez débloqué le bonus {{ $game->bonus->name }}</h2>
@endif


{{ trans('game.over') }} {!! link_to('game', trans('game.play')) !!}
