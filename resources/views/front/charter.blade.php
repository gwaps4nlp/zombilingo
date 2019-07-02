<?php
use Gwaps4nlp\Core\Models\ConstantGame;
$app_name = Config::get('app.name');
?>
@extends('front.template')

@section('css')
{!! Html::style('css/compte.css') !!}
@stop

@section('main')

<div class="row">
  <div class="col-xl-10 offset-xl-1">
    <div class="row">
      <div class="col-12" id="charte">
        @if($app_name == 'zombiludik')
          @include('lang/'.App::getLocale().'/charte_zlud')
        @else
          @include('lang/'.App::getLocale().'/charte')
        @endif
      </div>
    </div>
  </div>
</div>

@stop