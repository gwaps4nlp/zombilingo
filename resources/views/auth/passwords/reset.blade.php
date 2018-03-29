@extends('front.template')

@section('main')
	<div class="row">
		<!-- <div class="box"> -->
			<div class="col-md-10 mx-auto">
				@if(session()->has('error'))
					@include('partials/error', ['type' => 'danger', 'message' => session('error')])
				@endif	
				<h2 class="text-center m-5">{{ trans('front/password.title-reset') }}</h2>
				<p>{{ trans('front/password.reset-info') }}</p>

				{!! Form::open(['url' => 'password/reset', 'method' => 'post', 'role' => 'form']) !!}	

					<div class="row">

						{!! Form::hidden('token', $token) !!}
						{!! Form::control('email', 12, 'email', $errors, trans('front/password.email')) !!}
						{!! Form::control('password', 6, 'password', $errors, trans('front/password.password'), null, [trans('front/password.warning'), trans('front/password.warning-password')]) !!}
						{!! Form::control('password', 6, 'password_confirmation', $errors, trans('front/password.confirm-password')) !!}
						{!! Form::submit(trans('front/password.button-send'), ['col-lg-12']) !!}

					</div>

				{!! Form::close() !!}

			</div>
		<!-- </div> -->
	</div>
@stop

@section('scripts')

	<script>
		$(function() { $('.badge').popover();	});
	</script>

@stop