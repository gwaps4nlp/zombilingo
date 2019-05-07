<?php
use Gwaps4nlp\Models\ConstantGame;
use App\Services\Html\ModalBuilder as Modal;
$app_name = Config::get('app.name');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Loria et U. Paris-Sorbonne" />
        <meta name="description" content="@yield('description')" />
        <title>@yield('title') - {{ $app_name }}</title>
        <link rel="shortcut icon" type="image/x-icon" href="{!! asset('img/favicon.ico') !!}" />

        @if($app_name == 'zombiludik')
                {!! Html::style(mix("build/css/zlud.css")) !!}
        @else
                {!! Html::style(mix("build/css/app.css")) !!}
        @endif

        @yield('css')
    </head>
    <body class="{{ App::environment('local')?'test':'' }}">

		@yield('main')

		<div id="containerModal"></div>
		<script>
			var base_url = '{{ asset('') }}';
		</script>
		{!! Html::script('js/jQuery.js') !!}
		{!! Html::script('js/jQueryUI.js') !!}
		{!! Html::script('js/master.js') !!}
		{!! Html::script('js/bootstrap.min.js') !!}
		{!! Html::script('js/jquery.cookie.js') !!}

		@yield('scripts')

    </body>
</html>
