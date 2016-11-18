<?php
if($challenge){
    $challenge_starts_at = new \Carbon\Carbon($challenge->start_date);
    $challenge_ends_at = new \Carbon\Carbon($challenge->end_date);
}
?>

@extends('front.template-home')

@section('main')
<div class="row text-center" id="header_new">
    <div class="col-md-9 col-md-offset-1" id="blocLogo" title="{{ trans('site.home') }}">
        <a href="{!! url('') !!}">
            {!! Html::image('img/logo-home-force.png','ZombiLingo',['style'=>'width:70%']) !!}   
        </a>
    </div>
    @if($challenge)
        @if($challenge->type_score=="duel")
            <a href="{{ url('duel') }}?corpus_id={{ $challenge->corpus_id }}">
        @else
            <a href="{{ url('game') }}?corpus_id={{ $challenge->corpus_id }}">
        @endif    
            <span style="line-height:0.9em;position:absolute;top:0%;right:15%;width:15%;text-align:center;color:#4A1710;cursor:pointer" id="blocChallenge">
                <div id="tipChallenge" style="line-height:1.42857">
                    {!! $challenge->description !!}
                </div>
                <span style="margin-top:58%;font-size:0.7vw;" onblur="$('tipChallenge').show();">
                    {!! Html::image($challenge->image,'Challenge Foot',array('id'=>'logo-challenge', 'style'=>'height:200px;')) !!}<br/>
                    <span style="position:relative;left:25px;line-height:1em;">Challenge "{!! $challenge->name !!}"<br/>du {{ $challenge_starts_at->format('d/m') }} au {{ $challenge_ends_at->format('d/m') }}.</span><br/>
                </span>
            </span>
        </a>
    @endif
    @if(Auth::check())
        <div class="col-md-1" id="blocDeconnection">
            <a href="{!! url('auth/logout') !!}">
                <div id="deconnection" title="{{ trans('site.quit') }}"></div>
            </a>
            <a href="{!! url('user/home') !!}">
                <span id='username-info'>
                {{ Auth::user()->username}}
                </span>
            </a>
        </div>
    @endif
</div>
<div class="row text-center">
    <div class="col-md-10 col-sm-12 col-md-offset-1 colored"  id="homepage">
        <div class="row">
            <div class="col-md-3 col-sm-3">
                <div id="panel">
                    {!! Html::image('img/text-home.png','',['id'=>'explication-text']) !!}
                    <a style="position:absolute;margin-top:119%;top:0;left:56%;width:25%;" href="{{ url('informations') }}">{!! Html::image('img/fiole-home.png','',['id'=>'explication-text']) !!}</a>
                    <p style="position:absolute;margin-top:41%;top:0;padding-right:16%;width:91%;">
                    {{ trans('home.intro-game-1') }}
                    </p>
                    <p style="position:absolute;margin-top:66%;top:0;padding-right:30%;width:99%;padding-left:9%;color:#128547">
                    {{ trans('home.intro-game-2') }}
                    </p>
                    <p style="position:absolute;margin-top:97%;top:0;padding-right:30%;width:99%;padding-left:9%;">
                    {{ trans('home.intro-game-3') }}
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-sm-6" id="menu-home">
                @if(Auth::check())
                    <a href="{!! url('user/home') !!}" class="connected">
                        {!! Html::image('img/tombstone-demo.png','',['id'=>'menu-demo']) !!}
                        <div id="text-demo" class="text-menu">{{ Auth::user()->username }}</div>
                        <div id="explication-demo" class="explication">
                            Retrouve ici tes statistiques, et compare ton score avec celui de tes amis !
                        </div>
                    </a>
                @else
                    <a href="{!! route('demo') !!}">
                        {!! Html::image('img/tombstone-demo.png','',['id'=>'menu-demo']) !!}
                        <div id="text-demo" class="text-menu">Essayer</div>
                        <div id="explication-demo" class="explication">
                            Cette version limitée va te rendre accro ! Mais tu ne pourras pas sauvegarder !
                        </div>
                    </a>
                @endif
                <a href="{!! route('game') !!}" class="connected" id="link_game">
                    {!! Html::image('img/tombstone-play.png','',['id'=>'menu-play']) !!}
                    <div id="text-play" class="text-menu">Jouer</div>
                    <div id="explication-play" class="explication">
                        Pas de limite pour toi !<br/>Tu accèdes à toutes les options, bonus cachés !
                    </div>
                </a>
            </div> 
            <div class="col-md-3 col-sm-3">
                <div id="leader-board">
                     {!! Html::image('img/leader-board-large.png','') !!}
                    <div id="periode-board">
                        @if($challenge)
                            <div id="challenge" class="periode-choice focus">{{ trans('home.challenge') }}</div>
                            <input type="hidden" id="periode" value="challenge" />
                            @if($challenge->type_score=="annotations")
                                <div id="toggleScore" class="score-choice annotations">annotations</div>
                                <input type="hidden" id="type_score" value="annotations" />
                            @else
                                <div id="toggleScore" class="score-choice points">points</div>
                                <input type="hidden" id="type_score" value="points" />
                            @endif
                        @else
                            <div id="week" class="periode-choice focus">{{ trans('home.week') }}</div>
                            <input type="hidden" id="periode" value="week" />
                            <div id="toggleScore" class="score-choice points">points</div>
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
        <div id="footer" style="padding-top:59px;color:#3C1715;">
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
            <div class="row">
                <div class="col-md-2 col-sm-2 col-md-offset-2 col-sm-offset-2 text-center" style="background-color:white; padding-top: 20px; padding-bottom: 20px;">
                    <a href="http://www.paris-sorbonne.fr/" target="_blank">{!! Html::image('img/logo_sorbonne.png','') !!}</a>
                </div>
                <div class="col-md-2 col-sm-2 text-center" style="background-color:white; padding-top: 20px; padding-bottom: 20px;">
                    <a href="http://www.loria.fr/" target="_blank">{!! Html::image('img/logo_loria.png','') !!}</a>
                </div>
                <div class="col-md-2 col-sm-2 text-center" style="background-color:white; padding-top: 20px; padding-bottom: 20px;">
                    <a href="http://www.inria.fr/" target="_blank">{!! Html::image('img/logo_inria.png','') !!}</a>
                </div>
                <div class="col-md-2 col-sm-2 text-center" style="background-color:white; padding-top: 20px; padding-bottom: 20px;" >
                    <a href="http://www.culturecommunication.gouv.fr/Politiques-ministerielles/Langue-francaise-et-langues-de-France" target="_blank">{!! Html::image('img/logo_MCC.png','') !!}</a>
                </div>
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


