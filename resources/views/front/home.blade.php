<?php
if($challenge){
    $challenge_starts_at = new \Carbon\Carbon($challenge->start_date);
    $challenge_ends_at = new \Carbon\Carbon($challenge->end_date);
}
$app_name = Config::get('app.name');
?>

@extends('front.template-home')

@section('main')
<div class="container-fluid">
<div class="row text-center" id="header_new">

    <div class="col-2 mt-5 text-center" style="z-index: 1">
        <!-- <a href="https://lingoboingo.org/" style="padding-left:70px;" target="_blank"> -->
          <img
            src="{{ asset('img/zola.png') }}"
            data-toggle="tooltip"
            data-placement="bottom"
            title="J'accuse"
            style="width:100%"
          />
        <!-- </a> -->
    </div>
    <div class="col-8" id="container-logo" title="{{ trans('site.home') }}">
        <a href="{!! url('') !!}">
          @if($app_name == 'zombiludik')
           {!! Html::image('img/logo_zlud.png','ZombiLingo',['style'=>'width:76%']) !!}
          @else
           {!! Html::image('img/logo-home.png','ZombiLingo',['style'=>'width:80%']) !!}
          @endif

        </a>
    </div>

    @if($challenge)
        @if($challenge->type_score=="duel")
            <a href="{{ url('duel') }}?corpus_id={{ $challenge->corpus_id }}">
        @else
            <a href="{{ url('game') }}?corpus_id={{ $challenge->corpus_id }}" style="z-index:2">
        @endif
            <span style="line-height:0.9em;position:absolute;top:2%;right:15%;width:15%;text-align:center;color:#4A1710;cursor:pointer" id="blocChallenge">
                <div id="tipChallenge" style="line-height:1.42857;display:none;">
                    {!! $challenge->description !!}
                </div>

                <span style="margin-top:58%;font-size:0.7vw;" onblur="$('#tipChallenge').show();">
                    <!-- {!! Html::image($challenge->image,'Challenge '.$challenge->name ,array('id'=>'logo-challenge', 'style'=>'height:200px;')) !!}<br/> -->
                    <ul id="countdown-pad"></ul><br/>
                    <span style="position:relative;line-height:1em;">Challenge "{!! $challenge->name !!}"<br/>du {{ $challenge_starts_at->format('d/m') }} au {{ $challenge_ends_at->format('d/m') }}.</span><br/>
                </span>
            </span>
            <input type="hidden" value="{{ $challenge->corpus->number_answers }}" id="number_annotations" />
        </a>
    @endif
    @if(Auth::check())
        <div class="col-1" id="blocDeconnection42" style="z-index: 1">
            <a href="{!! route('logout') !!}">
                {!! Html::image('img/deco.png',trans('site.quit'),array('style'=>'height:120%;width:120%;max-height:87px;max-width:105px;min-width:58px;min-height:48px;')) !!}
            </a>
            <a href="{!! url('user/home') !!}">
                <span id='username-info'>
                {{ Auth::user()->username}}
                </span>
            </a>
        </div>
    @endif
</div>
<div id="homepage" class="row">
            <div class="col-12 col-md-3 col-sm-3">
                <div id="container-panel">
                    {!! Html::image('img/text-home.png','',['id'=>'background-panel']) !!}
                    <a id ="link-informations" alt="informations" title="informations" style="" href="{!! route('informations') !!}">
                        {!! Html::image('img/fiole-home.png','informations') !!}
                    </a>
                    <div id="panel">
                        <div>
                        {{ trans('home.intro-game-1') }}
                        </div>
                        <div style="color:#128547">
                        {{ trans('home.intro-game-2') }}
                        </div>
                        <div style="">
                        {{ trans('home.intro-game-3') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-sm-6" id="menu-home">
                <div id="container-menu-home">
                <div class="row">
                <div class="col-6 col-md-6 col-sm-6 link-home" id="link-game">
                <a href="{!! route('game') !!}" class="connected" id="link_game">
                    {!! Html::image('img/tombstone-play.png','',['id'=>'menu-play']) !!}
                    <div class="container-link">
                        <div id="text-play" class="text-menu">
                            <div>
                                {{ trans('site.play') }}
                            </div>
                        </div>
                        <div id="explication-play" style="flex:1;" class="explication">
                            {{ trans('home.intro-play-1') }}<br/>{{ trans('home.intro-play-2') }}
                        </div>
                    </div>
                </a>
                </div>
                <div class="col-6 col-md-6 col-sm-6 link-home" id="link-demo">
                @if(Auth::check())
                    <a href="{!! url('user/home') !!}" class="connected">
                        {!! Html::image('img/tombstone-demo.png','',['id'=>'menu-demo']) !!}
                        <div class="container-link">
                            <div id="text-demo" class="text-menu">
                                <div>
                                    {{ Auth::user()->username }}
                                </div>
                            </div>
                            <div id="explication-demo" class="explication">
                                {{ trans('home.intro-account') }}
                            </div>
                        </div>
                    </a>
                @else
                    <a href="{!! route('demo') !!}">
                        {!! Html::image('img/tombstone-demo.png','',['id'=>'menu-demo']) !!}
                        <div class="container-link">
                            <div id="text-demo" class="text-menu">
                                <div>
                                    {{ trans('site.try') }}
                                </div>
                            </div>
                            <div id="explication-demo" class="explication">
                                {{ trans('home.intro-demo') }}
                            </div>
                        </div>
                    </a>
                @endif
                </div>
                </div>
            </div>
            </div>
            <div class="col-3 col-3 col-md-3 col-sm-3">
                <div id="container-leader-board">
                     {!! Html::image('img/leader-board-homepage.png','',['style'=>'width:100%']) !!}
                    <div id="leader-board">
                    <div id="periode-board">
                        @if($challenge)
                            <div id="challenge" class="periode-choice focus">{{ trans('home.challenge') }}</div>
                            <input type="hidden" id="periode" value="challenge" />
                            @if($challenge->type_score=="annotations")
                                <div id="toggleScore" class="score-choice annotations">{{ trans('game.annotations') }}</div>
                                <input type="hidden" id="type_score" value="annotations" />
                            @else
                                <div id="toggleScore" class="score-choice points">{{ trans('game.points') }}</div>
                                <input type="hidden" id="type_score" value="points" />
                            @endif
                        @else
                            <div id="week" class="periode-choice focus">{{ trans('home.week') }}</div>
                            <input type="hidden" id="periode" value="week" />
                            <div id="toggleScore" class="score-choice points">{{ trans('game.points') }}</div>
                            <input type="hidden" id="type_score" value="points" />
                        @endif
                            <div id="month" class="periode-choice">{{ trans('home.month') }}</div>
                            <div id="total" class="periode-choice">{{ trans('home.total') }}</div>

                    </div>
                    <div id="leaders-1-2">
                    <?php
 						foreach(array_keys($scores) as $ranking_periode){
                        $rank = 1;
                            foreach ($scores[$ranking_periode]->splice(0,2) as $ranking) {
                                echo '<div user_id="'.$ranking->user_id.'" class="rank rank-points '.$ranking_periode.'">'.$rank . ' ' . $ranking->username . '&nbsp;: ' .Html::formatScore($ranking->score).'</div>';
                                $rank++;
                            }
						$rank = 1;
                            foreach ($scores_annotations[$ranking_periode]->splice(0,2) as $ranking) {
    		                    echo '<div user_id="'.$ranking->user_id.'" class="rank rank-annotations '.$ranking_periode.'">'.$rank . ' ' . $ranking->username . '&nbsp;: ' .Html::formatScore($ranking->score).'</div>';
    							$rank++;
    						}
						}
                    ?>
                    </div>
                    <div id="leaders-3-4-5">
                    <?php
 						foreach(array_keys($scores) as $ranking_periode){
                        $rank = 3;
                            foreach ($scores[$ranking_periode]->splice(0,3) as $ranking) {
                                echo '<div user_id="'.$ranking->user_id.'" class="rank rank-points '.$ranking_periode.'">'.$rank . ' ' . $ranking->username . '&nbsp;: ' .Html::formatScore($ranking->score).'</div>';
                                $rank++;
                            }
						$rank = 3;
                            foreach ($scores_annotations[$ranking_periode]->splice(0,3) as $ranking) {
    		                    echo '<div user_id="'.$ranking->user_id.'" class="rank rank-annotations '.$ranking_periode.'">'.$rank . ' ' . $ranking->username . '&nbsp;: ' .Html::formatScore($ranking->score).'</div>';
    							$rank++;
    						}
						}
                    ?>
                    </div>
                </div>
                </div>

            </div>
        </div>
        <div id="footer">
            <div class="row">
                <div class="col-md-10 col-md-offset-1 col-sm-12 text-center">
                    {{ trans_choice('home.number-registered', $numberUsers) }},
                    @if($lastRegisteredUser)
                    {{ trans('home.last-registered', ['name' => $lastRegisteredUser->username]) }},
                    @endif

                    @if($numberConnectedUsers > 0)
                        {!! link_to('user/connected',trans_choice('home.number-connected', $numberConnectedUsers),['style'=>'text-decoration:underline;']) !!}.
                        <span id="connected_users" style="display:inline-block;width:150px;text-align:left;">
                        @foreach($connectedUsers as $connectedUser)
                            <span class="connected_user">{{ $connectedUser->username }}</span>
                        @endforeach
                        </span>
                    @else
                        {{ trans_choice('home.number-connected', $numberConnectedUsers) }}
                    @endif
                </div>
            </div>
            <div class="row" style="justify-content: center;">
                <div style="background-color:white; padding: 20px 5px;margin-top: 16px;margin-right:10px;">
                    <a href="http://www.paris-sorbonne.fr/" target="_blank">{!! Html::image('img/logo_sorbonne_new.png','logo Sorbonne', ['style'=>'height:70px']) !!}</a>
                </div>
                <div style="background-color:white; padding: 20px 5px;">
                    <a href="http://www.loria.fr/" target="_blank">{!! Html::image('img/logo_loria.png','logo Loria') !!}</a>
                </div>
                <div style="background-color:white; padding: 20px 5px;">
                    <a href="http://www.inria.fr/" target="_blank">{!! Html::image('img/logo_inria.png','logo Inria') !!}</a>
                </div>
                <div style="background-color:white; padding: 20px 5px;" >
                    <a href="http://www.culturecommunication.gouv.fr/Politiques-ministerielles/Langue-francaise-et-langues-de-France" target="_blank">{!! Html::image('img/logo_MCC.png','') !!}</a>
                </div>
            </div>
        </div>
</div>
@stop

@section('css')
<style>
@if($challenge)
    .week, .month, .total {
        display: none;
    }
    @if($challenge->type_score=="annotations")
        .rank-points {
            display: none;
        }
    @else
        .rank-annotations {
            display: none;
        }
    @endif
@else
    .challenge, .month, .total, .rank-annotations {
        display: none;
    }
@endif
</style>
@stop

@section('scripts')
<script>
    var index=0;
    var connected_users;
    var user_id;
    function init() {
        animate();
    }


    function animate(){
        connected_users = $('#connected_users').children('span');

        var connected_user = connected_users[index];
        $(connected_user).fadeIn( 2000,
            function(){
                $(connected_user).fadeOut( 2000,function(){
                setTimeout(function(){ index++;next(); }, 100);
                });
            }
            );

    }
    function next(){
        if(index>=connected_users.length) index=0;
        animate();
    }

    window.onload = init();
</script>
@stop


