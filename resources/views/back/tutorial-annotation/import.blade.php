@extends('back.template')

@section('content')
	<h1>Import a file of items for the tutorials</h1>
	
		{!! Form::open(['url' => 'tutorial-annotation/import', 'method' => 'post', 'role' => 'form', 'files'=>true]) !!}
		File format (tab separated values) : <em>relation</em>   <em>level</em>   <em>type (1 or -1)</em>   <em>sentid</em>   <em>word_index</em>   <em>indication</em><br/><br/>
		{!! Form::control('file',0,'file',$errors,'File') !!}
		{!! Form::submit('Import', null,['class' => 'btn btn-success']) !!}
		{!! Form::close() !!}

@stop