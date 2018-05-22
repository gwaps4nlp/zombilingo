@extends('back.template')


@section('content')
	<h1>Number of annotations and of players by corpus</h1>
	<form>
		<input type="date" name="date" value="{{ $date??'' }}" />
		<input type="submit" value="validate" class="btn btn-success" />
	</form>
	<table class="table table-striped">

	<thead>
	<tr>
		<th style="text-align:left;">Name</th>
		<th>Language</th>
		<th>Starting date</th>
		<th>License</th>
		<th>Annotations</th>
		<th>Different players</th>
		<th>Action</th>
	</tr>
	</thead>

	<tbody>
	@foreach ($corpora as $corpus)
		@if($corpus->playable==1 or $corpus->id==14) <!-- display foot (14) even if not playable -->
		<tr>

		<td style="text-align:left;"><a href="{{ url('corpus/edit',['id'=>$corpus->id]) }}">{{ $corpus->name }}</a></td>

		<td>{{ $corpus->language->label }}</td>
		<td>{{ $corpus->created_at }}</td>
		<td><span data-toggle="tooltip" data-placement="auto left" title="{{ $corpus->license->label }}" class="license">{!! Html::image('img/'.$corpus->license->image) !!}</span></td>
		<td>
		<?php
		if($date){
			$number_annotations = $corpus->annotations_users_at_date($date)->count();
		} else {
			$number_annotations = $corpus->annotations_users()->count();
	  }
		?>
		{{ $number_annotations }}
		</td>
		<td>
		<?php
		if($date){
			$number_players = $corpus->count_players_at_date($date);
		} else {
			$number_players = $corpus->count_players();
	  }
		?>
		{{ $number_players }}
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
		@endif
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