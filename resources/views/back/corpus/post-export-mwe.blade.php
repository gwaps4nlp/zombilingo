@extends('back.template')

@section('content')
	<div id="exports_in_progress">
		<h4>Result of the export :</h4>
	    Number of mwes exported : {{ $parser->nb_mwes }}<br/>
	    Download : <a href="{{asset('/')}}{{ $parser->url_file }}"><span class="glyphicon glyphicon-download-alt"></span></a>
	    @if($parser->error)
	    	{{ $parser->error }}
	    @endif
	</div>

	@include('back.corpus.history')

@stop