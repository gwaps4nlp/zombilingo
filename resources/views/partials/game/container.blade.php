<?php
$mode = $game->mode;
if($mode=="admin-game"){
    $mode="game";
    $admin_mode = true;
} else
    $admin_mode = false;
?>
    
<div id="container-game" class="container-fluid">
<div class="row">
    <div id="block-profil" class="col-2 pl-5 pr-2 pt-3">
    @if($admin_mode && $game->save_mode == 'expert')
        Attention !<br/>Mode Expert
    @elseif($admin_mode && $game->save_mode == 'user')
        Attention !<br/>Réponse enregistrée sous {{ $game->user_playing->username }}
    @elseif($mode=="game"||$mode=="special")
		{!! link_to('user/home','',array('id'=>'profil','title'=> trans('site.account'))) !!}
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
    <div class="col-8 text-center">
        <h3 id="label-phenomenon" class="py-3">{{ $game->relation->description }}</h3>
        <div class="row game-element" id="phase" style="">
            <div id="progress">0%</div>
			<div id="progress-container" class="progress">
                <div class="progress-bar-game" id="phaseBar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100">  
                </div>
                {!! Html::image('img/bone.png','progression',array('id'=>'progressBar','style'=>'margin:0 20%;position:absolute;left:0;width:60%;')) !!}
			</div>
        </div>

    </div>
    @if($mode!="special")
    <div class="col-2 aideTool">
        <h4>{{ trans('game.need-help') }}</h4>
        <div id="savant" class="savant help">
            <div class="aideTip" id="helpRelation">
            @if(View::exists('help.'.App::getLocale().'.'.$game->relation->help_file))
                @include('help.'.App::getLocale().'.'.$game->relation->help_file)
            @endif
            <a class="more-info" href="{{ url('faq#'.$game->relation->help_file) }}" target="_blank">En savoir plus <i class="fa fa-hand-o-right" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
    @endif 
</div>
<div class="row">
    <div class="col-6" id="infos">
    </div>
</div>

@if($mode=="special")
<div class="row">
    <div class="col-12 col-lg-10 mx-auto text-center">
        @foreach($game->relations as $key => $relation)
			<a href="#phase" class="reponse" id_phenomene="{{ $relation['id'] }}">{{ $relation['name'] }}</a>
        @endforeach
    </div>
</div>
@endif
<div class="row" id="content">
    <div class="col-lg-1"></div>
    <div class="col-lg-10"><h3 id="indication" class="text-center">&nbsp;</h3></div>
    <div class="col-lg-1"></div>
    <div class="col-lg-1" style="top:50px;"></div>
    <div class="col-lg-10 mx-auto" id="sentence-container" style="top:50px;">
        <div id="sentence">

        </div>
    </div>
@if($mode!="demo")
    <div id="refuse" class="pt-4 col-lg-1 d-none d-lg-flex flex-column">
        <div class="refuse tool mt-5" data-toggle="popover" data-placement="left" data-content="{{ trans('game.not-phenomenom-here') }}">
        </div>
    </div>
@else
    <div class="pt-4 col-lg-1 d-none d-lg-flex flex-column">
    </div>
@endif
</div>

<div class="row text-center element-game-bottom">
    <h3 id="message-object" class="m-auto"></h3>
</div>

<div class="row game-element pl-3 element-game-bottom" id="bottom">
    @if($mode=="game"||$mode=="special")
        <div class="col-1">
            <a id="menuObject"></a>
        </div>
		    <div class="col-9 text-center">
            <div id="resultat"></div>
        </div>
        <div class="col-2 d-lg-none pr-5">
            <div id="refuse-sm" class="refuse">
            </div>
        </div>       
    @else
        <div class="col-10 mx-auto text-center">            
          <div id="resultat" class="d-inline"></div>
          @if($mode=="training" || $mode=="duel")
            <div id="refuse-sm" class="d-lg-none pr-5 float-right refuse"></div>
          @endif
        </div>
    @endif
</div>

@if($mode=="game"||$mode=="special")
<div class="row game-element pl-3 element-game-bottom" style="">
    <div id="inventory">
        <div class="contentObject">
            <div class="object tool" object_id="2">
                <span class="tip"></span>
                {!! Html::image('img/object/thumbs/main_midas.png','object') !!}
                <span class="nombre"></span>
                <br />
            </div>
            {!! Html::image('img/piece.png','object') !!}
            <br />
            <a class="btn btn-success buy" object_id="2"></a>
        </div>
    </div>
</div>


@endif

</div>

<div class="modal " id="modalMessage" data-backdrop="static" role="dialog">

</div>

<div class="row justify-content-around">
    <div class="col-4">
      @include('partials.game.quest')
    </div>
</div>

@include('partials.game.modal-report')
