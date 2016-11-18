@extends('back.template')

@section('main')


{!! Form::open(['url' => 'corpus/diff-by-relation', 'method' => 'get']) !!}
{!! Form::control('selection', 0, 'corpus_id', $errors, 'Corpus',$corpora,null,'Select a corpus...', $corpus->id) !!}
<div class="row">
<div class="col-md-5">
<label>left column :</label><br/>
<select name="parser1" id="parser1">
	<option value="ref" {{ ($parser1=='ref')?'selected':'' }}>Reference</option>
	@foreach($parsers as $parser)
	<option value="{{ $parser->id }}" {{ ($parser1==$parser->id)?'selected':'' }}>{{ $parser->name }}</option>
	@endforeach
</select>
</div>
<div class="col-md-1">
vs. 
</div>
<div class="col-md-5">
<label>first row :</label><br/>
<select name="parser2" id="parser2">
	<option value="ref" {{ ($parser2=='ref')?'selected':'' }}>Reference</option>
	@foreach($parsers as $parser)
	<option value="{{ $parser->id }}" {{ ($parser2==$parser->id)?'selected':'' }}>{{ $parser->name }}</option>
	@endforeach
</select>
</div>
<div class="col-md-1">
<input class="btn btn-success" type="submit" value="submit" />
</div>
</div>

{!! Form::close() !!}

<h3>Differences by Relation</h3>
<table class="table table-bordered stats">
	<tr>
		<th></th>
		@foreach($diff_by_relation as $col => $stat)
		<th style="padding:0 3px;">{{ $col }}</th>
		@endforeach
	</tr>
	@foreach($diff_by_relation as $col => $stat)
	<tr>
	<th style="padding:0 3px;">{{ $col }}</th>
		@foreach($stat as $row => $value)
			<?php
			$class="";
			if($row==$col) $class="identic coef_matrix";
			elseif($row!=$col && $value!=0) $class="different coef_matrix";
			else $value="";
			?>
			<td class="{{ $class }}"  data-params="corpus_id={{ $corpus->id }}&parser1_id={{ $parser1 }}&parser1_relation_id={{ $col }}&parser2_id={{ $parser2 }}&parser2_relation_id={{ $row }}">{{ $value }}</td>
		@endforeach
	</tr>
	@endforeach
</table>

@stop
@section('scripts')
<script>

var corpus = {!! json_encode($corpora_parsers) !!};
function initSelect(select_options=true){
	var corpus_id = $('#corpus_id').val();

	var selected = 0;
	$("#parser1 option").each(function(index,value){
		if($.inArray(parseInt($(this).val()),corpus[corpus_id])==-1){
			if(!$(this).parent().is("span")){
				$(this).wrap( "<span>" );
			}
			if(select_options)
				$(this).removeAttr('selected');
		}
		else {
			if($(this).parent().is("span")){
				$(this).unwrap();
			}
			if(select_options){
				if(++selected==1)
					$(this).attr('selected',true);
				else
					$(this).removeAttr('selected');
			}
		}
	});
	var selected = 0;
	$("#parser2 option").each(function(index,value){
		if($.inArray(parseInt($(this).val()),corpus[corpus_id])==-1){
			if(!$(this).parent().is("span")){
				$(this).wrap( "<span>" );
			}
			if(select_options)
				$(this).removeAttr('selected');
		}
		else {
			if($(this).parent().is("span")){
				$(this).unwrap();
			}
			if(select_options){
				if(++selected==2)
					$(this).attr('selected',true);
				else
					$(this).removeAttr('selected');
			}
		}
	});
}
$(function () {
	$(document).ready(function() {
	    initSelect(false);
	});
	$(document).on('change', '#corpus_id', function(event){
		initSelect();
	});
	$(document).on('click', '.coef_matrix', function(event){

		var nb_col = $(this).parent().children().length;
		// alert(nb_col);
		var target = $( event.target );
		if ($('#contentModal').length>0)
			$('#contentModal').remove();
		target.parents('tr').after( '<tr><td id="contentModal" colspan="'+nb_col+'"></td></tr>' );
		$('#contentModal').load(base_url+'corpus/compare-conll/?'+target.attr("data-params"));
		// $('#contentModal').attr('colspan',nb_col);
    });
});
</script>
@stop

@section('style')
<style>
body {
	color:black;
}
table.conll tr td:nth-child(2),table.conll tr td:nth-child(6){
    border-left: solid 1px grey;
}
table.conll tr.last {
	border-bottom: double 1px grey;
}
table.stats {
	font-size:90%;
}
table.stats th, table.stats td{
	text-align:center;
}
table.stats td.identic{
	color: #fff;
	background-color: #5cb85c;
	border-color: #4cae4c;
}
table.stats td.different{
	color: #fff;
	background-color: #d9534f;
	border-color: #d43f3a;
}
</style>
@stop