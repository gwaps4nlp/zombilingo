@if($game->relation->tutorial)
	@include('partials.game.container')
@else
	Tu dois d'abord faire la formation.
@endif