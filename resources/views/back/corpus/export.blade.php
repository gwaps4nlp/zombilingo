@extends('back.template')

@section('content')
	<div id="exports_in_progress" style="display:none;">
	    <h1>Export in progress</h1>
		<div id="in_progress">
		</div>
		@if(isset($exports_pending))
			@foreach($exports_pending as $import)
			<div id="corpus{{ $import->parser->corpus->id}}">Corpus {{ $import->parser->corpus->id}} : {{ $import->parser->corpus->name}}
			<p>Progression : <span id="sentences_done{{ $import->parser->corpus->id}}">{{ $import->parser->sentences_done}}</span> sur <span id="nb_sentences{{ $import->parser->corpus->id}}">{{ $import->parser->nb_sentences}}</span></p>
			</div>
			@endforeach
		@endif
	</div>
        <h1>Export a corpus</h1>
		@if(count($corpora))

			{!! Form::open(['url' => 'corpus/export', 'method' => 'post', 'role' => 'form', 'files'=>true]) !!}
			{!! Form::control('selection', 0, 'corpus_id', $errors, 'Corpus',$corpora,null,'Select a corpus...') !!}
			<div class="form-group">
				<label for="score_init" class="control-label">Score calculation :</label>
			</div>			
			{!! Form::control('text', 2, 'score_init', $errors, 'score_init','5',null,null) !!}
			{!! Form::control('text', 2, 'weight_level', $errors, 'weight_level','1',null,null) !!}
			{!! Form::control('text', 2, 'weight_confidence', $errors, 'weight_confidence','0',null,null) !!}
			<div style="clear:both;"></div>
			<div class="form-group  ">
				<label for="type_export" class="control-label">Type of export</label>
				<input type="radio" name="type_export" value="complete" checked="checked" /> Complete
				<!-- <input type="radio" name="type_export" value="simple_with_scores" /> Simple with scores -->
				<input type="radio" name="type_export" value="simple" /> Simple
			</div>			
			{!! Form::submit('Export', null,['class' => 'btn btn-success']) !!}
			{!! Form::close() !!}
		@else
			<h3>No corpus found.</h3>
		@endif

		@include('back.corpus.history')

@stop

@section('scripts')

    <script src="{{ asset('js/socket.io.js') }}"></script>

    <script>
	var socket = io('{{ Config::get('broadcasting.url') }}');

    socket.on("export-corpus"+":App\\Events\\BroadCastExport", function(message){
    	
		var corpus_id = message.parser.corpus.id;
		if(corpus_id){
			$('#exports_in_progress').show();
		}
		var corpus_html=$('#corpus'+corpus_id);
		if(corpus_html.length == 0){
			var content = 'Corpus '+corpus_id+' : '+message.parser.corpus.name;
			content+= '<p>Progression : <span id="sentences_done'+corpus_id+'"></span> / <span id="nb_sentences'+corpus_id+'"></span></p>';
			var corpus_html = $('<div id="corpus'+corpus_id+'">'+content+'</div>');
			$('#in_progress').append(corpus_html);
		}
        $('#sentences_done'+corpus_id).text(message.parser.sentences_done);
        $('#nb_sentences'+corpus_id).text(message.parser.nb_sentences);
		if(message.parser.error){
			$('#corpus'+corpus_id).append("<alert>Error : "+message.parser.error+"</alert>");
		}        
		if(message.parser.url_file){
			$('#corpus'+corpus_id).append('Téléchargement : <a href="{{asset('/')}}'+message.parser.url_file+'"><i class="fa fa-download"></i></a>');
		}
     });

    </script>


@stop