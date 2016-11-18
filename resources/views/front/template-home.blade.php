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
        {!! Html::style( elixir('css/app.css')) !!}
		
		@yield('css')

    </head>
    <body class="{{ App::environment('local')?'test':'' }}">

		@yield('main')
        <div id="containerModal"></div>
		<script>
            @include('js.data-js')
		</script>
        <script src="{{ asset(elixir("js/app.js")) }}"></script>

		@yield('scripts')

        <?php

            if(Auth::check()){
                //Coccinelle
                if(rand(0,100) < ConstantGame::get('proba-bat') && time() > Auth::user()->last_mwe+ConstantGame::get('time-mwe')){
                    echo '<a href="'.url('game/mwe/begin/1').'">'. HTML::image('img/coccinelle.png', 'rigor mortis', ['id'=>"coccinelle"]) . '</a>';
                    session()->put('mwe.enabled',1);
                    echo HTML::script('js/coccinelle.js');
                }else{
                    if(rand(0,100) < ConstantGame::get('proba-meat')){
                        echo HTML::image('img/viande.png', trans('game.you-won-an-object'), ['id'=>"viande"]);
                        session()->put('object_won',1);
						echo Html::script(route('asset',['asset'=>'js/bonus-object.js']));
                    }
                }
            }
        ?>
        <input type="hidden" id="connected" value="{{ (Auth::check())?Auth::user()->id:'0' }}" />
    </body>
</html>
