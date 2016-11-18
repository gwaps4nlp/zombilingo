@extends('back.master')

@section('css')

{!! Html::style('css/back/css/bootstrap.css') !!}
<style>
.herbe{display:none;}
h1{
  font-size:24px;
}
</style>
@yield('style')

@stop

@section('main')

	<div class="row">
		<div class="col-md-2 col-md-offset-1">
<ul class="nav nav-pills nav-stacked">
  <li class="dropdown">
    <a class="dropdown-toggle" href="{{ url('corpus/index') }}">{!! trans('back/index.corpus') !!} </a>
    <ul class="collapse-menu">
      <li>{!! link_to('corpus/index',trans('back/index.list-corpus')) !!}</li>
      <li>{!! link_to('corpus/import',trans('back/index.import')) !!}</li>
      <li>{!! link_to('corpus/export',trans('back/index.export')) !!}</li>
      <li>{!! link_to('corpus/export-mwe',trans('back/index.export-mwe')) !!}</li>
      <li>{!! link_to('corpus/import-from-url','Import from url (test)') !!}</li>
      <li>{!! link_to('annotation-user/index','Statistiques') !!}</li>
      <li>{!! link_to('corpus/compare','Statistiques par parser') !!}</li>
      <li>{!! link_to('corpus/diff-by-pos','Diff by pos') !!}</li>
    </ul>
  </li>
  <li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">Game</a>
    <ul class="collapse-menu">
      <li>{!! link_to('challenge/index',trans('back/index.challenges')) !!}</li>
      <li>{!! link_to('negative-annotation/index',trans('back/index.negative-items')) !!}</li>
      <li>{!! link_to('tutorial-annotation/index',trans('back/index.tutorial-items')) !!}</li>
      <li>{!! link_to('constant-game/index',trans('back/index.constants-game')) !!}</li>
      <li>{!! link_to('trophy/index',trans('back/index.trophies')) !!}</li>
      <li>{!! link_to('admin/mwe',trans('back/index.play-rigor-mortis')) !!}</li>
      <li>{!! link_to('mini-game/index','Jeux langue française') !!}</li>
    </ul>
  </li>    
  <li>{!! link_to('news',trans('back/index.news')) !!}</li>
  <li>{!! link_to('sentence/index',trans('back/index.sentences')) !!}</li>
  <li>{!! link_to('user/index-admin',trans('back/index.users')) !!}</li>
  <li>{!! link_to('admin/reporting',trans('back/index.reporting')) !!}</li>

  <li>{!! link_to('language',trans('back/index.language')) !!}</li>
</ul>
</div>
<div class="col-md-8">
@yield('content')
</div>
</div>
</div>
@stop