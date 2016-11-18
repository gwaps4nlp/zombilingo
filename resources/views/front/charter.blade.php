@extends('front.template')

@section('css')
{!! Html::style('css/compte.css') !!}
@stop

@section('main')

<div class="row">
    <div class="col-md-10 col-md-offset-1 center">
        <div class="row">
		    <div class="col-md-12" id="charte">
			@include('lang/'.App::getLocale().'/charte')
		    </div>
        </div>
    </div>
</div>

@stop