@extends('back.template')

@section('content')
	{!! link_to('corpus/create','Add a new corpus',['class'=>'btn btn-primary','style'=>'float:right;margin-top: 20px;']) !!}
	<h1>Index of corpora</h1>
	<table class="table table-striped">
	<thead>
	<tr>
		<th style="text-align:left;">Name</th>
		<th>Reference</th>
		<th>Playable</th>
		<th>Language</th>
		<th>License</th>
		<th>Sentences</th>
		<th>Action</th>
	</tr>
	</thead>
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

		<td>{{ $corpus->language->label }}</td>
		<td><span data-toggle="tooltip" data-placement="auto left" title="{{ $corpus->license->label }}" class="license">{!! Html::image('img/'.$corpus->license->image) !!}</span></td>
		<td>{{ $corpus->all_sentences()->count() }}</td>

		<td>
		<a href="{{ url('corpus/export',['id'=>$corpus->id]) }}"><span title="export" class="glyphicon glyphicon-download-alt"></span></a>
		<a href="{{ url('corpus/edit',['id'=>$corpus->id]) }}" style="margin-left:20px;"><span title="edit" class="glyphicon glyphicon-edit"></span></a>
		{!! Form::open(['url' => 'corpus/delete', 'method' => 'post', 'role' => 'form','style'=>'display:inline']) !!}
		<input type="hidden" name="id" value="{{ $corpus->id }}" />
		<button type="submit" title="delete" class="btn btn-link" onclick="return confirm('Etes-vous sûr de vouloir supprimer ce corpus ?')">
			<span class="glyphicon glyphicon-trash"></span>
		</button>
		{!! Form::close() !!}

		</td>
 	
		</tr>
	@endforeach
	</tbody>
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