@extends('back.template')

@section('content')
{!! Form::open(['url' => 'user/index-admin', 'method' => 'get', 'role' => 'form']) !!}
<div class="col-md-10 col-md-offset-1">
	{!! Form::control('selection', 3, 'role_id', $errors, '',$roles,null,'Tous les roles',null) !!}
</div>
<div class="col-md-10 col-md-offset-1">
	<div class="form-group col-lg-3 col-md-offset-3">
		<input type="submit" value="Filtrer" class="btn btn-success" />
	</div>
</div>
	{!! Form::close() !!}	

</form>
	<table class="table table-striped" id="classement">
		<thead>
		<tr>
			<th>Pseudo</th>
			<th>Rôles</th>
			<th>Points</th>
			<th>Niveau</th>
			<th>Mail</th>
			<th>Date</th>
		</tr>
		</thead>
		<tbody>
@foreach ($users as $user)
		<tr>
			<td><a href="{{ route('user.edit',['user'=>$user->id]) }}" class="text-secondary">{{ $user->username }}</a></td>
			<td>{{ $user->roles->implode('label', ', ') }}</td>
			<td>{{ $user->score }}</td>
			<td>{{ $user->level->id }}</td>
			<td>{{ $user->email }}</td>
			<td>{{ $user->created_at }}</td>
		</tr>
@endforeach
	</tbody>
	</table>

{{-- {{ $users->links() }} --}}

@stop