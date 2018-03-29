@extends('back.template')

@section('content')

	{!! Form::open(['url' => 'admin/reporting', 'method' => 'get', 'role' => 'form']) !!}
	{!! Form::select('relation_id', $relations,$request->input('relation_id'),['placeholder'=>'Tous les phénomènes', 'id' => 'select-relation']) !!}
	<select name="period">
		<option value="month">Mois</option>
		<option value="week" {!! $request->input('period')=='week'?"selected":'' !!}>Semaine</option>
	</select>
	<input type="submit" value="Rafraichir" class="btn btn-sm btn-success" />
	{!! Form::close() !!}	

	<div id="graph1" style="width:100%; height:400px;"></div>
	<br>
	<div id="graph2" style="width:100%; height:400px;"></div>
	<br>
	<div id="graph3" style="width:100%; height:400px;"></div>
	<br>
		{!! Form::open(['url' => 'admin/reporting', 'method' => 'get', 'role' => 'form']) !!}
		{!! Form::select('user_id', $users,$request->input('user_id'),['placeholder'=>'Tous les joueurs', 'id' => 'select-user']) !!}
		<input type="submit" value="Filtrer" class="btn btn-success" />
		{!! Form::close() !!}			
	<form>
	<div id="graph4" style="width:100%; height:400px;"></div>
	<br>
	<div id="graph5" style="width:100%; height:400px;"></div>

@stop

@section('scripts')
    {!! Html::script('js/reporting.js') !!}
    {!! Html::script('js/highcharts.js') !!}
<script type="text/javascript">
	var annByUsers = {!! $annotationsByUser !!};
	var annotationsByPeriod = {!! $annotations !!};
	var registrationsByPeriod = {!! $registrations->toJson() !!};
	var countByPhenom = {!! $annotationsByRelation !!};
	var daysByUser = {!! $daysOfActivityByUser !!};
	var label_period = "{{ $label_period }}";
</script>
@stop