@if($game->next_level || count($game->trophies) || count($game->bonuses))
  <div class="modal fade" id="modalNextLevel" role="dialog">
	<div class="modal-dialog" style="width:700px;">
	
	  <!-- Modal content-->
	  <div class="modal-content">
		<div class="modal-body">
		  <button type="button" class="closeNextLevel close" data-dismiss="modal">&times;</button>
			@if($game->next_level)
				<h1>{{ trans('game.go-up-level') }}</h1>
				
				<p><img src="" id="img_level"/></p>

			{{ trans('game.you-can-accede-to-the-phenomena') }}
			
			<?php
			$relations = $game->relations_repo->getByUser(Auth::user(),null,null,Auth::user()->level->id);
			?>

			<ul>
			@foreach($relations as $relation)
				<li>{{ $relation->name }}</li>
			@endforeach
			</ul>

			@endif
			


			@foreach($game->trophies as $trophy)
				<h2>{{ trans('game.trophy-won') }} {{ trans('game.name-'.$trophy['slug']) }} !</h2>
			@endforeach

			@if(count($game->bonuses))
				@foreach($game->bonuses as $bonus)
					<h2>{{ trans('game.bonus-won') }} {{ trans('game.name-bonus-'.$bonus['slug']) }} !</h2>
					<p>{{ trans('game.description-bonus-'.$bonus['slug']) }}</p>
				@endforeach
			@endif
			<button type="button" class="btn btn-default closeNextLevel" data-dismiss="modal">{{ trans('site.continue') }}</button>
			<div class="modal-footer">
			  
			</div>			
		</div>

	  </div>
	  
	</div>
  </div>
@endif

<div class="modal fade" id="modalEndGame" data-backdrop="static" role="dialog">
    <div class="modal-dialog">

      <div class="modal-content">

        <div class="modal-body">
			<div class="row" style="margin:0;">
			    <div class="col-xs-12 col-sm-12 col-md-12" id="endGame">
					
					@if(isset($game->mwe))
						<h2><a href="{!! url('game/mwe/begin/0') !!}">{{ trans('game.play-rigor-mortis') }}</a></h2>
					@endif

					<div class="row" style="font-size: 1.5em">
						<div class="col-md-3 col-md-offset-1 col-sm-3 col-sm-offset-1 col-xs-3 col-xs-offset-1">
							{{ trans('game.before') }}
						</div>
						<div class="col-md-3 col-md-offset-1 col-sm-3 col-sm-offset-1 col-xs-3 col-xs-offset-1">
							{{ trans('game.gains') }}
						</div>
						<div class="col-md-3 col-md-offset-1 col-sm-3 col-sm-offset-1 col-xs-3 col-xs-offset-1">
							{{ trans('game.total') }}
						</div>
					</div>

					<div class="row" style="font-size: 1.5em">
						<div class="col-md-1 col-sm-1 col-xs-1">
							{!! Html::image('img/cerveau_plein.png','') !!}
						</div>
						<div class="col-md-3 col-sm-3 col-xs-3">
						     {!! Html::formatScore($game->user->score - $game->points_earned) !!}
						</div>
						<div class="col-md-1 col-sm-1 col-xs-1">
							+
						</div>
						<div class="col-md-3 col-sm-3 col-xs-3">
						    {!! Html::formatScore($game->points_earned) !!}
						</div>
						<div class="col-md-1 col-sm-1 col-xs-1">
							=
						</div>
						<div class="col-md-3 col-sm-3 col-xs-3" id="totalCerveaux" >
							<span goal="{{ $game->user->score }}" value="{{ $game->user->score - $game->points_earned }}">{!! Html::formatScore($game->user->score - $game->points_earned) !!}</span>
						</div>
					</div>

					<div class="row" style="font-size: 1.5em">
						<div class="col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 col-xs-2 col-xs-offset-1">
							{{ trans('game.before') }}
						</div>
						<div class="col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 col-xs-2 col-xs-offset-1">
							{{ trans('game.gains') }}
						</div>
						<div class="col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 col-xs-2 col-xs-offset-1">
							{{ trans('game.spending') }}
						</div>
						<div class="col-md-2 col-md-offset-1 col-sm-2 col-sm-offset-1 col-xs-2 col-xs-offset-1">
							{{ trans('game.total') }}
						</div>
					</div>

					<div class="row" style="font-size: 1.5em">
						<div class="col-md-1 col-sm-1 col-xs-1">
							{!! Html::image('img/piece.png','',['style'=>'position: relative;left: -13px;']) !!}
						</div>
						<div class="col-md-2 col-sm-2 col-xs-2">
							{!! Html::formatScore($game->user->money - $game->money_earned + $game->money_spent) !!}
						</div>
						<div class="col-md-1 col-sm-1 col-xs-1">
							+
						</div>
						<div class="col-md-2 col-sm-2 col-xs-2">
							{!! Html::formatScore($game->money_earned) !!}
						</div>
						<div class="col-md-1 col-sm-1 col-xs-1">
							-
						</div>
						<div class="col-md-2 col-sm-2 col-xs-2">
							{!! Html::formatScore($game->money_spent) !!}
						</div>
						<div class="col-md-1 col-sm-1 col-xs-1">
							=
						</div>
						<div class="col-md-2 col-sm-2 col-xs-2" id="totalPiece" >
							<span goal="{{ $game->user->money }}" value="{{ $game->user->money - $game->money_earned }}">{!! Html::formatScore($game->user->money - $game->money_earned) !!}</span>
						</div>
					</div>
					@if($game->mode!="special")
					<div class="row" style="font-size: 1.5em;margin-top:20px;">
						{{ trans('game.progress-phenomenom') }} : 
							<?php
								$progress = 100*$game->relation->done/($game->relation->todo + $game->relation->done);
							?>
							<div class="progress" style="margin: 2% 10% 0%;">
								<div style="padding-left:5px;height:20px;line-height:20px;color:#888;position:absolute;font-size:0.9vw;">
									{!! Html::formatScore($game->relation->done) !!} / {!! Html::formatScore($game->relation->todo + $game->relation->done) !!} annotations
								</div>
						    	<div class="progress-bar progress-bar-success" role="progressbar" style="background-color:#75211f;width:{{ $progress }}%">
                                </div>
                                <div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:{{ 100-$progress }}%">
                                </div>
                            </div>
						
					</div>
					@endif
					@if($game->relation->todo==0)
						<h2>{{ trans('game.you-have-played-all-the-annotations') }}</h2>
					@endif
					@if(count($game->neighbors['inf']))
						<h3>{{ trans('game.players-behind') }}</h3>
						<ul>
						@foreach($game->neighbors['inf'] as $neighbor)
							{{ trans('game.points-won-today',['username'=>$neighbor->username, 'score'=>Html::formatScore($neighbor->score)]) }}<br />
						@endforeach
						</ul>
					@endif

			        <h2 class="row">
			        	@if($game->relation->todo>0 || $game->mode=='special')
			            	<a href="#" id_phenomene="{!! $game->relation_id !!}" action="{!! $game->mode !!}" id="nouvellePartie" style="font-size:24px" class="btn btn-success link-level" data-dismiss="modal">{{ trans('game.restart-same-phenomenon') }}</a>
			            @endif
			            <a href="{!! route('game') !!}" style="font-size:24px" class="btn btn-success change">{{ trans('game.change-phenomenom') }}</a>
			        </h2>
				    </div>
				</div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
.modal-dialog {
    width: 700px;
}
</style>
