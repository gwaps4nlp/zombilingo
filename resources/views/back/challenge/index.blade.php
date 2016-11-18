@extends('back.template')

@section('content')
<h1>Challenges</h1>
	{!! link_to('challenge/create','Add a new challenge',['class'=>'btn btn-primary','style'=>'float:right;margin-top: 20px;']) !!}
	
	<h1>Index of challenges</h1>
	<table class="table table-striped">
	<thead>
	<tr>
		<th style="text-align:left;">Name</th>
		<th>Type</th>
		<th>Start date</th>
		<th>End date</th>
		<th>Action</th>
	</tr>
	</thead>
	<tbody>
	@foreach ($challenges as $challenge)
		<tr>    

		<td style="text-align:left;"><a href="{{ url('challenge/edit',['id'=>$challenge->id]) }}">{{ $challenge->name }}</a></td> 
		<td>{{ $challenge->type_score }}</td>
		<td>{{ $challenge->start_date }}</td>
		<td>{{ $challenge->end_date }}</td>

		<td>
		<a href="{{ url('challenge/edit',['id'=>$challenge->id]) }}" style="margin-left:20px;"><span title="edit" class="glyphicon glyphicon-edit"></span></a>
		{!! Form::open(['url' => 'challenge/delete', 'method' => 'post', 'role' => 'form','style'=>'display:inline']) !!}
		<input type="hidden" name="id" value="{{ $challenge->id }}" />
		<button type="submit" title="delete" class="btn btn-link" onclick="return confirm('Etes-vous sûr de vouloir supprimer ce challenge ?')">
			<span class="glyphicon glyphicon-trash"></span>
		</button>
		{!! Form::close() !!}

		</td>
 	
		</tr>
	@endforeach
	</tbody>
	</table>

@stop

@section('style')
	{!! Html::style('css/bootstrap-datepicker3.css') !!}
@stop

@section('scripts')
	{!! Html::script('js/bootstrap-datepicker.js') !!}
	<script>
	$('.datepicker').datepicker({
	    format: 'dd/mm/yyyy',
	    autoclose: true,
	    todayBtn: "linked",
	});
	</script>
@stop