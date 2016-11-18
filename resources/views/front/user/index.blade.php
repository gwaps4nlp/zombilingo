@extends('front.template')

@section('main')

<div class="row">

    <div class="col-md-10 col-md-offset-1 center" id="classement">
  
        <div class="row" id="entete">
        <h1>
        @if($relation->id)
			{{ trans('game.ranking-relation', ['relation' => $relation->name]) }}
        @else
            {{ trans('game.overall-ranking') }}
        @endif
        </h1>
		<div class="col-md-6 col-md-offset-3">
			{!! Form::open(['url' => 'user/players', 'method' => 'get', 'role' => 'form']) !!}
			<div class="row">
			{!! Form::control('selection', 5, 'corpus_id', $errors, '',$corpora,null,trans('game.all-corpora'),$params['corpus_id']) !!}
			{!! Form::control('selection', 5, 'relation_id', $errors, '',$relations,null,trans('game.all-phenomena'),$params['relation_id']) !!}
			<input type="submit" value="Filtrer" class="btn btn-success" />
			</div>
			<div class="row">
			{!! Form::control('text', 10, 'username', $errors, '', '','','Trouver un joueur : pseudonyme') !!}
			<input type="submit" value="Chercher" class="btn btn-success" />
			</div>
			{!! Form::close() !!}	
        </div>
        </div>

	
	@if(count($users))

		<div class="ligne row" id="class-top">
			<div class="col-md-2">
				@if($params['sortby']=='score' && $params['order']=='desc')
					{!! link_to_action('UserController@getPlayers','Rang',array('sortby'=>'score','order'=>'asc','relation_id'=>$params['relation_id'],'corpus_id'=>$params['corpus_id'])) !!}
				@else
					{!! link_to_action('UserController@getPlayers','Rang',array('sortby'=>'score','order'=>'desc','relation_id'=>$params['relation_id'],'corpus_id'=>$params['corpus_id'])) !!}
				@endif
			</div>
			<div class="col-md-8 text-center">
				@if($params['sortby']=='username' && $params['order']=='desc')
					{!! link_to_action('UserController@getPlayers','Pseudonyme',array('sortby'=>'username','order'=>'asc','relation_id'=>$params['relation_id'],'corpus_id'=>$params['corpus_id'])) !!}
				@else
					{!! link_to_action('UserController@getPlayers','Pseudonyme',array('sortby'=>'username','order'=>'desc','relation_id'=>$params['relation_id'],'corpus_id'=>$params['corpus_id'])) !!}
				@endif
			</div>
			<div class="col-md-2 text-center">

				@if($params['sortby']=='score' && $params['order']=='desc')
					<a href="{!! route('players',array('sortby'=>'score','order'=>'asc','relation_id'=>$params['relation_id'],'corpus_id'=>$params['corpus_id']))  !!}">{!! Html::image('img/cerveau_plein.png') !!}</a>
				@else
					<a href="{!! route('players',array('sortby'=>'score','order'=>'desc','relation_id'=>$params['relation_id'],'corpus_id'=>$params['corpus_id']))  !!}">{!! Html::image('img/cerveau_plein.png') !!}</a>
				@endif

			</div>
		</div>
		<?php $index=1; ?>
		@foreach ($users as $user)
			@if($user->username == auth()->user()->username)
				<div class="ligne row" id="placeUser">
			@else
				<div class="ligne row">
			@endif
			<div class="col-md-2">
				{{ ($user->rank)? $user->rank : ($users->currentPage()-1)*$users->perPage()+$index++}}
			</div>
			<div user_id="{{ $user->id }}" class="text-center col-md-8">
				<a class="rank" user_id="{{ $user->id }}" href="{{ route('show-user',[$user->id]) }}">{{ $user->username }}</a>
			</div>
			<div class="text-center col-md-2">
				{{ Html::formatScore($user->score) }}
			</div>
			</div>

		@endforeach
		<div class="row">
		<div class="col-md-10 col-md-offset-1 text-center">
		{!! $users->render() !!}
		</div>
		</div>
    @else
		<center><h2 class="alert">{{ trans('game.no-player-phenomenon') }}</h2></center>
    @endif

    </div>
</div>
@stop
