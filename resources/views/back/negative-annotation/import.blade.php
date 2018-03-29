@extends('back.template')

@section('content')
	<h1>Import a file of negative items</h1>
	
		{!! Form::open(['url' => 'negative-annotation/import', 'method' => 'post', 'role' => 'form', 'files'=>true]) !!}
		File format (tab separated values) : <em>relation</em>   <em>sentid</em>   <em>word_index</em>   <em>explanation</em><br/><br/>
		{!! Form::control('file',0,'file',$errors,'File') !!}
		{!! Form::submit('Import', null,['class' => 'btn btn-success']) !!}
		{!! Form::close() !!}

@stop