@extends('back.template')

@section('content')
	<h1>Import a corpus</h1>
		@if(count($corpora))
		{!! Form::open(['url' => 'corpus/import', 'method' => 'post', 'role' => 'form', 'files'=>true]) !!}
		{!! Form::control('selection', 0, 'corpus_id', $errors, 'Corpus',$corpora,null,'Select a corpus...') !!}
		{!! Form::control('file',0,'corpus_file',$errors,'Fichier') !!}
		<div class="form-group">
		  <label for="sentence_filter" class="control-label">Mode</label>
		  <input type="radio" name="mode" value="insert" checked="true"/> Insert 
		  <input type="radio" name="mode" value="update"/> Update
		</div>	
		<div class="form-group">
		  <label for="sentence_filter" class="control-label">Parse</label>
		  <input type="radio" name="sentence_filter" value="all" checked="true"/> All 
		  <input type="radio" name="sentence_filter" value="1mod4"/> only id=1[4] 
		  <input type="radio" name="sentence_filter" value="1mod4"/> only id=3[4]
		</div>		
		{!! Form::submit('Import', null,['class' => 'btn btn-success']) !!}
		{!! Form::close() !!}
	@else
		<h2>Aucun corpus trouvé, il faut en créer un</h2>
	@endif
		</div>
	</div>
@stop