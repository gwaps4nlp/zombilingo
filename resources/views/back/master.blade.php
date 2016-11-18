<?php
use App\Models\ConstantGame;
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
    <body>
	    @include('front.header')
	    @include('front.navbar')

		@yield('main')
		<script src="{{ asset(elixir("js/app.js")) }}"></script>
		<script>
			var base_url = '{{ asset('') }}';
		</script>
		
		@yield('scripts')

    </body>
</html>
