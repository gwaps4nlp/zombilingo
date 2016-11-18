@extends('back.template')

@section('content')

<h1>Edit constant game</h1>
{!! Form::open(['url' => 'constant-game/edit', 'method' => 'post', 'role' => 'form']) !!}
	<div class="form-group  ">
		<label for="value" class="control-label">Key</label>
		<input class="form-control" type="text" value="{{ $constant->key }}" disabled="disabled"/>
	</div>
	<input type="hidden" name="id" value="{{ $constant->id }}" />
	{!! Form::control('text', 0, 'value', $errors, 'Value',$constant->value) !!}
	{!! Form::control('textarea', 0, 'description', $errors, 'Description de la constante',$constant->description) !!}

	<input type="submit" value="Save" class="btn btn-success" />
{!! Form::close() !!}
@stop