<?php
use Gwaps4nlp\Models\ConstantGame;
use App\Services\Html\ModalBuilder as Modal;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Loria et U. Paris-Sorbonne" />
		<meta name="description" content="@yield('description')" />
        <title>@yield('title') - Zombilingo</title>
        <link rel="shortcut icon" type="image/x-icon" href="{!! asset('img/favicon.ico') !!}" />

        <!-- CSS principal -->
        {!! Html::style('css/master.css') !!}
		
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
