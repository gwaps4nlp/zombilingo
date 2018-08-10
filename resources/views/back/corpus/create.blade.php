@extends('back.template')

@section('content')

<h1>Add a new corpus</h1>
{!! Form::open(['url' => 'corpus/create', 'method' => 'post', 'role' => 'form']) !!}

	{!! Form::control('text', 0, 'name', $errors, 'Name') !!}
	{!! Form::control('textarea', 0, 'description', $errors, 'Description') !!}
	{!! Form::control('text', 0, 'title', $errors, 'Title of the document') !!}
	{!! Form::control('text', 0, 'url_source', $errors, 'Source of the document (url or other)') !!}	
	{!! Form::control('text', 0, 'url_info_license', $errors, 'Information about license (url or other)') !!}
	<div class="form-group">
		<label for="reference">Type of Corpus</label><br />
		{!! Form::radioInLine('source_id', '1','Reference',null,$errors) !!}
		{!! Form::radioInLine('source_id', '3', 'Pre-annotated',null,$errors) !!}
		{!! Form::radioInLine('source_id', '5', 'Pre-annotated (for evaluation)', null, $errors) !!}		
	</div>
	{!! Form::control('selection', 0, 'license_id', $errors, 'License',$licenses,null,'Select a license...') !!}
	{!! Form::control('selection', 0, 'language_id', $errors, 'Language',$languages,null,'Select a language...') !!}

	<div class="form-group preannotated" style="display:none;" id="corpus_playable">
		<label for="reference">Corpus playable directly ?</label><br />
		{!! Form::radioInLine('playable', '1','Yes',null,$errors) !!}
		{!! Form::radioInLine('playable', '0', 'No',null,$errors) !!}
	</div>

	<div class="form-group preannotated"  style="display:none;" id="corpus_exportable">
		<label for="reference">Exportable corpus ?</label><br />
		{!! Form::radioInLine('exportable', '1','Yes',null,$errors) !!}
		{!! Form::radioInLine('exportable', '0', 'No',null,$errors) !!}
	</div>

	<div class="form-group preannotated" style="display:none;" id="bound_corpora">
		<label for="description" class="control-label">Subcorpora</label>
		{!! Form::select('subcorpus[]',$preannotated_corpora,null,['multiple'=>true,'class'=>'form-control']) !!}
		<label for="description" class="control-label">Bound reference corpora</label>
		{!! Form::select('reference_corpus[]',$reference_corpora,null,['multiple'=>true,'class'=>'form-control']) !!}
		<label for="description" class="control-label">Bound evaluation corpora</label>
		{!! Form::select('evaluation_corpus[]',$evaluation_corpora,null,['multiple'=>true,'class'=>'form-control']) !!}
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