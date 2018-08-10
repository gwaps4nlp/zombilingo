@if($game->next_level || count($game->trophies) || count($game->bonuses))
	@include('partials.game.modal-trophy')
@endif
<?php 
$data = [];
$annotation_ids = $game->already_played;
?>
<div class="modal fade out" id="modalEndGame" data-backdrop="static" role="dialog" style="z-index:5000">
    <div class="modal-dialog modal-lg">

      <div class="modal-content">

        <div class="modal-body">
        <div class="container-fluid">
			<div class="row">
			    <div class="col-12" id="endGame">
				    <div id="results" style="display:none;">
					    @include('partials.discussion.index',['annotation_ids'=>$annotation_ids,'hidden_titles'=>true])
				    </div>
			    	<div class="text-center">
					@if(isset($game->mwe))
						<h2><a href="{!! url('game/mwe/begin/0') !!}">{{ trans('game.play-rigor-mortis') }}</a></h2>
					@endif
					</div>
					<div style="font-size:1.7rem">
					<div class="row row-results">
						<div class="col-1"></div>
						<div class="col-3">
							{{ trans('game.before') }}
						</div>
						<div class="col-1"></div>
						<div class="col-3">
							{{ trans('game.gains') }}
						</div>
						<div class="col-1"></div>
						<div class="col-3">
							{{ trans('game.total') }}
						</div>
					</div>

					<div class="row row-results">
						<div class="col-1">
							{!! Html::image('img/cerveau_plein.png','') !!}
						</div>
						<div class="col-3">
						     {!! Html::formatScore($game->user->score - $game->points_earned) !!}
						</div>
						<div class="col-1">
							+
						</div>
						<div class="col-3">
						    {!! Html::formatScore($game->points_earned) !!}
						</div>
						<div class="col-1">
							=
						</div>
						<div class="col-3" id="totalCerveaux" >
							<span goal="{{ $game->user->score }}" value="{{ $game->user->score - $game->points_earned }}">{!! Html::formatScore($game->user->score - $game->points_earned) !!}</span>
						</div>
					</div>

					<div class="row row-results">
						<div class="col-1"></div>
						<div class="col-2">
							{{ trans('game.before') }}
						</div>
						<div class="col-1"></div>
						<div class="col-2">
							{{ trans('game.gains') }}
						</div>
						<div class="col-4">
							{{ trans('game.spending') }}
						</div>
						<div class="col-2">
							{{ trans('game.total') }}
						</div>
					</div>

					<div class="row row-results">
						<div class="col-1">
							{!! Html::image('img/piece.png','',['style'=>'position: relative;left: -13px;']) !!}
						</div>
						<div class="col-2">
							{!! Html::formatScore($game->user->money - $game->money_earned + $game->money_spent) !!}
						</div>
						<div class="col-1">
							+
						</div>
						<div class="col-2">
							{!! Html::formatScore($game->money_earned) !!}
						</div>
						<div class="col-1">
							-
						</div>
						<div class="col-2">
							{!! Html::formatScore($game->money_spent) !!}
						</div>
						<div class="col-1">
							=
						</div>
						<div class="col-2" id="totalPiece" >
							<span goal="{{ $game->user->money }}" value="{{ $game->user->money - $game->money_earned }}">{!! Html::formatScore($game->user->money - $game->money_earned) !!}</span>
						</div>
					</div>
					</div>
				    <div class="text-center">
				    	<a id="compare_results" class="btn btn-success mt-1  btn-sm" data-dismiss="modal" href="#">Comparer mes choix avec ceux des autres</a>
						<!-- Tu as répondu comme la majorité des joueurs à&nbsp;<span id="majority" class="answer_refused user_majority"></span>
						<button id="compare_results" class="btn btn-success mt-1  btn-sm" data-dismiss="modal" href="#">Comparer</button> -->
				    </div>
				    <div id="first-answers" class="text-center" style="display:none;">
						Sur <span class="nb-first-answer"></span> phrases, tu a été le 1er à donner une réponse.
				    </div>
				    <div id="first-answer" class="text-center" style="display:none;">
						Sur <span class="nb-first-answer"></span> phrase, tu a été le 1er à donner une réponse.
				    </div>
				    <div class="text-center mt-2">
				    	<div id="no-discussion" style="display:none;">
							Aucune phrase jouée n'a une discussion en cours <button id="compare_results" class="btn btn-success btn-sm" data-dismiss="modal">Entamer une discussion</button>
						</div>
				    	<div id="one-discussion" style="display:none;">
							Une phrase jouée a une discussion en cours <button id="compare_results" class="btn btn-success btn-sm" data-dismiss="modal">Voir</button>
						</div>
				    	<div id="several-discussions" style="display:none;">
							<span id="annotations_with_discussion"></span> phrases jouées ont des discussions en cours <button id="compare_results" class="btn btn-success btn-sm" data-dismiss="modal">Voir</button>
						</div>
				    </div>						
					@if($game->mode!="special")
					<div class="row justify-content-center" style="font-size: 1.5em;margin-top:20px;">
							<div class="">{{ trans('game.progress-phenomenom') }} :</div>
							<?php
								$progress = 100*$game->relation->done/($game->relation->todo + $game->relation->done);
							?>
							<div class="progress" style="margin: 2% 10% 0%; width:100%">
								<div style="padding-left:5px;line-height:20px;color:#888;position:absolute;font-size:0.9vw;">
									{!! Html::formatScore($game->relation->done) !!} / {!! Html::formatScore($game->relation->todo + $game->relation->done) !!} annotations
								</div>
						    	<div class="progress-bar progress-bar-success" role="progressbar" style="background-color:#75211f;width:{{ $progress }}%">
                                </div>
                                <div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:{{ 100-$progress }}%">
                                </div>
                            </div>
						
					</div>
					@endif
					<div class="row justify-content-center text-center">
					@if($game->relation->todo==0)
						<h2>{{ trans('game.you-have-played-all-the-annotations') }}</h2>
					@endif
					@if(count($game->neighbors['inf']))
						<h5>{{ trans('game.players-behind') }}</h5>
						<ul>
						@foreach($game->neighbors['inf'] as $neighbor)
							{{ trans('game.points-won-today',['username'=>$neighbor->username, 'score'=>Html::formatScore($neighbor->score)]) }}<br />
						@endforeach
						</ul>
					@endif
					</div>

			        <div class="row" id="block-replay">
			        	@if($game->relation->todo>0 || $game->mode=='special')
			            	<button href="#" id_phenomene="{!! $game->relation_id !!}" action="{!! $game->mode !!}" id="nouvellePartie" class="btn btn-lg btn-success link-level m-1" data-dismiss="modal">{{ trans('game.restart-same-phenomenon') }}</button>
			            @endif
			        	<button href="{!! route('game') !!}" class="btn btn-lg btn-success change m-1 link-level">{{ trans('game.change-phenomenom') }}</button>
			        </div>
				    </div>
				</div>
			</div>
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" type="text/css" href="{{ asset('brat/style-vis.css') }}"/>
<style type="text/css">
#block-replay {

}
.row-results {
	text-align: center;
}
.btn-index {
	font-size : 16px;
}
text {
    font-size: 18px;
    fill:#4a1710;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
}
.span text {
    font-size: 15px;
    text-anchor: middle;
    font-family: 'PT Sans Caption', sans-serif;
    pointer-events: none;
}
.arcs text {
    font-size: 11px;
    text-anchor: middle;
    font-family: 'PT Sans Caption', sans-serif;
    dominant-baseline: central;
    cursor: default;
}
.background, .background0 {
	fill: #9BC5AA;
}
g.highlight {
    font-family: 'shlop';
    color: #0e7f3c;
}

</style>