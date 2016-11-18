@extends('front.template')


@section('main')


<div class="row">

    <div class="col-md-10 col-md-offset-1 center" id="classement">
  
        <div class="row" id="entete">
	        <h1>
	        Résultats des challenges
	        </h1>
			<div class="col-md-6 col-md-offset-3">
				{!! Form::open(['url' => 'challenge/results', 'method' => 'get', 'role' => 'form']) !!}
				<div class="row">
				{!! Form::control('selection', 5, 'challenge_id', $errors, '',$challenges,null,'Select a challenge',(isset($challenge))?$challenge->id:null) !!}
				<input type="submit" value="Résultats" class="btn btn-success" />
				</div>
				
				{!! Form::close() !!}	
	        </div>
        </div>
		@if(isset($challenge))
			<h2>{{ $challenge->name }} du {{ $challenge->start_date->format('d/m/Y') }} au {{ $challenge->end_date->format('d/m/Y') }}</h2>
			<table>
			@foreach ($scores_challenge as $user)
				<?php print_r($user); ?>
				@if($user->username == auth()->user()->username)
					<div class="ligne row" id="placeUser">
				@else
					<div class="ligne row">
				@endif
				<div class="col-md-2">
					{{ ($user->rank)? $user->rank : ($users->currentPage()-1)*$users->perPage()+$index++}}
				</div>
				<div user_id="{{ $user->user_id }}" class="text-center col-md-8">
					<a class="rank" user_id="{{ $user->user_id }}" href="{{ route('show-user',[$user->user_id]) }}">{{ $user->username }}</a>
				</div>
				<div class="text-center col-md-2">
					{{ Html::formatScore($user->score) }}
				</div>
				</div>

			@endforeach
		@endif

    </div>
</div>


@stop