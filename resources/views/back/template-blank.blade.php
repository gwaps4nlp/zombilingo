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
  <div class="col-12">
    @yield('content')
  </div>
</div>

@stop