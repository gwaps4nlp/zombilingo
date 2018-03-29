@extends('front.template-game')

@section('css')
    {!! Html::style('css/bootstrap-tour.css') !!}
@stop

@section('main')

<div class="row container-site">
    <div id="block-game">
    <div class="row">
		<div class="col-10 mx-auto py-5" id="sentence-container">
			<div id="sentence">
		        <h2>{{ trans('game.intro-demo') }}</h2>
		        <p class="text-center">
		            <button class="btn btn-success btn-lg phenomene link-level" action="demo" id_phenomene="0" >{{ trans('game.submit-demo') }}</button>
		        </p>
			</div>    
	    </div>
    </div>
    </div>
</div>

@stop