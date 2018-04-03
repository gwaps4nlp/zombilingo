<?php
use Gwaps4nlp\Models\ConstantGame;
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
        {!! Html::style( mix('build/css/app.css')) !!}

		@yield('css')

    </head>
    <body class="{{ App::environment('local')?'test':'' }}" style="padding-top: 10px;">

		@yield('main')
        <div id="containerModal"></div>
		<script>
            @include('js.data-js')
		</script>

        {{-- <script src="{{ asset('js/socket.io.js') }}"></script> --}}
        <script src="{{ asset(mix("build/js/all.js")) }}"></script>


		@yield('scripts')

        <input type="hidden" id="connected" value="{{ (Auth::check())?Auth::user()->id:'0' }}" />
    </body>
</html>
