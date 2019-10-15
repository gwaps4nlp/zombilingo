@extends('back.template')

@section('content')
{!! Form::model($user, array('route' => array('user.update', $user->id), 'method' => 'post')) !!}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="col-xs-12 col-sm-12 col-md-12">

        <div class="form-group">

            <strong>Pseudo :</strong><br/>

			{{ $user->username }}

        </div>

    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">

        <div class="form-group">

            <strong>Email :</strong><br/>

            {{ $user->email }}
			{{-- {!! Form::text('email', null, array('placeholder' => 'Email','class' => 'form-control')) !!} --}}

        </div>

    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">

        <div class="form-group">

            <strong>Role(s) :</strong><br/>
			@foreach($roles as $role)
				<input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ $user->hasRole($role)?'checked="checked"':'' }}/>{{ $role->label }}<br/>
			@endforeach

        </div>

    </div>

	<div class="col-md-10 col-md-offset-1">
		<div class="form-group col-lg-3 col-md-offset-3">
			<input type="submit" value="Enregistrer" class="btn btn-success" />
		</div>
	</div>

	{!! Form::close() !!}	

@stop