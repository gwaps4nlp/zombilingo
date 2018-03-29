@extends('back.template')

<?php 
$total_number_sentences = 0;
$total_number_playable_sentences = 0;
?>

@section('content')
	{!! link_to('corpus/create','Add a new corpus',['class'=>'btn btn-primary','style'=>'float:right;margin-top: 20px;']) !!}
	<h1>Index of corpora</h1>
	<table class="table table-striped">

	<tbody>
	@foreach ($corpora as $corpus)
		<tr>    

		<td style="text-align:left;"><a href="{{ url('corpus/edit',['id'=>$corpus->id]) }}">{{ $corpus->name }}</a></td> 

			@if($corpus->source_id==1)
				<td>YES</td>
			@else
				<td>NO</td>
			@endif
			@if($corpus->playable==1)
				<td>YES</td>
			@else
				<td>NO</td>
			@endif
			@if($corpus->exportable==1)
				<td>YES</td>
			@else
				<td>NO</td>
			@endif
		<td>{{ $corpus->language->label }}</td>
		<td><span data-toggle="tooltip" data-placement="auto left" title="{{ $corpus->license->label }}" class="license">{!! Html::image('img/'.$corpus->license->image) !!}</span></td>
		<td>
		<?php
		$number_sentences = $corpus->all_sentences()->count();
		if($corpus->subcorpora->count()==0){
			$total_number_sentences+=$number_sentences;
			if($corpus->isPreAnnotated())
				$total_number_playable_sentences+=$number_sentences;
		}
		?>
		{{ $number_sentences }}<br/>
		</td>

		<td>
		<a href="{{ url('annotator/graph-corpus',['id'=>$corpus->id]) }}"><i title="view" class="fa fa-eye"></i></a>
		<a href="{{ url('corpus/export',['id'=>$corpus->id]) }}" style="margin-left:20px;"><i title="export" class="fa fa-download"></i></a>
		<a href="{{ url('corpus/edit',['id'=>$corpus->id]) }}" style="margin-left:20px;"><i title="edit" class="fa fa-edit"></i></a>
		{!! Form::open(['url' => 'corpus/delete', 'method' => 'post', 'role' => 'form','style'=>'display:inline']) !!}
		<input type="hidden" name="id" value="{{ $corpus->id }}" />
		<button type="submit" title="delete" class="btn btn-link" onclick="return confirm('Etes-vous sûr de vouloir supprimer ce corpus ?')">
			<i class="fa fa-trash"></i>
		</button>
		{!! Form::close() !!}

		</td>
 	
		</tr>
	@endforeach

	</tbody>
	<thead>
	<tr>
		<th style="text-align:left;">Name</th>
		<th>Reference</th>
		<th>Playable</th>
		<th>Exportable</th>		
		<th>Language</th>
		<th>License</th>
		<th>Sentences</th>
		<th>Action</th>
	</tr>
	<tr>
		<td style="text-align:left;">Total</td><td colspan="5"></td><td>{{ $total_number_sentences }}</td><td></td>
	</tr>
	<tr>
		<td style="text-align:left;">Total playable</td><td colspan="5"></td><td>{{ $total_number_playable_sentences }}</td><td></td>
	</tr>	
	</thead>	
	</table>

@stop

@section('scripts')
<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>
@stop

@section('style')
<style>
.license img{
	width:60px;	
}
th, td {
	text-align:center;
}
</style>
@stop