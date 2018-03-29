@extends('front.template')

@section('css')
{!! Html::style('css/compte.css') !!}
@stop

@section('main')

<div class="row">
    <div class="col-xl-10 offset-xl-1">
        <div class="row">
		    <div class="col-12" id="charte">
			@include('lang/'.App::getLocale().'/charte')
		    </div>
        </div>
    </div>
</div>

@stop