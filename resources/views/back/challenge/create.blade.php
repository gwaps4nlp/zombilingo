@extends('back.template')

@section('content')
<h1>Create a new challenge</h1>
{!! Form::open(['url' => 'challenge/create', 'method' => 'post', 'role' => 'form', 'files' => true]) !!}
	
	{!! Form::control('text', 0, 'name', $errors, 'Name') !!}
	{!! Form::control('textarea', 0, 'description', $errors, 'Description') !!}
	{!! Form::control('selection', 0, 'type_score', $errors, 'Type of challenge', $types_challenge,null,'Select a type of challenge...') !!}
	{!! Form::control('selection', 0, 'corpus_id', $errors, 'Corpus',$corpora,null,'Select a corpus...') !!}
	{!! Form::control('selection', 0, 'language_id', $errors, 'Language',$languages,null,'Select a language...') !!}
	{!! Form::control('file',0,'image',$errors,'Image (600px x 640px)') !!}
	<div class="form-group  {{ $errors->has('start_date') ? 'has-error' : '' }}" id="start_date">
		<label for="start_date" class="control-label">Date of start (dd/mm/yy)</label>
		{!! $errors->first('start_date', '<small class="help-block">:message</small>') !!}
			<input class="datepicker" name="start_date" value="{{ old('start_date') }}" />
	</div>
	<div class="form-group  {{ $errors->has('end_date') ? 'has-error' : '' }}" id="end_date">
		<label for="end_date" class="control-label">Date of end (dd/mm/yy)</label>
		{!! $errors->first('end_date', '<small class="help-block">:message</small>') !!}
			<input class="datepicker" name="end_date" value="{{ old('end_date') }}" />
	</div>


	<input type="submit" value="Save" class="btn btn-success" />
	<a href="{{ url('news/index') }}" class="btn btn-warning" role="button">Cancel</a>	
{!! Form::close() !!}

@stop

@section('style')
	{!! Html::style('css/bootstrap-datepicker3.css') !!}
@stop

@section('scripts')
	{!! Html::script('js/bootstrap-datepicker.js') !!}
	<script>
	$('.datepicker').datepicker({
	    format: 'dd/mm/yyyy',
	    autoclose: true,
	    todayBtn: "linked",
	});
	</script>
@stop