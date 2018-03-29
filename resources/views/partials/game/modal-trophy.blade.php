<div class="modal fade" id="modalNextLevel" role="dialog" data-backdrop="static">
<div class="modal-dialog" style="width:90%;">

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
			<p>{{ $trophy['description'] }}</p>
			<h2>{{ trans('game.trophy-won') }} {{ trans('game.name-'.$trophy['slug']) }} !</h2>
		@endforeach

		@if(count($game->bonuses))
			@foreach($game->bonuses as $bonus)
				<h2>{{ trans('game.bonus-won') }} {{ trans('game.name-bonus-'.$bonus['slug']) }} !</h2>
				<p>{{ trans('game.description-bonus-'.$bonus['slug']) }}</p>
			@endforeach
		@endif
		
		<div class="modal-footer text-center">
		<button type="button" class="btn btn-success closeNextLevel" data-dismiss="modal">{{ trans('site.continue') }}</button>  
		</div>
	</div>

  </div>
  
</div>
</div>