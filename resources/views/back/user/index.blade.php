@extends('back.template')

@section('content')

	<table class="table table-striped" id="classement">
		<thead>
		<tr>
			<th>Pseudo</th>
			<th>Rôle</th>
			<th>Points</th>
			<th>Niveau</th>
			<th>Mail</th>
			<th>Date</th>
		</tr>
		</thead>
		<tbody>
@foreach ($users as $user)
		<tr>
			<td>{{ $user->username }}</td>
			<td>{{ $user->role->label }}</td>
			<td>{{ $user->score }}</td>
			<td>{{ $user->level->id }}</td>
			<td>{{ $user->email }}</td>
			<td>{{ $user->created_at }}</td>
		</tr>
@endforeach
	</tbody>
	</table>


@stop