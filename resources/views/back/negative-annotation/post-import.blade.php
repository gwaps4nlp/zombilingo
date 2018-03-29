@extends('back.template')

@section('content')

	<h1>Import a file of negative items</h1>
	<h4>Number of negative items imported : {{ $parser->numberImported }}</h4>
	@if(count($parser->errors))
		<h4>Errors : {{ count($parser->errors) }}</h4>
		@foreach($parser->errors as $error)
			{{ $error }}<br/>
		@endforeach
	@endif
		
@stop