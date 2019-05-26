@extends('back.template')

@section('content')

<h1>List of constant game</h1>

<table class="table">
	<thead>
		<tr>
		<th>key</th>
		<th>value</th>
		<th>description</th>
		<th>action</th>
		</tr>
	</thead>
	<tbody>
	@foreach($constants as $constant)
	<tr>
	<td>{{ $constant->key }}</td>
	<td>{{ $constant->value }}</td>
	<td>{{ $constant->description }}</td>
	<td>{!! link_to('constant-game/edit/'.$constant->id,'edit') !!}</td>
	</tr> 
	@endforeach
	</tbody>
</table>
@stop