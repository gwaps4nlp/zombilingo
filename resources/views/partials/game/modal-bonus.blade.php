  <!-- Modal -->
  <div class="modal fade" id="modalEndGame" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">

        <div class="modal-body">
			<div class="row" style="margin:0;">
			    <div class="col-md-12" id="endGame">
					@if($game->next_level)
					  <div class="modal fade" id="myModal" role="dialog">
						<div class="modal-dialog" style="width:700px;">
						
						  <!-- Modal content-->
						  <div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title">{{ trans('game.go-up-level') }}</h4>
							</div>
							<div class="modal-body">
							  <p><img src="" id="img_level"/></p>
							</div>
							<div class="modal-footer">
							  <button type="button" class="btn btn-default" id="closeNextLevel" data-dismiss="modal">{{ trans('site.close') }}</button>
							</div>
						  </div>
						  
						</div>
					  </div>
					@endif

					@if(count($game->trophies))
						@foreach($game->trophies as $trophy)
							<h2>{{ trans('game.trophy-won') }} {{ $trophy }} !</h2>
						@endforeach
					@endif

					@if(count($game->bonuses))
						@foreach($game->bonuses as $bonus)
							<h2>{{ trans('game.bonus-won') }} {{ $bonus }} !</h2>
						@endforeach
					@endif

					@if(isset($game->mwe))
						<h2><a href="{!! url('game/mwe/begin/0') !!}">{{ trans('game.play-rigor-mortis') }}</a></h2>
					@endif

					<div class="row" style="font-size: 1.5em">
						<div class="col-md-3 col-md-offset-1">
							{{ trans('game.before') }}
						</div>
						<div class="col-md-3 col-md-offset-1">
							{{ trans('game.gains') }}
						</div>
						<div class="col-md-3 col-md-offset-1">
							{{ trans('game.total') }}
						</div>
					</div>

					<div class="row" style="font-size: 1.5em">
						<div class="col-md-1">
							{!! Html::image('img/cerveau_plein.png','') !!}
						</div>
						<div class="col-md-3">
						     {!! $game->user->score - $game->points_earned !!}
						</div>
						<div class="col-md-1">
							+
						</div>
						<div class="col-md-3">
						    {!! $game->points_earned !!}
						</div>
						<div class="col-md-1">
							=
						</div>
						<div class="col-md-3" id="totalCerveaux" >
							<span goal="{{ $game->user->score }}">{!! $game->user->score - $game->points_earned !!}</span>
						</div>
					</div>

					<div class="row" style="font-size: 1.5em">
						<div class="col-md-2 col-md-offset-1">
							{{ trans('game.before') }}
						</div>
						<div class="col-md-2 col-md-offset-1">
							{{ trans('game.gains') }}
						</div>
						<div class="col-md-2 col-md-offset-1">
							{{ trans('game.spending') }}
						</div>
						<div class="col-md-2 col-md-offset-1">
							{{ trans('game.total') }}
						</div>
					</div>

					<div class="row" style="font-size: 1.5em">
						<div class="col-md-1">
							{!! Html::image('img/piece.png','',['style'=>'position: relative;left: -13px;']) !!}
						</div>
						<div class="col-md-2">
							{!! $game->user->money - $game->money_earned + $game->money_spent !!}
						</div>
						<div class="col-md-1">
							+
						</div>
						<div class="col-md-2">
							{!! $game->money_earned !!}
						</div>
						<div class="col-md-1">
							-
						</div>
						<div class="col-md-2">
							{!! $game->money_spent !!}
						</div>
						<div class="col-md-1">
							=
						</div>
						<div class="col-md-2" id="totalPiece" >
							<span goal="{{ $game->user->money }}">{!! $game->user->money - $game->money_earned !!}</span>
						</div>
					</div>
					@if($game->mode!="special")
					<div class="row" style="font-size: 1.5em;margin-top:20px;">
						{{ trans('game.progress-phenomenom') }} : {{$game->relation->done}} / {{$game->relation->todo + $game->relation->done}}
					</div>
					@endif
					@if(count($game->neighbors['inf']))
						<h3>{{ trans('game.players-behind') }}</h3>
						<ul>
						@foreach($game->neighbors['inf'] as $neighbor)
							{{ trans('game.points-won-today',['username'=>$neighbor->username, 'score'=>$neighbor->score]) }}<br />
						@endforeach
						</ul>
					@else
					@endif

			        <h2 class="row">
			        	@if($game->relation->todo>0 || $game->mode=='special')
			            	<a href="#" id_phenomene="{!! $game->relation_id !!}" action="{!! $game->mode !!}" id="nouvellePartie" style="font-size:24px" class="btn btn-success" data-dismiss="modal">{{ trans('game.restart-same-phenomenon') }}</a>
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
