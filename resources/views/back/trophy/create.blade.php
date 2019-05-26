@extends('back.template')

@section('content')

<h1>New Trophy</h1>
{!! Form::open(['url' => 'trophy/create', 'method' => 'post', 'role' => 'form']) !!}
	{!! Form::control('text', 0, 'slug', $errors, 'Slug','trophy-') !!}
	{!! Form::control('text', 0, 'name', $errors, 'Name','') !!}
	{!! Form::control('text', 0, 'key', $errors, 'Criterion','') !!}
	{!! Form::control('text', 0, 'required_value', $errors, 'Required value','') !!}
	{!! Form::control('textarea', 0, 'description', $errors, 'Description','') !!}
	{!! Form::control('text', 0, 'points', $errors, 'Points','') !!}
	{!! Form::control('text', 0, 'is_secret', $errors, 'Is secret','') !!}

	<input type="submit" value="Save" class="btn btn-success" />
{!! Form::close() !!}
@stop