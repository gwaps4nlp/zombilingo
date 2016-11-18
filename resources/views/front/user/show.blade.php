@extends('front.template')

@section('main')
<div class="row" id="show-user">
    <div class="col-md-10 col-md-offset-1 center">
        <div class="row">
            <div class="col-md-6" id="left">
                <div>
                    <div id="result">
                    </div>
                    <h1>{{ $user->username }}
                        @if(!Auth::user()->hasFriend($user) && Auth::user()->id!=$user->id)
                            <br />
							<a href="#" id="demandeAmi" url="{{ url('user/ask-friend/'.$user->id) }}">{{ trans('site.ask-friend') }}</a>
                        @endif
                    </h1>
                    <p>
						{!! Html::image('img/cerveau_plein.png','Points') !!}
						{{ Html::formatScore($user->score) }}
                    </p>
                </div>
            </div>
            <div class="col-md-6" id="right">
                <div>
					<h1>Trophées</h1>
					@forelse ($user->trophies as $trophy)
						<span class="trophee">
						<span class="nom">{{ $trophy->name }}</span>
						{!! Html::image('img/trophee/'.$trophy->image,'Trophée') !!}
						<span class="description">{{ $trophy->description }}</span>
						</span>
					@empty
					{{ trans('game.no-trophy')}}
					@endforelse					
                </div>
            </div>
        </div>
    </div>
</div>

@stop

