@extends('front.template')

@section('main')
<div class="row">
	<div class="col-md-10 col-md-offset-1 center" style="text-align:center;" id="game">
		<h1>Joue avec les mots de la langue française</h1>
		<a class="btn btn-success btn-lg" href="{{ url('mini-game/definition') }}">Jeu des définitions</a>
		<a class="btn btn-success btn-lg" href="{{ url('mini-game/origin') }}">Jeu des origines</a>
		<a class="btn btn-success btn-lg" href="{{ url('mini-game/origin-proto') }}">Prototype borne</a>
	</div>
</div>
@stop