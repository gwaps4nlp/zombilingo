@extends('front.template')

@section('css')
    {!! Html::style('css/bootstrap-tour.css') !!}
    {!! Html::style('css/non-sass.css') !!}
@stop

@section('main')

<div class="row" >
    <div class="col-md-10 col-md-offset-1 center" id="blocJeu">
        <h1>{{ trans('game.intro-demo') }}</h1>
        <p style="text-align:center;">
            <br /><a class="btn btn-success btn-lg phenomene link-level" action="demo" id_phenomene="0" >{{ trans('game.submit-demo') }}</a>
        </p>
    </div>
</div>

@stop

