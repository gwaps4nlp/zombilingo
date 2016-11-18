<h1>{{ trans('game.new-duel') }}</h1>
{!! Form::open(['url' => 'duel/new', 'id' => 'form-new-duel', 'method' => 'post', 'role' => 'form']) !!}

<div class="row">
	<div class="col-md-8 col-md-offset-2">
		{!! Form::control('selection', 0, 'relation_id', $errors, "1-".trans('game.choice-phenomenon'),$relations,null,"Phénomène au hasard") !!}
	</div>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2">
		{!! Form::control('selection', 0, 'challenger_id', $errors, "2-".trans('game.choice-opponent')."*",$enemies,null,"Duel ouvert") !!}
	</div>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<?php
		$nb_turns = array( '10'=>10,'20'=>20,'50'=>50,'100'=>100);
		?>
		{!! Form::control('selection', 0, 'nb_turns', $errors, "3-".trans('game.number-turns'),$nb_turns,null,null) !!}
	</div>
</div>

<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<div class="form-group" style="margin-top:25px;">
			<a class="btn btn-danger btn-lg" href="{{ url('duel') }}">{{ trans('site.cancel') }}</a>
			<input type="submit" id="submitNewDuel" value="{{ trans('game.begin-the-duel') }}" class="btn btn-success btn-lg" />
		</div>
	</div>
</div>
{!! Form::close() !!}
<div>* {{ trans('game.asterisk-new-duel') }} {!! link_to('user/players', trans('site.find-friend')) !!}.</div>
<style>
div.form-group label{
	font-weight:500;
	font-size:24px;
}
#nb_turns {
	width:70px;
	margin:auto;
	text-align:center;
	display:inline;
}
</style>