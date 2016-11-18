<!-- Modal -->
<div class="modal fade" id="modalEndGame" data-backdrop="static" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
	    <div class="modal-content">
    		<div class="modal-body">
    			<div class="row" style="margin:0;">
				    <div class="col-md-12" id="endGame">
				    	@if($game->request->session()->has('duel.relation_id') && $game->request->session()->has('duel.duel_id') && $game->request->session()->get('duel.relation_id') == $game->relation_id)
							<h1>
							Bravo jeune zombi, tu peux maintenant faire le duel :
							{!! link_to('game/duel/begin/'.$game->request->session()->get('duel.duel_id'),'Commencer le duel',['class'=>'btn btn-large btn-success','action'=>'duel','style'=>'font-size:24px','id_phenomene'=>$game->request->session()->get('duel.duel_id')]) !!}
							</h1>
							<h1>
							Ou aider le fabuleux Prof. Frankenperrier en mode de jeu classique : 
							{!! link_to('#','Ici',['data-dismiss'=>'modal','class'=>'btn btn-large btn-success close-modal link-level','action'=>'game','style'=>'font-size:24px','id_phenomene'=>$game->relation_id]) !!}
							</h1>						
							<h1>
							Ou retourner au menu de choix des phénomènes
							{!! link_to('game','Ici',['class'=>'btn btn-large btn-success change','style'=>'font-size:24px']) !!}
							</h1>							
						@else
							<h1>
							Bravo jeune zombi, tu peux maintenant aider le fabuleux Prof. Frankenperrier : 
							{!! link_to('#','Ici',['data-dismiss'=>'modal','class'=>'btn btn-large btn-success close-modal link-level','action'=>'game','style'=>'font-size:24px','id_phenomene'=>$game->relation_id]) !!}
							</h1>						
							<h1>
							Ou retourner au menu de choix des phénomènes
							{!! link_to('game','Ici',['class'=>'btn btn-large btn-success change','style'=>'font-size:24px']) !!}
							</h1>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<style type="text/css">
#endGame {
    text-align: center;
    background: #9BC5AA none repeat scroll 0% 0%;
    margin-top: 0px;
    padding-bottom: 0px;
    border-radius: 6px;
}
.modal-body {
    position: relative;
    padding: 5px;
    background-color: #0E7F3C;
    border-radius: 6px;
}
.modal-content {
    border-radius: 9px;
}
.modal-dialog {
    width: 700px;
}
</style>