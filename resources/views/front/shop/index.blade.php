@extends('front.template')

@section('main')

<div class="row" id="blocJeu">
    <div id="shop" class="col-md-10 col-md-offset-1 center">
        <div class="row">
            <div id="caddy" class="col-md-4">

            </div>
            <div id="result" class="col-md-4 text-center">
            @if(count($games_in_progress))
                {{ trans('shop.game-in-progress-1') }}<br/>
                {{ trans('shop.game-in-progress-2') }}<br/>
                {{ trans('shop.game-in-progress-3') }}<br/>
                {{ trans('shop.game-in-progress-4') }}<br/>
               @foreach ($games_in_progress as $game_in_progress)
            		@if($game_in_progress->relation->type=='special')
               			- {!! link_to('game/special/begin/'.$game_in_progress->relation->id.'?corpus_id='.$game_in_progress->corpus->id,$game_in_progress->relation->name.' (corpus '.$game_in_progress->corpus->name.')') !!}<br/>
                	@else
						- {!! link_to('game/game/begin/'.$game_in_progress->relation->id.'?corpus_id='.$game_in_progress->corpus->id,$game_in_progress->relation->name.' (corpus '.$game_in_progress->corpus->name.')') !!}<br/>
                	@endif
               @endforeach
            @endif
            </div>
            <div id="purse" class="col-md-4">
                <h2>{!! Html::image('img/piece.png','money') !!}<span id="money">{{ $game->user->money}}</span></h2>
            </div>
        </div>

	@foreach ($game->inventaire() as $object)
		<?php
			$price = ($game->isInProgress())?$object->price_ingame:$object->price;
		?>
		<div class="object row" object_id="{{$object->id}}" id="{{$object->name}}">
			<div class="col-md-12">
			
			<span class="image">
				{!! Html::image('img/objet/'.$object->image,trans('game.name-'.$object->slug)) !!}
			</span>
			<p>
				<span class="name">
					{{ trans('game.name-'.$object->slug) }}
				</span><br />
				<span class="description">
					{!! Html::image('img/piece.png','prix') !!}&nbsp;
					{{ $price }}
				</span><br />
				@if(Auth::user()->money < $price)
					<span class="error" object_id="{{ $object->id }}">{{ trans('shop.not-enough-money') }}</span>
				@else
					<a class="buy btn btn-success" url="{{ url('game/buyObject') }}" object_id="{{ $object->id }}">
						{{ trans('shop.buy') }}
					</a><span class="error" object_id="{{ $object->id }}"></span>
				@endif
				<br />
				{{ trans('shop.in-inventory') }} : 
				<span class="owned" object_id="{{ $object->id }}">
					{{ $object->quantity }}
				</span><br />
				{{ trans('game.description-'.$object->slug) }}
			</p>
			</div>
			<div></div>
		</div>
	@endforeach
    </div>
</div>

@stop
