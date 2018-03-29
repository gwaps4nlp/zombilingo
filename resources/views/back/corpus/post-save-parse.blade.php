@extends('front.template-iframe')

@section('main')

	<h1>Import in progress</h1>
	<div id="in_progress">
	</div>
	@if(isset($imports_pending))
		@foreach($imports_pending as $import)
			<div id="corpus{{ $import->parser->corpus->id}}">Corpus {{ $import->parser->corpus->id}} : {{ $import->parser->corpus->name}}
		<p>Progression : <span id="lines_done{{ $import->parser->corpus->id}}">{{ $import->parser->lines_done}}</span> sur <span id="number_of_lines{{ $import->parser->corpus->id}}">{{ $import->parser->number_of_lines}}</span></p>
		<p>Number of sentences imported : <span id="sentences_done{{ $import->parser->corpus->id}}">0</span></p>
		<p>Errors : <br/><span id="errors{{ $import->parser->corpus->id}}"></span></p>
			</div>
		@endforeach
	@endif
		
@stop

@section('scripts')

<script src="{{ asset('js/socket.io.js') }}"></script>

<script>
var socket = io('{{ Config::get('broadcasting.url') }}');

socket.on("import-corpus"+":App\\Events\\BroadCastImport", function(message){
	var corpus_id = message.parser.corpus.id;
	var corpus_html=$('#corpus'+corpus_id);
	if(corpus_html.length == 0){
		var content = 'Corpus '+corpus_id+' : '+message.parser.corpus.name;
		content+= '<p>Done : <span id="lines_done'+corpus_id+'"></span> / <span id="number_of_lines'+corpus_id+'"></span></p>';
		content+= '<p>Number of sentences imported : <span id="sentences_done'+corpus_id+'"></span></p>';
		content+= '<p>Errors : <br/><span id="errors'+corpus_id+'"></span></p>';
		var corpus_html = $('<div id="corpus'+corpus_id+'">'+content+'</div>');
		$('#in_progress').append(corpus_html);
	}
	if(message.parser.error!="")
		$('#errors'+corpus_id).append(message.parser.error+'<br/>');
	$('#sentences_done'+corpus_id).text(message.parser.sentences_done);
	$('#lines_done'+corpus_id).text(message.parser.lines_done);
	$('#number_of_lines'+corpus_id).text(message.parser.number_of_lines);
 });

</script>


@stop