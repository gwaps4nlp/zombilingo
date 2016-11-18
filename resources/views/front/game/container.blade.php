@extends('front.template')

@section('css')
    @if($game->mode=='mwe')
    {!! Html::style('css/mwe.css') !!}
    @endif
    {!! Html::style('css/bootstrap-tour.css') !!}
    {!! Html::style('css/non-sass.css') !!}
@stop

@section('main')
<div class="row">
    <div class="col-md-10 col-md-offset-1 center" id="blocJeu">
        @include('partials.'.$game->mode.'.container')
    </div>
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
	@else
    $(document).ready(function(){
        initGame('{{$game->mode}}','{{$game->relation->id}}');
    })
	@endif
    </script>
@stop