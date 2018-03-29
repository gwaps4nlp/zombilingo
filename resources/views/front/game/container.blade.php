@extends('front.template-game')

@section('css')
    @if($game->mode=='mwe')
    {!! Html::style('css/mwe.css') !!}
    @endif
    {!! Html::style('css/bootstrap-tour.css') !!}
@stop

@section('main')
<div id="block-game" class="container-game">
    @include('partials.'.$game->mode.'.container')
</div>
@stop

@section('scripts')
    {!! Html::script('js/bootstrap-tour.js') !!}
    @if($game->user->level_id < 2)
        {!! Html::script('js/tour-basic.js') !!}
        {!! Html::script('js/tour-game.js') !!}
    @endif
    <script>
	@if($game->mode=='mwe')
    $(document).ready(function(){
        initMwe();
    })
    @elseif($game->mode=='special')
    $(document).ready(function(){
        initGame('{{$game->mode}}','{{$game->relation->id}}');
    })  
    @elseif($game->mode=='admin-game')
    $(document).ready(function(){
        initGame('{{$game->mode}}','{{$game->relation->id}}');
    })
    @elseif($game->mode=='duel')
    $(document).ready(function(){
        initGame('{{$game->mode}}','{{$game->duel->id}}');
    })  
	@elseif($game->mode=='upl')
    $(document).ready(function(){
        initGame('{{$game->mode}}','{{$game->stage->id}}');
    })	
	@else
    $(document).ready(function(){
        initGame('{{$game->mode}}','{{$game->relation->id}}');
    })
	@endif
    </script>
@stop