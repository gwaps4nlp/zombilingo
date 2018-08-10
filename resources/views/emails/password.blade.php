<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>{{ trans('front/password.email-title') }}</h2>
		
		<div>
			{{ trans('front/password.email-intro') }}, {{ trans('front/password.email-link') }} :<br/>
			{!! link_to('password/reset/' . $token) !!}.<br/><br/>
			{{ trans('front/password.email-expire') }} {{ config('auth.reminder.expire', 60)}} {{ trans('front/password.minutes') }}.<br/><br/>
			
			{{ trans('front/password.team-site') }}.
		</div>
	</body>
</html>
