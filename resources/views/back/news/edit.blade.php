@extends('back.template')

@section('content')

<h1>Edit news</h1>
{!! Form::open(['url' => 'news/edit/'.$news->id, 'method' => 'post', 'role' => 'form']) !!}
	{!! Form::control('textarea', 0, 'content', $errors, 'Content',$news->content) !!}
	{!! Form::control('selection', 0, 'language_id', $errors, 'Language',$languages,null,'Select a language...',$news->language_id) !!}
	<input type="submit" value="Save" class="btn btn-success" />
	<a href="{{ url('news/index') }}" class="btn btn-warning" role="button">Cancel</a>
{!! Form::close() !!}

@stop