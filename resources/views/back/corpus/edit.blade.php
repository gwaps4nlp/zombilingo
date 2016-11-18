@extends('back.template')

@section('content')

<h1>Edit corpus</h1>
{!! Form::open(['url' => 'corpus/edit/'.$corpus->id, 'method' => 'post', 'role' => 'form']) !!}

	{!! Form::control('text', 0, 'name', $errors, 'Name',$corpus->name) !!}
	{!! Form::control('textarea', 0, 'description', $errors, 'Description',$corpus->description) !!}
	{!! Form::control('text', 0, 'title', $errors, 'Title of the document',$corpus->title) !!}
	{!! Form::control('text', 0, 'url_source', $errors, 'Source of the document (url or other)',$corpus->url_source) !!}	
	{!! Form::control('text', 0, 'url_info_license', $errors, 'Information about license (url or other)',$corpus->url_info_license) !!}	
	<div class="form-group">
		<label for="reference">Type of corpus</label><br />
		{!! Form::radioInLine('source_id', '1', 'Reference', $corpus->source_id,$errors ) !!}
		{!! Form::radioInLine('source_id', '3', 'Pre-annotated', $corpus->source_id, $errors) !!}
		{!! Form::radioInLine('source_id', '5', 'Pre-annotated (for evaluation)', $corpus->source_id, $errors) !!}
	</div>
	{!! Form::control('selection', 0, 'license_id', $errors, 'License',$licenses,null,'Select a license...',$corpus->license_id) !!}
	{!! Form::control('selection', 0, 'language_id', $errors, 'Language',$languages,null,'Select a language...',$corpus->language_id) !!}

	<div style="display:none;" class="form-group preannotated" id="corpus_playable">
		<label for="reference">Corpus playable directly ?</label><br />
		{!! Form::radioInLine('playable', '1','Yes',$corpus->playable,$errors) !!}
		{!! Form::radioInLine('playable', '0', 'No',$corpus->playable,$errors) !!}
	</div>
	
	<div style="display:none;" class="form-group preannotated" id="bound_corpora">
		<label for="description" class="control-label">Subcorpora</label>
		{!! Form::select('subcorpus[]',$preannotated_corpora,$corpus->subcorpora->pluck('id')->toArray(),['multiple'=>true,'class'=>'form-control']) !!}	
		<label for="description" class="control-label">Bound reference corpora</label>
		{!! Form::select('reference_corpus[]',$reference_corpora,$corpus->bound_corpora->pluck('id')->toArray(),['multiple'=>true,'class'=>'form-control']) !!}
		<label for="description" class="control-label">Bound evaluation corpora</label>
		{!! Form::select('evaluation_corpus[]',$evaluation_corpora,$corpus->evaluation_corpora->pluck('id')->toArray(),['multiple'=>true,'class'=>'form-control']) !!}
	</div>

	<input type="submit" value="Save" class="btn btn-success" />
	<a href="{{ url('corpus/index') }}" class="btn btn-warning" role="button">Cancel</a>
{!! Form::close() !!}

@stop

@section('scripts')
<script>
if($( "input:radio[name=source_id]:checked").val()==3){
	$('.preannotated').show();
}
$(document).ready(function () {
    $('input:radio[name=source_id]').change(function () {
        if (this.value==3) {
            $('.preannotated').show();
        } else {
        	$('.preannotated').hide();
        }
    });
});
</script>
@stop