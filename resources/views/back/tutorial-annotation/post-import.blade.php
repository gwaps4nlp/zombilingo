@extends('back.template')

@section('content')

	<h1>Import a file of items for the tutorials</h1>
	<h4>Number of items imported : {{ $parser->numberImported }}</h4>
	@if(count($parser->errors))
		<h4>Errors : {{ count($parser->errors) }}</h4>
		@foreach($parser->errors as $error)
			{{ $error }}<br/>
		@endforeach
	@endif
		
@stop