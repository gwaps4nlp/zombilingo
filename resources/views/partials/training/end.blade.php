<!-- Modal -->
<div class="modal fade" id="modalEndGame" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
	    <div class="modal-content">
    		<div class="modal-body">
    			<!-- <div class="row" style="margin:0;"> -->
				    <div id="endGame" class="text-center">
				    	@if($game->request->session()->has('duel.relation_id') && $game->request->session()->has('duel.duel_id') && $game->request->session()->get('duel.relation_id') == $game->relation_id)
							<h3>
							Bravo jeune zombi, tu peux maintenant faire le duel :
							{!! link_to('game/duel/begin/'.$game->request->session()->get('duel.duel_id'),'Commencer le duel',['class'=>'btn btn-large btn-success','action'=>'duel','style'=>'font-size:24px','id_phenomene'=>$game->request->session()->get('duel.duel_id'),'id'=>'btn-duel']) !!}
							</h3>
							<h3>
							Ou aider le fabuleux Prof. Frankenperrier en mode de jeu classique : 
							{!! link_to('#','Ici',['data-dismiss'=>'modal','class'=>'btn btn-large btn-success link-level','action'=>'game','style'=>'font-size:24px','id_phenomene'=>$game->relation_id,'id'=>'btn-play']) !!}
							</h3>						
							<h3>
							Ou retourner au menu de choix des phénomènes
							{!! link_to('game','Ici',['class'=>'btn btn-large btn-success change','style'=>'font-size:24px','id'=>'btn-back-to-index']) !!}
							</h3>							
						@else
							<h3>
							Bravo jeune zombi, tu peux maintenant aider le fabuleux Prof. Frankenperrier : 
							{!! link_to('#','Ici',['data-dismiss'=>'modal','class'=>'btn btn-large btn-success link-level','action'=>'game','style'=>'font-size:24px','id_phenomene'=>$game->relation_id,'id'=>'btn-play']) !!}
							</h3>						
							<h3>
							Ou retourner au menu de choix des phénomènes
							{!! link_to('game','Ici',['class'=>'btn btn-large btn-success change','style'=>'font-size:24px','id'=>'btn-back-to-index']) !!}
							</h3>
						@endif
					</div>
				</div>
			<!-- </div> -->
		</div>
	</div>
</div>
<style type="text/css">
.modal-dialog {
    width: 700px;
}
</style>