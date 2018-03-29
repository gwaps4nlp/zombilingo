<!-- Modal -->
<div class="modal fade" id="modalEndGame" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
	    <div class="modal-content">
    		<div class="modal-body">
    			<div class="row">
				    <div class="col-md-12 text-center" id="endGame">
						<h1>{{ trans('game.end-demo-title') }}</h1>
						<p class="p-5">
						{{ trans('game.points-won',['points'=>$game->points_earned]) }}<br/>
						{!! trans('game.end-demo-text',['link'=>link_to('register',trans('game.now'))]) !!}
						</p>
					</div>
				</div>
    			<div class="row">
				    <div class="col-md-12 text-center" id="endGame">
						<a href="{{ route('register') }}" class="btn btn-success btn-lg link" role="button">{{ trans('site.submit-register') }}</a>
						<a href="{{ route('demo') }}" class="btn btn-success btn-lg link" role="button">{{ trans('game.replay') }}</a>
						<a href="{{ route('home') }}" class="btn btn-success btn-lg link" role="button">{{ trans('site.home') }}</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>    