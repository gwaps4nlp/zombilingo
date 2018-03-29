@extends('back.template')

@section('content')

	{!! link_to('language/create','Add a new language',['class'=>'btn btn-primary','style'=>'float:right;margin-top: 20px;']) !!}
	
	<h1>Languages</h1>
	TEST
	@foreach ($files as $file)
		{{ $file }}
	@endforeach	
	<table class="table table-striped">
	<thead>
	<tr>
		<th style="text-align:left;">Label</th>
		<th>Abrev.</th>
	</tr>
	</thead>
	<tbody>
	@foreach ($languages as $language)
		<tr>    

		<td style="text-align:left;"><a href="{{ url('language/edit',['id'=>$language->id]) }}">{{ $language->label }}</a></td> 
		<td>{{ $language->slug }}</td>
		<td>
		<a href="{{ url('challenge/edit',['id'=>$language->id]) }}" style="margin-left:20px;"><i title="edit" class="fa fa-edit"></i></a>
		{!! Form::open(['url' => 'challenge/delete', 'method' => 'post', 'role' => 'form','style'=>'display:inline']) !!}
		<input type="hidden" name="id" value="{{ $language->id }}" />
		<button type="submit" title="delete" class="btn btn-link" onclick="return confirm('Etes-vous sûr de vouloir supprimer ce challenge ?')">
			<i class="fa fa-trash"></i>
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