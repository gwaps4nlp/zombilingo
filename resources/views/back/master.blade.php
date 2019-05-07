<?php
use Gwaps4nlp\Core\Models\ConstantGame;
$app_name = Config::get('app.name');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="author" content="Loria et U. Paris-Sorbonne" />
        <meta name="description" content="@yield('description')" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') - {{ $app_name }}</title>
        <link rel="shortcut icon" type="image/x-icon" href="{!! asset('img/favicon.ico') !!}" />

        @if($app_name == 'zombiludik')
                {!! Html::style(mix("build/css/zlud.css")) !!}
        @else
                {!! Html::style(mix("build/css/app.css")) !!}
        @endif

        @yield('css')
    </head>
    <body>
        @include('front.navbar')
        <div class="container-admin container-fluid">
          @yield('container')
		  @yield('main')
        </div>
        <script>
            @include('js.data-js')
        </script>
        <script src="{{ asset('js/socket.io.js') }}"></script>
        <script src="{{ asset(mix("build/js/all.js")) }}"></script>

		@yield('scripts')

    </body>
</html>
