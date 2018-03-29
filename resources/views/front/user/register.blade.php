@extends('front.template')


@section('main')

	@if($errors->has('message'))
	    @include('partials/error', ['type' => 'danger', 'message' => $errors->first('message')])
	@endif

	@if($errors->has('email_reset'))
	    @include('partials/error', ['type' => 'danger', 'message' => $errors->first('email_reset')])
	@endif        

	@if (session('status'))
		@include('partials/error', ['type' => 'success', 'message' => session('status')])
	@endif


	<div class="pb-5">
	<div class="row">
	    <div class="col-md-6 pt-4 py-md-5 pl-5 pr-5">
			
			{!! Form::open(['url' => 'login', 'method' => 'post', 'role' => 'form']) !!}
			<h2 class="text-center">{{ trans('site.connection') }}</h2>
					{!! Form::control('text', 0, 'log', $errors, trans('site.pseudo')) !!}

					{!! Form::control('password', 0, 'password_log', $errors, trans('site.password')) !!}

					<input type="submit" value="{{ trans('site.submit-login') }}" class="btn btn-success"/>
			{!! Form::close() !!}
			
			 <h2 class="text-center pt-4 pt-md-5">{{ trans('site.forgotten-password') }}</h2>
			
			{!! Form::open(['url' => 'password/email', 'method' => 'post', 'role' => 'form']) !!}	

			{!! Form::control('email', 0, 'email_reset', $errors, trans('site.email')) !!}

					
				<input type="submit" value="{{ trans('site.submit-reset') }}" class="btn btn-success"/>

				{!! Form::close() !!}

	    </div>

	    <div class="col-md-6 pt-4 py-md-5 pl-5 pl-md-3 pr-5">
		{!! Form::open(['url' => 'register', 'method' => 'post', 'role' => 'form']) !!} 
		<h2 class="text-center">{{ trans('site.register') }}</h2>

				{!! Form::control('text', 0, 'username', $errors, trans('site.pseudo')) !!}

				{!! Form::control('password', 0, 'password', $errors, trans('site.password')) !!}

				{!! Form::control('password', 0, 'password_confirmation', $errors, trans('site.confirm-password')) !!}


				{!! Form::control('email', 0, 'email', $errors, trans('site.email').'*') !!}

			
			<input type="submit" action="post" id="btn_registration" value="{!! (trans('site.submit-register')) !!}" class="btn btn-success" />

		{!! Form::close() !!}

		{!! trans('site.asterisk-register') !!}
	    </div>
	</div>
	<hr/>
	<div class="row">
	    <div class="col-12 px-5" id="charte">
		@include('lang/'.App::getLocale().'/charte')
	    </div>
	</div>
	</div>

@stop