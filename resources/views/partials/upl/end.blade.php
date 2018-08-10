@if(count($game->trophies) || count($game->bonuses))
	@include('partials.game.modal-trophy')
@endif
<div class="modal fade" id="modalEndGame" data-backdrop="static" role="dialog">
    <div class="modal-dialog modal-lg">
	    <div class="modal-content">
    		<div class="modal-body">
    			<div class="row">
				    <div class="col-md-12 text-center" id="endGame">
				    	@if($game->mode_stage=='expert')
				    		<h4>Fin de la séquence</h4>
							<a class="btn btn-success" href="{{ url('game/upl/begin/'.$game->stage->id.'?admin=1') }}">Jouer la séquence en mode test</a>
							<a class="btn btn-success" href="{{ url('upl/admin-index?open='.$game->stage->id) }}" >Retour à l'administration</a>	    		
				    	@elseif($game->stage->mode=='demo')
							<div>
								Tu as trouvé {{ $game->correct_answers }} {{ trans_choice('game.mwe',$game->correct_answers) }}.
							</div>
							<div>
								<strong>Tu aurais pu en trouver {{ $game->number_upls }}.</strong>
							</div>				    	
				    		<div>
								Me revoilà ! Alors, ces résultats ? Pas terrible... non je plaisante. Cette première partie m’a servi à tester ton intuition, trait important de tout aventurier. Tu ne pourras plus la refaire mais tu peux consulter tes <a href="{{ url('upl/results/'.$game->stage->id) }}">résultats</a>.
								<br/><br/>
								Maintenant, passons aux choses sérieuses, je vais t’expliquer plus en détail à quoi ressemble ce que nous cherchons à travers de petites formations, retrouve-moi dans les <a href="{{ url('game/upl') }}">couloirs de la pyramide</a>.
							</div>
							<!-- <h4>Tu as fini la phase d'apprentissage !</h4> -->
							<div>
								<button class="btn btn-success link-level" data-dismiss="modal" action="upl" id_phenomene="{{ $game->next_stage->id }}">Faire la première formation</button>
								<a class="btn btn-success" href="{{ url('game/upl') }}" >Retour au menu</a>
							</div>
						@elseif($game->stage->mode=='training')
							<div>
								@if($game->next_stage->mode=='game')
									<h4>Bravo ! Tu as complété toutes les formations !</h4>
									<p>(Tu pourras continuer à y accéder à tout moment si tu en as besoin)</p>
									<p>Tu gagneras des points pour chaque expression trouvée, d’autres récompenses telles que des badges sont également à décrocher.</p>
									<p>Maintenant, tu sais ce qu’il te reste à faire ? Trouve les expressions multi-mots et deviens le meilleur aventurier !</p>
									<button class="btn btn-success link-level" data-dismiss="modal" action="upl" id_phenomene="{{ $game->next_stage->id }}">Jouer</button> ou 
									<a href="{{ url('game/upl') }}" class="btn btn-success">Retour au menu</a>
								@else
									<button class="btn btn-success link-level" data-dismiss="modal" action="upl" id_phenomene="{{ $game->next_stage->id }}">Formation suivante</button> ou 
									<a href="{{ url('game/upl') }}" class="btn btn-success">Retour au menu</a>
								@endif
							</div>

						@else($game->stage->mode=='game')
							<div>
								<div class="row justify-content-center mt-3">
		                            @if($game->number_sentences_to_do==0)
										<h2>Félicitations ! Tu as fait toutes les phrases du niveau !</h2>
									@else
										<h3 class="mb-3">Ta progression sur ce niveau :</h3>
									@endif

									<?php
										$number_sentences_done = $game->number_sentences_stage - $game->number_sentences_to_do;
										$progress = 100*($number_sentences_done)/($game->number_sentences_stage);
									?>
									<div class="progress" style="margin: 3% 15% 0%; width:100%">
										<div style="padding-left:5px;line-height:20px;color:#888;position:absolute;font-size:0.9vw;">
											{!! Html::formatScore($number_sentences_done) !!} / {!! Html::formatScore($game->number_sentences_stage) !!} phrases
										</div>
								    	<div class="progress-bar progress-bar-success" role="progressbar" style="background-color:#75211f;width:{{ $progress }}%">
		                                </div>
		                                <div class="progress-bar progress-bar-danger" role="progressbar" style="color:#000;background-color:#fff;width:{{ 100-$progress }}%">
		                                </div>
		                            </div>
		                            <div class="m-4">	
		                            @if($game->number_sentences_to_do>0)
										<button class="btn btn-success link-level" data-dismiss="modal" action="upl" id_phenomene="{{ $game->stage->id }}">Rejouer</button>
									@endif
									<a href="{{ url('game/upl') }}" class="btn btn-success">Retour au menu</a>
									</div>
								</div>
								</div>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>    