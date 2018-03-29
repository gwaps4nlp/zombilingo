<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Loria et U. Paris-Sorbonne" />

        {!! Html::style('css/back/css/bootstrap.css') !!}
     
		@yield('css')

    </head>
    <body class="{{ App::environment('local')?'test':'' }}">
		@yield('main')
    <script src="{{ asset(mix("build/js/all.js")) }}" />
		@yield('scripts')
    </body>
</html>
