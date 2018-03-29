@extends('front.master')

@section('container')
<div class="parallax">
  <div id="body-game" class="parallax__layer parallax__layer--back">
  </div>
  <div class="container parallax__layer parallax__layer--base px-0" style="padding-top:70px;">
    @yield('main')
  </div>
</div>
<style type="text/css">
.parallax {
  perspective: 1px;
  height: 100vh;
  overflow-x: hidden;
  overflow-y: auto;
}
.parallax__layer {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}
.parallax__layer--base {
  transform: translateZ(0);
}
.parallax__layer--back {
  transform: translateZ(-5px)  scale(6);
}	
body {
	padding-top:0;
}
</style>
@stop
