{!! Form::open(['url' => 'auth/register', 'method' => 'post', 'role' => 'form']) !!} 
<h1 class="text-center">Inscription</h1>

    <div class="form-group">
        {!! Form::control('text', 0, 'username', $errors, trans('front/register.pseudo')) !!}
    </div>

    <div class="form-group">
        {!! Form::control('password', 0, 'password', $errors, trans('front/register.password')) !!}
    </div>

    <div class="form-group">
        {!! Form::control('password', 0, 'password_confirmation', $errors, trans('front/register.confirm-password')) !!}
    </div>
    <div class="form-group">
        {!! Form::control('email', 0, 'email', $errors, trans('front/register.email').'*') !!}
    </div>
	{!! Form::submit(trans('front/site.submit-register'), ['btn btn-success']) !!}

{!! Form::close() !!}

{!! trans('front/site.asterisk-register') !!}


