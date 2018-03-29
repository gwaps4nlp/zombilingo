<!-- Modal -->
<?php
$duel=$game->duel;
?>
<div class="modal fade" id="modalEndGame" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
	    <div class="modal-content">
    		<div class="modal-body">
    			<div class="row" style="margin:0;">
				    <div class="col-md-12" id="endGame">
						<h1>
						{{ $duel->relation->name }}
						</h1>
			            <table class="table">
			                    <tr>
			                    @foreach($duel->users()->get() as $key=>$user)
			                        @if($key==0)
			                        <td style="text-align:right;width:50%;">
			                        @else
			                        <td style="text-align:left;width:50%;">
			                        @endif
			                            <div style="min-height:3em">{{ $user->username }}</div>
										@if($duel->state!='completed')
				                            <?php $progress = round(100*$user->pivot->turn/$duel->nb_turns); ?>
				    						<div class="progress">
				                                <div class="progress-bar progress-bar-success" role="progressbar" style="background-color:#75211f;width:{{ $progress }}%">
				                                    {{ $progress }}%
				                                </div>
				                                <div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:{{ 100-$progress }}%">
				                                @if(!$progress)
				                                    0%
				                                @endif
				                                </div>
				                            </div>
				                        @else
				                        	{{ $user->pivot->score }}
				                        @endif
			                        </td>
			                        @if($key==0)
			                            <td style="width:0%;">vs.</td>
			                        @endif
			                    @endforeach
			                    
			                    @if(count($duel->users)<$duel->nb_users)
			                        <td style="width:50%;text-align:left;">
			                        	<div style="min-height:3em;">en attente d'un adversaire</div>
			                            <div class="progress">
			                                <div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:100%">
			                                    0%
			                                </div>
			                            </div>
			                        </td>
			                    @endif
			                    </tr>
			            </table>
			            <div class="text-center">
					        @if($duel->state!='completed')
								@if(isset($progress_challenger))
									Ton adversaire a pour l'instant complété sa série à {{ $progress_challenger }}%.<br/>
								@else
									Ton adversaire n'a pas encore terminé sa série.<br/>
								@endif
								Tu seras prévenu du résultat dès qu'il aura fini.
							@else
					        	<button style="font-size:17px" class="btn btn-success duel-completed" id_phenomene="{{ $duel->id }}">{{ trans('game.compare-answers') }}</button><br/><br/>
					        	Phénomène level {{ $duel->level_id }} = scores x {{ $duel->level_id }}<br/>
								@foreach($duel->users()->get() as $user)
									{{ $user->pivot->rank }}<sup>e</sup> {{ trans_choice('game.duel-score-gained',$user->pivot->final_score,['score'=>$user->pivot->final_score,'username'=>$user->username]) }}<br/>
								@endforeach
							@endif
						</div>
						<br/>					
				        <div class="text-center">
					        @if($duel->state=='completed')
					        	<a href="{!! url('duel/revenge',[$duel->id]) !!}" style="font-size:17px" class="btn btn-success change">{{ trans('game.make-revenge') }}</a>
					        @endif
				           	<a href="{!! url('duel/new') !!}" style="font-size:17px" id="openNewDuel" class="btn btn-success change">{{ trans('game.new-duel') }}</a>
				            <a href="{!! url('duel') !!}" style="font-size:17px" class="btn btn-success change">{{ trans('game.back-to-menu') }}</a>
				        </div>					
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">

div.progress {
    color:black;
}
</style>