<?php
$mode = $game->mode;
if($mode=="admin-game")
    $mode="game";
?>
<div class="row" id="tete">
    <div class="col-md-2" title="{{ trans('site.account') }}">
    @if($mode=="game"||$mode=="special")
		{!! link_to('user/home','',array('id'=>'profil')) !!}
        <span id="progress_score"></span>
    @elseif($mode=="duel")
    <div id="profil-duel">
        {{ $game->duel->user($game->user)->username }}<br/>
        vs.<br/>
        @if($game->duel->challenger($game->user))
            {{ $game->duel->challenger($game->user)->username }}
        @else
            en attente d'un adversaire
        @endif
    </div>
    @elseif($mode=="admin-game")
        {{ trans('game.gains') }} : <span id="points_earned"></span>
	@endif
    </div>
    <div class="col-md-8 text-center">
        <h1 id="nom_phenomene">{{ $game->relation->description }}</h1>
        <div class="row" id="phase" style="position:absolute;text-align:center;">
        <div id="progress" style="z-index:1;color:#4A1710;position:absolute;text-align:center;width:100%;height:100px;line-height:92px;">0%</div>
			<div id="progressContainer" class="progress" style="width:60%;margin:0 20%;height:100px;display:block;background-color:#FFFFFC;">
                <div style="position:absolute;width: 0%;height:100%;background-color:#75211f;" class="progress-bar-game" id="phaseBar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">            
                    
                </div>
                {!! Html::image('img/bone.png','logo',array('id'=>'progressBar','style'=>'margin:0 20%;position:absolute;left:0;width:60%;')) !!}

			</div>
        </div>
    </div>
    @if($mode!="special")
    <div class="col-md-2 aideTool">
        <h4 style="position:relative;padding-left:63%;width:0;">{{ trans('game.need-help') }}</h4>
        <div class="savant">
            <div class="aideTip" id="helpRelation">
            @if(View::exists('help.'.App::getLocale().'.'.$game->relation->help_file))
                @include('help.'.App::getLocale().'.'.$game->relation->help_file)
            @endif
            </div>
        </div>
    </div>
    @endif 
</div>
<div class="row">
    <div class="col-md-6" id="infos">
    </div>
</div>
@if($mode=="special")
<div class="row">
    <div class="col-md-10 col-md-offset-1 text-center">
        @foreach($game->relations as $key => $relation)
			<a href="#phase" class="reponse" id_phenomene="{{ $relation['id'] }}">{{ $relation['name'] }}</a>
        @endforeach
    </div>
</div>
@endif
<div class="row">
    <div class="col-md-10 col-md-offset-1"><h3 id="indication" class="text-center"></h3></div>
    <div class="col-md-10 col-md-offset-1" id="phrase" style="top:50px;"></div>
@if($mode!="demo")
    <div class="col-md-1">
        <a id="refuse" class="tool"  style="top:80px;">
            <span class="tip">
                {{ trans('game.not-phenomenom-here') }}
            </span>
        </a>
    </div>
@endif
</div>

<div class="row text-center" style="position:relative;top:50px;">
    <h2 id="message-objet"></h2>
</div>

<div class="row" id="bottom" style="position:relative;top:50px;">
    @if($mode=="game"||$mode=="special")
        <div class="col-md-1">
            <a id="menuObjet"></a>
        </div>
		<div class="col-md-10 text-center">
            <div id="resultat"></div>
        </div>
    @else
        <div class="col-md-10 col-md-offset-1 text-center">            
			<div id="resultat"></div>
        </div>
    @endif
</div>

@if($mode=="game"||$mode=="special")
<div class="row" style="position:relative;top:50px;">
    <div class="col-md-12" id="inventaire">
<div class="contentObjet"><div class="objet tool" object_id="2"><span class="tip"></span>{!! Html::image('img/objet/thumbs/main_midas.png','object') !!}<span class="nombre"></span><br></div>{!! Html::image('img/piece.png','object') !!}<br><a class="btn btn-success buy" object_id="2"></a></div>
    </div>
</div>
<span id="sentence_id"></span>
@endif

@include('partials.game.modal-report')


<div class="modal fade" id="modalMessage" role="dialog">

</div>

<style>
#form-report .form-group{
    text-align:left;
}
#form-report label{
    font-weight:500;
}
</style>
