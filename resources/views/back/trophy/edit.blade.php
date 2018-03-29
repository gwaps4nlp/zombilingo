@extends('back.template')

@section('content')

<h1>Edit Trophy</h1>
{!! Form::open(['url' => 'trophy/edit', 'method' => 'post', 'role' => 'form']) !!}
	<div class="form-group  ">
		<label for="value" class="control-label">Key</label>
		<input class="form-control" type="text" value="{{ $trophy->slug }}" disabled="disabled"/>
	</div>
	<input type="hidden" name="id" value="{{ $trophy->id }}" />
	{!! Form::control('text', 0, 'name', $errors, 'Name',$trophy->name) !!}
	{!! Form::control('text', 0, 'key', $errors, 'Criterion',$trophy->key) !!}
	{!! Form::control('text', 0, 'required_value', $errors, 'Require value',$trophy->required_value) !!}
	{!! Form::control('textarea', 0, 'description', $errors, 'Description',$trophy->description) !!}
	{!! Form::control('text', 0, 'points', $errors, 'Points',$trophy->points) !!}
	{!! Form::control('text', 0, 'is_secret', $errors, 'Is secret',$trophy->is_secret) !!}

	<input type="submit" value="Save" class="btn btn-success" />
{!! Form::close() !!}
@stop