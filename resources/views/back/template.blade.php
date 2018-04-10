@extends('back.master')

@section('css')

<style>
.herbe{display:none;}
h1{
  font-size:24px;
}
ul.nav > li > a {
  padding: 5px 0px;
}
ul.nav > li > ul > li > a {
  padding: 5px 15px;
}
</style>
@yield('style')

@stop

@section('container')

<div class="row">
	<div class="col-2">
    <ul class="nav flex-column" role="tablist" aria-multiselectable="true">
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="collapse" data-target="#collapseCorpus" aria-expanded="false" aria-controls="collapseCorpus" href="{{ url('corpus/index') }}">{!! trans('back/index.corpus') !!} </a>
        <ul class="collapse" id="collapseCorpus">
          <li>{!! link_to('corpus/index',trans('back/index.list-corpus')) !!}</li>
          <li>{!! link_to('corpus/import',trans('back/index.import')) !!}</li>
          <li>{!! link_to('corpus/export',trans('back/index.export')) !!}</li>
          <li>{!! link_to('corpus/export-mwe',trans('back/index.export-mwe')) !!}</li>
          <li>{!! link_to('corpus/import-from-url','Import url or text') !!}</li>
          <li>{!! link_to('annotation-user/index','Statistiques') !!}</li>
          <li>{!! link_to('corpus/compare','Statistiques par parser') !!}</li>
          <li>{!! link_to('corpus/diff-by-relation','Diff by relation') !!}</li>
          <li>{!! link_to('corpus/stat-player','Statistics about players') !!}</li>
        </ul>
      </li>
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="collapse" data-target="#collapseGame" aria-expanded="false" aria-controls="collapseGame" href="#">Game</a>
        <ul class="collapse" id="collapseGame">
          <li>{!! link_to('challenge/index',trans('back/index.challenges')) !!}</li>
          <li>{!! link_to('negative-annotation/index',trans('back/index.negative-items')) !!}</li>
          <li>{!! link_to('tutorial-annotation/index',trans('back/index.tutorial-items')) !!}</li>
          <li>{!! link_to('constant-game/index',trans('back/index.constants-game')) !!}</li>
          <li>{!! link_to('trophy/index',trans('back/index.trophies')) !!}</li>
          <li>{!! link_to('admin/mwe',trans('back/index.play-rigor-mortis')) !!}</li>
          <li>{!! link_to('mini-game/index','Jeux langue française') !!}</li>
        </ul>
      </li>
      <li>{!! link_to('annotator/index','Annotateur') !!}</li>
      <li>{!! link_to('faq/admin-index','FAQ') !!}</li>
      <li>{!! link_to('message/admin-index','Forum') !!}</li>
      <li>{!! link_to('news',trans('back/index.news')) !!}</li>
      <li>{!! link_to('sentence/index',trans('back/index.sentences')) !!}</li>
      <li>{!! link_to('user/index-admin',trans('back/index.users')) !!}</li>
      <li>{!! link_to('admin/reporting',trans('back/index.reporting')) !!}</li>
      <li>{!! link_to('translation',trans('back/index.translations')) !!}</li>
      <li>{!! link_to('language',trans('back/index.language')) !!}</li>
    </ul>
  </div>
  <div class="col-10">
    @yield('content')
  </div>
</div>

@stop