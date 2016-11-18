@extends('front.template')

@section('main')
	<div class="row">
		<div class="box">
			<div class="col-lg-10 center col-lg-offset-1">

				<h2 class="text-center">Fréquence d'envoi des notifications par email *</h2>
				@if(session()->has('message'))
					@include('partials/error', ['type' => 'success', 'message' => session('message')])
				@endif
				{!! Form::open(['url' => 'auth/unsubscribe', 'method' => 'post', 'role' => 'form']) !!}

					<div class="row">
						{!! Form::control('email', 6, 'email', $errors, trans('front/password.email'),$email) !!}
						<div class="form-group col-lg-12 {{ $errors->has('email_frequency_id') ? 'has-error' : '' }}">
						<label class="control-label" for="frequency">{{ trans('site.email-frequency') }}</label>
				              <div style="">
					              @foreach($email_frequency as $frequency)
					              		<input type="radio" name="email_frequency_id" value="{{ $frequency->id }}" /> {{ trans('site.'.$frequency->slug) }}<br/>
					              @endforeach
				              </div>
				        {!! $errors->first('email_frequency_id', '<small class="help-block">:message</small>') !!}      
				        </div>
						{!! Form::submit('Valider', ['col-lg-12']) !!}

					</div>

				{!! Form::close() !!}<br/>
				<em>* Tu peux aussi gérer cette option depuis ton compte &gt; "Envoi des emails".</em>
			</div>
		</div>
	</div>
@stop