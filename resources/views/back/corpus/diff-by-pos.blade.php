@extends('back.template')

@section('main')


{!! Form::open(['url' => 'corpus/diff-by-pos', 'method' => 'get']) !!}
{!! Form::control('selection', 0, 'corpus_id', $errors, 'Corpus',$corpora,null,'Select a corpus...') !!}
<select name="parser1">
	<option value="ref" {{ ($parser1=='ref')?'selected':'' }}>Reference</option>
	@foreach($parsers as $parser)
	<option value="{{ $parser->id }}" {{ ($parser1==$parser->id)?'selected':'' }}>{{ $parser->name }}</option>
	@endforeach
</select> vs. 
<select name="parser2">
	<option value="ref" {{ ($parser2=='ref')?'selected':'' }}>Reference</option>
	@foreach($parsers as $parser)
	<option value="{{ $parser->id }}" {{ ($parser2==$parser->id)?'selected':'' }}>{{ $parser->name }}</option>
	@endforeach
</select>
<input type="submit" value="submit" />

{!! Form::close() !!}

<h3>Differences by POS</h3>
<table class="table table-bordered stats">
	<tr>
		<th></th>
		@foreach($diff_by_pos as $col => $stat)
		<th style="padding:0 3px;">{{ str_replace('_pos','',$col) }}</th>
		@endforeach
	</tr>
	@foreach($diff_by_pos as $col => $stat)
	<tr>
	<th style="padding:0 3px;">{{ str_replace('_pos','',$col) }}</th>
		@foreach($stat as $row => $value)
			<?php
			$class="";
			if($row==$col) $class="identic";
			elseif($row!=$col && $value!=0) $class="different";
			else $value="";
			?>
			<td class="{{ $class }}">{{ $value }}</td>
		@endforeach
	</tr>
	@endforeach
</table>

<h3>Differences by Categories</h3>
<table class="table table-bordered stats">
	<tr>
		<th></th>
		@foreach($diff_by_cat as $col => $stat)
		<th style="padding:0 3px;">{{ str_replace('_pos','',$col) }}</th>
		@endforeach
	</tr>
	@foreach($diff_by_cat as $col => $stat)
	<tr>
	<th style="padding:0 3px;">{{ str_replace('_pos','',$col) }}</th>
		@foreach($stat as $row => $value)
			<?php
			$class="";
			if($row==$col) $class="identic";
			elseif($row!=$col && $value!=0) $class="different";
			else $value="";
			?>
			<td class="{{ $class }}">{{ $value }}</td>
		@endforeach
	</tr>
	@endforeach
</table>

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