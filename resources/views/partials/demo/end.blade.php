<!-- Modal -->
<div class="modal fade" id="modalEndGame" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
	    <div class="modal-content">
    		<div class="modal-body">
    			<div class="row">
				    <div class="col-md-12" id="endGame">
						<h1>{{ trans('game.end-demo-title') }}</h1>
						{{ trans('game.end-demo-title',['points'=>$game->points_earned]) }}<br/>
						{!! trans('game.end-demo-text',['link'=>link_to('auth/register',trans('game.now'))]) !!}<br/>

						<a href="{{ url('auth/register') }}" class="btn btn-success btn-lg link" role="button">{{ trans('site.submit-register') }}</a>
						<a href="{{ url('game/demo/') }}" class="btn btn-success btn-lg link" role="button">{{ trans('game.replay') }}</a>
						<a href="{{ url('') }}" class="btn btn-success btn-lg link" role="button">{{ trans('site.home') }}</a>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>    