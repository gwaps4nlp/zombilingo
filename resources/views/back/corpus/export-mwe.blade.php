@extends('back.template')

@section('content')
        <h1>Export a corpus</h1>

		{!! Form::open(['url' => 'corpus/export-mwe', 'method' => 'post', 'role' => 'form']) !!}
		{!! Form::submit('Export', null,['class' => 'btn btn-success']) !!}
		{!! Form::close() !!}

		@include('back.corpus.history')

@stop
