@extends('back.template')

@section('content')

<h1>List of trophies</h1>

<table class="table">
	<thead>
		<tr>
		<th>name</th>
		<th>key</th>
		<th>criterion</th>
		<th>required value</th>
		<th>description</th>
		<th>points</th>
		<th>action</th>
		</tr>
	</thead>
	<tbody>
	@foreach($trophies as $trophy)
	<tr>
	<td>{{ $trophy->name }}</td>
	<td>{{ $trophy->slug }}</td>
	<td>{{ $trophy->key }}</td>
	<td>{{ $trophy->required_value }}</td>
	<td>{{ $trophy->description }}</td>
	<td>{{ $trophy->points }}</td>
	<td>{!! link_to('trophy/edit/?id='.$trophy->id,'edit') !!}</td>
	</tr> 
	@endforeach
	</tbody>
</table>
@stop